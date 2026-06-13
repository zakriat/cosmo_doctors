<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Clinic\Models\ClinicsService;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use App\Exports\Traits\CurrencyFormatting;

class ClinicsServiceExport implements FromCollection, WithHeadings, WithEvents, WithCustomStartCell
{
    use CurrencyFormatting;
    public array $columns;

    public array $dateRange;

    public function __construct($columns, $dateRange)
    {
        $this->columns = $columns;
        $this->dateRange = $dateRange;
    }

    public function headings(): array
    {
        $modifiedHeadings = [];

        foreach ($this->columns as $column) {
            switch ($column) {
                case 'system_service_id':
                    $modifiedHeadings[] = 'Service Name';
                    break;
                case 'charges':
                    $modifiedHeadings[] = 'Price';
                    break;
                case 'category_id':
                    $modifiedHeadings[] = 'Category';
                    break;
                case 'vendor_id':
                    $modifiedHeadings[] = 'Clinic Admin';
                    break;
                default:
                    // Capitalize each word and replace underscores with spaces
                    $modifiedHeadings[] = ucwords(str_replace('_', ' ', $column));
                    break;
            }
        }

        return $modifiedHeadings;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $userId = auth()->id();


        $query = ClinicsService::SetRole(auth()->user())
            ->with('sub_category', 'doctor_service', 'ClinicServiceMapping', 'systemservice')
            ->where('status', 1);

        $query->whereDate('created_at', '>=', $this->dateRange[0]);
        $query->whereDate('created_at', '<=', $this->dateRange[1]);

        $query = $query->get();

        $newQuery = $query->map(function ($row) {
            $selectedData = [];

            foreach ($this->columns as $column) {
                switch ($column) {
                    case 'system_service_id':
                        $selectedData[$column] = $row->name ?? '-';
                        break;
                    case 'charges':
                        // Format amount with currency symbol from currency table
                        $selectedData[$column] = $this->formatAmountWithCurrencyNoDecimals($row->charges);
                        break;
                    case 'status':
                        $selectedData[$column] = 'Inactive';
                        if ($row[$column]) {
                            $selectedData[$column] = 'Active';
                        }
                        break;
                    case 'system_service_category':
                        $selectedData[$column] = optional($row->specialty)->name;
                        break;
                    case 'vendor_id':
                        $selectedData[$column] = optional($row->vendor)->full_name;
                        break;
                    case 'category_id':
                        $selectedData[$column] = $row->category->name;
                        break;
                    default:
                        $selectedData[$column] = $row[$column];
                        break;
                }
            }

            return $selectedData;
        });

        return $newQuery;
    }
    public function startCell(): string
    {
        return 'A3'; // Data starts from row 3
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => exportSheetHeader(
                'Clinic Service List',
                $this->columns,
                $this->dateRange
            ),
        ];
    }
}
