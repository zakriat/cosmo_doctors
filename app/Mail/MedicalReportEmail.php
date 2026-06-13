<?php

namespace App\Mail;

use App\Models\EncounterMedicalReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class MedicalReportEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $medicalReport;
    public $filePath;

    public function __construct($medicalReport, $filePath)
    {
        $this->medicalReport = $medicalReport;
        $this->filePath = $filePath;
    }

    public function build()
    {

        return $this->view('mail.medical_report');
                   
    }
}


?>