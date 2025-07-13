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

class TestResultNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;
    public $recipientType;
    public $pdfContent;
    public $uploadedPdf;

    public function __construct(array $emailData, string $recipientType, $pdfContent = null, $uploadedPdf = null)
    {
        $this->emailData = $emailData;
        $this->recipientType = $recipientType;
        $this->pdfContent = $pdfContent;
        $this->uploadedPdf = $uploadedPdf;
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

        // Add generated PDF if exists and is valid
        if ($this->pdfContent && is_string($this->pdfContent)) {
            $attachments[] = Attachment::fromData(
                fn() => $this->pdfContent,
                'certificate.pdf'
            )->withMime('application/pdf');
        }

        // Add uploaded PDF if exists and is valid
        if ($this->uploadedPdf && $this->uploadedPdf->isValid()) {
            $attachments[] = Attachment::fromPath($this->uploadedPdf->getRealPath())
                ->as($this->uploadedPdf->getClientOriginalName())
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
