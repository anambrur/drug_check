<?php

namespace App\Mail;

use App\Models\Admin\ClearingHousePlan;
use App\Models\ClearingHouseEnrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClearingHouseEnrollmentConfirmed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public ClearingHouseEnrollment $enrollment;
    public ClearingHousePlan $pricing;

    public function __construct(ClearingHouseEnrollment $enrollment, ClearingHousePlan $pricing)
    {
        $this->enrollment = $enrollment;
        $this->pricing = $pricing;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Clearing House Enrollment Confirmed - ' . $this->enrollment->company_name,
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
            view: 'emails.clearing_house_enrollment_confirmed',
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
