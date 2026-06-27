<?php

namespace App\Mail;

use App\Models\ConsortiumEnrollment;
use App\Models\Admin\ConsortiumPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConsortiumEnrollmentConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public $enrollment;
    public $pricing;

    /**
     * Create a new message instance.
     */
    public function __construct(ConsortiumEnrollment $enrollment, ConsortiumPlan $pricing)
    {
        $this->enrollment = $enrollment;
        $this->pricing = $pricing;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Random Consortium Enrollment Confirmed - ' . $this->enrollment->company_name)
            ->view('emails.consortium_enrollment_confirmed')
            ->from(config('mail.from.address', 'info@mydrugcheck.com'), config('mail.from.name', 'My Drug Check'));
    }
}
