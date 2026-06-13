<?php

namespace Modules\Clinic\Http\Controllers;

use App\Models\User;
use App\Authorizable;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Clinic\Models\ClinicsCategory;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Modules\RequestService\Models\RequestService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Modules\Clinic\Http\Requests\ClinicsCategoryRequest;

class ClinicsCategoryController extends Controller
{
    protected string $exportClass = '\App\Exports\ClincsCategoryExport';

    public function __construct()
    {
        $this->module_title = 'category.title';

        // module name
        $this->module_name = 'category';

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
        $module_action = 'List';
        $filter = [
            'status' => $request->status,
        ];
        $module_name = $this->module_name;
        $type = $request->type;
        $categoryId = is_array($request->get('parent_id')) ? null : $request->get('parent_id'); 
        $parentcategory = ClinicsCategory::whereNull('parent_id')->get();
        $data = $request->id ? RequestService::where('id', $request->id)->first() : null;
        // if ($data !== null) {
        //     $data->is_status = 'accept';
        //     $data->save();
        // }
        $vendor = User::role(['vendor'])->where('status', 1)->where('is_banned', 0)->get();
        $columns = CustomFieldGroup::columnJsonValues(new ClinicsCategory());
        $customefield = CustomField::exportCustomFields(new ClinicsCategory());
        $export_import = true;
        $type = $request->type;
        $export_columns = [
            [
                'value' => 'name',
                'text' => __('category.lbl_name'),
            ],
            [
                'value' => 'parent_id',
                'text' => __('category.parent_category'),
            ],
            [
                'value' => 'featured',
                'text' => __('messages.featured'),
            ],
            [
                'value' => 'status',
                'text' => __('category.lbl_status'),
            ],
        ];
        $export_url = route('backend.category.export');
        
        // Ensure scalar values for view
        $categoryId = is_array($categoryId) ? null : $categoryId;
        $type = is_array($type) ? null : $type;
        
        if ($type === 'category' && $data) {
            $notification_data = [
                'id' => $data->id,
                'name' => $data->name,
                'description' => $data->description,
                'type' => $data->type,
                'vendor_id' => $data->vendor_id,
            ];
            sendNotificationOnBookingUpdate('accept_request_service', $notification_data);
        }
        return view('clinic::backend.categories.index_datatable', compact('module_action', 'parentcategory', 'module_name', 'filter', 'data', 'vendor', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url', 'type','categoryId'));

        // return view('clinic::backend.categories.index_datatable', compact('module_name', 'filter', 'module_action', 'columns', 'customefield'));
    }
    public function index_list(Request $request)
    {
        $term = trim($request->q);

        $query_data = ClinicsCategory::query();

        if ($request->filled('vendor_id')) {
            $query_data->where('vendor_id', $request->vendor_id);
        }

        if ($request->filled('parent_id')) {
            $query_data->where('parent_id', $request->parent_id);
        } else {
            $query_data->whereNull('parent_id');
        }

        $query_data = $query_data->where('status', 1)->get();

        $data = $query_data->map(function ($row) {
            return [
                'id' => $row->id,
                'name' => $row->name,
            ];
        });

        return response()->json($data);
    }
    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                $ClinicsCategory = ClinicsCategory::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('clinic.cliniccategory_status');
                break;

            case 'change-featured':
                $ClinicsCategory = ClinicsCategory::whereIn('id', $ids)->update(['featured' => $request->featured]);
                if ($request->featured == 1) {

                    $message = __('messages.add_category_as_featured');
                } else {

                    $message = __('messages.remove_category_from_featured');
                }

                break;


            case 'delete':
                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }

                ClinicsCategory::whereIn('id', $ids)->delete();
                $message = __('messages.clinic.cliniccategory_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('service_providers.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function update_status(Request $request, ClinicsCategory $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('clinic.cliniccategory_status')]);
    }


    public function update_featured(Request $request, ClinicsCategory $id)
    {

        $id->update(['featured' => $request->featured]);

        if ($request->featured == 1) {

            $message = __('messages.add_category_as_featured');
        } else {

            $message = __('messages.remove_category_from_featured');
        }

        return response()->json(['status' => true, 'message' => $message]);
    }



