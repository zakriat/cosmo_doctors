<?php

namespace Modules\Appointment\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Appointment\Models\Medicine;
use Yajra\DataTables\DataTables;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Maatwebsite\Excel\Facades\Excel;

class MedicinesController extends Controller
{
    protected string $exportClass = '\App\Exports\MedicineExport';

    public function __construct()
    {
        $this->module_title = 'medicines.title';
        $this->module_name = 'medicines';
        $this->module_icon = 'fa-solid fa-pills';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];

        $module_action = 'List';
        $columns = CustomFieldGroup::columnJsonValues(new Medicine());
        $customefield = CustomField::exportCustomFields(new Medicine());

        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => __('medicines.lbl_name'),
            ],
            [
                'value' => 'generic_name',
                'text' => __('medicines.lbl_generic_name'),
            ],
            [
                'value' => 'brand_name',
                'text' => __('medicines.lbl_brand_name'),
            ],
            [
                'value' => 'strength',
                'text' => __('medicines.lbl_strength'),
            ],
            [
                'value' => 'dosage_form',
                'text' => __('medicines.lbl_dosage_form'),
            ],
            [
                'value' => 'manufacturer',
                'text' => __('medicines.lbl_manufacturer'),
            ],
            [
                'value' => 'category',
                'text' => __('medicines.lbl_category'),
            ],
            [
                'value' => 'price',
                'text' => __('medicines.lbl_price'),
            ],
            [
                'value' => 'status',
                'text' => __('medicines.lbl_status'),
            ],
        ];

        $export_url = route('backend.medicines.export');

        return view('appointment::backend.medicines.index_datatable', compact(
            'module_action', 'filter', 'columns', 'customefield',
            'export_import', 'export_columns', 'export_url'
        ));
    }

    /**
     * Get data for DataTables
     */
    public function index_data(DataTables $datatable, Request $request)
    {
        $query = Medicine::query();

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }

        return $datatable->eloquent($query)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row" id="datatable-row-' . $data->id . '" name="datatable_ids[]" value="' . $data->id . '" onclick="dataTableRowCheck(' . $data->id . ')">';
            })
            ->addColumn('action', function ($data) {
                return view('appointment::backend.medicines.action_column', compact('data'));
            })
            ->editColumn('name', function ($data) {
                return view('appointment::backend.medicines.name_column', compact('data'));
            })
            ->editColumn('price', function ($data) {
                return $data->price ? '$' . number_format($data->price, 2) : '-';
            })
            ->editColumn('status', function ($data) {
                $checked = $data->status ? 'checked="checked"' : '';
                return '
                    <div class="form-check form-switch">
                        <input type="checkbox" data-url="' . route('backend.medicines.update_status', $data->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input" id="datatable-row-' . $data->id . '" name="status" value="' . $data->id . '" ' . $checked . '>
                    </div>
                ';
            })
            ->filterColumn('name', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->where(function ($q) use ($keyword) {
                        $q->where('name', 'like', '%' . $keyword . '%')
                          ->orWhere('generic_name', 'like', '%' . $keyword . '%')
                          ->orWhere('brand_name', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->orderColumn('name', function ($query, $order) {
                $query->orderBy('name', $order);
            }, 1)
            ->rawColumns(['action', 'name', 'status', 'check'])
            ->toJson();
    }

    /**
     * Get list for dropdowns
     */
    public function index_list(Request $request)
    {
        $query = Medicine::active();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $medicines = $query->orderBy('name')->get();

        $data = $medicines->map(function ($medicine) {
            return [
                'id' => $medicine->id,
                'name' => $medicine->display_name,
                'generic_name' => $medicine->generic_name,
                'brand_name' => $medicine->brand_name,
                'strength' => $medicine->strength,
                'dosage_form' => $medicine->dosage_form,
                'formulae' => $medicine->formulae,
                'side_effects' => $medicine->side_effects,
                'url' => $medicine->url,
                'bnf_url' => $medicine->bnf_url,
                'indication' => $medicine->indication,
                'contraindication' => $medicine->contraindication,
                'drug_interactions' => $medicine->drug_interactions,
            ];
        });

        if ($request->is('api/*')) {
            return response()->json(['status' => true, 'data' => $data, 'message' => __('medicines.medicine_list')]);
        }

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'strength' => 'nullable|string|max:100',
            'dosage_form' => 'nullable|string|max:100',
            'manufacturer' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'url' => 'nullable|url|max:500',
        ]);

        $data = $request->except(['_token', '_method']);
        $data['status'] = $request->has('status') ? 1 : 0;

        $medicine = Medicine::create($data);

        if ($request->custom_fields_data) {
            $medicine->updateCustomFieldData(json_decode($request->custom_fields_data));
        }

        $message = __('messages.create_form', ['form' => __('medicines.singular_title')]);

        if ($request->is('api/*')) {
            return response()->json(['message' => $message, 'data' => $medicine, 'status' => true], 200);
        }

        return redirect()->route('backend.medicines.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $medicine = Medicine::findOrFail($id);
        return response()->json(['data' => $medicine, 'status' => true]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $medicine = Medicine::findOrFail($id);
        $customefield = CustomField::exportCustomFields(new Medicine());

        return response()->json([
            'medicine' => $medicine,
            'customfields' => $customefield,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'strength' => 'nullable|string|max:100',
            'dosage_form' => 'nullable|string|max:100',
            'manufacturer' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'url' => 'nullable|url|max:500',
        ]);

        $medicine = Medicine::findOrFail($id);
        $data = $request->except(['_token', '_method']);
        $data['status'] = $request->has('status') ? 1 : 0;

        $medicine->update($data);

        if ($request->custom_fields_data) {
            $medicine->updateCustomFieldData(json_decode($request->custom_fields_data));
        }

        $message = __('messages.update_form', ['form' => __('medicines.singular_title')]);

        if ($request->is('api/*')) {
            return response()->json(['message' => $message, 'data' => $medicine, 'status' => true], 200);
        }

        return redirect()->route('backend.medicines.index')->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (\Auth::user()->hasAnyRole(['demo_admin'])) {
            return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
        }

        $medicine = Medicine::findOrFail($id);
        $medicine->delete();

        $message = __('medicines.medicine_delete');
        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Update status
     */
    public function update_status(Request $request, Medicine $id)
    {
        $id->update(['status' => $request->status]);
        return response()->json(['status' => true, 'message' => __('medicines.medicine_status')]);
    }

    /**
     * Export medicines
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        $filename = 'medicines_' . date('Y-m-d_H-i-s');
        
        if ($format === 'csv') {
            return Excel::download(new \App\Exports\MedicineExport($request), $filename . '.csv', \Maatwebsite\Excel\Excel::CSV);
        }
        
        return Excel::download(new \App\Exports\MedicineExport($request), $filename . '.xlsx');
    }

    /**
     * Bulk action
     */
    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                Medicine::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('medicines.medicine_status');
                break;

            case 'delete':
                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                Medicine::whereIn('id', $ids)->delete();
                $message = __('medicines.medicine_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('medicines.invalid_action')]);
        }

        return response()->json(['status' => true, 'message' => $message]);
    }
}