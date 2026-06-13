<?php

namespace Modules\Clinic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Modules\Clinic\Models\ClinicsCategory;
use Modules\Clinic\Models\SystemService;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Modules\RequestService\Models\RequestService;
use Modules\Clinic\Http\Requests\SystemServiceRequest;
use Modules\Clinic\Models\ClinicsService;
use Illuminate\Support\Facades\Log;

class SystemServiceController extends Controller
{
    protected string $module_name;
    protected string $module_icon;
    protected string $exportClass = '\App\Exports\SystemServiceExport';
    public function __construct()
    {

        // module name
        $this->module_name = 'system-service';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
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
        $module_title = __('service.system_service_list');
        $columns = CustomFieldGroup::columnJsonValues(new SystemService());
        $customefield = CustomField::exportCustomFields(new SystemService());
        $type=$request->type;
        $data = RequestService::where('id', $request->id)->first();
        // if ($data !== null) {
        //     $data->is_status = 'accept';
        //     $data->save();
        // }
        $categories = ClinicsCategory::whereNull('parent_id')->get();
        $subcategories = ClinicsCategory::whereNotNull('parent_id')->get();


        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => __('service.lbl_name'),
            ],
            [
                'value' => 'category_id',
                'text' =>  __('service.lbl_category_id'),
            ],
            [
                'value' => 'status',
                'text' => __('service.lbl_status'),

            ],
        ];
        if ($type === 'system_service') {
            $notification_data = [
                'id' => $data->id,
                'name' => $data->name,
                'description' => $data->description,
                'type' => $data->type,
                'vendor_id' => $data->vendor_id,
            ];
            sendNotificationOnBookingUpdate('accept_request_service', $notification_data);
        }
        $export_url = route('backend.system-service.export');
        return view('clinic::backend.systemservice.index', compact('module_action','module_title', 'filter', 'categories','data','type', 'subcategories','export_import', 'export_columns', 'export_url','columns', 'customefield',));
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $module_name = $this->module_name;
        // Show in descending order by id
        $query = SystemService::query()->orderByDesc('id');

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
            if (isset($filter['category_id'])) {
                $query->where('category_id', $filter['category_id']);
            }
            if (isset($filter['sub_category_id'])) {
                $query->where('subcategory_id', $filter['sub_category_id']);
            }
        }

        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $data->id . '"  name="datatable_ids[]" value="' . $data->id . '" onclick="dataTableRowCheck(' . $data->id . ')">';
            })
            ->editColumn('name', function ($data) {
                return '<img src=' . $data->file_url . " class='avatar avatar-50 rounded-pill me-3'> $data->name";
            })
            ->addColumn('action', function ($data) use ($module_name) {
                return view('clinic::backend.systemservice.action_column', compact('module_name', 'data'));
            })
            ->editColumn('status', function ($row) {
                $checked = '';
                if ($row->status) {
                    $checked = 'checked="checked"';
                }
                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="' . route('backend.system-service.update_status', $row->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $row->id . '"  name="status" value="' . $row->id . '" ' . $checked . '>
                    </div>
                ';
            })
            ->editColumn('featured', function ($row) {
                $checked = '';
                if ($row->featured) {
                    $checked = 'checked="checked"';
                }
                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="' . route('backend.system-service.update_featured', $row->id) . '" data-token="' . csrf_token() . '" class="switch-status-featured form-check-input"  id="datatable-row-' . $row->id . '"  name="featured" value="' . $row->id . '" ' . $checked . '>
                    </div>
                ';
            })
            ->editColumn('category_id', function ($data) {
                $category = isset($data->category->name) ? $data->category->name : '-';
                return $category;
            })
            ->filterColumn('category', function ($query, $keyword) {
                $query->whereHas('category', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->editColumn('subcategory_id', function ($data) {
                $subcategory = isset($data->sub_category->name) ? $data->sub_category->name : '-';
                return $subcategory;
            })
            ->editColumn('updated_at', function ($data) {
                $module_name = $this->module_name;
                $diff = Carbon::now()->diffInHours($data->updated_at);
                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            });

        $customFieldColumns = CustomField::customFieldData($datatable, SystemService::CUSTOM_FIELD_MODEL, null);

        return $datatable->rawColumns(array_merge(['action', 'name','image', 'status', 'check','featured'], $customFieldColumns))
            ->toJson();
    }

    public function index_list(Request $request)
    {

        $data = SystemService::query();

        $data = $data->get();


        return response()->json($data);
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                $SystemService = SystemService::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('clinic.systemservice_status');
                break;

            case 'change-featured':
                $ClinicsCategory = SystemService::whereIn('id', $ids)->update(['featured' => $request->featured]);
                if($request->featured==1){

                    $message= __('messages.add_service_as_featured');

                }else{

                    $message=__('messages.remove_service_from_featured');
                }

                    break;


            case 'delete':

                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }

                SystemService::whereIn('id', $ids)->delete();
                $message = __('clinic.systemservice_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('service_providers.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function update_status(Request $request, SystemService $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('clinic.systemservice_status')]);
    }

    public function update_featured(Request $request, SystemService $id)
    {

        $id->update(['featured' => $request->featured]);

        if($request->featured==1){

            $message= __('messages.add_service_as_featured');

        }else{

            $message=__('messages.remove_service_from_featured');
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clinic::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SystemServiceRequest $request)
    {
        $data = $request->except('file_url');
        $query = SystemService::create($data);
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

        $message = __('messages.create_form', ['form' => __('service.system_service_list')]);

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
        return view('clinic::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            Log::info('SystemService edit called with ID: ' . $id);

            $data = SystemService::findOrFail($id);
            Log::info('SystemService found: ' . $data->name);

            if (!is_null($data)) {
                try {
                    $custom_field_data = $data->withCustomFields();
                    $data['custom_field_data'] = collect($custom_field_data->custom_fields_data)
                        ->filter(function ($value) {
                            return $value !== null;
                        })
                        ->toArray();
                    Log::info('Custom fields loaded successfully');
                } catch (\Exception $e) {
                    // If custom fields fail, continue without them
                    Log::warning('Custom fields error in SystemService edit: ' . $e->getMessage());
                    $data['custom_field_data'] = [];
                }
            }

            $data['file_url'] = $data->file_url;
            Log::info('SystemService edit completed successfully');

            return response()->json(['data' => $data, 'status' => true]);
        } catch (\Exception $e) {
            Log::error('SystemService edit error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['data' => null, 'status' => false, 'message' => 'Failed to load service data'], 500);
        }
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = SystemService::findOrFail($id);
        $request_data = $request->except('file_url');
        $data->update($request_data);
        if ($request->custom_fields_data) {
            $data->updateCustomFieldData(json_decode($request->custom_fields_data));
        }

        // Only remove existing image when explicitly requested
        if ($request->boolean('remove_image')) {
            $data->clearMediaCollection('file_url');
        }

        if ($request->hasFile('file_url')) {
            storeMediaFile($data, $request->file('file_url'), 'file_url');
        }

        $clinic_services = ClinicsService::where('system_service_id', $id)->get();
        if($clinic_services){
            foreach($clinic_services as $clinic_service){
                $clinic_service->update([
                    'type' => $data->type,
                    'is_video_consultancy' => $data->is_video_consultancy
                ]);
            }
        }

        $message = __('messages.update_form', ['form' => __('service.system_service_list')]);

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
        if (env('IS_DEMO')) {
            return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
        }

        $data = SystemService::with('clinicservice')->findOrFail($id);
        $data->clinicservice()->delete();
        $data->delete();

        $message = __('messages.delete_form', ['form' => __('service.system_service_list')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function getSubcategories(Request $request)
    {
         Log::info('Fetching subcategories for category_id:', ['category_id' => $request->category_id]);
        $categoryId = $request->query('category_id');

        $subcategories = ClinicsCategory::where('parent_id', $categoryId)
            ->select('id', 'name')
            ->get();

        return response()->json($subcategories);
    }


}
