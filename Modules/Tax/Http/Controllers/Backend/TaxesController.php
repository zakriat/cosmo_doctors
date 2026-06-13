<?php

namespace Modules\Tax\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Appointment\Trait\AppointmentTrait;
use Modules\Clinic\Models\ClinicsService;
use Modules\Tax\Http\Requests\TaxRequest;
use Modules\Tax\Models\Tax;
use Modules\Tax\Models\TaxService;
use Yajra\DataTables\DataTables;

class TaxesController extends Controller
{
    // use Authorizable;
    use AppointmentTrait;
    protected string $exportClass = '\App\Exports\TaxExport';
    public function __construct()
    {
        // Page Title
        $this->module_title = 'tax.title';

        // module name
        $this->module_name = 'tax';

        // directory path of the module
        $this->module_path = 'tax::backend';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => 'fa-regular fa-sun',
            'module_name' => $this->module_name,
            'module_path' => $this->module_path,
        ]);
        $this->middleware(['permission:view_tax'])->only('index');
        $this->middleware(['permission:edit_tax'])->only('edit', 'update');
        $this->middleware(['permission:add_tax'])->only('store');
        $this->middleware(['permission:delete_tax'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $module_action = 'List';
        $filter = [
            'status' => $request->status,
        ];
        $export_import = true;
        $export_columns = [
            [
                'value' => 'title',
                'text' => __('tax.lbl_title'),
            ],
            [
                'value' => 'value',
                'text' => __('tax.lbl_value'),
            ],
            [
                'value' => 'type',
                'text' => __('tax.lbl_Type'),
            ],
            [
                'value' => 'status',
                'text' => __('tax.lbl_status'),
            ],
        ];
        $export_url = route('backend.tax.export');
        return view('tax::backend.tax.index_datatable', compact('module_action', 'filter', 'export_import', 'export_columns', 'export_url'));
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {

        $query_data = Tax::Active();


        if ($request->has('module_type') && $request->module_type != '') {

            $query_data = $query_data->whereNull('module_type')->orWhere('module_type', $request->module_type);
        }
        if ($request->has('tax_type') && $request->tax_type != '') {

            $query_data = $query_data->whereNull('tax_type')->orWhere('tax_type', $request->tax_type);
        }

        $data = $query_data->where('status', 1)->get();
        $data = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'value' => $item->value,
                'type' => $item->type,
                'tax_type' => $item->tax_type,
                'status' => $item->status,
                'module_type' => $item->module_type,
            ];
        });

        // dd($data);

        return response()->json($data);
    }

    public function update_status(Request $request, Tax $id)
    {
        $id->update(['status' => $request->status]);

        $this->updateInclusiveTaxOnServices();

        return response()->json(['status' => true, 'message' => __('service_providers.status_update')]);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $query = Tax::query();

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        return $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row "  id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->addColumn('action', function ($data) {
                return view('tax::backend.tax.action_column', compact('data'));
            })
            ->editColumn('status', function ($row) {
                $checked = '';
                if ($row->status) {
                    $checked = 'checked="checked"';
                }

                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="' . route('backend.tax.update_status', $row->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $row->id . '"  name="status" value="' . $row->id . '" ' . $checked . '>
                    </div>
                ';
            })
            ->editColumn('value', function ($data) {
                if ($data->type === 'fixed') {
                    $value = \Currency::format($data->value ?? 0);

                    return $value;
                }
                if ($data->type === 'percent') {
                    $value = $data->value . '%';

                    return $value;
                }
            })
            ->editColumn('type', function ($data) {
                return ucwords($data->type);
            })
            ->editColumn('tax_type', function ($data) {
                return ucwords($data->tax_type);
            })
            ->editColumn('updated_at', function ($data) {
                $module_name = $this->module_name;

                $diff = Carbon::now()->diffInHours($data->updated_at);

                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })
            ->rawColumns(['action', 'status', 'check', 'type'])
            ->orderColumns(['id'], '-:column $1')
            ->make(true);
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        // dd($actionType, $ids, $request->status);
        switch ($actionType) {
            case 'change-status':
                $service_providers = Tax::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_tax_update');
                break;

            case 'delete':
                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                Tax::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_tax_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('service_providers.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(TaxRequest $request)
    {
        $data = $request->all();
         $data['module_type'] = 'services';

        $query = Tax::create($data);

        $this->updateInclusiveTaxOnServices();

        $message = __('messages.create_form', ['form' => __($this->module_title)]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $module_action = 'Edit';

        $data = Tax::findOrFail($id);

        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(TaxRequest $request, $id)
    {
        $tax = Tax::findOrFail($id); // Fetch the Tax record
        $data = $request->all();
        $data['module_type'] = 'services';
        $tax->update($data);

        $this->updateInclusiveTaxOnServices();

        $message = __('messages.update_form', ['form' => __($this->module_title)]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        if (env('IS_DEMO')) {
            return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
        }

        $data = Tax::findOrFail($id);

        $data->delete();

        $this->updateInclusiveTaxOnServices();

        $message = __('messages.delete_form', ['form' => __($this->module_title)]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    private function updateInclusiveTaxOnServices()
    {
        $inclusive_services = ClinicsService::where('is_inclusive_tax', 1)->get();

        $all_inclusive_taxes = Tax::where('tax_type', 'inclusive')
            ->where('module_type', 'services')
            ->where('status', 1)
            ->get();

        $inclusive_tax_json = json_encode($all_inclusive_taxes);

        foreach ($inclusive_services as $service) {
            $original_amount = $service->charges ?? 0;
            $discount_value = $service->discount_value;
            $discount_type = $service->discount_type;

            if (!empty($discount_value) && !empty($discount_type)) {
                if ($discount_type === 'percentage') {
                    $discounted_amount = $original_amount - ($original_amount * $discount_value / 100);
                } else {
                    $discounted_amount = $original_amount - $discount_value;
                }
                $service_amount = max(0, $discounted_amount);
            } else {
                $service_amount = $original_amount;
            }

            $tax_result = $this->calculate_inclusive_tax($service_amount, $inclusive_tax_json);

            $service->inclusive_tax = $inclusive_tax_json;
            $service->inclusive_tax_price = $tax_result['total_inclusive_tax'];
            $service->save();
        }
    }
}
