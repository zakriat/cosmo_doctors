<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $meetingDetails;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($meetingDetails)
    {
        $this->meetingDetails = $meetingDetails;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Appointment Confirmation')->view('appointment::backend.appointment.appointment_confirmation');
    }
}
