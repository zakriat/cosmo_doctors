<?php

namespace App\Exports;

use Modules\Clinic\Models\DoctorClinicMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Clinic\Models\DoctorSession;
use Modules\Clinic\Models\Doctor;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
class DoctorSessionExport implements FromCollection, WithHeadings, WithEvents, WithCustomStartCell
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
        $userId = auth()->id();
        $query = DoctorClinicMapping::query()->with(['clinics', 'doctor']);
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')) {
            $query;
        } else if (auth()->user()->hasRole('vendor')) {
            $query = DoctorClinicMapping::query()->with('clinics')->whereHas('clinics', function ($query) use ($userId) {
                $query->where('vendor_id', $userId);
            })->whereHas('doctor', function ($query) use ($userId) {
                $query->where('vendor_id', $userId)
                    ->whereNull('deleted_at');
            });

        } else if (auth()->user()->hasRole('doctor')) {
            $query = DoctorClinicMapping::query()->where('doctor_id', $userId);
        } else if (auth()->user()->hasRole('receptionist')) {
            $query = DoctorClinicMapping::query()->with('clinics')->whereHas('clinics.receptionist', function ($query) use ($userId) {
                $query->where('receptionist_id', $userId);
            });
        }

        $query->whereDate('created_at', '>=', $this->dateRange[0]);

        $query->whereDate('created_at', '<=', $this->dateRange[1]);

        $query->orderBy('updated_at', 'desc');

        $query = $query->get();

        $newQuery = $query->map(function ($row) {
            $selectedData = [];

            foreach ($this->columns as $column) {
                switch ($column) {
                    case 'varification_status':
                        $selectedData[$column] = __('customer.msg_unverified');
                        if ($row['email_verified_at']) {
                            $selectedData[$column] = __('customer.msg_verified');
                        }
                        break;

                    case 'doctor_id':
                        $doctorData = Doctor::where('doctor_id', $row->doctor_id)->first();
                        $doctor = $doctorData->user;
                        if ($doctor) {
                            $selectedData[$column] = $doctor->first_name . ' ' . $doctor->last_name;
                        } else {
                            return '';
                        }

                        break;


                    case 'Clinic Name':
                        $clinic = $row->clinics;
                        if ($clinic) {
                            $selectedData[$column] = $clinic->name;
                        } else {
                            $selectedData[$column] = '';
                        }
                        break;

                    case 'status':
                        $selectedData[$column] = 'Inactive';
                        if ($row[$column]) {
                            $selectedData[$column] = 'Active';
                        }
                        break;

                    case 'Working Day':
                        $allDays = DoctorSession::where('doctor_id', $row->doctor_id)
                            ->where('is_holiday', 0)
                            ->where('clinic_id', $row->clinic_id)
                            ->pluck('day')
                            ->toArray();
                        $selectedData[$column] = implode(',', $allDays);
                        break;
                    case 'start_time':
                        $startTime = DoctorSession::where('doctor_id', $row->doctor_id)
                            ->where('clinic_id', $row->clinic_id)->first();
                        if ($startTime) {
                            $selectedData[$column] = $startTime->start_time;
                        } else {
                            $selectedData[$column] = '';
                        }
                        break;
                    case 'end_time':
                        $endTime = DoctorSession::where('doctor_id', $row->doctor_id)
                            ->where('clinic_id', $row->clinic_id)->first();
                        if ($endTime) {
                            $selectedData[$column] = $endTime->end_time;
                        } else {
                            $selectedData[$column] = '';
                        }
                        break;

                    case 'service_providers':
                        $selectedData[$column] = implode(', ', optional($row->mainServiceProvider)->pluck('name')->toArray()) ?? '-';
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
                'Doctor Session List',
                $this->columns,
                $this->dateRange
            ),
        ];
    }
}
