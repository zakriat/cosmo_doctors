<?php

namespace Modules\Slider\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Clinic\Models\SystemService;
use Modules\Clinic\Models\ClinicsCategory;
use Modules\Constant\Models\Constant;
use Modules\CustomField\Models\CustomFieldGroup;
use Modules\Service\Models\SystemServiceCategory;
use Modules\Slider\Http\Requests\SliderRequest;
use Modules\Slider\Models\Slider;
use Yajra\DataTables\DataTables;


class SlidersController extends Controller
{
    // use Authorizable;
    protected $module_title = 'slider.title';
    protected $module_name = 'app_banners';

    public function __construct()
    {
        // Page Title
        $this->module_title = 'slider.title';

        // module name
        $this->module_name = 'app_banners';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => 'fa-regular fa-sun',
            'module_name' => $this->module_name,
        ]);
        $this->middleware(['permission:view_app_banner'])->only('index');
        $this->middleware(['permission:edit_app_banner'])->only('edit', 'update');
        $this->middleware(['permission:add_app_banner'])->only('store');
        $this->middleware(['permission:delete_app_banner'])->only('destroy');
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
        $columns = CustomFieldGroup::columnJsonValues(new Slider());
        $types = Constant::where('type', 'SLIDER_TYPES')->pluck('name', 'id');
        $module_action = 'List';
        $modules = SystemService::select('id', 'name')->get();
        $categories = ClinicsCategory::all();

        return view('slider::backend.sliders.index_datatable', compact('module_action', 'types', 'modules', 'filter', 'columns', 'categories'));
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {
        $query_data = Constant::where('type', 'SLIDER_TYPES')->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->value,
                'name' => $row->name,
            ];
        }

        return response()->json($data);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $module_name = $this->module_name;
        // Update the query to include all necessary relationships
        $query = Slider::with(['systemService', 'systemServiceCategory', 'typeConstant']);
        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row "  id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->addColumn('image', function ($row) {
                $img = $row->getFirstMediaUrl('file_url') ?: asset('img/default.webp');
                return '<img src="' . $img . '" alt="slider-image" class="avatar avatar-50 rounded-pill">';
            })
            ->addColumn('action', function ($data) use ($module_name) {
                return view('slider::backend.sliders.action_column', compact('module_name', 'data'));
            })
            ->editColumn('link', function ($data) {
                return $data->link ?? '-';
            }) 
          ->editColumn('type', function ($data) {
                return ucfirst($data->type) ?? '-';
            })
            ->editColumn('link_id', function ($data) {
                // Get the type constant value
                $typeValue = $data->typeConstant ? $data->typeConstant->value : $data->type;

                // If link_id is 0 or null, return dash
                if (empty($data->link_id) || $data->link_id == 0) {
                    return '-';
                }

                // For service type
                if ($typeValue == '79' || $typeValue == 'service') {
                    return $data->systemService ? $data->systemService->name : '-';
                }

                // For category type
                if ($typeValue == '78' || $typeValue == 'category') {
                    return $data->systemServiceCategory ? $data->systemServiceCategory->name : '-';
                }

                return '-';
            })
            ->filterColumn('link_id', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where(function ($q2) use ($keyword) {
                        $q2->where('type', 'category')
                            ->whereHas('systemServiceCategory', function ($q3) use ($keyword) {
                                $q3->where('name', 'like', "%$keyword%");
                            });
                    })->orWhere(function ($q2) use ($keyword) {
                        $q2->where('type', 'service')
                            ->whereHas('systemService', function ($q3) use ($keyword) {
                                $q3->where('name', 'like', "%$keyword%");
                            });
                    });
                });
            })
            ->editColumn('status', function ($row) {
                $checked = '';
                if ($row->status) {
                    $checked = 'checked="checked"';
                }

                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="' . route('backend.app_banners.update_status', $row->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $row->id . '"  name="status" value="' . $row->id . '" ' . $checked . '>
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

            ->orderColumns(['id'], '-:column $1');

        return $datatable->rawColumns(array_merge(['action', 'status', 'check', 'image']))
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $types = Constant::where('type', 'SLIDER_TYPES')->pluck('name', 'value');
        $modules = SystemService::select('id', 'name')->get();
        $categories = ClinicsCategory::all();

        return view('slider::backend.sliders.sliderForm_offcanvas', compact('types', 'modules', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(SliderRequest $request)
    {
        try {
            $data = $request->except('file_url');
            // Normalize empty link to null so clearing URL persists
            $data['link'] = $request->filled('link') ? $request->input('link') : null;

            // Handle empty link_id - convert empty string to null or provide default
            if (isset($data['link_id']) && $data['link_id'] === '') {
                $data['link_id'] = null;
            }

            // If link_id is still null and database requires it, provide a default value
            if (!isset($data['link_id']) || $data['link_id'] === null) {
                $data['link_id'] = 0; // Use 0 as default instead of null
            }

            $query = Slider::create($data);

            if ($request->hasFile('file_url')) {
                storeMediaFile($query, $request->file('file_url'));
            }
            $message = __('messages.create_form', ['form' => __($this->module_title)]);

            if ($request->is('api/*')) {
                return response()->json(['message' => $message, 'data' => $data, 'status' => true], 200);
            } elseif ($request->ajax()) {
                return response()->json(['message' => $message, 'status' => true], 200);
            } else {
                return redirect()->route('backend.app-banners.index')->with('success', $message);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'An error occurred while creating the slider: ' . $e->getMessage(),
                    'error_details' => $e->getMessage(),
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $module_action = 'Show';

        $data = Slider::findOrFail($id);

        return view('slider::backend.sliders.show', compact('module_action', "$data"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $slider = Slider::with('module')->findOrFail($id);
        $types = Constant::where('type', 'SLIDER_TYPES')->pluck('name', 'value');
        $modules = SystemService::select('id', 'name')->get();
        $categories = ClinicsCategory::all();

        return view('slider::backend.sliders.sliderForm_offcanvas', compact('slider', 'types', 'modules', 'categories'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(SliderRequest $request, $id)
    {
        try {
            $query = Slider::findOrFail($id);
            $data = $request->except('file_url');
       
            $data['link'] = $request->filled('link') ? $request->input('link') : null;

            
            // Handle empty link_id - convert empty string to null or provide default
            if (isset($data['link_id']) && $data['link_id'] === '') {
                $data['link_id'] = null;
            }

            // If link_id is still null and database requires it, provide a default value
            if (!isset($data['link_id']) || $data['link_id'] === null) {
                $data['link_id'] = 0;
            }

            $updated = $query->update($data);

            if ($request->hasFile('file_url')) {
                // Clear existing media first
                $query->clearMediaCollection('file_url');

                // Store new media
                storeMediaFile($query, $request->file('file_url'));
            }

            $message = __('messages.update_form', ['form' => __($this->module_title)]);

            if ($request->is('api/*')) {
                return response()->json(['message' => $message, 'data' => $data, 'status' => true], 200);
            } elseif ($request->ajax()) {
                return response()->json(['message' => $message, 'status' => true], 200);
            } else {
                return redirect()->route('backend.app-banners.index')->with('success', $message);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'An error occurred while updating the app banner: ' . $e->getMessage(),
                    'error_details' => $e->getMessage()
                ], 500);
            }
            throw $e;
        }
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

        $data = Slider::findOrFail($id);

        $data->delete();

        $message = __('messages.delete_form', ['form' => __('slider.singular_title')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function restore($id)
    {
        $module_action = 'Restore';

        $data = Slider::withTrashed()->find($id);
        $data->restore();

        return redirect('app/app-banners');
    }

    public function update_status(Request $request, Slider $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('service_providers.status_update')]);
    }

    public function testUpdate($id)
    {
        $slider = Slider::findOrFail($id);
        return response()->json([
            'current_data' => $slider->toArray(),
            'status' => true
        ]);
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                $services = Slider::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_slider_update');
                break;

            case 'delete':

                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                Slider::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_slider_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('service_providers.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => __('messages.bulk_update')]);
    }

    public function slider_list(Request $request, $type)
    {

        switch ($type) {
            case 'category':
                $category = SystemServiceCategory::where('status', 1)->get();
                return $category;
                break;

            case 'service':

                $service = SystemService::where('status', 1)->get();
                return $service;
                break;
        }
    }
}
