<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Appointment\Models\BillingRecord;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use App\Currency\CurrencyChange;
use App\Exports\Traits\CurrencyFormatting;

class BillingExport implements FromCollection, WithHeadings, WithEvents, WithCustomStartCell
{
    use CurrencyFormatting;
    public array $columns;

    public array $dateRange;

    public function __construct($columns, $dateRange)
    {
        $this->columns = $columns;
        $this->dateRange = $dateRange;
    }
    public function startCell(): string
    {
        return 'A3'; // Data starts from row 3
    }
    public function headings(): array
    {
        $modifiedHeadings = [];

        foreach ($this->columns as $column) {
            // Custom label mapping
            switch ($column) {
                case 'user_id':
                    $modifiedHeadings[] = 'Patient Name';
                    break;
                case 'clinic_id':
                    $modifiedHeadings[] = 'Clinic Name';
                    break;
                case 'doctor_id':
                    $modifiedHeadings[] = 'Doctor Name';
                    break;
                case 'service_id':
                    $modifiedHeadings[] = 'Service Name';
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
        $query = BillingRecord::SetRole(auth()->user());

        $query->whereDate('created_at', '>=', $this->dateRange[0]);

        $query->whereDate('created_at', '<=', $this->dateRange[1]);

        $query = $query->get();

        // Initialize currency formatter
        $currencyChange = new CurrencyChange();

        $newQuery = $query->map(function ($row) use ($currencyChange) {
            $selectedData = [];

            foreach ($this->columns as $column) {
                switch ($column) {
                    case 'encounter_id':
                        $selectedData[$column] = $row->encounter_id;

                        break;

                    case 'user_id':
                        $selectedData[$column] = optional($row->user)->full_name;

                        break;
                    case 'doctor_id':

                        $selectedData[$column] = optional($row->doctor)->full_name;
                        break;

                    case 'clinic_id':
                        $selectedData[$column] = optional($row->clinic)->name;
                        break;

                    case 'service_id':
                        $selectedData[$column] = optional($row->clinicservice)->name;
                        break;

                    case 'total_amount':
                        // Format amount with currency symbol from currency table
                        $selectedData[$column] = $this->formatAmountWithCurrencyNoDecimals($row->total_amount);
                        break;

                    case 'date':
                        $selectedData[$column] = $row->date;
                        break;

                    case 'payment_status':
                        if ($row->payment_status) {
                            $payment_status = 'Paid';
                        } else {
                            $payment_status = 'Unpaid';
                        }
                        $selectedData[$column] = $payment_status;
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
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => exportSheetHeader(
                'Billing list', // Change this per file, e.g. 'Billing Module'
                $this->columns,
                $this->dateRange
            ),
        ];
    }
}
