<?php

namespace Modules\Clinic\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\LocationHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\Nurse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Carbon\Carbon;
use Modules\Clinic\Models\Clinics;
use Modules\World\Models\Country;
use Modules\World\Models\State;
use Modules\World\Models\City;
use Hash;

class NurseController extends Controller
{
    use LocationHelper;
    protected string $exportClass = '\App\Exports\NurseExport';
    
    public function __construct()
    {
        // Page Title
        $this->module_title = 'Nurse';

        // module name
        $this->module_name = 'nurse';

        // directory path of the module
        $this->module_path = 'clinic::backend';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => 'fa-regular fa-user-nurse',
            'module_name' => $this->module_name,
            'module_path' => $this->module_path,
        ]);
        
        $this->middleware(['permission:view_clinic_nurse_list'])->only('index');
        $this->middleware(['permission:edit_clinic_nurse_list'])->only('edit', 'update');
        $this->middleware(['permission:add_clinic_nurse_list'])->only('store');
        $this->middleware(['permission:delete_clinic_nurse_list'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $module_action = 'List';
        $module_title = 'clinic.nurse';
        $columns = CustomFieldGroup::columnJsonValues(new User());
        $customefield = CustomField::exportCustomFields(new User());

        $vendors = [];
        $clinicCenters = [];
        $countries = [];
        
        if (config('app.multi_vendor_enabled', false) && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin'))) {
            $vendors = User::role('vendor')->where('status', 1)->where('is_banned', 0)->get(['id', 'name']);
        }
        
        $clinicCenters = Clinics::where('status', 1)->get(['id', 'name']);
        $countries = Country::where('status', 1)->get(['id', 'name']);

        $export_import = true;
        $export_columns = [
            [
                'value' => 'Name',
                'text' => __('service.lbl_name'),
            ],
            [
                'value' => 'mobile',
                'text' => __('customer.lbl_phone_number'),
            ],
            [
                'value' => 'email',
                'text' => __('appointment.lbl_email'),
            ],
            [
                'value' => 'specialization',
                'text' => __('nurse.lbl_specialization'),
            ],
            [
                'value' => 'Clinic Center',
                'text' => __('clinic.singular_title'),
            ],
            [
                'value' => 'status',
                'text' => __('customer.lbl_status'),
            ],
        ];
        
        $export_url = route('backend.nurse.export');
        
        return view('clinic::backend.nurse.index', compact('module_action', 'module_title', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url', 'vendors', 'clinicCenters', 'countries'));
    }

    public function index_list(Request $request)
    {
        $term = trim($request->q);

        $query_data = User::role(['nurse'])->with('media')->where(function ($q) use ($term) {
            if (!empty($term)) {
                $q->orWhere('first_name', 'LIKE', "%$term%");
                $q->orWhere('last_name', 'LIKE', "%$term%");
            }
        });

        $query_data = $query_data->where('status', 1)->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'name' => $row->full_name,
                'avatar' => $row->profile_image,
                'email' => $row->email,
                'mobile' => $row->mobile,
                'created_at' => $row->created_at,
            ];
        }

        return response()->json($data);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $module_name = $this->module_name;
        $query = User::role('nurse')->with('nurse');
        $userId = auth()->id();

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        
        $query->orderBy('created_at', 'desc');

        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->addColumn('action', function ($data) use ($module_name) {
                return view('clinic::backend.nurse.action_column', compact('module_name', 'data'));
            })
            ->editColumn('image', function ($data) {
                return "<img src='" . $data->profile_image . "'class='avatar avatar-50 rounded-pill'>";
            })
            ->editColumn('nurse_id', function ($data) {
                return view('clinic::backend.nurse.user_id', compact('data'));
            })
            ->filterColumn('nurse_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->where('first_name', 'like', '%' . $keyword . '%')->orWhere('last_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%');
                }
            })
            ->orderColumn('nurse_id', function ($query, $order) {
                $query->orderByRaw("CONCAT(first_name, ' ', last_name) $order");
            }, 1)
            ->editColumn('clinic_id', function ($data) {
                return view('clinic::backend.nurse.clinic_id', compact('data'));
            })
            ->editColumn('specialization', function ($data) {
                return optional($data->nurse)->specialization ?? '--';
            })
            ->editColumn('status', function ($row) {
                $checked = '';
                if ($row->status) {
                    $checked = 'checked="checked"';
                }

                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="' . route('backend.nurse.update_status', $row->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $row->id . '"  name="status" value="' . $row->id . '" ' . $checked . '>
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

        // Custom Fields For export
        $customFieldColumns = CustomField::customFieldData($datatable, User::CUSTOM_FIELD_MODEL, null);

        return $datatable->rawColumns(array_merge(['action', 'status', 'check', 'image', 'clinic_id'], $customFieldColumns))
            ->toJson();
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;

        $message = __('nurse.bulk_update');
        
        switch ($actionType) {
            case 'change-status':
                $nurse = User::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('nurse.bulk_nurse_update');
                break;

            case 'delete':
                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('nurse.permission_denied'), 'status' => false], 200);
                }
                User::whereIn('id', $ids)->delete();
                $message = __('nurse.bulk_nurse_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('service_providers.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }
    
    public function update_status(Request $request, User $id)
    {
        $id->update(['status' => $request->status]);
        return response()->json(['status' => true, 'message' => __('service_providers.status_update')]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->except(['profile_image', '_token', '_method']);

            // Auto-set UK country if not provided
            if (empty($data['country'])) {
                $data['country'] = 229; // UK
            }

            // Handle custom state/city entries with duplicate prevention
            if (!empty($data['state'])) {
                $data['state'] = $this->getOrCreateState($data['state']);
            }
            if (!empty($data['city'])) {
                $data['city'] = $this->getOrCreateCity($data['city']);
            }

            // Determine vendor context
            $vendorId = auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')
                ? ($request->filled('vendor_id') ? $request->vendor_id : auth()->user()->id)
                : auth()->user()->id;

            $data['email_verified_at'] = Carbon::now();
            $data['user_type'] = 'nurse';
            $data['status'] = $request->has('status') ? 1 : 1;
            $data['password'] = Hash::make($data['password']);

            // Create User
            $user = User::create($data);
            
            if (function_exists('multiVendor') && multiVendor()) {
                $clinic = Clinics::where('id', $request->clinic_id)
                    ->when(auth()->user()->hasRole('vendor'), function ($q) use ($vendorId) {
                        $q->where('vendor_id', $vendorId);
                    })
                    ->first();

                if (!$clinic) {
                    throw new \Exception(__('clinic.invalid_clinic_selection'));
                }
            }

            $user->syncRoles(['nurse']);

            $nurse = Nurse::create([
                'nurse_id' => $user->id,
                'clinic_id' => $request->clinic_id,
                'vendor_id' => $vendorId,
            ]);

            if ($request->custom_fields_data) {
                $user->updateCustomFieldData(json_decode($request->custom_fields_data));
            }
            
            if ($request->hasFile('profile_image')) {
                storeMediaFile($user, $request->file('profile_image'), 'profile_image');
            }

            $message = __('messages.create_form', ['form' => __('nurse.singular_title')]);

            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'status' => true,
                    'data' => [
                        'user_id' => $user->id,
                        'nurse_id' => $nurse->id ?? null,
                        'mapping' => $nurse,
                    ],
                ], 200);
            }

            return redirect()->route('backend.nurse.index')->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Nurse store error: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json(['message' => 'Error creating nurse: ' . $e->getMessage(), 'status' => false], 500);
            }

            return redirect()->back()->with('error', 'Error creating nurse: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = User::role(['nurse'])->where('id', $id)->with('nurse')->first();

        if (!is_null($data)) {
            $custom_field_data = $data->withCustomFields();
            $data['custom_field_data'] = collect($custom_field_data->custom_fields_data)
                ->filter(function ($value) {
                    return $value !== null;
                })
                ->toArray();
        }

        // Include state and city names for proper dropdown display
        if ($data->state) {
            $state = \Modules\World\Models\State::find($data->state);
            $data['state_name'] = $state ? $state->name : null;
        }
        
        if ($data->city) {
            $city = \Modules\World\Models\City::find($data->city);
            $data['city_name'] = $city ? $city->name : null;
        }

        $data['clinic_id'] = $data->nurse->clinic_id ?? null;
        $data['vendor_id'] = $data->nurse->vendor_id ?? null;

        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $data = User::role(['nurse'])->findOrFail($id);
            
            $request_data = $request->except(['profile_image', 'password', '_token', '_method']);

            // Handle custom state/city entries with duplicate prevention
            if (!empty($request_data['state'])) {
                $request_data['state'] = $this->getOrCreateState($request_data['state']);
            }
            if (!empty($request_data['city'])) {
                $request_data['city'] = $this->getOrCreateCity($request_data['city']);
            }

            if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')) {
                $request->vendor_id = $request->filled('vendor_id') ? $request->vendor_id : auth()->user()->id;
            } else {
                $request->vendor_id = optional($data->nurse)->vendor_id ?: auth()->user()->id;
            }

            $data->update($request_data);
            
            $nurse = Nurse::firstOrNew(['nurse_id' => $data->id]);
            $nurse->fill([
                'nurse_id' => $data->id,
                'clinic_id' => $request->clinic_id,
                'vendor_id' => $request->vendor_id,
            ]);
            $nurse->save();

            if ($request->hasFile('profile_image')) {
                storeMediaFile($data, $request->file('profile_image'), 'profile_image');
            }

            if ($request->has('remove_image') && $request->remove_image == '1') {
                $data->clearMediaCollection('profile_image');
            }

            $message = __('messages.update_form', ['form' => __('nurse.singular_title')]);

            return response()->json(['message' => $message, 'status' => true], 200);
        } catch (\Exception $e) {
            \Log::error('Nurse update error: ' . $e->getMessage());
            return response()->json(['message' => 'Error updating nurse: ' . $e->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = User::role('nurse')->findOrFail($id);
        $data->nurse()->forceDelete();
        $data->tokens()->delete();
        $data->forceDelete();
        
        $message = __('messages.delete_form', ['form' => __('nurse.singular_title')]);

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function change_password(Request $request)
    {
        $data = $request->all();
        $nurse_id = $data['nurse_id'];
        $user = User::role(['nurse'])->findOrFail($nurse_id);

        // Check old password
        if (!isset($data['old_password']) || !Hash::check($data['old_password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => __('nurse.old_password_incorrect'),
                'all_message' => ['old_password' => [__('nurse.old_password_incorrect')]]
            ], 422);
        }

        // Prevent same as old password
        if (Hash::check($data['password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => __('nurse.new_password_same_as_old'),
                'all_message' => ['password' => [__('nurse.new_password_same_as_old')]]
            ], 422);
        }

        // Update password
        $request_data = $request->only('password');
        $request_data['password'] = Hash::make($request_data['password']);
        $user->update($request_data);

        $message = __('nurse.password_update');

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function getStates(Request $request)
    {
        $countryId = $request->get('country_id');
        $states = State::where('country_id', $countryId)
            ->where('status', 1)
            ->get(['id', 'name']);
        
        return response()->json($states);
    }

    public function getCities(Request $request)
    {
        $stateId = $request->get('state_id');
        $cities = City::where('state_id', $stateId)
            ->where('status', 1)
            ->get(['id', 'name']);
        
        return response()->json($cities);
    }
    
    public function getClinicCenters(Request $request)
    {
        $clinics = Clinics::where('status', 1)->get(['id', 'name']);
        return response()->json($clinics);
    }
}