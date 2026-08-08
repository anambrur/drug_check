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

class DotTestPaymentConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public PortfolioTestApplication $application;

    public function __construct(PortfolioTestApplication $application)
    {
        $this->application = $application->loadMissing('portfolio');
    }

    public function envelope(): Envelope
    {
        $testName = $this->application->portfolio->title ?? 'DOT Test';

        return new Envelope(
            subject: 'DOT Payment Confirmation for ' . $testName,
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
            view: 'emails.dot_test_payment_confirmation',
            with: [
                'application' => $this->application,
                'portfolio' => $this->application->portfolio,
                'amount' => number_format($this->application->amount / 100, 2, '.', ''),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }

    public function failed(\Throwable $e): void
    {
        Log::error('Queued DOT test payment confirmation email failed.', [
            'application_id' => $this->application->id,
            'error' => $e->getMessage(),
        ]);

        $this->application->forceFill([
            'customer_notified_at' => null,
            'notifications_sent_at' => null,
        ])->save();
    }
}
