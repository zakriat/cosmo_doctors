<?php

namespace Modules\Appointment\Http\Controllers\Backend;
use Illuminate\Support\Facades\Response;
use App\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Modules\Appointment\Models\Appointment;
use Illuminate\Http\Request;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Yajra\DataTables\DataTables;
use Modules\Appointment\Models\PatientEncounter;
use Modules\Constant\Models\Constant;
use Modules\Appointment\Models\EncouterMedicalHistroy;
use Modules\Appointment\Models\EncounterPrescription;
use Modules\Appointment\Models\EncounterOtherDetails;
use Modules\Appointment\Models\EncounterMedicalReport;
use App\Mail\MedicalReportEmail;
use App\Mail\PrescriptionListMail;
use Illuminate\Support\Facades\Mail;
use Modules\Appointment\Models\EncounterTemplate;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PrescriptionImport;
use Auth;
use League\Csv\Reader;
use PDF;
use Modules\Appointment\Trait\EncounterTrait;
use App\Exports\EncounterPrescriptionExport;
use Modules\Appointment\Transformers\MedicalReportRescource;
use Modules\Appointment\Transformers\PrescriptionRescource;
use Modules\Appointment\Models\BillingRecord;
use Modules\Appointment\Models\BillingItem;
use Modules\CustomForm\Models\CustomForm;
use Modules\Clinic\Models\DoctorServiceMapping;
use Modules\Appointment\Models\TemplateMedicalHistory;
use Modules\Appointment\Models\TemplatePrescription;
use Modules\Appointment\Models\TemplateOtherDetails;

class PatientEncounterController extends Controller
{
    use EncounterTrait;

    protected string $exportClass = '\App\Exports\EncounterExport';

    //protected string $exportClassdata = '\App\Exports\EncounterPrescriptionExport';


