<?php

namespace Modules\Clinic\Http\Controllers;
use App\Authorizable;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Clinic\Models\DoctorSession;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Setting;
use Modules\Clinic\Models\Doctor;
use Modules\Clinic\Models\Clinics;
use Modules\Clinic\Models\DoctorClinicMapping;
use Modules\Clinic\Models\Receptionist;

class DoctorSessionController extends Controller
{
    protected string $exportClass = '\App\Exports\DoctorSessionExport';

    public function __construct()
    {
        // Page clinic_name
        $this->module_title = 'clinic.doctor_session';
        // module name
        $this->module_name = 'doctor-session';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }


    public function index_list(Request $request)
    {
        
        $clinic_id = $request->clinic_id;

        $doctor_id = $request->doctor_id;

        $data = DoctorSession::where('clinic_id', $clinic_id)->where('doctor_id', $doctor_id)->get();

        $clinicMappingData = [];

        if ($request->has('clinic_id') && !empty($clinic_id) && $request->has('doctor_id') && !empty($doctor_id)) {
            $clinicMappingData = DoctorClinicMapping::where('clinic_id', $clinic_id)
                ->where('doctor_id', $doctor_id)
                ->get();
        }
        
         
        return response()->json(['data' => $data, 'clinic_mapping_data' => $clinicMappingData,  'status' => true]);
    }

    public function session_list(Request $request)
    {
        $clinic_ids = $request->input('clinic_id');
        $clinicIds = explode(",", $clinic_ids);
        $doctor_id = $request->doctor_id;

        $clinics = Clinics::with(['doctorsessions' => function ($query) {
            $query->where('is_holiday', '!=', 1);
        }])->whereIn('id', $clinicIds)->get();
        
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('demo_admin')) {
            $clinics = Clinics::with(['doctorsessions' => function ($query) {
                $query->where('is_holiday', '!=', 1);
            }])->whereIn('id', $clinicIds)->get();
        } else if(auth()->user()->hasRole('vendor')){
          
            $clinics = Clinics::SetVendor()->with(['doctorsessions' => function ($query) {
                $query->where('is_holiday', '!=', 1);
            }])->whereIn('id', $clinicIds)->get();
        } else if(auth()->user()->hasRole('receptionist')){
            $clinics = Clinics::with(['doctorsessions' => function ($query) {
                $query->where('is_holiday', '!=', 1);
            }])->whereHas('receptionist', function ($qry) use ($clinicIds) {
                $qry->where('clinic_id', $clinicIds);
            })->get();
           
        } else if (auth()->user()->hasRole('doctor')) {
            $clinics = Clinics::with(['doctorsessions' => function ($query) {
                $query->where('is_holiday', '!=', 1);
            }])->whereIn('id', $clinicIds)->get();
        }


        $clinicSessions = $clinics->map(function ($clinic) {
            $days = [];
            foreach ($clinic->doctorsessions as $session) {
                $days[] = $session->day;
            }
            $days = array_values(array_unique($days));
            
            return [
                'clinic_id' => $clinic->id,
                'clinic_name' => $clinic->name,
                'avatar' => $clinic->file_url,
                'days' => $days,
            ];
        });
        return response()->json($clinicSessions);
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
        $create_title = 'clinic.doctor_title';
        $columns = CustomFieldGroup::columnJsonValues(new User());

        $customefield = CustomField::exportCustomFields(new User());
        $export_import = true;
        $export_columns = [
            [
                'value' => 'doctor_id',
                'text' => __('clinic.doctor_title'),
            ],
            [
                'value' => 'Clinic Name',
                'text' => __('clinic.singular_title'),
            ],
            [
                'value' => 'Working Day',
                'text' => __('customer.working_day'),
            ],
            [
                'value' => 'start_time',
                'text' => __('customer.start_time'),
            ],
            [
                'value' => 'end_time',
                'text' => __('customer.end_time'),
            ]
        ];
        $export_url = route('backend.doctor-session.export');

