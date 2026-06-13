<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Service\Models\Service;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing; // Make sure this is imported
use App\Exports\Traits\CurrencyFormatting;

class ServicesExport implements FromCollection, WithHeadings, WithEvents, WithCustomStartCell
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
            // Capitalize each word and replace underscores with spaces
            $modifiedHeadings[] = ucwords(str_replace('_', ' ', $column));
        }

        return $modifiedHeadings;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Service::query()
            ->with(['category', 'sub_category'])
            ->withCount(['service_providers', 'employee']);

        $query->whereDate('created_at', '>=', $this->dateRange[0]);

        $query->whereDate('created_at', '<=', $this->dateRange[1]);

        $query = $query->orderBy('updated_at', 'desc');

        $query = $query->get();

        $newQuery = $query->map(function ($row) {
            $selectedData = [];
            foreach ($this->columns as $column) {
                switch ($column) {
                    case 'status':
                        $selectedData[$column] = 'Inactive';
                        if ($row[$column]) {
                            $selectedData[$column] = 'Active';
                        }
                        break;

                    case 'default_price':
                        $selectedData[$column] = $this->formatAmountWithCurrencyNoDecimals($row->default_price);
                        break;

                    case 'duration_min':
                        $selectedData[$column] = $row->duration_min . ' Min';
                        break;

                    case 'service_providers':
                        $selectedData[$column] = $row->service_providers_count ?? 0;
                        break;

                    case 'employees':
                        $selectedData[$column] = $row->employee_count ?? 0;
                        break;

                    case 'category':
                        $category = isset($row->category->name) ? $row->category->name : '-';
                        if (isset($row->sub_category->name)) {
                            $category = $category . ' > ' . $row->sub_category->name;
                        }
                        $selectedData[$column] = $category;
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
                'Service Module', // Change this per module
                $this->columns,
                $this->dateRange
            ),
        ];
    }
}