    public function index_data(Datatables $datatable, Request $request)
    {
        $module_name = $this->module_name;
        $query = ClinicsCategory::with('mainCategory');

        $filter = $request->filter;

        if (isset($filter)) {

            if (isset($filter['parent_category'])) {
                $query->where('id', $filter['parent_category']);
            }
            if (isset($filter['clinic_category'])) {
                $query->where('id', $filter['clinic_category']);
            }
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        // if(auth()->user()->hasRole('admin')|| auth()->user()->hasRole('demo_admin')){
        //     $query = ClinicsCategory::query()->with('media','mainCategory');
        // }

        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->editColumn('name', function ($data) {
                return "<img src='" . $data->file_url . "' class='avatar avatar-50 rounded-pill me-2'> <a>$data->name</a>";
            })

            ->editColumn('parent_id', function ($data) {
                return $data->mainCategory->name ?? '-';
            })
            ->addColumn('action', function ($data) use ($module_name) {
                return view('clinic::backend.categories.action_column', compact('module_name', 'data'));
            })

            ->editColumn('status', function ($row) {
                $checked = '';
                if ($row->status) {
                    $checked = 'checked="checked"';
                }

                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="' . route('backend.category.update_status', $row->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $row->id . '"  name="status" value="' . $row->id . '" ' . $checked . '>
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
                        <input type="checkbox" data-url="' . route('backend.category.update_featured', $row->id) . '" data-token="' . csrf_token() . '" class="switch-status-featured form-check-input"  id="datatable-row-' . $row->id . '"  name="featured" value="' . $row->id . '" ' . $checked . '>
                    </div>
                ';
            })
            ->editColumn('updated_at', function ($data) {

                $diff = Carbon::now()->diffInHours($data->updated_at);

                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })
            ->editColumn('created_at', function ($data) {

                $diff = Carbon::now()->diffInHours($data->created_at);

                if ($diff < 25) {
                    return $data->created_at->diffForHumans();
                } else {
                    return $data->created_at->isoFormat('llll');
                }
            })
            ->orderColumns(['id'], '-:column $1');

        // Custom Fields For export
        $customFieldColumns = CustomField::customFieldData($datatable, ClinicsCategory::CUSTOM_FIELD_MODEL, null);

        return $datatable->rawColumns(array_merge(['action', 'name', 'parent_id', 'status', 'featured', 'image', 'check'], $customFieldColumns))
            ->toJson();
    }

    public function index_nested(Request $request)
    {
        $categories = ClinicsCategory::with('mainCategory')->whereNull('parent_id')->get();

        $filter = [
            'status' => $request->status,
        ];
        $parentID = $request->category_id ?? null;

        $module_action = 'List';

        $module_title = 'Sub-Categories';
        $columns = CustomFieldGroup::columnJsonValues(new ClinicsCategory());
        $customefield = CustomField::exportCustomFields(new ClinicsCategory());

        return view('clinic::backend.categories.index_nested_datatable', compact('parentID', 'module_action', 'filter', 'categories', 'module_title', 'columns', 'customefield'));
    }

    public function index_nested_data(Request $request, Datatables $datatable)
    {
        $query = ClinicsCategory::query()->with('media', 'mainCategory')->whereNotNull('parent_id')->orderBy('updated_at', 'desc');

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
            ->addColumn('action', function ($data) {
                return view('clinic::backend.categories.sub_action_column', compact('data'));
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
                        <input type="checkbox" data-url="' . route('backend.category.update_status', $row->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $row->id . '"  name="status" value="' . $row->id . '" ' . $checked . '>
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
        $customFieldColumns = CustomField::customFieldData($datatable, ClinicsCategory::CUSTOM_FIELD_MODEL, null);

        return $datatable->rawColumns(array_merge(['action', 'status', 'image', 'check'], $customFieldColumns))
            ->toJson();
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
    public function store(ClinicsCategoryRequest $request)
    {
        try {
            $data = $request->except('file_url', 'doctor_ids');
            $data['slug'] = strtolower(Str::slug($request->name, '-'));
            $query = ClinicsCategory::create($data);
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

            // Handle doctor assignments (if provided)
            if ($request->has('doctor_ids') && is_array($request->doctor_ids)) {
                $clinicId = \Modules\Clinic\Models\Clinics::first()->id ?? 2; // Get first clinic or default to 2
                
                foreach ($request->doctor_ids as $doctorId) {
                    \Modules\Clinic\Models\DoctorCategoryMapping::create([
                        'doctor_id' => $doctorId,
                        'category_id' => $query->id,
                        'clinic_id' => $clinicId,
                        'status' => 1
                    ]);
                }
            }

            $message = __('messages.create_form', ['form' => __('category.singular_title')]);

            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => $query
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error creating category: ' . $e->getMessage()
            ], 500);
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
        $data = ClinicsCategory::with('mainCategory')->findOrFail($id);

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
        
        // Load assigned doctors
        $assignedDoctors = \Modules\Clinic\Models\DoctorCategoryMapping::where('category_id', $id)
            ->pluck('doctor_id')
            ->toArray();
        $data['assigned_doctors'] = $assignedDoctors;

        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClinicsCategoryRequest $request, $id)
    {
        try {
            $query = ClinicsCategory::findOrFail($id);
            
            // Log the update operation for debugging
            \Log::info('Updating category', [
                'id' => $id,
                'original_name' => $query->name,
                'new_name' => $request->name,
                'request_data' => $request->except('file_url', 'doctor_ids')
            ]);

            $data = $request->except('file_url', 'doctor_ids');
            
            // Regenerate slug if name has changed
            if ($request->has('name') && $query->name !== $request->name) {
                $data['slug'] = strtolower(Str::slug($request->name, '-'));
            }

            $query->update($data);

            if ($request->custom_fields_data) {
                $query->updateCustomFieldData(json_decode($request->custom_fields_data));
            }

            if ($request->hasFile('file_url')) {
                storeMediaFile($query, $request->file('file_url'));
            }

            // Remove image if requested
            if ($request->input('remove_image') == '1') {
                $query->clearMediaCollection('file_url');
            }

            // Handle doctor assignments (if provided)
            if ($request->has('doctor_ids')) {
                $clinicId = \Modules\Clinic\Models\Clinics::first()->id ?? 2;
                
                // Remove existing assignments
                \Modules\Clinic\Models\DoctorCategoryMapping::where('category_id', $id)->delete();
                
                // Add new assignments
                if (is_array($request->doctor_ids)) {
                    foreach ($request->doctor_ids as $doctorId) {
                        \Modules\Clinic\Models\DoctorCategoryMapping::create([
                            'doctor_id' => $doctorId,
                            'category_id' => $id,
                            'clinic_id' => $clinicId,
                            'status' => 1
                        ]);
                    }
                }
            }

            $message = __('messages.update_form', ['form' => __('category.singular_title')]);

            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => $query
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error updating category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if(\Auth::user()->hasAnyRole(['demo_admin'])){
          
            return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
        }

        $data = ClinicsCategory::where('id', $id)->delete();

        $message = __('messages.delete_form', ['form' => __('category.singular_title')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function datatable_view(Request $request)
    {
        $module_action = 'List';
        $filter = [
            'status' => $request->status,
        ];
        $parentcategory = ClinicsCategory::with('media')->whereNotNull('parent_id')->get();
        $data = RequestService::select('id', 'name', 'description', 'type')->where('id', $request->id)->first();
        $columns = CustomFieldGroup::columnJsonValues(new ClinicsCategory());
        $customefield = CustomField::exportCustomFields(new ClinicsCategory());
        $type = $request->type;
        return view('clinic::backend.categories.index_datatable', compact('module_action', 'filter', 'customefield', 'columns', 'parentcategory', 'data', 'type'));
    }

    /**
     * Get parent categories for AJAX requests
     */
    public function parentCategories()
    {
        try {
            $parentCategories = ClinicsCategory::whereNull('parent_id')
                ->select('id', 'name')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $parentCategories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching parent categories: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get custom fields for AJAX requests
     */
    public function customFields()
    {
        try {
            $customFields = CustomField::exportCustomFields(new ClinicsCategory());

            return response()->json([
                'status' => true,
                'data' => $customFields
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching custom fields: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get parent services for category dropdown
     * This replaces parentCategories() to show services instead of categories
     */
    public function parentServices()
    {
        try {
            // Load all active services (removed system_service_id filter to show all services)
            $services = \Modules\Clinic\Models\ClinicsService::where('status', 1)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $services
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching services: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available doctors for category assignment
     */
    public function getAvailableDoctors()
    {
        try {
            $doctors = \Modules\Clinic\Models\Doctor::with('user:id,first_name,last_name')
                ->where('status', 1)
                ->get()
                ->map(function($doctor) {
                    return [
                        'id' => $doctor->doctor_id,
                        'name' => $doctor->user->first_name . ' ' . $doctor->user->last_name,
                        'specialization' => $doctor->specialization ?? 'General Practice'
                    ];
                });

            return response()->json([
                'status' => true,
                'data' => $doctors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error fetching doctors: ' . $e->getMessage()
            ], 500);
        }
    }
}
