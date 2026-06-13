<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailMailableSend extends Mailable
{
    use Queueable, SerializesModels;

    public $mailable;

    public $data;

    public $templateData;

    public $type;

    /**
     * Create a new message instance.
     */
    public function __construct($mailable, $data, $type = '')
    {
        $this->mailable = $mailable ?? '';
        $this->data = $data;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // $this->templateData = $this->mailable->defaultNotificationTemplateMap->mail_template_detail;
        if (!empty($this->data['mailTemplate'] ?? null)) {
            $this->templateData = $this->data['mailTemplate'];
        } elseif (!empty($this->data['mail'] ?? null)) {
            $this->templateData = $this->data['mail'];
        } elseif (!empty($this->mailable->mail_template_detail ?? null)) {
            $this->templateData = $this->mailable->mail_template_detail;
        } else {
            $this->templateData = '<p>No mail template found.</p>';
        }

        foreach ($this->data as $key => $value) {
            if (is_scalar($value) || is_null($value)) {
                $this->templateData = str_replace('[[ '.$key.' ]]', (string) $value, $this->templateData);
            }
        }

        $message = $this->markdown('mail.markdown');

        $files = isset($this->data['attachments']) ? json_decode($this->data['attachments']) : [];

        foreach ($files as $file) {
            $message->attach($file); // attach each file
        }

        return $message; //Send mail
    }
}