        return view('clinic::backend.doctor_session.index_datatable', compact('module_action','filter','create_title', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url'));


        // return view('clinic::backend.doctor_session.index_datatable', compact('module_action','filter','create_title'));
    }



    public function index_data(Datatables $datatable, Request $request)
    {
        $userId = auth()->id();
        $query = DoctorClinicMapping::SetRole(auth()->user())->with(['clinics','doctor']);
        $module_name = $this->module_name;

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
            ->addColumn('action', function ($query) {
                $data = User::role('doctor')->with('doctor', 'doctorsession')->where('id',$query->doctor_id)->first();
                $other_settings=Setting::where('name','is_provider_push_notification')->first();

                $enable_push_notification=0;

                if(!empty($other_settings)){

                    $enable_push_notification= $other_settings->val;

                }
                return view('clinic::backend.doctor_session.action_column', compact('data','enable_push_notification','query'));
            })
            ->editColumn('doctor_id', function ($data) {
                $doctorData = Doctor::with('user')->where('doctor_id',$data->doctor_id)->first();

                    if ($doctorData) {
                        
                        return view('clinic::backend.doctor_session.user_id', compact('doctorData'));
                    }
                    else{
                        return '';
                    }

            })

            ->filterColumn('doctor_id', function ($query, $keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('doctor_id', 'like', '%'.$keyword.'%')
                    ->orWhereHas('doctor', function($q) use ($keyword) {
                        $q->where('doctor_id', 'like', '%'.$keyword.'%')
                            ->orWhereHas('user', function($q) use ($keyword) {
                                $q->where('first_name', 'like', '%'.$keyword.'%')
                                ->orWhere('last_name', 'like', '%'.$keyword.'%');
                            });
                    });
                });
            })
            ->orderColumn('doctor_id', function ($query, $order) {
                $query->with(['doctor' => function($query) use ($order) {
                    $query->with(['user' => function($query) use ($order) {
                        $query->orderBy('first_name', $order);
                    }])->take(1);
                }]);
            }, 1)
            ->editColumn('clinic_id', function ($data) {
                    $clinic = $data->clinics;
                    if ($clinic) {
                        return $clinic ->name;
                    }else{
                        return '';
                    }

            })

            ->filterColumn('clinic_id', function ($query, $keyword) {
                $query->whereHas('clinics', function($q) use ($keyword) {
                    $q->where('name', 'like', '%'.$keyword.'%');
                });
            })
            ->orderColumn('clinic_id', function ($query, $order) {
                $query->whereHas('clinics', function($query) use ($order) {
                $query->orderByRaw("CONCAT(name) $order");
                   });
            }, 1)

            ->editColumn('day', function ($data) {
                $allDays = DoctorSession::where('doctor_id', $data->doctor_id)
                ->where('clinic_id', $data->clinic_id)
                ->where('is_holiday', '===','0')
                ->pluck('day')
                ->toArray();

                return implode(',', $allDays);
            })

            ->filterColumn('day', function ($query, $keyword) {
                $days = explode(',', $keyword);
                $days = DoctorSession::query();
                $query->orderBy('created_at', 'desc');
                $query->where(function ($subQuery) use ($days) {
                    foreach ($days as $day) {
                        $subQuery->orWhere('day', 'LIKE', '%' . trim($day) . '%');
                    }
                });

            })
            

           
            ->orderColumns(['id'], '-:column $1');
        // Custom Fields For export
        $customFieldColumns = CustomField::customFieldData($datatable, User::CUSTOM_FIELD_MODEL, null);

        return $datatable->rawColumns(array_merge(['action','status', 'is_banned', 'email_verified_at', 'check', 'image', 'user_type'], $customFieldColumns))
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
    public function store(Request $request)
    {
        $data = $request->all();
        
        $doctor_id = $data['doctor_id'];
        $clinic_id = $data['clinic_id'];
        $weekdays = $data['weekdays'];


        $doctorClinicMapping = DoctorClinicMapping::updateOrCreate(
            ['clinic_id' => $clinic_id, 'doctor_id' => $doctor_id],
        );

        // Delete existing sessions for the doctor in the specified clinic
            DoctorSession::where('clinic_id', $clinic_id)
            ->where('doctor_id', $doctor_id)
            ->delete();

        // Create new sessions
        foreach ($weekdays as $key => $value) {
            $value['clinic_id'] = $clinic_id;
            $value['doctor_id'] = $doctor_id;

            DoctorSession::create($value);
        }

        $message = __('clinic.doctor_session_added');

        return response()->json(['message' => $message, 'data' => $data,  'status' => true], 200);
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
        $module_action = 'Edit';
        $data = DoctorSession::where('doctor_id',$id)->get();
        return response()->json(['data' => $data, 'status' => true]);
    }

    public function EditSessionData(Request $request)
    {

        $module_action = 'Edit';
        $data = DoctorSession::where('doctor_id',$id)->get();
        return response()->json(['data' => $data, 'status' => true]);
    }

    

  
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $clinicId = $request->clinic_id;
        $weekdays = $data['weekdays'];
    
