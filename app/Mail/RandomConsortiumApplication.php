<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RandomConsortiumApplication extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $certificatePath;


    /**
     * Create a new message instance.
     */
    public function __construct($data, $certificatePath = null)
    {
        $this->data = $data;
        $this->certificatePath = $certificatePath;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Random Consortium Application - ' . $this->data['company_name'])
            ->view('emails.random_consortium_application')
            ->from('drugcheck@skyroshop.com', 'My Drug Check')
            ->replyTo($this->data['der_email'], $this->data['der_name'])
            ->with([
                'company' => [
                    'name' => $this->data['company_name'],
                    'address' => $this->data['company_address'],
                    'city' => $this->data['company_city'],
                    'state' => $this->data['company_state'],
                    'zip' => $this->data['company_zip'],
                    'phone' => $this->data['company_phone'],
                ],
                'der' => [
                    'name' => $this->data['der_name'],
                    'email' => $this->data['der_email'],
                    'phone' => $this->data['der_phone'],
                ],
                'certificate' => [
                    'start_date' => $this->data['certificate_start_date'],
                    'file_path' => $this->certificatePath,
                ]
            ])
            ->when($this->certificatePath, function ($message) {
                $message->attach(public_path($this->certificatePath));
            });
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
