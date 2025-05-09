<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestResultNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;
    public $recipientType;

    /**
     * Create a new message instance.
     */
    public function __construct(array $emailData, string $recipientType)
    {
        $this->emailData = $emailData;
        $this->recipientType = $recipientType;
    }

    public function build()
    {
        $subject = $this->recipientType === 'company'
            ? 'Test Notification for Employee: ' . $this->emailData['employee_name']
            : 'Your Test Schedule Notification';

        return $this->subject($subject)
            ->view('emails.test-notification')
            ->with([
                'data' => $this->emailData,
                'type' => $this->recipientType
            ]);
    }


    /**
     * Get the message envelope.
     */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Test Result Notification',
    //     );
    // }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
