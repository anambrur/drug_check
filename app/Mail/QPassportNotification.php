<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class QPassportNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $firstName;
    public $lastName;
    public $questOrderId;
    public $qpassportContent;
    public $fileExtension;

    public function __construct($firstName, $lastName, $questOrderId, $qpassportContent, $fileExtension = 'pdf')
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->questOrderId = $questOrderId;
        $this->qpassportContent = $qpassportContent;
        $this->fileExtension = $fileExtension;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Quest Diagnostics QPassport',
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
            view: 'emails.qpassport',
            with: [
                'firstName' => $this->firstName,
                'lastName' => $this->lastName,
                'questOrderId' => $this->questOrderId,
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn () => $this->qpassportContent,
                "QPassport-{$this->questOrderId}.{$this->fileExtension}"
            )->withMime($this->fileExtension === 'pdf' ? 'application/pdf' : 'image/tiff'),
        ];
    }
}