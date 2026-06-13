<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $subject;
    public $details;
    public $filePath;
    public $filename;

    public function __construct($data, $subject, $details, $filePath, $filename)
    {
        $this->data = $data;
        $this->subject = $subject;
        $this->details = $details;
        $this->filePath = $filePath;
        $this->filename = $filename;
    }

    public function build()
    {
        return $this->subject($this->subject)
            ->view('appointment::backend.clinic_appointment.invoice')
            ->with(['details' => $this->details])
            ->attach($this->filePath, [
                'as' => $this->filename,
                'mime' => 'application/pdf',
            ]);
    }
}
