<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientRegistrationNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $client;
    public $email;
    public $password;

    public function __construct($client, $email, $password)
    {
        $this->client = $client;
        $this->email = $email;
        $this->password = $password;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->getFormattedSubject(),
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
            replyTo: [
                new Address(
                    config('mail.reply_to.address', config('mail.from.address')),
                    config('mail.reply_to.name', config('mail.from.name'))
                ),
            ]
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client-registration',
            with: [
                'client' => $this->client,
                'email' => $this->email,
                'password' => $this->password,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }

    protected function getFormattedSubject(): string
    {
        return 'Your ' . $this->client->company_name . ' Account Access';
    }
}
