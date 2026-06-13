<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Appointment\Models\PatientEncounter;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
class EncounterExport implements FromCollection, WithHeadings, WithEvents, WithCustomStartCell
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
        $query = PatientEncounter::SetRole(auth()->user());

        $query->whereDate('created_at', '>=', $this->dateRange[0]);

        $query->whereDate('created_at', '<=', $this->dateRange[1]);

        $query = $query->get();



        $newQuery = $query->map(function ($row) {
            $selectedData = [];

            foreach ($this->columns as $column) {
                switch ($column) {

                    case 'user_id':
                        $selectedData[$column] = optional($row->user)->full_name;

                        break;
                    case 'doctor_id':

                        $selectedData[$column] = optional($row->doctor)->full_name;
                        break;

                    case 'clinic_id':
                        $selectedData[$column] = optional($row->clinic)->name;
                        break;

                    case 'encounter_date':
                        $selectedData[$column] = $row->encounter_date;
                        break;
                    case 'status':
                        $selectedData[$column] = 'close';
                        if ($row[$column]) {
                            $selectedData[$column] = 'open';
                        }
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
                'Encounter List',
                $this->columns,
                $this->dateRange
            ),
        ];
    }
}
