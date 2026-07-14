<?php

namespace App\Mail;

use App\Models\Admin\ConsortiumPlan;
use App\Models\ConsortiumEnrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConsortiumEnrollmentAdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public ConsortiumEnrollment $enrollment;
    public ConsortiumPlan $pricing;

    public function __construct(ConsortiumEnrollment $enrollment, ConsortiumPlan $pricing)
    {
        $this->enrollment = $enrollment;
        $this->pricing = $pricing;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Paid Consortium Enrollment - ' . $this->enrollment->company_name,
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
            view: 'emails.consortium_enrollment_admin_notification',
            with: [
                'enrollment' => $this->enrollment,
                'pricing' => $this->pricing,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
