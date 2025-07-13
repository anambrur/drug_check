<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Admin\Employee;
use App\Models\Admin\SelectionProtocol;

class EmployeeSelectedNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $employee;
    public $protocol;

    public function __construct(Employee $employee, SelectionProtocol $protocol)
    {
        $this->employee = $employee;
        $this->protocol = $protocol;
    }

    public function envelope(): Envelope
    {
        $companyName = $this->employee->clientProfile->company_name ??
            $this->protocol->client->company_name ??
            config('app.name');

        return new Envelope(
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
            replyTo: [
                new Address(
                    config('mail.reply_to.address', config('mail.from.address')),
                    config('mail.reply_to.name', config('mail.from.name'))
                ),
            ],
            subject: 'Random Drug Test Selection Notification - ' . $companyName,
        );
    }

    public function content(): Content
    {
        
        return new Content(
            view: 'emails.employee-selected',
            with: [
                'employee' => $this->employee,
                'protocol' => $this->protocol,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
