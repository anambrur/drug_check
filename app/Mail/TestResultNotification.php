<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\File;

class TestResultNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;
    public $recipientType;
    public $pdfContent;
    public $databasePdf;

    public function __construct(array $emailData, string $recipientType, $databasePdf = null)
    {
        $this->emailData = $emailData;
        $this->recipientType = $recipientType;
        $this->databasePdf = $databasePdf;
    }

    public function envelope(): Envelope
    {
        $subject = $this->recipientType === 'company'
            ? 'Test Notification for Employee: ' . $this->emailData['employee_name']
            : 'Your Test Schedule Notification';

        return new Envelope(
            subject: $subject,
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
        // Ensure we always return valid content
        return new Content(
            view: 'emails.test-notification',
            with: [
                'data' => $this->emailData ?? [],
                'type' => $this->recipientType ?? 'employee'
            ]
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        // Add database PDF if exists and is valid file path
        if ($this->databasePdf && is_string($this->databasePdf) && File::exists($this->databasePdf)) {
            $attachments[] = Attachment::fromPath($this->databasePdf)
                ->as('test_result_report.pdf')
                ->withMime('application/pdf');
        }

        return $attachments;
    }

    // Fallback build method for compatibility
    public function build()
    {
        return $this->view('emails.test-notification')
            ->with([
                'data' => $this->emailData ?? [],
                'type' => $this->recipientType ?? 'employee'
            ]);
    }
}