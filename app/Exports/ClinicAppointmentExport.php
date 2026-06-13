<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Appointment\Models\Appointment;
use App\Models\Setting;
use Modules\Currency\Models\Currency;
use App\Exports\Traits\CurrencyFormatting;

class ClinicAppointmentExport implements FromCollection, WithHeadings, WithCustomStartCell, WithEvents
{
    use CurrencyFormatting;
    public array $columns;
    public array $dateRange;
    public $exportDoctorId;

    public function __construct($columns, $dateRange, $exportDoctorId)
    {
        $this->columns = $columns;
        $this->dateRange = $dateRange;
        $this->exportDoctorId = $exportDoctorId;
    }

    /**
     * Get the default currency for formatting amounts
     */
    private function getDefaultCurrency()
    {
        return Currency::getAllCurrency()->where('is_primary', 1)->first();
    }

    /**
     * Format amount with currency symbol
     */
    private function formatAmount($amount)
    {
        $currency = $this->getDefaultCurrency();
        if (!$currency || !$amount) {
            return $amount;
        }

        $noOfDecimal = $currency->no_of_decimal;
        $decimalSeparator = $currency->decimal_separator;
        $thousandSeparator = $currency->thousand_separator;
        $currencyPosition = $currency->currency_position;
        $currencySymbol = $currency->currency_symbol;

        // If amount is a whole number, don't show decimals
        if (floor($amount) == $amount) {
            $noOfDecimal = 0;
        }

        return formatCurrency($amount, $noOfDecimal, $decimalSeparator, $thousandSeparator, $currencyPosition, $currencySymbol);
    }

    /**
     * Define the starting cell for the data rows.
     */
    public function startCell(): string
    {
        return 'A3'; // Data starts from row 3
    }

    /**
     * Define the headings for the data table.
     */
    public function headings(): array
    {
        return array_map(function ($column) {
            return ucwords(str_replace('_', ' ', $column));
        }, $this->columns);
    }

    /**
     * Collect the data to be exported.
     */
    public function collection()
    {
        $query = Appointment::SetRole(auth()->user())
            ->with('payment', 'commissionsdata', 'patientEncounter', 'cliniccenter', 'appointmenttransaction')
            ->whereRaw(
                'CAST(CONCAT(appointment_date, " ", appointment_time) AS DATETIME) >= ?',
                [$this->dateRange[0]]
            )
            ->whereRaw(
                'CAST(CONCAT(appointment_date, " ", appointment_time) AS DATETIME) <= ?',
                [$this->dateRange[1]]
            )
            ->orderBy('id', 'asc')
            ->get();

        return $query->map(function ($row) {
            $selectedData = [];

            foreach ($this->columns as $column) {
                switch ($column) {
                    case 'Patient Name':
                        $selectedData[$column] = optional($row->user)->full_name;
                        break;

                    case 'doctor':
                        $selectedData[$column] = optional($row->doctor)->full_name;
                        break;

                    case 'services':
                        $selectedData[$column] = optional($row->clinicservice)->name;
                        break;

                    case 'start_date_time':
                        $setting = Setting::where('name', 'date_formate')->first();
                        $dateformate = $setting ? $setting->val : 'Y-m-d';
                        $setting = Setting::where('name', 'time_formate')->first();
                        $timeformate = $setting ? $setting->val : 'h:i A';
                        $date = date($dateformate, strtotime($row->appointment_date ?? '--'));
                        $time = date($timeformate, strtotime($row->appointment_time ?? '--'));

                        $selectedData[$column] = $date . ' ' . $time;
                        break;

                    case 'payment_status':
                        if ($row->appointmenttransaction) {
                            if ($row->appointmenttransaction->payment_status == 1) {
                                $selectedData[$column] = 'Paid';
                            } elseif ($row->appointmenttransaction->advance_payment_status == 1) {
                                $selectedData[$column] = 'Advance Paid';
                            } else {
                                $selectedData[$column] = 'Pending';
                            }
                        } else {
                            $selectedData[$column] = 'Pending';
                        }
                        break;

                    case 'service_amount':
                    case 'total_amount':
                    case 'advance_paid_amount':
                    case 'amount':
                        $selectedData[$column] = $this->formatAmountWithCurrencyNoDecimals($row[$column]);
                        break;

                    case 'status':
                        $statusMap = [
                            'pending' => 'Pending',
                            'confirmed' => 'Confirmed',
                            'check_in' => 'Check In',
                            'checkout' => 'Completed',
                            'cancelled' => 'Cancelled',
                        ];
                        $selectedData[$column] = $statusMap[$row[$column]] ?? ucfirst($row[$column]);
                        break;

                    default:
                        $selectedData[$column] = $row[$column];
                        break;
                }
            }

            return $selectedData;
        });
    }

    /**
     * Customize the sheet using events.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => exportSheetHeader(
                'Clinic Appointment List',
                $this->columns,
                $this->dateRange
            ),
        ];
    }
}
