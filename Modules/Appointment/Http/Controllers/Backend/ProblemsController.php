<?php

namespace Modules\Appointment\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Constant\Models\Constant;
use Yajra\DataTables\DataTables;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Carbon\Carbon;
use Illuminate\Support\Str;
class ProblemsController extends Controller
{
    public function __construct()
    {
        // Page Title
        $this->module_title = 'appointment.problem';
        // module name
        $this->module_name = 'problems';

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
     */
    public function index()
    {
        $filter = [];

        $columns = CustomFieldGroup::columnJsonValues(new Constant());
        $customefield = CustomField::exportCustomFields(new Constant());
        $problems = Constant::where('type', 'encounter_problem')->get();
        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => __('appointment.lbl_name'),
            ],
            [
                'value' => 'updated_at',
                'text' => __('appointment.lbl_update_at'),
            ]
        ];
        $export_url = route('backend.encounter-template.export');

        return view('appointment::backend.problems.index_datatable', compact('filter', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url', 'problems'));
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {

            case 'delete':
                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                Constant::whereIn('id', $ids)->delete();
                $message = __('appointment.problem_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('service_providers.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $query = Constant::query();
        $query = $query->where('type', 'encounter_problem');

        if (auth()->user()->hasRole('doctor')) {
            $query = $query->where('created_by', auth()->user()->id);
        }

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }

            if (isset($filter['template_name'])) {
                $query->where('id', $filter['template_name']);
            }
        }

        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $data->id . '"  name="datatable_ids[]" value="' . $data->id . '" onclick="dataTableRowCheck(' . $data->id . ')">';
            })
            ->addColumn('action', function ($data) {
                return view('appointment::backend.problems.action_column', compact('data'));
            })
            ->editColumn('name', function ($data) {
                return ucwords(str_replace('_', ' ', $data->name));
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
            ->orderColumns(['id'], '-:column $1');

        // Custom Fields For export
        $customFieldColumns = CustomField::customFieldData($datatable, Constant::CUSTOM_FIELD_MODEL, null);

        return $datatable->rawColumns(array_merge(['action', 'name', 'check'], $customFieldColumns))
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('appointment::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data['name'] = $request->name;
        $data['type'] = 'encounter_problem';
        $data['value'] = strtolower(Str::slug($request->name, '-'));
        $query = Constant::create($data);
        if ($request->custom_fields_data) {
            $query->updateCustomFieldData(json_decode($request->custom_fields_data));
        }
        $message = __('messages.create_form', ['form' => __('appointment.observation')]);

        if ($request->is('api/*')) {
            return response()->json(['message' => $message, 'data' => $data, 'status' => true], 200);
        } else {
            return response()->json(['message' => $message, 'status' => true], 200);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('appointment::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = Constant::findOrFail($id);

        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $query = Constant::findOrFail($id);
        $data['name'] = $request->name;
        $data['type'] = 'encounter_problem';
        $data['value'] = strtolower(Str::slug($request->name, '_'));
        $query->update($data);

        $message = __('messages.update_form', ['form' => __('appointment.lbl_problem')]);

        if ($request->is('api/*')) {
            return response()->json(['message' => $message, 'data' => $data, 'status' => true], 200);
        } else {
            return response()->json(['message' => $message, 'status' => true], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Constant::findOrFail($id);

        $data->delete();

        $message = __('appointment.problem_delete');

        return response()->json(['message' => $message, 'status' => true], 200);
    }
}
