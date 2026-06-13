<?php

namespace Modules\Appointment\Trait;
use Modules\Appointment\Models\EncounterPrescription;
use PDF;
use Modules\Appointment\Models\PatientEncounter;
use Carbon\Carbon;

trait EncounterTrait
{  

    
    public function generateEncounter($appointment){

        $encouter_detail=[

            'encounter_date'=>Carbon::now(),
            'user_id'=>$appointment->user_id,
            'clinic_id'=>$appointment->clinic_id,
            'doctor_id'=>$appointment->doctor_id,
            'appointment_id'=>$appointment->id,
            'status'=>1,

        ];

        $encounter_data=PatientEncounter::create($encouter_detail);

        return  $encounter_data;


    }




    public function exportPDF($encounter_id)
    {
     
    }

    public function exportCSV($encounter_id)
    {
        $users = User::all();
        $csvFileName = 'users.csv';

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Email']); // CSV headers
            foreach ($users as $user) {
                fputcsv($file, [$user->id, $user->name, $user->email]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportXLSX($encounter_id)
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }


}
