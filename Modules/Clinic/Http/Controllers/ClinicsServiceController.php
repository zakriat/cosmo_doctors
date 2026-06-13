<?php

namespace Modules\Clinic\Http\Controllers;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Modules\Clinic\Models\ClinicsCategory;
use Modules\Clinic\Models\ClinicsService;
use Yajra\DataTables\DataTables;
use Modules\Clinic\Http\Requests\ClinicsServiceRequest;
use Carbon\Carbon;
use Modules\Clinic\Models\Doctor;
use Illuminate\Support\Str;
use Modules\Clinic\Models\ClinicServiceMapping;
use Modules\Clinic\Models\DoctorServiceMapping;
use App\Models\User;
use Modules\Clinic\Models\Clinics;
use Modules\Appointment\Models\PatientEncounter;
use Modules\Clinic\Models\SystemService;
use Modules\Appointment\Trait\AppointmentTrait;
use Modules\Clinic\Models\Receptionist;
use Modules\Tax\Models\Tax;
use Modules\Clinic\Trait\ClinicTrait;


class ClinicsServiceController extends Controller
{
    use AppointmentTrait;
    use ClinicTrait;

    protected string $exportClass = '\App\Exports\ClinicsServiceExport';


    public function __construct()
    {
        // Page Title
        $this->module_title = 'service.title';
        // module name
        $this->module_name = 'services';

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
    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];
        $module_action = 'List';
        $columns = CustomFieldGroup::columnJsonValues(new ClinicsService());
        $customefield = CustomField::exportCustomFields(new ClinicsService());

        $categories = ClinicsCategory::whereNull('parent_id')->get();
        $subcategories = ClinicsCategory::whereNotNull('parent_id')->get();
        $doctor = User::role('doctor')->SetRole(auth()->user())->with('doctor', 'doctorclinic')->get();
        $clinic = Clinics::SetRole(auth()->user())->with('clinicdoctor', 'specialty', 'clinicdoctor', 'receptionist')->where('status', 1)->get();
        $service = ClinicsService::SetRole(auth()->user())->with('sub_category', 'doctor_service', 'ClinicServiceMapping', 'systemservice')->where('status', 1)->get();

        $prices = $service->pluck('charges');
        $minPrice = 0;
        $maxPrice = $prices->max();

        $interval = 50;
        $priceRanges = [];
        if ($maxPrice <= $interval) {
            $priceRanges[] = [$minPrice, $maxPrice];
        } else {
            for ($i = $minPrice; $i <= $maxPrice; $i += $interval) {
                $priceRanges[] = [$i, min($i + $interval, $maxPrice)];
            }
        }

        $doctor_id = null;
        if ($request->has('doctor_id')) {
            $doctor_id = $request->doctor_id;
        }

        $export_import = true;
        $export_columns = [
            [
                'value' => 'system_service_id',
                'text' => __('service.lbl_name'),
            ],
            [
                'value' => 'charges',
                'text' => __('service.lbl_price'),
            ],
            [
                'value' => 'duration_min',
                'text' => __('service.lbl_duration'),
            ],
            [
                'value' => 'category_id',
                'text' => __('service.lbl_category_id'),
            ],
        ];

        if (multiVendor() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin'))) {
            $export_columns[] = [
                'value' => 'vendor_id',
                'text' => __('multivendor.singular_title'),
            ];
        }