    public function __construct()
    {
        // Page Title
        $this->module_title = 'appointment.encounter';
        // module name
        $this->module_name = 'encounter';

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
        $columns = CustomFieldGroup::columnJsonValues(new Appointment());
        $customefield = CustomField::exportCustomFields(new Appointment());
        $patients = User::role('user')->where('status', 1)->get();

        $export_import = true;
        $export_columns = [
            [
                'value' => 'user_id',
                'text' => __('appointment.lbl_patient_name'),
            ],
            [
                'value' => 'clinic_id',
                'text' => __('appointment.lbl_clinic'),
            ],
            [
                'value' => 'doctor_id',
                'text' => __('appointment.lbl_doctor'),
            ],

            [
                'value' => 'encounter_date',
                'text' => __('appointment.lbl_date'),
            ],

            [
                'value' => 'status',
                'text' => __('service.lbl_status'),
            ],

        ];
        $export_url = route('backend.encounter.export');

        return view('appointment::backend.patient_encounter.index_datatable', compact('module_action', 'filter', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url', 'patients'));
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {
        $query_data = PatientEncounter::SetRole(auth()->user())->with('appointment');

        $query_data = $query_data->where('status',1)->orderBy('appointment_id', 'desc')->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'text' => 'Encounters#' . $row->id,
                'clinic_id' => $row->clinic_id,
                'user_id' => $row->user_id,
                'doctor_id' => $row->doctor_id,
                'appointment_id' => $row->appointment_id,
                'service_id' => optional($row->appointment)->service_id,
                'date' => date('d-m-Y', strtotime(customDate($row->encounter_date))),
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
                $clinic = PatientEncounter::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('appointment.encounter_status');
                break;

            case 'delete':
                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                PatientEncounter::whereIn('id', $ids)->delete();
                $message = __('appointment.encounter_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('service_providers.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $query = PatientEncounter::SetRole(auth()->user());

        $customform = CustomForm::where('module_type', 'patient_encounter_module')
            ->where('status', 1)
            ->get();

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }

            if (isset($filter['patient_name'])) {
                $query->where('user_id', $filter['patient_name']);
            }
            if (isset($filter['clinic_name'])) {
                $query->where('clinic_id', $filter['clinic_name']);
            }
            if (isset($filter['doctor_name'])) {
                $query->where('doctor_id', $filter['doctor_name']);
            }
            // Add encounter_id filter if present
            if (isset($filter['encounter_id'])) {
                $query->where('id', $filter['encounter_id']);
            }
        }

        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $data->id . '"  name="datatable_ids[]" value="' . $data->id . '" onclick="dataTableRowCheck(' . $data->id . ')">';
            })
            ->addColumn('action', function ($data) use ($customform) {
                return view('appointment::backend.patient_encounter.datatable.action_column', compact('data', 'customform'));
            })

            ->editColumn('clinic_id', function ($data) {
                return view('appointment::backend.patient_encounter.clinic_id', compact('data'));
            })
            // Show encounter_id as "Encounter#ID" if encounter_id exists, else show '--'
            ->addColumn('encounter_id', function ($data) {
                if (!empty($data->id)) {
                    return '#' . $data->id;
                }
                return '--';
            })
            ->editColumn('appointment_id', function ($data) {
                if (!empty($data->appointment_id)) {
                    return '#' . $data->appointment_id;
                }
                return '--';
            })

            ->editColumn('user_id', function ($data) {
                return view('appointment::backend.clinic_appointment.user_id', compact('data'));
            })

            ->editColumn('encounter_date', function ($data) {
                return formatDate($data->encounter_date) ?? '--';
            })

            ->editColumn('doctor_id', function ($data) {
                return view('appointment::backend.clinic_appointment.doctor_id', compact('data'));
            })

            ->editColumn('status', function ($data) {
                return view('appointment::backend.patient_encounter.verify_action', compact('data'));
            })

            ->filterColumn('doctor_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('doctor', function ($query) use ($keyword) {
                        $query->where('first_name', 'like', '%' . $keyword . '%')
                            ->orWhere('last_name', 'like', '%' . $keyword . '%')
                            ->orWhere('email', 'like', '%' . $keyword . '%');
                    });
                }
            })

            ->filterColumn('user_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('user', function ($query) use ($keyword) {
                        $query->where('first_name', 'like', '%' . $keyword . '%')
                            ->orWhere('last_name', 'like', '%' . $keyword . '%')
                            ->orWhere('email', 'like', '%' . $keyword . '%');
                    });
                }
            })

            ->filterColumn('clinic_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('clinic', function ($query) use ($keyword) {
                        $query->where('name', 'like', '%' . $keyword . '%')
                            ->orWhere('email', 'like', '%' . $keyword . '%');
                    });
                }
            })

            // Add filterColumn for encounter_id (which is actually the "id" column)
            ->filterColumn('encounter_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    // Remove any leading "#" if present
                    $keyword = ltrim($keyword, '#');
                    $query->where('id', $keyword);
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
            // Order by appointment_id in descending order
            ->orderColumns(['appointment_id'], '-:column $1');

        // Custom Fields For export
        $customFieldColumns = CustomField::customFieldData($datatable, User::CUSTOM_FIELD_MODEL, null);

        // Add 'encounter_id' to rawColumns so it is not escaped
        return $datatable->rawColumns(array_merge(['action', 'status', 'is_banned', 'email_verified_at', 'check', 'image', 'encounter_id'], $customFieldColumns))
            ->toJson();
    }

    public function store(Request $request)
    {
        $data = $request->all();
// dd($data);   
        $data['vendor_id'] = isset($data['vendor_id']) ? $data['vendor_id'] : Auth::id();

        $encounter_details = PatientEncounter::create($data);
        $billing_record = [
            'encounter_id' => $encounter_details->id,
            'user_id' => $encounter_details->user_id,
            'clinic_id' => $encounter_details->clinic_id,
            'doctor_id' => $encounter_details->doctor_id,
        ];
        $billingrecord = BillingRecord::create($billing_record);

        if ($request->is('api/*')) {
            $message = __('appointment.save_encounter');
            return response()->json(['message' => $message, 'data' => $data, 'status' => true], 200);
        } else {
            $message = __('appointment.save_encounter');
            return response()->json(['message' => $message, 'data' => $data, 'status' => true], 200);
        }
    }

    public function edit($id)
    {
        $module_action = 'Edit';

        $data = PatientEncounter::findOrFail($id);

        return response()->json(['data' => $data, 'status' => true]);
    }


    public function update(Request $request, $id)
    {
        $data = PatientEncounter::findOrFail($id);

        $request_data = $request->all();

        $data->update($request_data);

        $message = __('messages.update_form', ['form' => __('appointment.patient_encounter')]);

        if ($request->is('api/*')) {
            return response()->json(['message' => $message, 'data' => $data, 'status' => true], 200);
        } else {
            return response()->json(['message' => $message, 'status' => true], 200);
        }
    }

    public function encounterDetail($id)
    {

        $data = PatientEncounter::where('id', $id)->with('user', 'user.cities', 'user.countries', 'clinic', 'doctor', 'medicalHistroy', 'prescriptions', 'EncounterOtherDetails', 'medicalReport', 'appointmentdetail', 'billingrecord')->first();

        $data['selectedProblemList'] = $data->medicalHistroy()->where('type', 'encounter_problem')->get();
        $data['selectedObservationList'] = $data->medicalHistroy()->where('type', 'encounter_observations')->get();
        $data['notesList'] = $data->medicalHistroy()->where('type', 'encounter_notes')->get();
        $data['prescriptions'] = $data->prescriptions()->get();
        $data['other_details'] = $data->EncounterOtherDetails()->value('other_details') ?? null;
        $data['medicalReport'] = $data->medicalReport()->get();
        $data['signature'] = optional(optional($data->doctor)->doctor)->Signature ?? null;
        $data['appointment_status'] = $data->appointmentdetail->status ?? null;
        $data['payment_status'] = $data->appointmentdetail->appointmenttransaction->payment_status ?? null;
        $data['billingrecord'] = $data->billingrecord ?? null;
        $data['billingrecord_payment'] = $data->billingrecord->payment_status ?? null;
        $data['encounter_date'] = formatDate($data['encounter_date']);
        $data['customform'] = CustomForm::where('module_type', 'appointment_module')
            ->where('status', 1)
            ->get()
            ->filter(function ($item) {
                $showInArray = json_decode($item->show_in, true);
                return in_array('encounter', $showInArray);
            });

        return response()->json(['data' => $data, 'status' => true]);

    }

    public function saveEncouterDetails(Request $request)
    {

        $encounter = PatientEncounter::where('id', $request->encounter_id)->first();

        $user_id = $request->user_id;
        $encounter_id = $request->encounter_id;

        if ($encounter) {

            if ($request->filled('template_id') && $request->template_id != null) {

                $encounter->update(['encounter_template_id' => $request->template_id]);
            }

            if ($request->filled('other_details') && $request->other_details != null) {

                $other_details = [

                    'encounter_id' => $encounter_id,
                    'user_id' => $user_id,
                    'other_details' => $request->other_details

                ];

                EncounterOtherDetails::updateOrCreate(
                    ['encounter_id' => $encounter_id, 'user_id' => $user_id],
                    $other_details
                );

            }


            EncouterMedicalHistroy::where('encounter_id', $encounter_id)->where('user_id', $user_id)->forceDelete();

            if ($request->filled('notesList') && $request->notesList != null) {

                foreach ($request->notesList as $notes) {

                    $notes_details = [

                        'encounter_id' => $encounter_id,
                        'user_id' => $user_id,
                        'type' => 'encounter_notes',
                        'title' => $notes['title'],

                    ];

                    EncouterMedicalHistroy::create($notes_details);

                }
            }

            if ($request->filled('selectedObservationList') && $request->selectedObservationList != null) {

                foreach ($request->selectedObservationList as $observation) {

                    $observation_details = [

                        'encounter_id' => $encounter_id,
                        'user_id' => $user_id,
                        'type' => 'encounter_observations',
                        'title' => $observation['title'],

                    ];

                    EncouterMedicalHistroy::create($observation_details);

                }
            }

            if ($request->filled('selectedproblemList') && $request->selectedproblemList != null) {

                foreach ($request->selectedproblemList as $problem) {

                    $problem_details = [

                        'encounter_id' => $encounter_id,
                        'user_id' => $user_id,
                        'type' => 'encounter_problem',
                        'title' => $problem['title'],

                    ];

                    EncouterMedicalHistroy::create($problem_details);

                }
            }



            EncounterPrescription::where('encounter_id', $encounter_id)->where('user_id', $user_id)->forceDelete();

            if ($request->filled('prescriptionList') && $request->prescriptionList != null) {

                foreach ($request->prescriptionList as $prescription) {

                    $prescription = [

                        'encounter_id' => $encounter_id,
                        'user_id' => $user_id,
                        'name' => $prescription['name'],
                        'frequency' => $prescription['frequency'],
                        'duration' => $prescription['duration'],
                        'instruction' => $prescription['instruction'],

                    ];

                    EncounterPrescription::create($prescription);

                }
            }

            $message = __('appointment.encounter_detail_save');

            return response()->json(['message' => $message, 'status' => true], 200);

        }


    }


    public function saveEncouter(Request $request)
{
    $encounter = PatientEncounter::where('id', $request->encounter_id)->first();
    $user_id = $request->user_id;
    $encounter_id = $request->encounter_id;

    if ($encounter) {
        if ($request->filled('template_id') && $request->template_id != null) {
            $encounter->update(['encounter_template_id' => $request->template_id]);
        }

        if ($request->filled('other_details') && $request->other_details != null) {
            $other_details = [
                'encounter_id' => $encounter_id,
                'user_id' => $user_id,
                'other_details' => $request->other_details
            ];

            EncounterOtherDetails::updateOrCreate(
                ['encounter_id' => $encounter_id, 'user_id' => $user_id],
                $other_details
            );
        }


        if ($request->filled('template_id') && $request->template_id != null) {
            $templateId = $request->template_id;

             EncouterMedicalHistroy::where('encounter_id', $encounter_id)
            ->where('user_id', $user_id)
            ->forceDelete();


            $selectedEncouterMedicalHistroy = TemplateMedicalHistory::where('template_id', $templateId)->get();


            $selectedTemplatePrescription = TemplatePrescription::where('template_id', $templateId)->get();
            $selectedTemplateOtherDetails = TemplateOtherDetails::where('template_id', $templateId)->get();

            foreach ($selectedEncouterMedicalHistroy as $medicalHistory) {
                if ($medicalHistory->type === 'encounter_problem') {
                    EncouterMedicalHistroy::create([
                        'encounter_id' => $encounter_id,
                        'user_id' => $user_id,
                        'type' => 'encounter_problem',
                        'title' => $medicalHistory->title,
                    ]);
                }

                if ($medicalHistory->type === 'encounter_observations') {
                    EncouterMedicalHistroy::create([
                        'encounter_id' => $encounter_id,
                        'user_id' => $user_id,
                        'type' => 'encounter_observations',
                        'title' => $medicalHistory->title,
                    ]);
                }

                if ($medicalHistory->type === 'encounter_notes') {
                    EncouterMedicalHistroy::create([
                        'encounter_id' => $encounter_id,
                        'user_id' => $user_id,
                        'type' => 'encounter_notes',
                        'title' => $medicalHistory->title,
                    ]);
                }
            }

            EncounterPrescription::where('encounter_id', $encounter_id)
                ->where('user_id', $user_id)
                ->forceDelete();

            foreach ($selectedTemplatePrescription as $prescription) {
                EncounterPrescription::create([
                    'encounter_id' => $encounter_id,
                    'user_id' => $user_id,
                    'name' => $prescription->name,
                    'frequency' => $prescription->frequency,
                    'duration' => $prescription->duration,
                    'instruction' => $prescription->instruction,
                ]);
            }

            foreach ($selectedTemplateOtherDetails as $detail) {
                EncounterOtherDetails::updateOrCreate(
                    ['encounter_id' => $encounter_id, 'user_id' => $user_id],
                    ['other_details' => $detail->other_details]
                );
            }
        }

        $message = __('appointment.encounter_detail_save');
        return response()->json(['message' => $message, 'status' => true], 200);
    }

    return response()->json(['message' => __('appointment.encounter_not_found'), 'status' => false], 404);
}


    public function saveSelectOption(Request $request)
    {

        if ($request->type == 'encounter_problem' || $request->type == 'encounter_observations') {

            $data=[

                'name'=>$request->name,
                'type'=>$request->type,
                'value'=>str_replace(' ', '_', strtolower($request->name)),
            ];

            $constant = Constant::updateOrCreate(
                ['value' => $data['value'], 'type' => $data['type']],
                $data
            );

        }

        //    $query=Constant::create($data);

        // /return response()->json(['data' => $constant_data, 'last_selected_id'=>$query->id,'status'=>true]);

        $medical_histroy = [

            'encounter_id' => $request->encounter_id,
            'user_id' => $request->user_id,
            'type' => $request->type,
            'title' => $request->name,
        ];


      $encounter_detail = EncouterMedicalHistroy::updateOrCreate(
        [
            'title' => $request->name,
            'user_id' => $request->user_id,
            'type' => $request->type,
            'encounter_id' => $request->encounter_id,
        ],
        $medical_histroy
    );

        $constant_data = Constant::where('type', $request->type)->get();

        $medical_histroy = EncouterMedicalHistroy::where('encounter_id', $encounter_detail->encounter_id)->where('type', $encounter_detail->type)->get();

        return response()->json(['data' => $constant_data, 'medical_histroy' => $medical_histroy, 'status' => true]);

    }

    public function removeHistroyData(Request $request)
    {

        $id = $request->id;

        if ($id) {

            $encounter_details = EncouterMedicalHistroy::where('id', $id)->first();

            $encounter_id = $encounter_details->encounter_id;

            $encounter_details->forceDelete();

            $medical_histroy = EncouterMedicalHistroy::where('encounter_id', $encounter_id)->where('type', $request->type)->get();

            return response()->json(['medical_histroy' => $medical_histroy, 'status' => true]);

        }

    }

    public function destroy($id)
    {
        $data = PatientEncounter::findOrFail($id);

        $data->delete();

        $message = __('appointment.encounter_delete_successfully');

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function savePrescription(Request $request)
    {

        $request_data = $request->all();

        $data = [

            'name' => $request->name,
            'type' => $request->type,
            'value' => str_replace(' ', '_', strtolower($request->name)),
        ];


        // $constant = Constant::updateOrCreate(
        //     ['value' => $data['value'], 'type' => $data['type']],
        //     $data
        // );

        $prescription = EncounterPrescription::create($request_data);

        if ($request->is('api/*')) {

            $message = __('appointment.encounter_prescription_save');
            return response()->json(['message' => $message, 'data' => new PrescriptionRescource($prescription), 'status' => true], 200);

        } else {

            $data = PatientEncounter::where('id',$request_data['encounter_id'])->with('user','prescriptions')->first();

            if(!empty($data)){

                $html = view('appointment::backend.patient_encounter.component.prescription_table', ['data' => $data])->render();
              }

               return response()->json(['html' => $html]);

        }


    }

    public function editPrescription($id)
    {

        $prescription = EncounterPrescription::where('id', $id)->first();

        return response()->json(['data' => $prescription, 'status' => true]);

    }

    public function updatePrescription(Request $request, $id)
    {

        $data = $request->all();

        $prescription = EncounterPrescription::where('id', $id)->first();

        $data_value = [

            'name' => $request->name,
            'type' => $request->type,
            'value' => str_replace(' ', '_', strtolower($request->name)),
        ];


        $constant = Constant::updateOrCreate(
            ['value' => $data_value['value'], 'type' => $data_value['type']],
            $data_value
        );

        $prescription->update($data);

        if ($request->is('api/*')) {

            $message = __('appointment.encounter_prescription_update');
            return response()->json(['message' => $message, 'data' => new PrescriptionRescource($prescription), 'status' => true], 200);

        } else {

            $data = PatientEncounter::where('id',$data['encounter_id'])->with('user','prescriptions')->first();

            if(!empty($data)){

                $html = view('appointment::backend.patient_encounter.component.prescription_table', ['data' => $data])->render();
              }

               return response()->json(['html' => $html]);

        }


    }

    public function deletePrescription(Request $request, $id)
    {

        $prescription = EncounterPrescription::where('id', $id)->first();

        $encounter_id = $prescription->encounter_id;

        $prescription->forceDelete();

        if ($request->is('api/*')) {

            $message = __('appointment.prescription_delete');

            return response()->json(['message' => $message, 'status' => true], 200);

        } else {

            $data = PatientEncounter::where('id',$encounter_id)->with('user','prescriptions')->first();

            if(!empty($data)){

                $html = view('appointment::backend.patient_encounter.component.prescription_table', ['data' => $data])->render();
              }

               return response()->json(['html' => $html]);
        }


    }


    public function saveOtherDetails(Request $request)
    {

        $request_data = $request->all();

        $data = [

            'other_details' => $request->other_details,
            'encounter_id' => $request->encounter_id,
            'user_id' => $request->user_id,
        ];

        $otherDetails = EncounterOtherDetails::updateOrCreate(
            ['encounter_id' => $data['encounter_id'], 'user_id' => $data['user_id']],
            $data
        );


        return response()->json(['otherDetails' => $otherDetails, 'status' => true]);

    }

    public function saveMedicalReport(Request $request)
    {

        $data = $request->all();

        $html='';

        $medical_report=EncounterMedicalReport::create($data);

        if ($request->hasFile('file_url')) {
            storeMediaFile($medical_report, $request->file('file_url'));
        }

        if ($request->is('api/*')) {

            $medical_report = new MedicalReportRescource($medical_report);

            $message = __('appointment.medical_report_save');
            return response()->json(['message' => $message, 'data' => $medical_report, 'status' => true], 200);
        } else {


            $data = PatientEncounter::where('id',$data['encounter_id'])->with('user','medicalReport')->first();

            if(!empty($data)){

                $html = view('appointment::backend.patient_encounter.component.medical_report_table', ['data' => $data])->render();
              }

               return response()->json(['html' => $html]);

            }


    }

    public function editMedicalReport(Request $request, $id)
    {
        
        $medical_report=EncounterMedicalReport::where('id',$id)->first();


        return response()->json(['data' => $medical_report, 'status' => true]);


    }

    public function updateMedicalReport(Request $request, $id)
    {

        $data = $request->all();

        $html='';


        $medical_report=EncounterMedicalReport::where('id',$id)->first();

        $medical_report->update($data);


        if ($request->hasFile('file_url')) {

            storeMediaFile($medical_report, $request->file('file_url'));
        }


        if ($request->is('api/*')) {

            $medical_report = new MedicalReportRescource($medical_report);

            $message = __('appointment.medical_report_update');

            return response()->json(['message' => $message, 'data' => $medical_report, 'status' => true], 200);

        } else {

            $data = PatientEncounter::where('id',$data['encounter_id'])->with('user','medicalReport')->first();

            if(!empty($data)){

                $html = view('appointment::backend.patient_encounter.component.medical_report_table', ['data' => $data])->render();
              }

               return response()->json(['html' => $html]);

            }


    }


    public function deleteMedicalReport(Request $request, $id)
    {

         $medical_report=EncounterMedicalReport::where('id',$id)->first();

         $encounter_id= $medical_report->encounter_id;
         $html='';


        $medical_report->forceDelete();

        if ($request->is('api/*')) {

            $message = __('appointment.medical_report_delete');

            return response()->json(['message' => $message, 'status' => true], 200);

        } else {

            $data = PatientEncounter::where('id',$encounter_id)->with('user','medicalReport')->first();

            if(!empty($data)){

                $html = view('appointment::backend.patient_encounter.component.medical_report_table', ['data' => $data])->render();
              }

               return response()->json(['html' => $html]);

        }



      }



    public function GetReportData(Request $request)
    {

        $encounter_id = $request->encounter_id;

        $medical_report = EncounterMedicalReport::where('encounter_id', $encounter_id)->get();

        return response()->json(['medical_report' => $medical_report, 'status' => true]);

    }


    public function SendMedicalReport(Request $request)
    {

        $encounter_id=$request->id;

        $data = PatientEncounter::where('id',$encounter_id)->first();

        $user_id= $data->user_id;

        $user=User::where('id',$user_id)->first();

        $medicalReport = EncounterMedicalReport::where('id', $data['report_id'])->first();


        if ($user && $medicalReport) {

            $filePath = $medicalReport->file_url;

                Mail::to($user->email)->send(new MedicalReportEmail($medicalReport, $filePath));
                $message = __('appointment.medical_report_send');
                return response()->json(['message'=> $message,'status'=>true]);

           } else {
            $message  = __('appointment.something_wrong');
            return response()->json(['message'=> $message,'status'=>false]);
        }

    }

    public function sendPrescription(Request $request)
    {


        $encounter_id=$request->id;

        $data = PatientEncounter::where('id',$encounter_id)->first();

        $user_id= $data->user_id;

        $user = User::where('id', $user_id)->first();

        $prescriptionList = EncounterPrescription::where('encounter_id', $encounter_id)->get();


        if ($user && $prescriptionList) {

                Mail::to($user->email)->send(new PrescriptionListMail($prescriptionList));
                $message = __('appointment.prescription_send');
                return response()->json(['message'=> $message,'status'=>true]);

           } else {
            $message  = __('appointment.something_wrong');
            return response()->json(['message'=> $message ,'status'=>false]);
        }

    }




    public function importPrescription(Request $request)
    {
         $file = $request->file('file_url');

         if (!$file->isValid()) {
             return response()->json(['error' => 'Invalid file', 'status' => false]);
         }

         if (!in_array($file->getClientOriginalExtension(), ['csv', 'xlsx'])) {
             return response()->json(['error' => 'Invalid file type', 'status' => false], 400);
         }

         if ($file->getClientOriginalExtension() === 'csv') {
             $records = $this->importCsv($file, $request->user_id, $request->encounter_id);
         } elseif ($file->getClientOriginalExtension() === 'xlsx') {

            Excel::import(new PrescriptionImport($request->user_id, $request->encounter_id), $file);

        }

         $prescription=EncounterPrescription::where('encounter_id',$request->encounter_id)->get();

         return response()->json(['prescription'=>$prescription,'status'=>true]);
    }

    public function exportPrescriptionData(Request $request)
    {

        $data = $request->all();

        $type = $data['type'];

        $encounter_id = $data['encounter_id'];

        $prescriptionList = EncounterPrescription::where('encounter_id', $encounter_id)->get(['id', 'name', 'frequency', 'duration', 'instruction']);

        switch ($type) {
            case 'pdf':

                $pdf = PDF::loadView('pdf.prescripcription', ['prescriptionList' => $prescriptionList]);
                $pdf->setPaper('A4', 'landscape');

                   return $pdf->download('prescripcription.pdf');

                 break;
            case 'csv':

                $csvContent = "ID,Name,Frequency,Duration,Instruction\n";
                $csvContent .= "2,test1,34,1,efergr\n";

                $headers = [
                    'Content-Disposition' => 'attachment; filename="test.csv"',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ];


                return Response::make($csvContent, 200, $headers);


        return Response::make($csvContent, 200, $headers);

                break;
            case 'xlsx':
                return $this->exportXLSX($encounter_id);
                break;
            default:
                return redirect()->back()->with('error', 'Unsupported file format.');
        }

    }


       protected function importCsv($file, $user_id, $encounter_id)
       {
           $filePath = $file->getRealPath();
           $csv = Reader::createFromPath($filePath, 'r');
           $csv->setHeaderOffset(0);
           $records = [];

           foreach ($csv->getRecords() as $record) {
               $file_record = [
                   'user_id' => $user_id,
                   'encounter_id' => $encounter_id,
                   'name' => $record['name'],
                   'frequency' => $record['frequency'],
                   'duration' => $record['duration'],
                   'instruction' => $record['instruction'],
               ];

               EncounterPrescription::create($file_record);

               $constant_record=[

                   'type'=>'encounter_prescription',
                   'name'=> $record['name'],
                   'value'=>str_replace(' ', '_',$record['name']),

                ];

                $constant = Constant::updateOrCreate(
                  ['value' => $constant_record['value'], 'type' => $constant_record['type']],
                  $constant_record
              );

               $records[] = $file_record;
           }

           return $records;
       }



       public function EncouterDetailPage(Request $request , $id){

        $module_title ="Encounter Dashboard";

        $data = PatientEncounter::where('id',$id)->with('user','user.cities','user.countries','clinic','doctor','medicalHistroy','prescriptions','EncounterOtherDetails','medicalReport','appointmentdetail.clinicservice.systemservice','billingrecord')->first();
        $data['selectedProblemList'] =  $data->medicalHistroy()->where('type','encounter_problem')->get();
        $data['selectedObservationList'] = $data->medicalHistroy()->where('type', 'encounter_observations')->get();
        $data['notesList'] = $data->medicalHistroy()->where('type', 'encounter_notes')->get();
        $data['prescriptions'] = $data->prescriptions()->get();
        $data['other_details'] = $data->EncounterOtherDetails()->value('other_details') ?? null;
        $data['medicalReport'] = $data->medicalReport()->get();
        $data['signature'] = optional(optional($data->doctor)->doctor)->Signature ?? null;
        $data['appointment_status'] = $data->appointmentdetail->status ?? null;
        $data['payment_status'] = $data->appointmentdetail->appointmenttransaction->payment_status ?? null;
        $data['billingrecord']=$data->billingrecord ?? null;
        $data['billingrecord_payment']=$data->billingrecord->payment_status ?? null;
        $data['customform'] = CustomForm::where('module_type', 'appointment_module')
        ->where('status', 1)
        ->get()
        ->filter(function($item) {
            $showInArray = json_decode($item->show_in, true);
            return in_array('encounter', $showInArray);
        });

        $template_data = EncounterTemplate::with('templateMedicalHistroy','templatePrescriptions','TemplateOtherDetails')->Where('status', 1)->get();

        $encounter_data=encounter();

        $problem_list= Constant::where('type', 'encounter_problem')->get();
        $observation_list= Constant::where('type', 'encounter_observations')->get();
        $prescription_list = EncounterPrescription::all()->map(function ($item) {
            return [
                'value' => $item->id,
                'label' => $item->name,
            ];
        })->toArray();

        return view('appointment::backend.patient_encounter.encounter_detail_page', compact('module_title','data','template_data','encounter_data','problem_list','observation_list','prescription_list'));

       }
       public function getTemplateData($templateId, Request $request)
       {
        $selectedEncouterMedicalHistroy = TemplateMedicalHistory::where('template_id', $templateId)->get();
        $selectedTemplatePrescription = TemplatePrescription::where('template_id', $templateId)->get();
        $selectedTemplateOtherDetails = TemplateOtherDetails::where('template_id', $templateId)->get();
        $problem_list= Constant::where('type', 'encounter_problem')->get();
        $observation_list= Constant::where('type', 'encounter_observations')->get();

        if ($selectedEncouterMedicalHistroy->isNotEmpty() || $selectedTemplatePrescription->isNotEmpty()) {
            $problemHtml = '';
            $observationHtml = '';
            $noteHtml = '';
            $PrescriptionHtml = '';
            $otherdetailHtml = '';
            // Iterate through the collection
            foreach ($selectedEncouterMedicalHistroy as $medicalHistory) {
                if ($medicalHistory->type === 'encounter_problem') {
                    // Generate problem HTML using a Blade view
                    $problemHtml = view('appointment::backend.patient_encounter.component.encounter_problem', [
                        'data' => [
                            'id' => $request->encounter_id ?? '',
                            'user_id' => $request->user_id ?? '',
                            'status' => $request->status ?? '0',
                            'selectedProblemList' => $selectedEncouterMedicalHistroy->where('type', 'encounter_problem'),
                        ],
                        'problem_list' => $problem_list,
                    ])->render();
                }

                if ($medicalHistory->type === 'encounter_observations') {
                    // Generate observation HTML using a Blade view

                    $observationHtml = view('appointment::backend.patient_encounter.component.encounter_observation', [
                        'data' => [
                            'id' => $request->encounter_id ?? '',
                            'user_id' => $request->user_id ?? '',
                            'status' => $request->status ?? '0',
                            'selectedObservationList' => $selectedEncouterMedicalHistroy->where('type', 'encounter_observations'),
                        ],
                        'observation_list' => $observation_list,
                    ])->render();


                }

                if ($medicalHistory->type === 'encounter_notes') {
                    // Generate note HTML using a Blade view
                    $noteHtml = view('appointment::backend.patient_encounter.component.encounter_note', [
                        'data' => [
                            'id' => $request->encounter_id ?? '',
                            'user_id' => $request->user_id ?? '',
                            'status' => $request->status ?? '0',
                            'notesList' => $selectedEncouterMedicalHistroy->where('type', 'encounter_notes'),
                        ],

                    ])->render();
                }
            }

            foreach ($selectedTemplatePrescription as $TemplatePrescription) {
                if ($TemplatePrescription !== null) {
                    // Generate problem HTML using a Blade view
                    $PrescriptionHtml = view('appointment::backend.patient_encounter.component.prescription_table', [
                        'data' => [
                            'id' => $request->encounter_id ?? '',
                            'user_id' => $request->user_id ?? '',
                            'status' => $request->status ?? '0',
                            'prescriptions' => $selectedTemplatePrescription,
                        ],

                    ])->render();
                }


            }

            foreach ($selectedTemplateOtherDetails as $detail) {
                $otherdetailHtml = $detail->other_details ;
            }





            // Return response as JSON
            return response()->json([
                'is_encounter_problem' => !empty($problemHtml),
                'problem_html' => $problemHtml,
                'is_encounter_observation' => !empty($observationHtml),
                'observation_html' => $observationHtml,
                'is_encounter_note' => !empty($noteHtml),
                'note_html' => $noteHtml,
                'is_encounter_precreption' => !empty($PrescriptionHtml),
                'precreption_html' => $PrescriptionHtml,
                'is_encounter_otherdetail' => !empty($otherdetailHtml),
                'other_detail_html' => $otherdetailHtml,
            ]);
        }  else {
            return response()->json([
                'is_encounter_problem' => false,
                'problem_html' => '',
                'is_encounter_observation' => false,
                'observation_html' => '',
                'is_encounter_note' => false,
                'note_html' => '',
            ]);
        }


     }

    /**
     * Close encounter directly without billing modal
     */
    public function closeEncounterDirect(Request $request)
    {
        try {
            $encounterId = $request->encounter_id;
            
            // Find the encounter
            $encounter = PatientEncounter::find($encounterId);
            
            if (!$encounter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Encounter not found'
                ], 404);
            }
            
            // Check if encounter is already closed
            if ($encounter->status == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Encounter is already closed'
                ], 400);
            }
            
            // Close the encounter
            $encounter->update(['status' => 0]);
            
            // Update billing record if exists
            $billingRecord = BillingRecord::where('encounter_id', $encounterId)->first();
            if ($billingRecord) {
                $billingRecord->update(['payment_status' => 1]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Encounter closed successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while closing the encounter: ' . $e->getMessage()
            ], 500);
        }
    }

}
