<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\Product;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
class ProductExport implements FromCollection, WithHeadings, WithEvents, WithCustomStartCell
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
        $query = Product::with(['brand', 'categories']);

        $query->whereDate('created_at', '>=', $this->dateRange[0]);

        $query->whereDate('created_at', '<=', $this->dateRange[1]);

        $query = $query->get();

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

                    case 'brand':
                        $selectedData[$column] = $row->brand->name ?? '-';
                        break;

                    case 'categories':
                        if (count($row->categories) > 0) {
                            foreach ($row->categories as $key => $value) {
                                $categories = $value->name;
                            }
                        } else {
                            $categories = '-';
                        }

                        $selectedData[$column] = $categories;
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
                'Product List',
                $this->columns,
                $this->dateRange
            ),
        ];
    }
}
