<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Clinic\Models\Receptionist;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
class DoctorExport implements FromCollection, WithHeadings, WithEvents, WithCustomStartCell
{
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
        $query = User::role('doctor')->SetRole(auth()->user())->with('doctor', 'doctorclinic');

        $query->whereDate('users.created_at', '>=', $this->dateRange[0]);

        $query->whereDate('users.created_at', '<=', $this->dateRange[1]);

        $query->orderBy('users.updated_at', 'desc');

        $query = $query->get();

        $newQuery = $query->map(function ($row) {
            $selectedData = [];

            foreach ($this->columns as $column) {
                switch ($column) {
                    case 'Name':
                        $selectedData[$column] = $row->full_name;
                        break;

                    case 'mobile':
                        $selectedData[$column] = $row->mobile;
                        break;

                    case 'varification_status':
                        $selectedData[$column] = __('customer.msg_unverified');
                        if ($row['email_verified_at']) {
                            $selectedData[$column] = __('customer.msg_verified');
                        }
                        break;

                    case 'is_banned':
                        $selectedData[$column] = 'no';
                        if ($row[$column]) {
                            $selectedData[$column] = 'yes';
                        }
                        break;
                    case 'Clinic Center':
                        $clinics = $row->doctor->doctorclinic->map(function ($doctorclinic) {
                            return optional($doctorclinic->clinics)->name;
                        })->implode(',');
                        $selectedData[$column] = $clinics;
                        break;

                    case 'status':
                        $selectedData[$column] = 'Inactive';
                        if ($row[$column]) {
                            $selectedData[$column] = 'Active';
                        }
                        break;

                    case 'service_providers':
                        $selectedData[$column] = implode(', ', optional($row->mainServiceProvider)->pluck('name')->toArray()) ?? '-';
                        break;

                    case 'gender':
                        // Make first letter capital
                        $selectedData[$column] = ucfirst(strtolower($row[$column]));
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
                'Doctor List',
                $this->columns,
                $this->dateRange
            ),
        ];
    }
}
