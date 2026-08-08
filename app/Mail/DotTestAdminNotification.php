<?php

namespace App\Mail;

use App\Models\PortfolioTestApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DotTestAdminNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public PortfolioTestApplication $application;

    public function __construct(PortfolioTestApplication $application)
    {
        $this->application = $application->loadMissing('portfolio');
    }

    public function envelope(): Envelope
    {
        $applicant = trim(($this->application->first_name ?? 'User') . ' ' . substr((string) ($this->application->last_name ?? ''), 0, 1) . '.');

        return new Envelope(
            subject: 'DOT Application Submission: ' . $applicant . ' - ' . ($this->application->reason_for_testing ?? 'Test Registration'),
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
            replyTo: [
                new Address(
                    $this->application->email ?: config('mail.from.address'),
                    trim(($this->application->first_name ?? 'User') . ' ' . ($this->application->last_name ?? ''))
                ),
            ]
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.dot_test_admin_notification',
            with: [
                'application' => $this->application,
                'portfolio' => $this->application->portfolio,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }

    public function failed(\Throwable $e): void
    {
        Log::error('Queued DOT test admin notification email failed.', [
            'application_id' => $this->application->id,
            'error' => $e->getMessage(),
        ]);

        $this->application->forceFill([
            'admin_notified_at' => null,
            'notifications_sent_at' => null,
        ])->save();
    }
}
