<?php

namespace App\Exports;

use Modules\Appointment\Models\Medicine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class MedicineExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Medicine::query();

        // Apply filters if any
        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        $columns = $this->request->get('columns', []);
        
        $headings = [];
        $availableHeadings = [
            'name' => 'Medicine Name',
            'generic_name' => 'Generic Name',
            'brand_name' => 'Brand Name',
            'strength' => 'Strength',
            'dosage_form' => 'Dosage Form',
            'manufacturer' => 'Manufacturer',
            'category' => 'Category',
            'formulae' => 'Formulae',
            'indication' => 'Indication',
            'side_effects' => 'Side Effects',
            'contraindication' => 'Contraindication',
            'drug_interactions' => 'Drug Interactions',
            'pregnancy_category' => 'Pregnancy Category',
            'storage_conditions' => 'Storage Conditions',
            'price' => 'Price',
            'url' => 'Reference URL',
            'status' => 'Status',
        ];

        foreach ($columns as $column) {
            if (isset($availableHeadings[$column])) {
                $headings[] = $availableHeadings[$column];
            }
        }

        return $headings;
    }

    /**
     * @param Medicine $medicine
     * @return array
     */
    public function map($medicine): array
    {
        $columns = $this->request->get('columns', []);
        
        $data = [];
        foreach ($columns as $column) {
            switch ($column) {
                case 'name':
                    $data[] = $medicine->name;
                    break;
                case 'generic_name':
                    $data[] = $medicine->generic_name;
                    break;
                case 'brand_name':
                    $data[] = $medicine->brand_name;
                    break;
                case 'strength':
                    $data[] = $medicine->strength;
                    break;
                case 'dosage_form':
                    $data[] = $medicine->dosage_form;
                    break;
                case 'manufacturer':
                    $data[] = $medicine->manufacturer;
                    break;
                case 'category':
                    $data[] = $medicine->category;
                    break;
                case 'formulae':
                    $data[] = $medicine->formulae;
                    break;
                case 'indication':
                    $data[] = $medicine->indication;
                    break;
                case 'side_effects':
                    $data[] = $medicine->side_effects;
                    break;
                case 'contraindication':
                    $data[] = $medicine->contraindication;
                    break;
                case 'drug_interactions':
                    $data[] = $medicine->drug_interactions;
                    break;
                case 'pregnancy_category':
                    $data[] = $medicine->pregnancy_category;
                    break;
                case 'storage_conditions':
                    $data[] = $medicine->storage_conditions;
                    break;
                case 'price':
                    $data[] = $medicine->price ? '$' . number_format($medicine->price, 2) : '';
                    break;
                case 'url':
                    $data[] = $medicine->url;
                    break;
                case 'status':
                    $data[] = $medicine->status ? 'Active' : 'Inactive';
                    break;
                default:
                    $data[] = '';
            }
        }

        return $data;
    }
}