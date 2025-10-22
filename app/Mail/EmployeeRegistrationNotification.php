<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmployeeRegistrationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $email;
    public $password;
    public $companyName;

    public function __construct($employee, $email, $password, $companyName)
    {
        $this->employee = $employee;
        $this->email = $email;
        $this->password = $password;
        $this->companyName = $companyName;
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
            view: 'emails.employee-registration',
            with: [
                'employee' => $this->employee,
                'email' => $this->email,
                'password' => $this->password,
                'companyName' => $this->companyName,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }

    protected function getFormattedSubject(): string
    {
        return 'Your Employee Account Access - ' . $this->companyName;
    }
}
