<?php

namespace App\Mail;

use App\Models\ConsortiumEnrollment;
use App\Models\Admin\ConsortiumPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConsortiumEnrollmentAdminNotification extends Mailable
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
        return $this->subject('New Paid Consortium Enrollment - ' . $this->enrollment->company_name)
            ->view('emails.consortium_enrollment_admin_notification')
            ->from(config('mail.from.address', 'info@mydrugcheck.com'), config('mail.from.name', 'DrugCheckr'));
    }
}
