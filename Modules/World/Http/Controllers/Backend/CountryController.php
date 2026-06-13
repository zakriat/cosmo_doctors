<?php

namespace Modules\World\Http\Controllers\Backend;

use App\Models\User;
use App\Authorizable;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CustomField\Models\CustomFieldGroup;
use Modules\CustomField\Models\CustomField;
use Modules\World\Models\Country;
use Yajra\DataTables\DataTables;

class CountryController extends Controller
{
    // use Authorizable;
    protected string $exportClass = '\App\Exports\CountryExport';

    public function __construct()
    {
        // Page Title
        $this->module_title = 'country.title';
        // module name
        $this->module_name = 'country';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];

        $module_action = 'List';
        $columns = CustomFieldGroup::columnJsonValues(new User());

        $customefield = CustomField::exportCustomFields(new User());
        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' =>  __('service.lbl_name'),
            ],
            [
                'value' => 'status',
                'text' => __('service.lbl_status'),
            ],
        ];
        $export_url = route('backend.country.export');
        return view('world::backend.country.index_datatable', compact('module_action', 'filter', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url'));
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {
        $term = trim($request->search);

        $query = Country::query()->where('status', 1);

        if (!empty($term)) {
            $query->where(function ($query) use ($term) {
                $query->where('name', 'like', "%$term%")
                    ->orWhere('currency_name', 'like', "%$term%")
                    ->orWhere('symbol', 'like', "%$term%")
                    ->orWhere('currency_code', 'like', "%$term%");
            });
        }

        $query_data = $query->get();

        $data = $query_data->map(function ($row) {
            return [
                'id' => $row->id,
                'name' => $row->name,
                'currency_name' => $row->currency_name,
                'symbol' => $row->symbol,
                'currency_code' => $row->currency_code,
                'dial_code' => $row->dial_code,
            ];
        });
 
        if ($request->is('api/*')) {
            return response()->json(['status' => true, 'data' => $data, 'message' => __('country_list')]);
        }

        return response()->json($data);
    }


    public function index_data(Datatables $datatable, Request $request)
    {
        $query = Country::query()->where('status', 1);

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }

        return $datatable->eloquent($query)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $data->id . '"  name="datatable_ids[]" value="' . $data->id . '" onclick="dataTableRowCheck(' . $data->id . ')">';
            })
            ->addColumn('action', function ($data) {
                return view('world::backend.country.action_column', compact('data'));
            })

            ->editColumn('status', function ($data) {
                // return $data->getStatusLabelAttribute();
                $checked = '';
                if ($data->status) {
                    $checked = 'checked="checked"';
                }

                return '
                                <div class="form-check form-switch ">
                                    <input type="checkbox" data-url="' . route('backend.country.update_status', $data->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $data->id . '"  name="status" value="' . $data->id . '" ' . $checked . '>
                                </div>
                            ';
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
            ->rawColumns(['action', 'status', 'check'])
            ->orderColumns(['id'], '-:column $1')
            ->toJson();
        //custom field for export
        $customFieldColumns = CustomField::customFieldData($datatable, User::CUSTOM_FIELD_MODEL, null);

        return $datatable->rawColumns(array_merge(['action', 'status', 'name', 'status', 'check', 'image'], $customFieldColumns))
            ->toJson();
    }

    public function update_status(Request $request, Country $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => 'Status Updated']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $data = Country::create($request->all());

        $message = 'New Country Added';

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

        $data = Country::findOrFail($id);

        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = Country::findOrFail($id);

        $data->update($request->all());

        $message = 'Product Tax Updated Successfully';

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
        $data = Country::findOrFail($id);

        $data->delete();

        $message = 'Country Deleted Successfully';

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');
        // dd($actionType, $ids, $request->status);
        switch ($actionType) {
            case 'change-status':
                $customer = Country::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_customer_update');
                break;

            case 'delete':
                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                Country::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_customer_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('service_providers.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => __('messages.bulk_update')]);
    }
}
