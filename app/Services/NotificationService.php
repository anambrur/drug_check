<?php

namespace App\Services;

use App\Mail\TestStoreNotification;
use Illuminate\Support\Facades\Log;
use App\Mail\TestResultNotification;
use Illuminate\Support\Facades\Mail;
use App\Models\Admin\ContactInfoWidget;

class NotificationService
{
    public function sendTestNotification($mailData, string $recipientType, $pdfContent = null, $uploadedPdf = null): bool
    {
        try {
            if (!in_array($recipientType, ['company', 'employee'])) {
                throw new \InvalidArgumentException("Invalid recipient type");
            }
            if ($recipientType === 'company' && empty($mailData->clientProfile->der_contact_email)) {
                throw new \RuntimeException("Company DER email not found");
            }

            if ($recipientType === 'employee' && empty($mailData->employee->email)) {
                throw new \RuntimeException("Employee email not found");
            }

            $contact_info_widget = ContactInfoWidget::first();
            // Calculate overall result
            $overallResult = $mailData->resultPanel->contains('result', 'positive')
                ? 'Positive'
                : 'Negative';

            // Prepare email data
            $emailData = [
                'has_attachments' => $pdfContent || $uploadedPdf,
                'has_custom_attachment' => (bool)$uploadedPdf,
                'company_name' => $mailData->clientProfile->company_name ?? 'Company',
                'address' => $mailData->clientProfile->address ?? '',
                'city' => $mailData->clientProfile->city ?? '',
                'state' => $mailData->clientProfile->state ?? '',
                'zip' => $mailData->clientProfile->zip ?? '',
                'phone' => $mailData->clientProfile->phone  ?? '',
                'overall_result' => $overallResult,

                'employee_name' => ($mailData->employee->first_name ?? '') . ' ' . ($mailData->employee->last_name ?? ''),
                'test_date' => $mailData->date_of_collection ?? 'N/A',
                'test_time' => $mailData->time_of_collection ?? 'N/A',
                'reason_for_test' => $mailData->reason_for_test ?? 'Not specified',
                'collection_location' => $mailData->collection_location ?? 'Not specified',
                'additional_text' => $mailData->additional_text ?? '',
                'test_name' => $mailData->testAdmin->test_name ?? 'N/A',
                'test_method' => $mailData->testAdmin->method,
                'test_regulation' => $mailData->testAdmin->regulation,
                'specimen' => $mailData->testAdmin->specimen ?? 'N/A',
                'status' => $mailData->status ?? 'N/A',

                'contact_info_widget' => [
                    'description' => $contact_info_widget->description,
                    'address' => $contact_info_widget->address,
                    'phone' => $contact_info_widget->phone,
                    'email' => $contact_info_widget->email,
                ],
                'test_panels' => $mailData->resultPanel->map(function ($panel) {
                    return [
                        'drug_name' => $panel->drug_name,
                        'drug_code' => $panel->drug_code,
                        'result' => $panel->result,
                        'cut_off_level' => $panel->cut_off_level,
                        'conf_level' => $panel->conf_level,
                    ];
                })->toArray(),
            ];

            // Determine recipient
            $recipient = $recipientType === 'company'
                ? $mailData->clientProfile->der_contact_email
                : $mailData->employee->email;

            // Send email
            // Mail::to($recipient)->send(new TestResultNotification($emailData, $recipientType,$pdfContent));

            // dd($pdfContent , $uploadedPdf);

            // Ensure we have valid PDF content or null
            $pdfContent = $pdfContent ?: null;
            $uploadedPdf = $uploadedPdf ?: null;


            Mail::to($recipient)->send(new TestResultNotification(
                $emailData,
                $recipientType,
                $pdfContent,
                $uploadedPdf
            ));



            Log::info("Successfully sent notification to {$recipientType}: {$recipient}");
            return true;
        } catch (\InvalidArgumentException $e) {
            Log::warning($e->getMessage());
            return false;
        } catch (\RuntimeException $e) {
            Log::warning($e->getMessage());
            return false;
        } catch (\Exception $e) {
            Log::error("Failed to send {$recipientType} notification: " . $e->getMessage());
            return false;
        }
    }


    public function sendTestNotificationStore($result, $recipientType)
    {

        try {
            $emailData = [
                'company_name' => $result->clientProfile->company_name ?? 'Company',
                'employee_name' => $result->employee->first_name . ' ' . $result->employee->last_name ?? 'Employee',
                'test_date' => $result->date_of_collection,
                'test_time' => $result->time_of_collection,
                'test_reason' => $result->reason_for_test,
                'collection_location' => $result->collection_location,
                // 'status' => $mail_data->status,
                'reason_for_test' => $result->reason_for_test ?? 'Not specified',
                'collection_location' => $result->collection_location ?? 'Not specified',
                'additional_text' => $result->additional_text ?? '',
                'test_name' => $result->testAdmin->test_name ?? 'N/A',
                'test_method' => $result->testAdmin->method,
                'test_regulation' => $result->testAdmin->regulation,
                'specimen' => $result->testAdmin->specimen ?? 'N/A',
            ];
            // dd($emailData);

            Mail::to(
                $recipientType === 'company'
                    ? $result->clientProfile->der_contact_email
                    : $result->employee->email
            )->send(new TestStoreNotification($emailData, $recipientType));

            toastr()->success("Notification sent successfully to {$recipientType}", 'Notification Sent');
        } catch (\Exception $e) {
            Log::error("Failed to send {$recipientType} notification: " . $e->getMessage());

            toastr()->error('Failed to send notification', 'Notification Error');
        }
    }
}
