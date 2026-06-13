<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PrescriptionListMail extends Mailable
{
    use Queueable, SerializesModels;

    public $prescriptionList;

    public function __construct($prescriptionList)
    {
        $this->prescriptionList = $prescriptionList;
    }

    public function build()
    {
        return $this->view('mail.prescription_list')
                    ->with(['prescriptionList' => $this->prescriptionList]);
    }
}
