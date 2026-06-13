<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Appointment\Models\EncounterPrescription;

class EncounterPrescriptionExport implements FromCollection, WithHeadings
{
    public array $columns;

    public int $encounter_id;

    public function __construct($columns, $encounter_id)
    {
        $this->columns = $columns;
        $this->encounter_id = $encounter_id;
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
        $query = EncounterPrescription::query();

        $query->where('encounter_id', $this->encounter_id);

        $query = $query->get();

        $newQuery = $query->map(function ($row) {
            $selectedData = [];

            foreach ($this->columns as $column) {
                switch ($column) {

                    case 'ID':
                        $selectedData[$column] = $row->id;

                        break;
                    case 'Name':

                        $selectedData[$column] = $row->name;
                        break;

                    case 'Frequency':
                        $selectedData[$column] = $row->frequency;
                        break;

                    case 'Duration':
                        $selectedData[$column] = $row->duration;
                        break;
                    case 'Instruction':
                        $selectedData[$column] = $row->instruction;
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
                'Encounter Prescription List',
                $this->columns,
                $this->dateRange
            ),
        ];
    }
}
