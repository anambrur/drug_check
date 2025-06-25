<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DotApplicationReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $reason;

    public function __construct($data, $reason)
    {
        $this->data = $data;
        $this->reason = $reason;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->getFormattedSubject(),
            from: new Address(
                config('mail.from.address', 'noreply@mhanam.com'),
                config('mail.from.name', 'My Drug Check')
            ),
            replyTo: [
                new Address(
                    $this->data['email'] ?? config('mail.from.address'),
                    ($this->data['first_name'] ?? 'User') . ' ' . ($this->data['last_name'] ?? '')
                ),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.dot_application',
            with: [
                'data' => $this->data,
                'reason' => $this->reason,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }

    private function getFormattedSubject(): string
    {
        return 'DOT Application Submission: ' . 
               ($this->data['first_name'] ?? 'User') . ' ' . 
               substr($this->data['last_name'] ?? '', 0, 1) . '. - ' . 
               ($this->data['reason_for_testing'] ?? 'Test Registration');
    }
}