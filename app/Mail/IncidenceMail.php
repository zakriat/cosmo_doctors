<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IncidenceMail extends Mailable
{
    use Queueable, SerializesModels;
    public $bodyData;
    /**
     * Create a new message instance.
     */
    public function __construct($bodyData)
    {
        $this->bodyData = $bodyData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Created New Incidence report',
        );
    }

    public function build()
    {
        return $this->subject('Created New Incidence report')->view('mail.incidence-report', ['data' => $this->bodyData]);
    }
}