        $export_columns[] = [
            'value' => 'status',
            'text' => __('service.lbl_status'),
        ];
        $export_url = route('backend.services.export');
        return view('clinic::backend.services.index_datatable', compact('module_action', 'filter', 'categories', 'subcategories', 'clinic', 'service', 'priceRanges', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url', 'doctor', 'doctor_id'));
    }
    public function index_list(Request $request)
    {
        $category_id = $request->category_id;
        
        $data = ClinicsService::SetRole(auth()->user())->with('category', 'sub_category', 'doctor_service', 'ClinicServiceMapping', 'systemservice');
        
        if (isset($category_id)) {
            $data->where('category_id', $category_id);
        }
        
        
        if ($request->has('clinicId') && $request->clinicId) {
            $clinic_id = $request->clinicId;
            $data->whereHas('ClinicServiceMapping', function ($query) use ($clinic_id) {
                $query->where('clinic_id', $clinic_id);
            });
        }
        
        if ($request->has('doctorId') && $request->doctorId) {
            $doctor_id = $request->doctorId;
            $data->whereHas('doctor_service', function ($query) use ($doctor_id) {
                $query->where('doctor_id', $doctor_id);
            });
        }
        
        // Support snake_case doctor_id parameter (from appointment form)
        if ($request->has('doctor_id') && $request->doctor_id) {
            $doctor_id = $request->doctor_id;
            $data->whereHas('doctor_service', function ($query) use ($doctor_id) {
                $query->where('doctor_id', $doctor_id);
            });
        }
        
        if ($request->has('clinic_id')) {
            $clinicId = explode(",", $request->clinic_id);
            $data->whereHas('ClinicServiceMapping', function ($query) use ($clinicId) {
                $query->whereIn('clinic_id', $clinicId);
            });
        }
        
        if ($request->filled('encounter_id') && $request->encounter_id != null) {
            
            $encounterDetails = PatientEncounter::where('id', $request->encounter_id)->with('appointment')->first();
            
            if ($encounterDetails) {
                $doctor_id = $encounterDetails->doctor_id;
                $clinic_id = $encounterDetails->clinic_id;
                $service_id = $request->service_id ?? null;
                
                // Fetch all services associated with the doctor and clinic
                $services = DoctorServiceMapping::where('doctor_id', $doctor_id)
                ->where('clinic_id', $clinic_id)
                ->pluck('service_id');
                
                if ($services->isNotEmpty()) {
                    $data->whereIn('id', $services);
                    if ($service_id != null) {
                        $data->whereNotIn('id', [$service_id]);
                    }
                }
            }
            
        }
        
        $query_data = $data->get();
        // dd($query_data);

        $data = [];

        foreach ($query_data as $row) {
            $doctorService = $row->doctor_service->first();
            $data[] = [
                'id' => $row->id,
                'name' => $row->name,
                'avatar' => $row->file_url,
                'charges' => optional($doctorService)->charges,
                'inclusive_tax_price' => optional($doctorService)->inclusive_tax_price ?? 0,
                'inclusive_tax' => $row->inclusive_tax ?? null,
            ];
        }

        return response()->json($data);
    }

    public function service_price(Request $request)
    {
        $service_charge = 0;
        $discount_amount = 0;
        $inclusive_tax_data = [];
        $inclusive_tax_amount = 0;
        $is_inclusive_tax = false;
        
        if ($request->has(key: 'service_id') && $request->has('doctor_id')) {
            $serviceId = $request->service_id;
            $doctorId = $request->doctor_id;
            
            $data = ClinicsService::where('id', $serviceId)
            ->with([
                'doctor_service' => function ($query) use ($doctorId) {  
                    $query->where('doctor_id', $doctorId);
                }
                ])
                ->first();
                
                if ($data && $data->doctor_service->isNotEmpty()) {
                    $doctorService = $data->doctor_service->first();
                    $service_charge = $doctorService->charges;
                    
                    // First, check if inclusive tax is enabled and calculate it on base price
                    if ($data->is_inclusive_tax == 1 && $data->inclusive_tax) {
                        $is_inclusive_tax = true;
                        $inclusive_tax_json = json_decode($data->inclusive_tax, true);
                        
                        if ($inclusive_tax_json) {
                            foreach ($inclusive_tax_json as $tax) {
                                $tax_amount = 0;
                                if ($tax['type'] == 'percent') {
                                    $tax_amount = $service_charge * $tax['value'] / 100;
                                } elseif ($tax['type'] == 'fixed') {
                                    $tax_amount = $tax['value'];
                                }
                                
                                $inclusive_tax_amount += $tax_amount;
                                $inclusive_tax_data[] = [
                                    'title' => $tax['title'] ?? 'Tax',
                                    'type' => $tax['type'],
                                    'value' => $tax['value'],
                                    'amount' => round($tax_amount, 2)
                                ];
                            }
                        }
                        
                        // Add inclusive tax to service charge (base + inclusive tax)
                        $service_charge = $service_charge + $inclusive_tax_amount;
                    }
                    
                    // Now apply discount on the price that includes inclusive tax
                    if ($data->discount == 1) {
                        $discount_amount = ($data->discount_type == 'percentage')
                        ? $service_charge * $data->discount_value / 100
                        : $data->discount_value;
                        
                        $service_charge = $service_charge - $discount_amount;
                    } else {
                        $discount_amount = 0;
                    }
                }else{
                    $service_charge = $data->charges ?? 0;
                    if ($data->is_inclusive_tax == 1 && $data->inclusive_tax) {
                        $is_inclusive_tax = true;
                        $inclusive_tax_json = json_decode($data->inclusive_tax, true);
                        
                        if ($inclusive_tax_json) {
                            foreach ($inclusive_tax_json as $tax) {
                                $tax_amount = 0;
                                if ($tax['type'] == 'percent') {
                                    $tax_amount = $service_charge * $tax['value'] / 100;
                                } elseif ($tax['type'] == 'fixed') {
                                    $tax_amount = $tax['value'];
                                }
                                
                                $inclusive_tax_amount += $tax_amount;
                                $inclusive_tax_data[] = [
                                    'title' => $tax['title'] ?? 'Tax',
                                    'type' => $tax['type'],
                                    'value' => $tax['value'],
                                    'amount' => round($tax_amount, 2)
                                ];
                            }
                        }
                        
                        // Add inclusive tax to service charge (base + inclusive tax)
                        $service_charge = $service_charge + $inclusive_tax_amount;
                    }
                    
                    // Now apply discount on the price that includes inclusive tax
                    if ($data->discount == 1) {
                        $discount_amount = ($data->discount_type == 'percentage')
                        ? $service_charge * $data->discount_value / 100
                        : $data->discount_value;
                        
                        $service_charge = $service_charge - $discount_amount;
                    } else {
                        $discount_amount = 0;
                    }
                }
        }
        
        $response_data = [
            'service_charge' => $service_charge,
            'inclusive_tax_data' => $inclusive_tax_data,
            'inclusive_tax_amount' => $inclusive_tax_amount,
            'is_inclusive_tax' => $is_inclusive_tax,
            'discount' => $discount_amount ?? 0,
            'discount_amount' => $discount_amount ?? 0,
            'base_price' => isset($doctorService) ? $doctorService->charges : $data->charges ?? 0,
            'tax_type' => isset($data) ? $data->tax_type ?? 'exclusive' : 'exclusive',
            'discount_type' => isset($data) ? $data->discount_type : null,
            'discount_value' => isset($data) ? $data->discount_value : null
        ];

        return response()->json($response_data);
    }

    /* category wise service list */
    public function categort_services_list(Request $request)
    {
        $category = $request->category_id;
        $categoryService = ClinicsService::where('category_id', $category)->get();

        return $categoryService;
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                $ClinicsService = ClinicsService::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('clinic.clinicservice_status');
                break;

            case 'delete':

                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }

                ClinicsService::whereIn('id', $ids)->delete();
                $message = __('clinic.clinicservice_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('service_providers.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function update_status(Request $request, ClinicsService $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('clinic.clinicservice_status')]);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
         
        $userId = auth()->id();
        $user = auth()->user();
        $module_name = $this->module_name;

        $query = ClinicsService::SetRole($user)
            ->with('category', 'sub_category', 'doctor_service', 'ClinicServiceMapping', 'systemservice', 'vendor')
            ->withCount(['doctor_service']);

        if ($user->hasRole('doctor')) {
            $query->whereHas('doctor_service', function ($q) use ($userId) {
                $q->where('doctor_id', $userId);
            });
        }

        if ($request->doctor_id !== null) {
            $doctor_id = $request->doctor_id;
            $query->whereHas('doctor_service', function ($query) use ($doctor_id) {
                $query->where('doctor_id', $doctor_id);
            });
        }

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }

