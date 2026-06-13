<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Modules\Clinic\Models\Clinics;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class ClinicExport implements FromCollection, WithHeadings, WithEvents, WithCustomStartCell
{
    public array $columns;
    public array $dateRange;

    public function __construct($columns, $dateRange)
    {
        $this->columns = $columns;
        $this->dateRange = $dateRange;
    }
    public function startCell(): string
    {
        return 'A3'; // Data starts from row 5
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

        $query = Clinics::SetRole(auth()->user())
            ->with('clinicdoctor', 'specialty', 'clinicdoctor', 'receptionist')
            ->whereDate('created_at', '>=', $this->dateRange[0])
            ->whereDate('created_at', '<=', $this->dateRange[1])
            ->get();

        $newQuery = $query->map(function ($row) {
            $selectedData = [];

            foreach ($this->columns as $column) {
                switch ($column) {
                    case 'status':
                        $selectedData[$column] = 'inactive';
                        if ($row[$column]) {
                            $selectedData[$column] = 'active';
                        }
                        break;
                    case 'system_service_category':
                        $selectedData[$column] = optional($row->specialty)->name;
                        break;
                    case 'vendor_id':
                        $selectedData[$column] = optional($row->vendor)->full_name;
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
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->columns));

                // Show "From Date" and "To Date" in one line, centered
                $sheet->setCellValue('A1', "From Date: {$this->dateRange[0]}    To Date: {$this->dateRange[1]}");
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->getStyle("A1")->getFont()->setBold(true);
                $sheet->getStyle("A1")->getFont()->setSize(12);
                $sheet->getStyle("A1")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Set custom column widths based on the columns selected
                $customWidths = [
                    'name' => 15,
                    'system_service_category' => 15,
                    'description' => 30,
                    'contact_number' => 15,
                    'vendor_id' => 15,
                    'status' => 12,
                ];

                foreach ($this->columns as $index => $column) {
                    $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
                    $width = isset($customWidths[$column]) ? $customWidths[$column] : 20;
                    $sheet->getColumnDimension($columnLetter)->setWidth($width);
                }

                // Style the headings row
                $headerRow = 3; // Since data starts at A3, headings are at row 3
                $headerRange = "A{$headerRow}:{$lastColumn}{$headerRow}";
                $sheet->getStyle($headerRange)->getFont()->setBold(true);
                $sheet->getStyle($headerRange)->getFont()->setSize(12);
                $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
