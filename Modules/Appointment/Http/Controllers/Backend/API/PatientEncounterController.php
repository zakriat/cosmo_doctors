<?php

namespace Modules\Appointment\Http\Controllers\Backend\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Appointment\Models\PatientEncounter;
use Modules\Appointment\Transformers\EncounterResource;
use Modules\Appointment\Transformers\EncounterDetailsResource;
use PDF;
use Illuminate\Support\Facades\File;

class PatientEncounterController extends Controller
{
    public function encounterList(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $encounter_data = PatientEncounter::SetRole(auth()->user())->with('user', 'clinic', 'doctor', 'appointment','soap');

        if($request->has('clinic_id') && ($request->clinic_id !='')){
            $encounter_data->where('clinic_id',$request->clinic_id);
        }

        $encounter = $encounter_data->paginate($perPage);
        $encounterCollection = EncounterResource::collection($encounter);

        if ($request->filled('id')) {

            $encounter = $encounter_data->where('id', $request->id)->first();
            $responseData = new EncounterDetailsResource($encounter);

            return response()->json([
                'status' => true,
                'data' => $responseData,
                'message' => __('appointment.encounter_details'),
            ], 200);
        }

        return response()->json([
            'status' => true,
            'data' => $encounterCollection,
            'message' => __('appointment.encounter_list'),
        ], 200);
    }

    public function encounterInvoice(Request $request)
    {
        $id = $request->id;
        $data = PatientEncounter::where('id', $id)->with('user', 'clinic', 'doctor', 'medicalHistroy', 'prescriptions', 'EncounterOtherDetails', 'medicalReport', 'appointmentdetail', 'billingrecord')->first();

        $data['selectedProblemList'] =  $data->medicalHistroy()->where('type', 'encounter_problem')->get();
        $data['selectedObservationList'] = $data->medicalHistroy()->where('type', 'encounter_observations')->get();
        $data['notesList'] = $data->medicalHistroy()->where('type', 'encounter_notes')->get();
        $data['prescriptions'] = $data->prescriptions()->get();
        $data['other_details'] = $data->EncounterOtherDetails()->value('other_details') ?? null;
        $data['signature'] = optional(optional($data->doctor)->doctor)->Signature ?? null;
        $pdf = PDF::loadHTML(view("appointment::backend.encounter_template.invoice", ['data' => $data])->render())
            ->setOptions(['defaultFont' => 'sans-serif']);
        $baseDirectory = storage_path('app/public');
        $highestDirectory = collect(File::directories($baseDirectory))->map(function ($directory) {
            return basename($directory);
        })->max() ?? 0;
        $nextDirectory = intval($highestDirectory) + 1;
        while (File::exists($baseDirectory . '/' . $nextDirectory)) {
            $nextDirectory++;
        }
        $newDirectory = $baseDirectory . '/' . $nextDirectory;
        File::makeDirectory($newDirectory, 0777, true);

        $filename = 'invoice_' . $id . '.pdf';
        $filePath = $newDirectory . '/' . $filename;

        $pdf->save($filePath);

        $url = url('storage/' . $nextDirectory . '/' . $filename);
        return response()->json(['status' => true, 'link' => $url], 200);
    }

    public function downloadPrescription(Request $request)
    {
        $id = $request->id;

        $data = PatientEncounter::where('id', $id)->with('user', 'clinic', 'doctor', 'medicalHistroy', 'prescriptions', 'EncounterOtherDetails', 'medicalReport', 'appointmentdetail', 'billingrecord')->first();


        $data['prescriptions'] = $data->prescriptions()->get();
        $data['signature'] = optional(optional($data->doctor)->doctor)->Signature ?? null;

        $pdf = PDF::loadHTML(view("appointment::backend.encounter_template.prescription", ['data' => $data])->render())
            ->setOptions(['defaultFont' => 'sans-serif']);

        $baseDirectory = storage_path('app/public');
        $highestDirectory = collect(File::directories($baseDirectory))->map(function ($directory) {
            return basename($directory);
        })->max() ?? 0;
        $nextDirectory = intval($highestDirectory) + 1;
        while (File::exists($baseDirectory . '/' . $nextDirectory)) {
            $nextDirectory++;
        }
        $newDirectory = $baseDirectory . '/' . $nextDirectory;
        File::makeDirectory($newDirectory, 0777, true);

        $filename = 'prescription_' . $id . '.pdf';
        $filePath = $newDirectory . '/' . $filename;

        $pdf->save($filePath);

        $url = url('storage/' . $nextDirectory . '/' . $filename);

        return response()->json(['status' => true, 'link' => $url], 200);

    }
}
