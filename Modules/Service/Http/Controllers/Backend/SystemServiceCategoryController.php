<?php

namespace Modules\Service\Http\Controllers\backend;

use App\Authorizable;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Service\Models\SystemServiceCategory;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Modules\RequestService\Models\RequestService;

class SystemServiceCategoryController extends Controller
{
    protected string $exportClass = '\App\Exports\SystemServiceCategoryExport';

    public function __construct()
    {
        // Page Title
        $this->module_title = 'clinic.specialization';

        // module name
        $this->module_name = 'specializations';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => 'fa-regular fa-sun',
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
        $module_name = $this->module_name;
        $module_action = 'List';
        $type = $request->type;
        $data = RequestService::where('id', $request->id)->first();
        // if ($data !== null) {
        //     $data->is_status = 'accept';
        //     $data->save();
        // }
        $systemcategory = SystemServiceCategory::where('status', 1)->get();
        $columns = CustomFieldGroup::columnJsonValues(new SystemServiceCategory());
        $customefield = CustomField::exportCustomFields(new SystemServiceCategory());
        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => __('category.lbl_name'),
            ],
            [
                'value' => 'category_id',
                'text' => __('category.lbl_category'),
            ],
            [
                'value' => 'status',
                'text' => __('category.lbl_status'),
            ],

        ];
        $export_url = route('backend.specializations.export');
        
        if ($type === 'specialization') {
            $notification_data = [
                'id' => $data->id,
                'name' => $data->name,
                'description' => $data->description,
                'type' => $data->type,
                'vendor_id' => $data->vendor_id,
            ];
            sendNotificationOnBookingUpdate('accept_request_service', $notification_data);
        }
        return view('service::backend.systemservicecategory.index_datatable', compact('module_action', 'filter', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url', 'systemcategory', 'data', 'type'));
    }
    public function index_list(Request $request)
    {
        $query_data = SystemServiceCategory::where('status', 1);


        $query_data = $query_data->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'name' => $row->name,
            ];
        }


        return response()->json($data);
    }



    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                $branches = SystemServiceCategory::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_specialization_update');
                break;

            case 'delete':
                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }

                SystemServiceCategory::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_specialization_update');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('clinic.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' =>$message]);
    }

    public function update_status(Request $request, $id)
    {
        $query =  SystemServiceCategory::where('id', $id);
        $query->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('clinic.status_update')]);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $module_name = $this->module_name;
        $query = SystemServiceCategory::with('mainCategory');

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }

            if (isset($filter['category_name'])) {
                $query->where('id', $filter['category_name']);
            }
        }

        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->editColumn('name', function ($row) {
                // return "<img src='".$row->feature_image."' class='avatar avatar-50 rounded-pill me-2'> <a href='".route('backend.specializations.index_nested', ['category_id' => $row->id])."'>$row->name</a>";
                return "<img src='" . $row->file_url . "' class='avatar avatar-50 rounded-pill me-3'>$row->name";
            })

            ->editColumn('parent_id', function ($row) {
                return optional($row->mainCategory)->name ?? '-';
            })

            ->addColumn('action', function ($data) use ($module_name) {
                return view('service::backend.systemservicecategory.action_column', compact('module_name', 'data'));
            })

            ->editColumn('status', function ($row) {
                $checked = '';
                if ($row->status) {
                    $checked = 'checked="checked"';
                }

                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="' . route('backend.specializations.update_status', $row->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $row->id . '"  name="status" value="' . $row->id . '" ' . $checked . '>
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
            ->editColumn('created_at', function ($data) {
                $module_name = $this->module_name;

                $diff = Carbon::now()->diffInHours($data->created_at);

                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })
            ->orderColumns(['id'], '-:column $1');

        // Custom Fields For export
        $customFieldColumns = CustomField::customFieldData($datatable, SystemServiceCategory::CUSTOM_FIELD_MODEL, null);

        return $datatable->rawColumns(array_merge(['action', 'status', 'image', 'check', 'name', 'parent_id'], $customFieldColumns))
            ->toJson();
    }

    public function index_nested(Request $request)
    {
        $categories = SystemServiceCategory::with('mainCategory')->whereNull('parent_id')->get();

        $filter = [
            'status' => $request->status,
        ];
        $parentID = $request->category_id ?? null;

        $module_action = 'List';

        $module_title = 'Sub-Categories';
        $columns = CustomFieldGroup::columnJsonValues(new SystemServiceCategory());
        $customefield = CustomField::exportCustomFields(new SystemServiceCategory());

        return view('service::backend.systemservicecategory.index_nested_datatable', compact('parentID', 'module_action', 'filter', 'categories', 'module_title', 'columns', 'customefield'));
    }

    public function index_nested_data(Request $request, Datatables $datatable)
    {

        $module_name = $this->module_name;

        $query = SystemServiceCategory::query()->with('media', 'mainCategory')->whereNotNull('parent_id')->orderBy('updated_at', 'desc');



        // $request->category_id
        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
            if (isset($filter['column_category'])) {
                $query->where('parent_id', $filter['column_category']);
            }
        }

        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->addColumn('action', function ($data) use ($module_name) {
                return view('service::backend.systemservicecategory.action_column', compact('module_name', 'data', 'edit_permission', 'delete_permission'));
            })

            ->addColumn('image', function ($data) {
                return '<img src=' . $data->file_url . " class='avatar avatar-50 rounded-pill'>";
            })
            ->editColumn('mainCategory.name', function ($data) {
                return $data->mainCategory->name ?? '-';
            })

            ->editColumn('status', function ($row) {
                $checked = '';
                if ($row->status) {
                    $checked = 'checked="checked"';
                }

                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="' . route('backend.specializations.update_status', $row->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $row->id . '"  name="status" value="' . $row->id . '" ' . $checked . '>
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
            ->editColumn('updated_at', function ($data) {
                $module_name = $this->module_name;

                $diff = Carbon::now()->diffInHours($data->updated_at);

                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })
            ->editColumn('created_at', function ($data) {
                $module_name = $this->module_name;

                $diff = Carbon::now()->diffInHours($data->created_at);

                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })

            ->orderColumns(['id'], '-:column $1');

        // Custom Fields For export
        $customFieldColumns = CustomField::customFieldData($datatable, SystemServiceCategory::CUSTOM_FIELD_MODEL, null);

        return $datatable->rawColumns(array_merge(['action', 'status', 'image', 'check'], $customFieldColumns))
            ->toJson();
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('service::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->except('file_url','custom_fields_data');
        $data['name'] = $request->name;

        $query = SystemServiceCategory::updateOrCreate($data);
        $RequestService = RequestService::where('name', $query->name)->first();
        if ($RequestService !== null) {
            $RequestService->is_status = 'accept';
            $RequestService->save();
        }
        if ($request->custom_fields_data) {

            $query->updateCustomFieldData(json_decode($request->custom_fields_data));
        }

        if ($request->hasFile('file_url')) {
            storeMediaFile($query, $request->file('file_url'));
        }

        $message = __('messages.create_form', ['form' => __('clinic.specialization')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $module_action = 'Show';

        $data = SystemServiceCategory::with('mainCategory')->findOrFail($id);

        return view('service::backend.systemservicecategory.show', compact('module_action', "$data"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = SystemServiceCategory::with('mainCategory')->findOrFail($id);

        if (!is_null($data)) {
            $custom_field_data = $data->withCustomFields();
            $data['custom_field_data'] = collect($custom_field_data->custom_fields_data)
                ->filter(function ($value) {
                    return $value !== null;
                })
                ->toArray();
        }

        $data['file_url'] = $data->file_url;
        $data['category_name'] = $data->mainCategory->name ?? null;

        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $query = SystemServiceCategory::findOrFail($id);

        $data = $request->except('file_url');

        $query->update($data);

        if ($request->custom_fields_data) {

            $query->updateCustomFieldData(json_decode($request->custom_fields_data));
        }
        if ($request->hasFile('file_url')) {
            storeMediaFile($query, $request->file('file_url'));
        }
        $message = __('messages.update_form', ['form' => __('clinic.specialization')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (env('IS_DEMO')) {
            return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
        }

        $data = SystemServiceCategory::findOrFail($id);

        $data->delete();

        $message = __('messages.delete_form', ['form' => __('clinic.specialization')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }
}