            if (isset($filter['service_id'])) {
                $query->where('system_service_id', $filter['service_id']);
            }

            if (isset($filter['price'])) {
                $priceRange = explode('-', $filter['price']);
                if (count($priceRange) === 2) {
                    $minPrice = (int) $priceRange[0];
                    $maxPrice = (int) $priceRange[1];
                    $query->whereBetween('charges', [$minPrice, $maxPrice]);
                }
            }

            if (isset($filter['category_id'])) {
                $query->where('category_id', $filter['category_id']);
            }

            if (isset($filter['sub_category_id'])) {
                $query->where('subcategory_id', $filter['sub_category_id']);
            }

            if (isset($filter['doctor_id'])) {
                $query->whereHas('doctor_service', function ($query) use ($filter) {
                    $query->where('doctor_id', $filter['doctor_id']);
                });
            }

            if (isset($filter['clinic_id'])) {
                $query->whereHas('ClinicServiceMapping', function ($query) use ($filter) {
                    $query->where('clinic_id', $filter['clinic_id']);
                });

            }
            if (isset($filter['clinic_admin'])) {
                $query->where('vendor_id', $filter['clinic_admin']);
            }

        }

        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $data->id . '"  name="datatable_ids[]" value="' . $data->id . '" onclick="dataTableRowCheck(' . $data->id . ')">';
            })
            ->editColumn('name', function ($data) {
                return '<img src="' . $data->file_url . '" class="avatar avatar-50 rounded-pill me-3">' . $data->name;
            })

            ->addColumn('action', function ($data) use ($module_name) {
                return view('clinic::backend.services.action_column', compact('module_name', 'data'));
            })

            ->editColumn('charges', function ($data) {
                return \Currency::format($data->charges);
            })
            ->editColumn('duration_min', function ($data) {
                return $data->duration_min . ' Min';
            })
            ->editColumn('status', function ($row) {
                $checked = '';
                if ($row->status) {
                    $checked = 'checked="checked"';
                }

                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="' . route('backend.services.update_status', $row->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $row->id . '"  name="status" value="' . $row->id . '" ' . $checked . '>
                    </div>
                ';
            })

            ->editColumn('category_id', function ($data) {
                $category = isset($data->category->name) ? $data->category->name : '-';
                if (isset($data->sub_category->name)) {
                    $category = $category . ' > ' . $data->sub_category->name;
                }

                return $category;
            })
            ->editColumn('vendor_id', function ($data) {
                $vendor = optional($data->vendor)->full_name;
                return $vendor;
            })
            ->filterColumn('category', function ($query, $keyword) {
                $query->whereHas('category', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->filterColumn('vendor_id', function ($query, $keyword) {
                $query->whereHas('vendor', function ($q) use ($keyword) {
                    $q->where('first_name', 'LIKE', '%' . $keyword . '%')
                      ->orWhere('last_name', 'LIKE', '%' . $keyword . '%');
                });
            })
            ->editColumn('doctor', function ($data) use ($user) {
                if ($user->hasRole('doctor')) {
                    $data->doctor_service_count = $data->doctor_service->where('doctor_id', $user->id)->count();
                    return "<button type='button' data-assign-module='" . $data->id . "' data-assign-target='#service-doctor-assign-form' data-assign-event='doctor_assign' class='btn btn-sm p-0 text-primary' data-bs-toggle='tooltip' title='Assign Doctor To Service'><span class='bg-primary-subtle rounded tbl-badge'><b>$data->doctor_service_count</b></button></span>";
                } else {
                    return "<span class='bg-primary-subtle rounded tbl-badge'><b>$data->doctor_service_count</b> <button type='button' data-assign-module='" . $data->id . "' data-assign-target='#service-doctor-assign-form' data-assign-event='doctor_assign' class='btn btn-sm p-0 text-primary' data-bs-toggle='tooltip' title='Assign Doctor To Service'><i class='ph ph-plus'></i></button></span>";
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

            ->orderColumns(['id'], '-:column $1');

        $customFieldColumns = CustomField::customFieldData($datatable, ClinicsService::CUSTOM_FIELD_MODEL, null);

        return $datatable->rawColumns(array_merge(['action', 'name', 'image', 'status', 'check', 'doctor', 'vendor_id'], $customFieldColumns))
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
    // public function store(ClinicsServiceRequest $request)
    // {
    // //  dd($request->all());
    //     $data = $request->except('file_url');
    //     $clinicid = $request->clinic_id;
    //     if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')) {
    //         $data['vendor_id'] = $request->filled('vendor_id') ? $request->vendor_id : auth()->user()->id;
    //     } elseif (auth()->user()->hasRole('receptionist')) {
    //         $vendor_id = Receptionist::where('receptionist_id', auth()->user()->id)
    //             ->whereHas('clinics', function ($query) use ($clinicid) {
    //                 $query->where('clinic_id', $clinicid);
    //             })
    //             ->pluck('vendor_id')
    //             ->first();
    //         $data['vendor_id'] = $vendor_id;
    //     } else {
    //         $data['vendor_id'] = auth()->user()->id;
    //     }
    //     if ($request->has('system_service_id') && $request->system_service_id != null) {
    //         $systemService = SystemService::where('id', $data['system_service_id'])->first();
    //         $data['name'] = $systemService->name;
    //     }

    //      if($data['discount']==0){

    //          $data['discount_value']=0;
    //          $data['discount_type']=null;
    //          $data['service_discount_price'] = $data['charges'];
    //      }else{
    //         $data['discount_price'] = $data['discount_type'] == 'percentage' ? $data['charges'] * $data['discount_value'] / 100 : $data['discount_value'];
    //         $data['service_discount_price'] = $data['charges'] - $data['discount_price'];
    //      }
    //     $inclusive_tax_price = $this->inclusiveTaxPrice($data);

    //     $data['inclusive_tax'] =  $inclusive_tax_price['inclusive_tax'];
    //     $data['inclusive_tax_price'] = $inclusive_tax_price['inclusive_tax_price'];


    //     $query = ClinicsService::create($data);

    //     if ($request->has('clinic_id') && $request->clinic_id != null) {

    //         $clinic_ids = explode(',', $request->clinic_id);

    //         foreach ($clinic_ids as $value) {

    //             $service_mapping_data = [

    //                 'service_id' => $query['id'],
    //                 'clinic_id' => $value,

    //             ];

    //             ClinicServiceMapping::create($service_mapping_data);
    //         }
    //     }
    //     if ($request->has('doctor_id') && $request->doctor_id != null && $request->has('clinic_id') && $request->clinic_id != null) {

    //         $inclusive_tax_price = $this->inclusiveTaxPrice($query);
    //         $query['inclusive_tax_price'] = $inclusive_tax_price['inclusive_tax_price'];

    //         $service_mapping = [
    //             'service_id' => $query['id'],
    //             'clinic_id' => $request->clinic_id,
    //             'doctor_id' => $request->doctor_id,
    //             'charges' => $query['charges'] ?? 0,
    //             'inclusive_tax_price' => $query['inclusive_tax_price'] ?? 0,
    //         ];

    //         DoctorServiceMapping::updateOrCreate(
    //             [
    //                 'service_id' => $query['id'],
    //                 'clinic_id' => $request->clinic_id,
    //                 'doctor_id' => $request->doctor_id
    //             ],
    //             $service_mapping
    //         );

    //     }
    //     if ($request->custom_fields_data) {
    //         $query->updateCustomFieldData(json_decode($request->custom_fields_data));
    //     }

    //     if ($request->hasFile('file_url')) {
    //         storeMediaFile($query, $request->file('file_url'));
    //     }

    //     $message = __('messages.create_form', ['form' => __('service.singular_title')]);

    //     if ($request->is('api/*')) {
    //         return response()->json(['message' => $message, 'data' => $data, 'status' => true], 200);
    //     } else {
    //         return response()->json(['message' => $message, 'status' => true], 200);
    //     }
    // }
    public function store(ClinicsServiceRequest $request)
    {
        // dd($request->all());
        $data = $request->except('file_url');

        // Normalize type/service_type
        if (isset($data['service_type']) && empty($data['type'])) {
            $data['type'] = $data['service_type'];
        }
        if (isset($data['type']) && empty($data['service_type'])) {
            $data['service_type'] = $data['type'];
        }
        $clinicIds = $request->clinic_id; // already array (because clinic_id[] in form)

        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')) {
            $data['vendor_id'] = $request->filled('vendor_id') ? $request->vendor_id : auth()->user()->id;
        } elseif (auth()->user()->hasRole('receptionist')) {
            $vendor_id = Receptionist::where('receptionist_id', auth()->user()->id)
                ->whereHas('clinics', function ($query) use ($clinicIds) {
                    $query->whereIn('clinic_id', (array) $clinicIds); // ✅ use array
                })
                ->pluck('vendor_id')
                ->first();
            $data['vendor_id'] = $vendor_id;
        } else {
            $data['vendor_id'] = auth()->user()->id;
        }

        if ($request->filled('system_service_id')) {
            $systemService = SystemService::find($data['system_service_id']);
            if ($systemService) {
                $data['name'] = $systemService->name;
            }
        }
        $inclusive_tax_price = $this->inclusiveTaxPrice($data);
        $data['inclusive_tax'] = $inclusive_tax_price['inclusive_tax'];
        $data['inclusive_tax_price'] = $inclusive_tax_price['inclusive_tax_price'];

        // ✅ Handle discount safely
        if (!isset($data['discount']) || $data['discount'] == 0) {
            $data['discount'] = 0;
            $data['discount_value'] = 0;
            $data['discount_type'] = null;
            $data['service_discount_price'] = $data['charges'] + $data['inclusive_tax_price'];
        } else {
            $data['discount_price'] = $data['discount_type'] == 'percentage'
                ? $data['charges'] * $data['discount_value'] / 100
                : $data['discount_value'];

            $data['service_discount_price'] = ($data['charges'] + $data['inclusive_tax_price']) - $data['discount_price'];
        }

        // Tax calculation
        

        $query = ClinicsService::create($data);

        // ✅ Save service → clinic mappings (for both in_clinic and online types)
        if ($request->has('clinic_id') && is_array($clinicIds)) {
            foreach ($clinicIds as $value) {
                ClinicServiceMapping::create([
                    'service_id' => $query->id,
                    'clinic_id'  => $value,
                ]);
            }
        }

        // ✅ Save doctor → service mappings for each clinic
        if (($query->type ?? 'in_clinic') === 'in_clinic' && $request->filled('doctor_id') && is_array($clinicIds)) {
            foreach ($clinicIds as $value) {
                $inclusive_tax_price = $this->inclusiveTaxPrice($query);
                $query['inclusive_tax_price'] = $inclusive_tax_price['inclusive_tax_price'];

                DoctorServiceMapping::updateOrCreate(
                    [
                        'service_id' => $query->id,
                        'clinic_id'  => $value,
                        'doctor_id'  => $request->doctor_id,
                    ],
                    [
                        'charges'            => $query->charges ?? 0,
                        'inclusive_tax_price'=> $query->inclusive_tax_price ?? 0,
                    ]
                );
            }
        }

        if ($request->filled('custom_fields_data')) {
            $query->updateCustomFieldData(json_decode($request->custom_fields_data, true));
        }

        if ($request->hasFile('file_url')) {
            storeMediaFile($query, $request->file('file_url'));
        }

        $message = __('messages.create_form', ['form' => __('service.singular_title')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }


    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $module_action = 'Show';

        $data = ClinicsService::findOrFail($id);

        return view('clinic::backend.services.show', compact('module_action', "$data"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = ClinicsService::with('ClinicServiceMapping')->findOrFail($id);
        if (!is_null($data)) {
            $custom_field_data = $data->withCustomFields();
            $data['custom_field_data'] = collect($custom_field_data->custom_fields_data)
            ->filter(function ($value) {
                return $value !== null;
            })
            ->toArray();
        }
        // dd($data );

        $data['clinic_admin_id'] = $data->vendor_id ?? null;
        $data['clinic_id'] = $data->ClinicServiceMapping->pluck('clinic_id') ?? [];
        $data['file_url'] = $data->file_url;
//  dd($data['clinic_id']);

        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ClinicsServiceRequest $request, $id)
    {
        // Debug: Log the incoming request data
        \Log::info('Service Update Request', [
            'id' => $id,
            'request_data' => $request->except('file_url'),
            'clinic_id' => $request->clinic_id,
            'vendor_id' => $request->vendor_id,
            'system_service_id' => $request->system_service_id,
            'file_url' => $request->file_url,
            'has_file' => $request->hasFile('file_url')
        ]);
     
        $data = ClinicsService::findOrFail($id);
        $request_data = $request->except('file_url');

        // Normalize type/service_type
        if (isset($request_data['service_type']) && empty($request_data['type'])) {
            $request_data['type'] = $request_data['service_type'];
        }
        if (isset($request_data['type']) && empty($request_data['service_type'])) {
            $request_data['service_type'] = $request_data['type'];
        }

        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')) {
            $request_data['vendor_id'] = $request->filled('vendor_id') ? $request->vendor_id : auth()->user()->id;
        }

        // Handle system service update (like in store method)
        if ($request->filled('system_service_id')) {
            $systemService = SystemService::find($request_data['system_service_id']);
            if ($systemService) {
                $request_data['name'] = $systemService->name;
                \Log::info('System Service Updated', [
                    'old_name' => $data->name,
                    'new_name' => $systemService->name,
                    'system_service_id' => $request_data['system_service_id']
                ]);
            }
        }

        $inclusive_tax_price = $this->inclusiveTaxPrice($request_data);
// dd($inclusive_tax_price);
        $request_data['inclusive_tax'] = $inclusive_tax_price['inclusive_tax'];
        $request_data['inclusive_tax_price'] = $inclusive_tax_price['inclusive_tax_price'];

        // Safely get discount and related fields, defaulting to 0 or null if not set
        $discount = isset($request_data['discount']) ? $request_data['discount'] : 0;
        $charges = isset($request_data['charges']) ? $request_data['charges'] : 0;
        $discount_value = isset($request_data['discount_value']) ? $request_data['discount_value'] : 0;
        $discount_type = isset($request_data['discount_type']) ? $request_data['discount_type'] : null;

        if ($discount == 0) {
            $request_data['discount_value'] = 0;
            $request_data['discount_type'] = null;
            $request_data['service_discount_price'] = $charges + $inclusive_tax_price['inclusive_tax_price'];
        } else {
            $request_data['discount_price'] = ($discount_type == 'percentage')
                ? $charges * $discount_value / 100
                : $discount_value;
            $request_data['service_discount_price'] = ($charges + $inclusive_tax_price['inclusive_tax_price']) - (isset($request_data['discount_price']) ? $request_data['discount_price'] : 0);
        }

        
// dd($request_data);
        $data->update($request_data);
        
        // Debug: Log the updated data
        \Log::info('Service Updated', [
            'id' => $data->id,
            'updated_data' => $data->fresh()->toArray()
        ]);

        // Save clinic mappings for both in_clinic and online types
        if ($request->has('clinic_id') && $request->clinic_id != null) {
            // Handle array or comma-separated string
            $clinic_ids = is_array($request->clinic_id)
                ? $request->clinic_id
                : explode(',', $request->clinic_id);

            $clinic_ids = array_filter(array_map('intval', $clinic_ids)); // Remove empty values

            \Log::info('Clinic Mapping Update', [
                'service_id' => $data->id,
                'clinic_ids_received' => $request->clinic_id,
                'clinic_ids_processed' => $clinic_ids,
                'type' => $data->type
            ]);

            // Only update mappings if we have clinic_ids to save
            if (!empty($clinic_ids)) {
                // Remove ALL existing mappings for this service first
                ClinicServiceMapping::where('service_id', $data->id)->delete();

                // Create new mappings
                foreach ($clinic_ids as $value) {
                    ClinicServiceMapping::create([
                        'service_id' => $data->id,
                        'clinic_id'  => $value,
                    ]);
                }
                
                \Log::info('Clinic Mappings Saved', [
                    'service_id' => $data->id,
                    'count' => count($clinic_ids)
                ]);
            } else {
                \Log::warning('No clinic IDs to save, existing mappings preserved', [
                    'service_id' => $data->id
                ]);
            }
        } else {
            \Log::warning('clinic_id not provided in request', [
                'service_id' => $data->id,
                'has_clinic_id' => $request->has('clinic_id'),
                'clinic_id_value' => $request->clinic_id
            ]);
        }

        if ($request->custom_fields_data) {
            $data->updateCustomFieldData(json_decode($request->custom_fields_data));
        }

        // Handle file upload and removal
        if ($request->hasFile('file_url')) {
            // New file uploaded - store it
            storeMediaFile($data, $request->file('file_url'), 'file_url');
            \Log::info('New Image Uploaded', ['service_id' => $data->id]);
        } elseif ($request->boolean('remove_image') || ($request->has('file_url') && ($request->file_url === null || $request->file_url === 'null' || $request->file_url === ''))) {
            // Image was removed - clear the media collection
            $data->clearMediaCollection('file_url');
            \Log::info('Image Removed', ['service_id' => $data->id, 'remove_image_flag' => $request->boolean('remove_image'), 'file_url_value' => $request->file_url]);
        } elseif ($request->has('file_url') && $request->file_url != null && $request->file_url !== 'null' && !$request->hasFile('file_url')) {
            // Existing image URL sent as string - keep existing image (do nothing)
            // This means the user didn't change the image
            \Log::info('Image Preserved', ['service_id' => $data->id, 'image_url' => $request->file_url]);
        }

        $message = __('messages.update_form', ['form' => __('service.singular_title')]);

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

        if(\Auth::user()->hasAnyRole(['demo_admin'])){

            return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
        }

        $data = ClinicsService::with('ClinicServiceMapping')->findOrFail($id);
        $data->ClinicServiceMapping()->delete();
        $data->delete();

        $message = __('messages.delete_form', ['form' => __('service.singular_title')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function trashed()
    {
        $module_name_singular = Str::singular($this->module_name);

        $module_action = 'Trash List';

        $data = ClinicsService::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate();

        return view('clinic::backend.services.trash', compact("$data", 'module_name_singular', 'module_action'));
    }

    public function restore($id)
    {
        $data = ClinicsService::withTrashed()->find($id);
        $data->restore();

        $message = __('messages.service_data');

        return response()->json(['message' => $message, 'status' => true]);
    }

    public function assign_doctor_list(Request $request)
    {

        if (auth()->user()->hasRole('doctor')) {
            $query = DoctorServiceMapping::with('doctors')->where('doctor_id', auth()->user()->id)->where('service_id', $request->service_id)->where('clinic_id', $request->clinic_id);
        } else {
            $query = DoctorServiceMapping::with('doctors')->where('service_id', $request->service_id)->where('clinic_id', $request->clinic_id);
        }

        $query_data = $query->get();
        $data = [];

        if ($query_data) {

            foreach ($query_data as $row) {

                $data[] = [
                    'service_mapping_id' => $row->id,
                    'doctor_id' => $row->doctors->doctor_id,
                    'doctor_name' => $row->doctors->user ? $row->doctors->user->first_name . ' ' . $row->doctors->user->last_name : null,
                    'avatar' => $row->doctors->user ? $row->doctors->user->profile_image : null,
                    'charges' => $row->charges
                ];
            }

        }



        // $doctorIds = DoctorServiceMapping::where('service_id', $request->service_id)->pluck('doctor_id');
        // $doctorServiceMapping = DoctorServiceMapping::whereIn('doctor_id', $doctorIds)->get();

        // $doctors = $query->whereIn('doctor_id', $doctorIds)->get();
        // $doctor_service = $doctors->map(function ($doctor) use ($doctorServiceMapping) {
        //     $user = $doctor->user;
        //     $mapping = $doctorServiceMapping->where('doctor_id', $doctor->doctor_id)->first();

        //     return [
        //         'id' => $mapping ? $mapping->id : null,
        //         'doctor_id' => $doctor->doctor_id,
        //         'doctor_name' => $user ? $user->first_name . ' ' . $user->last_name : null,
        //         'avatar' => $user ? $user->profile_image : null,
        //         'charges' => $mapping ? $mapping->charges : null,
        //     ];
        // });

        return response()->json(['status' => true, 'data' => $data]);
    }
    public function assign_doctor_update($id, Request $request)
    {
        $service = ClinicsService::findOrFail($id);
        DoctorServiceMapping::where('service_id', $id)->where('clinic_id', $request->clinic_id)->forceDelete();

        foreach ($request->doctors as $key => $doctor) {
            $service['charges'] = $doctor['charges'] ?? 0;
            if($service['discount']==0){

                $service['discount_value']=0;
                $service['discount_type']=null;
                $service['service_discount_price'] = $service['charges'];
            }else{
                $service['discount_price'] = $service['discount_type'] == 'percentage' ? $service['charges'] * $service['discount_value'] / 100 : $service['discount_value'];
                $service['service_discount_price'] = $service['charges'] - $service['discount_price'];
             }
            $inclusive_tax_price = $this->inclusiveTaxPrice($service);
            $doctor['inclusive_tax_price'] = $inclusive_tax_price['inclusive_tax_price'];
            $service_mapping = [
                'service_id' => $id,
                'clinic_id' => $request->clinic_id,
                'doctor_id' => $doctor['doctor_id'],
                'charges' => $doctor['charges'] ?? 0,
                'inclusive_tax_price' => $doctor['inclusive_tax_price'] ?? 0,
            ];

            DoctorServiceMapping::updateOrCreate(
                [
                    'service_id' => $id,
                    'clinic_id' => $request->clinic_id,
                    'doctor_id' => $doctor['doctor_id']
                ],
                $service_mapping
            );
        }

        return response()->json(['status' => true, 'message' => __('clinic.doctor_service_update')]);
    }

    public function ServiceDetails(Request $request)
    {
        $serviceDetails = [];

        if ($request->filled('service_id') && $request->service_id != null && $request->filled('encounter_id') && $request->encounter_id != null) {

            $encounterDetails = PatientEncounter::with('appointment')->where('id', $request->encounter_id)->first();

            $doctor_id = $encounterDetails->doctor_id;
            $clinic_id = $encounterDetails->clinic_id;

            $serviceDetails = ClinicsService::where('id', $request->service_id)
                ->with([
                    'doctor_service' => function ($query) use ($doctor_id, $clinic_id) {
                        $query->where('doctor_id', $doctor_id)
                            ->where('clinic_id', $clinic_id)->first();
                    }
                ])->first();

            $doctorService = $serviceDetails && $serviceDetails->doctor_service ? $serviceDetails->doctor_service->first() : null; // because it's a relationship (hasMany or morphMany)

            // FIXED CALCULATION FLOW: Add inclusive tax BEFORE applying discount
            // OLD FLOW (COMMENTED): Discount → Inclusive Tax
            // NEW FLOW: Base Charge → Inclusive Tax → Discount
            // This matches AppointmentTrait and BillingRecordTrait
            
            // Initialize variables to avoid undefined variable errors
            $finalCharge = 0;
            $final_inclusive_amount = 0;

            if ($doctorService) {
                $baseCharge = $doctorService->charges;
                $discountType = $serviceDetails->discount_type;
                $discountValue = $serviceDetails->discount_value;

                // OLD CODE (COMMENTED): Discount was applied first, then inclusive tax
                // if ($discountType == 'percentage') {
                //     $finalCharge = $baseCharge - ($baseCharge * $discountValue / 100);
                // } elseif ($discountType == 'fixed') {
                //     $finalCharge = $baseCharge - $discountValue;
                // } else {
                //     $finalCharge = $baseCharge;
                // }
                // $final_inclusive_amount_array = $this->calculate_inclusive_tax($finalCharge, $serviceDetails->inclusive_tax);
                // $final_inclusive_amount = $final_inclusive_amount_array['total_inclusive_tax'];

                // NEW CODE: Step 1 - Calculate inclusive tax on BASE charge FIRST
                $final_inclusive_amount_array = $this->calculate_inclusive_tax($baseCharge, $serviceDetails->inclusive_tax);
                $final_inclusive_amount = $final_inclusive_amount_array['total_inclusive_tax'];
                
                // Step 2 - Add inclusive tax to base charge
                $chargeWithInclusiveTax = $baseCharge + $final_inclusive_amount;
                
                // Step 3 - Apply discount on (base + inclusive tax)
                if ($discountType == 'percentage') {
                    $finalCharge = $chargeWithInclusiveTax - ($chargeWithInclusiveTax * $discountValue / 100);
                } elseif ($discountType == 'fixed') {
                    $finalCharge = $chargeWithInclusiveTax - $discountValue;
                } else {
                    $finalCharge = $chargeWithInclusiveTax;
                }

                // Optional: ensure final charge is not negative
                $finalCharge = max($finalCharge, 0);
            }

            $servicePricedata = [];
            if ($encounterDetails->appointment == null) {
                $servicePricedata = $this->getServiceAmount($request->service_id, $doctor_id, $clinic_id);

                $serviceDetails['tax_data'] = $servicePricedata['service_amount'] > 0 ? $this->calculateTaxdata($servicePricedata['service_amount']) : null;
            } else {
                $taxes = optional(optional($encounterDetails->appointment)->appointmenttransaction)->tax_percentage;
                $serviceTax = 0;
                $gstPercentage = 0;
                if (is_string($taxes)) {
                    $taxes = json_decode($taxes, true);
                }
                if (is_array($taxes)) {
                    foreach ($taxes as $tax) {
                        if ($tax['type'] === 'fixed') {
                            $serviceTax = $tax['value'];
                        } elseif ($tax['type'] === 'percent') {
                            $gstPercentage = $tax['value'];
                        }
                    }
                }
                $gstAmount = optional($encounterDetails->appointment)->service_amount * ($gstPercentage / 100);
                $totalTax = $serviceTax + $gstAmount;
                $servicePricedata = [
                    'service_price' => optional($encounterDetails->appointment)->service_price,
                    'doctor_charge_with_discount' => $finalCharge,
                    'service_amount' => optional($encounterDetails->appointment)->service_amount,
                    'total_amount' => optional($encounterDetails->appointment)->total_amount,
                    'duration' => optional($encounterDetails->appointment)->duration ?? 0,
                    'total_tax' => $totalTax ?? 0,
                    'discount_type' => optional(optional($encounterDetails->appointment)->appointmenttransaction)->discount_type,
                    'discount_value' => optional(optional($encounterDetails->appointment)->appointmenttransaction)->discount_value,
                    'discount_amount' => optional(optional($encounterDetails->appointment)->appointmenttransaction)->discount_amount,
                    'final_inclusive_amount' => $final_inclusive_amount ?? 0,
                ];

                $service_amount = optional($encounterDetails->appointment)->service_amount;
                $tax_data = [];
                $taxes = json_decode(optional(optional($encounterDetails->appointment)->appointmenttransaction)->tax_percentage);
                if (is_array($taxes) || is_object($taxes)) {
                    foreach ($taxes as $tax) {
                        $amount = 0;
                        if ($tax->type == 'percent') {
                            $amount = ($tax->value / 100) * $service_amount;
                        } else {
                            $amount = $tax->value ?? 0;
                        }
                        $tax_data[] = [
                            'title' => $tax->title,
                            'value' => $tax->value,
                            'type' => $tax->type,
                            'amount' => (float) number_format($amount, 2),
                            'tax_type' => $tax->tax_scope ?? $tax->tax_type,
                        ];
                    }
                }
                $serviceDetails['tax_data'] = $tax_data;
            }

            $serviceDetails['service_price_data'] = $servicePricedata;
        }

        return response()->json(['status' => true, 'data' => $serviceDetails]);
    }
    public function discountPrice(Request $request)
    {
        $serviceCharge = 0;
        $discountAmount = 0;

        if ($request->has(['service_id', 'doctor_id'])) {
            $serviceId = $request->service_id;
            $doctorId = $request->doctor_id;

            $clinicService = ClinicsService::with([
                'doctor_service' => function ($query) use ($doctorId) {
                    $query->where('doctor_id', $doctorId);
                }
            ])->find($serviceId);

            if ($clinicService && $clinicService->doctor_service->isNotEmpty()) {
                $doctorService = $clinicService->doctor_service->first();
                $serviceCharge = $doctorService->charges;

                if ($clinicService->discount) {
                    $discountAmount = ($clinicService->discount_type === 'percentage')
                        ? ($serviceCharge * $clinicService->discount_value) / 100
                        : $clinicService->discount_value;
                }
            }
        }

        return response()->json([
            'service_charge' => $serviceCharge,
            'discount_amount' => $discountAmount,
        ]);
    }

    public function initData()
    {
        try {
            // Fetch Categories
            $categories = ClinicsCategory::select('id', 'name')
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get();

            // Fetch System Services
            $systemServices = SystemService::select('id', 'name')
                ->orderBy('name')
                ->get();

            // Fetch Clinic Admins
            $clinicAdmins = user::select('id', \DB::raw("CONCAT(first_name, ' ', last_name) as name"))
            ->where('user_type', 'vendor')
            ->orderBy('first_name')
            ->get();

            // Fetch Clinics (for non-multivendor or all clinics)
            $clinics = Clinics::select('id', 'name')
                ->where('status', 1)
                ->orderBy('name')
                ->get();
 
            return response()->json([
                'categories' => $categories,
                'systemServices' => $systemServices,
                'clinicAdmins' => $clinicAdmins,
                'clinics' => $clinics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to load service form data',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


     

}
