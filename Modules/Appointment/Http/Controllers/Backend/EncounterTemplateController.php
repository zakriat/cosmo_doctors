<?php

namespace Modules\Appointment\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\CustomField\Models\CustomField;
use Modules\CustomField\Models\CustomFieldGroup;
use Yajra\DataTables\DataTables;
use Modules\Appointment\Models\EncounterTemplate;
use Modules\Appointment\Models\TemplateMedicalHistory;
use Modules\Appointment\Models\TemplatePrescription;
use Modules\Appointment\Models\TemplateOtherDetails;
use Carbon\Carbon;
use Modules\Appointment\Models\EncounterPrescription;
use Modules\Constant\Models\Constant;

class EncounterTemplateController extends Controller
{
    protected string $exportClass = '\App\Exports\EncounterTemplateExport';

    public function __construct()
    {
        // Page Title
        $this->module_title = 'sidebar.encounter_template';
        // module name
        $this->module_name = 'encounter-template';

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
        $columns = CustomFieldGroup::columnJsonValues(new EncounterTemplate());
        $customefield = CustomField::exportCustomFields(new EncounterTemplate());
        $encounter_templates = EncounterTemplate::where('status', 1)->get();

        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => __('appointment.template_name'),
            ],
            [
                'value' => 'status',
                'text' => __('appointment.lbl_status'),
            ]
        ];
        $export_url = route('backend.encounter-template.export');

        return view('appointment::backend.encounter_template.index_datatable', compact('module_action', 'filter', 'columns', 'customefield', 'export_import', 'export_columns', 'export_url', 'encounter_templates'));
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        switch ($actionType) {
            case 'change-status':
                $clinic = EncounterTemplate::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('appointment.encounter_template_status');
                break;

            case 'delete':
                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                EncounterTemplate::whereIn('id', $ids)->delete();
                $message = __('appointment.encounter_template_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('service_providers.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $query = EncounterTemplate::query();

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
                return view('appointment::backend.encounter_template.action_column', compact('data'));
            })

            ->editColumn('status', function ($row) {
                $checked = '';
                if ($row->status) {
                    $checked = 'checked="checked"';
                }

                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="' . route('backend.encounter-template.update_status', $row->id) . '" data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  id="datatable-row-' . $row->id . '"  name="status" value="' . $row->id . '" ' . $checked . '>
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
        $customFieldColumns = CustomField::customFieldData($datatable, EncounterTemplate::CUSTOM_FIELD_MODEL, null);

        return $datatable->rawColumns(array_merge(['action', 'status', 'check'], $customFieldColumns))
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('appointment::create');
    }

    public function index_list(Request $request)
    {
        $term = trim($request->q);

        $query_data = EncounterTemplate::Where('status', 1)->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'name' => $row->name,
            ];
        }

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        EncounterTemplate::create($data);

        if ($request->is('api/*')) {
            $message = __('appointment.enconter_template_save');
            return response()->json(['message' => $message, 'data' => $data, 'status' => true], 200);
        } else {
            $message = __('appointment.enconter_template_save');
            return response()->json(['message' => $message, 'data' => $data, 'status' => true], 200);
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
        return view('appointment::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = EncounterTemplate::findOrFail($id);

        $data->delete();

        $message = __('appointment.encounter_template_delete');

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function updateStatus($id, Request $request)
    {
        $data = EncounterTemplate::where('id', $id)->first();

        $status = $request->status;

        if (isset($request->action_type) && $request->action_type == 'update-status') {

            $status = $request->value;
        }
        $data->status = $status;
        $data->update();

        $message = __('appointment.status_update');

        return response()->json(['message' => $message, 'status' => true]);
    }

    public function templateDetail($id)
    {

        $data = EncounterTemplate::where('id', $id)->with('templateMedicalHistroy', 'templatePrescriptions', 'TemplateOtherDetails')->first();
        $medical_histroy = TemplateMedicalHistory::where('template_id', 1)->where('type', 'encounter_observations')->get();

        $data['selectedProblemList'] = $data->templateMedicalHistroy()->where('type', 'encounter_problem')->get();
        $data['selectedObservationList'] = $medical_histroy;
        $data['notesList'] = $data->templateMedicalHistroy()->where('type', 'encounter_notes')->get();
        $data['prescriptions'] = $data->templatePrescriptions()->get();
        $data['other_details'] = $data->TemplateOtherDetails()->value('other_details') ?? null;
        $problem_list = Constant::where('type', 'encounter_problem')->get();
        $observation_list = Constant::where('type', 'encounter_observations')->get();
        $prescription_list = EncounterPrescription::all()->map(function ($item) {
            return [
                'value' => $item->id,
                'label' => $item->name,
            ];
        })->toArray();

        return view('appointment::backend.encounter_template.template_detail', compact('data', 'problem_list', 'observation_list', 'prescription_list'));

        // return response()->json(['data' => $data, 'status' => true]);

    }

    public function saveTemplateHistroy(Request $request)
    {

        if ($request->type == 'encounter_problem' || $request->type == 'encounter_observations') {

            $data = [

                'name' => $request->name,
                'type' => $request->type,
                'value' => str_replace(' ', '_', strtolower($request->name)),
            ];

            $constant = Constant::updateOrCreate(
                ['value' => $data['value'], 'type' => $data['type']],
                $data
            );
        }

        $medical_histroy = TemplateMedicalHistory::firstOrCreate(
            [
                'template_id' => $request->template_id,
                'type' => $request->type,
                'title' => $request->name,
            ],
            [
                'template_id' => $request->template_id,
                'type' => $request->type,
                'title' => $request->name,
            ]
        );

        $constant_data = Constant::where('type', $request->type)->get();

        $medical_histroy = TemplateMedicalHistory::where('template_id', $request->template_id)
        ->where('type', $request->type)->get();

        return response()->json(['data' => $constant_data, 'medical_histroy' => $medical_histroy, 'status' => true]);
    }


    public function removeTemplateHistroy(Request $request)
    {

        $id = $request->id;

        if ($id) {

            $template_detail = TemplateMedicalHistory::where('id', $id)->first();

            $template_id = $template_detail->template_id;

            $template_detail->forceDelete();

            $medical_histroy = TemplateMedicalHistory::where('template_id', $template_id)->where('type', $request->type)->get();

            return response()->json(['medical_histroy' => $medical_histroy, 'status' => true]);

        }

    }

    public function savePrescription(Request $request)
    {

        $request_data = $request->all();


        $data = [

            'name' => $request->name,
            'type' => $request->type,
            'value' => str_replace(' ', '_', strtolower($request->name)),
        ];

        $constant = Constant::updateOrCreate(

            ['value' => $data['value'], 'type' => $data['type']],

            $data
        );

        $templateDetail = TemplatePrescription::create($request_data);

        $prescription = TemplatePrescription::where('template_id', $templateDetail->template_id)->get();

        $data = EncounterTemplate::where('id', $templateDetail->template_id)->with('templatePrescriptions')->first();
        $data['prescriptions'] = $data->templatePrescriptions()->get();

        return response()->json([
            'prescription' => $prescription,
            'status' => true,
            'html' => view(
                'appointment::backend.patient_encounter.component.prescription_table',
                compact('data')
            )->render(),
            'message' => 'Prescription saved successfully.',
        ]);

    }

    public function editPrescription($id)
    {

        $prescription = TemplatePrescription::where('id', $id)->first();

        return response()->json(['data' => $prescription, 'status' => true]);

    }

    public function updatePrescription(Request $request, $id)
    {

        $data = $request->all();

        $prescriptioUpdate = TemplatePrescription::where('id', $id)->first();

        $prescriptioUpdate->update($data);

        $prescription = TemplatePrescription::where('template_id', $prescriptioUpdate->template_id)->get();


        $data = EncounterTemplate::where('id', $prescriptioUpdate->template_id)->with('templatePrescriptions')->first();
        $data['prescriptions'] = $data->templatePrescriptions()->get();

        return response()->json([
            'prescription' => $prescription,
            'status' => true,
            'html' => view(
                'appointment::backend.patient_encounter.component.prescription_table',
                compact('data')
            )->render(),
            'message' => 'Prescription saved successfully.',
        ]);

    }

    public function deletePrescription($id)
    {

        $prescription = TemplatePrescription::where('id', $id)->first();

        $template_id = $prescription->template_id;

        $prescription->forceDelete();

        $prescription = TemplatePrescription::where('template_id', $prescription->template_id)->get();

        $data = EncounterTemplate::where('id', $template_id)->with('templatePrescriptions')->first();
        $data['prescriptions'] = $data->templatePrescriptions()->get();

        return response()->json([
            'prescription' => $prescription,
            'status' => true,
            'html' => view(
                'appointment::backend.patient_encounter.component.prescription_table',
                compact('data')
            )->render(),
            'message' => 'Prescription saved successfully.',
        ]);
    }


    public function saveOtherDetails(Request $request)
    {

        $request_data = $request->all();

        $data = [

            'other_details' => $request->other_details,
            'template_id' => $request->template_id,

        ];

        $otherDetails = TemplateOtherDetails::updateOrCreate(
            ['template_id' => $data['template_id']],
            $data
        );

        return response()->json(['otherDetails' => $otherDetails, 'status' => true]);

    }

    public function getTemplateDetails($id)
    {

        $data = EncounterTemplate::where('id', $id)->with('templateMedicalHistroy', 'templatePrescriptions', 'TemplateOtherDetails')->first();

        $data['selectedProblemList'] = $data->templateMedicalHistroy()->where('type', 'encounter_problem')->get();
        $data['selectedObservationList'] = $data->templateMedicalHistroy()->where('type', 'encounter_observations')->get();
        $data['notesList'] = $data->templateMedicalHistroy()->where('type', 'encounter_notes')->get();
        $data['prescriptions'] = $data->templatePrescriptions()->get();
        $data['other_details'] = $data->TemplateOtherDetails()->value('other_details') ?? null;

        return response()->json(['data' => $data, 'status' => true]);

    }



}
