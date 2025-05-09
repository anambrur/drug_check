<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClientRegistrationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $email;
    public $password;

    public function __construct($client, $email, $password)
    {
        $this->client = $client;
        $this->email = $email;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Your Company Account Access - ' . $this->client->company_name)
            ->view('emails.client-registration')
            ->with([
                'client' => $this->client,
                'email' => $this->email,
                'password' => $this->password
            ]);
    }
}