        if ($clinicId) {
            // If clinic_id is provided
            $doctorSessions = DoctorSession::where('doctor_id', $id)->where('clinic_id', $clinicId)->get();
        } else {
            // If clinic_id is not provided or is null
            $doctorSessions = DoctorSession::where('doctor_id', $id)->get();
        }
    
        foreach ($weekdays as $weekday) {
            foreach ($doctorSessions as $doctorSession) {
                $doctorId = $doctorSession->doctor_id;
                $day = $weekday['day'];
                
                $updateData = [
                    'start_time' => $weekday['start_time'],
                    'end_time' => $weekday['end_time'],
                    'is_holiday' => $weekday['is_holiday'],
                    'breaks' => $weekday['breaks']
                ];
    
                if ($clinicId) {
                    // Update or create with clinic_id
                    $doctorSession = DoctorSession::updateOrCreate(
                        ['doctor_id' => $doctorId, 'clinic_id' => $clinicId, 'day' => $day],
                        $updateData
                    );
                } else {
                    // Update or create without clinic_id
                    $doctorSession = DoctorSession::updateOrCreate(
                        ['doctor_id' => $doctorId, 'day' => $day],
                        $updateData
                    );
                }
            }
        }
    
        $message = __('messages.update_form', ['form' => __('clinic.doctor_session')]);
    
        return response()->json(['message' => $message, 'status' => true, 'data' => $doctorSession], 200);
    }    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $sessions = DoctorSession::where('doctor_id', $id)->get();

        foreach ($sessions as $session) {
            $session->delete();
        }
        if ($sessions->count() > 0) {
            $message = __('clinic.doctor_session_delete');
            return response()->json(['message' => $message, 'status' => true], 200);
        } else {
            $message = __('No sessions found for the specified doctor_id.');
            return response()->json(['message' => $message, 'status' => false], 404);
        }
    }

    public function EditDoctorMapping(Request $request)
    {

        $data = DoctorClinicMapping::where('id',$request->id)->first();
        return response()->json(['data' => $data, 'status' => true]);
    }

    // Controller method
    public function doctorClinics(Request $request)
    {
        $doctorId = $request->input('doctor_id');

        // Fetch clinic mapping
        $clinicMapping = DoctorClinicMapping::where('doctor_id', $doctorId)
            ->with(['clinics:id,name'])
            ->get()
            ->map(function ($m) {
                return [
                    'clinic_id'   => $m->clinic_id,
                    'clinic_name' => optional($m->clinics)->name ?? 'Unnamed Clinic',
                ];
            })
            ->values();

        // Fetch all sessions for this doctor, grouped by clinic_id and day
        $sessions = DoctorSession::where('doctor_id', $doctorId)->get();

        // Group sessions by clinic_id
        $sessionsByClinic = [];
        foreach ($sessions as $session) {
            $clinicId = $session->clinic_id;
            if (!isset($sessionsByClinic[$clinicId])) {
                $sessionsByClinic[$clinicId] = [];
            }
            $sessionsByClinic[$clinicId][] = [
                'id'         => $session->id,
                'day'        => $session->day,
                'start_time' => $session->start_time,
                'end_time'   => $session->end_time,
                'is_holiday' => $session->is_holiday,
                'breaks'     => is_string($session->breaks) ? json_decode($session->breaks, true) : $session->breaks,
                'clinic_id'  => $session->clinic_id,
                'doctor_id'  => $session->doctor_id,
            ];
        }

        return response()->json([
            'success' => true,
            'clinic_mapping_data' => $clinicMapping,
            'doctor_id' => $doctorId,
            'sessions' => $sessionsByClinic,
        ]);
    }

    public function getDoctorSessions($doctorId)
    {
        $sessions = DoctorSession::where('doctor_id', $doctorId)->get();

        $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
        $weekdays = [];

        foreach ($days as $day) {
            $session = $sessions->where('day', $day)->first();
            $breaks = [];

            if ($session) {
                if (is_string($session->breaks)) {
                    $breaks = json_decode($session->breaks, true) ?? [];
                } elseif (is_array($session->breaks)) {
                    $breaks = $session->breaks;
                }
            }

            $weekdays[] = [
                'day'        => $day,
                'start_time' => $session->start_time ?? '09:00',
                'end_time'   => $session->end_time ?? '18:00',
                'is_holiday' => $session->is_holiday ?? false,
                'breaks'     => $breaks,
                'clinic_id'  => $session->clinic_id ?? null,
                'id'         => $session->id ?? null,
            ];
        }

        return response()->json([
            'doctor_id' => $doctorId,
            'weekdays'  => $weekdays,
        ]);
    }




    

}   
