<?php

namespace App\Services;

use App\Models\AdminOrderNotification;
use App\Models\ConsortiumEnrollment;
use App\Models\PortfolioTestApplication;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class AdminOrderNotificationService
{
    public function notifyPaidApplication(PortfolioTestApplication $application): ?AdminOrderNotification
    {
        $type = $application->isDot() ? 'dot' : 'non_dot';
        $typeLabel = $application->isDot() ? 'DOT' : 'Non-DOT';
        $name = $application->applicantDisplayName();
        $amount = $application->formatted_amount;
        $company = $application->resolveCompanyName();

        $title = "New {$typeLabel} order — {$name}";
        $bodyParts = array_filter([
            $company !== '—' ? $company : null,
            $amount,
        ]);
        $body = implode(' · ', $bodyParts) ?: null;

        $linkRoute = $application->isDot()
            ? 'admin.orders.dot-testing'
            : 'admin.orders.non-dot-testing';

        return $this->createOnce($application, $type, $title, $body, $linkRoute);
    }

    public function notifyPaidConsortium(ConsortiumEnrollment $enrollment): ?AdminOrderNotification
    {
        $company = $enrollment->company_name ?: 'Company';
        $amount = $enrollment->formatted_amount;

        return $this->createOnce(
            $enrollment,
            'consortium',
            "New consortium enrollment — {$company}",
            $amount,
            'consortium-enrollments.index'
        );
    }

    public function notifyPaidClearingHouse(\App\Models\ClearingHouseEnrollment $enrollment): ?AdminOrderNotification
    {
        $company = $enrollment->company_name ?: 'Company';
        $amount = $enrollment->formatted_amount;

        return $this->createOnce(
            $enrollment,
            'clearing_house',
            "New clearing house enrollment — {$company}",
            $amount,
            'clearing-house-enrollments.index'
        );
    }

    protected function createOnce(
        Model $notifiable,
        string $type,
        string $title,
        ?string $body,
        string $linkRoute,
        array $linkParams = []
    ): ?AdminOrderNotification {
        $existing = AdminOrderNotification::query()
            ->where('notifiable_type', $notifiable->getMorphClass())
            ->where('notifiable_id', $notifiable->getKey())
            ->where('type', $type)
            ->first();

        if ($existing) {
            return $existing;
        }

        try {
            return AdminOrderNotification::create([
                'type' => $type,
                'title' => $title,
                'body' => $body,
                'notifiable_type' => $notifiable->getMorphClass(),
                'notifiable_id' => $notifiable->getKey(),
                'link_route' => $linkRoute,
                'link_params' => $linkParams ?: null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to create admin order notification', [
                'type' => $type,
                'notifiable_type' => $notifiable->getMorphClass(),
                'notifiable_id' => $notifiable->getKey(),
                'error' => $e->getMessage(),
            ]);

            return AdminOrderNotification::query()
                ->where('notifiable_type', $notifiable->getMorphClass())
                ->where('notifiable_id', $notifiable->getKey())
                ->where('type', $type)
                ->first();
        }
    }
}
