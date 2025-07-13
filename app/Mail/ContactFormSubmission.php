<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormSubmission extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        $subject = $this->data['contact_subject'] ??
            $this->data['contact_name'] . ' - ' .
            $this->data['contact_phone'] . ' - ' .
            $this->data['contact_email'];

        return $this->subject($subject)
            ->from('drugcheck@skyroshop.com', 'My Drug Check')
            ->replyTo($this->data['contact_email'], $this->data['contact_name'])
            ->view('emails.contact_form', [
                'data' => $this->data
            ]);
    }


    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
