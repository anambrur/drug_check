<?php

namespace App\Services\Quest;

use App\Models\Admin\QuestOrder;
use App\Models\Admin\QuestOrderScreen;
use Illuminate\Support\Facades\Log;

class QuestOrderScreenService
{
    public function applyStatus(QuestOrder $order, array $payload, string $rawXml): void
    {
        $screenType = $payload['screen_type'] ?: 'drug';
        $existing = QuestOrderScreen::where('quest_order_id', $order->id)
            ->where('screen_type', $screenType)
            ->first();

        $incomingAt = $payload['order_status_datetime'];
        if (
            $existing
            && $existing->order_status_datetime
            && $incomingAt
            && $incomingAt->lt($existing->order_status_datetime)
        ) {
            Log::info('Quest OrderStatus skipped (out of order)', [
                'order_id' => $order->id,
                'quest_order_id' => $order->quest_order_id,
                'screen_type' => $screenType,
                'incoming' => (string) $incomingAt,
                'stored' => (string) $existing->order_status_datetime,
            ]);

            return;
        }

        $attributes = [
            'order_status' => $payload['order_status_id'],
            'order_status_datetime' => $incomingAt,
            'status_raw_xml' => $this->truncateRawXml($rawXml),
        ];

        $this->mergeNonEmpty($attributes, $existing, [
            'specimen_id' => $payload['specimen_id'] ?: null,
            'lab_accession_number' => $payload['lab_accession_number'] ?: null,
            'collected_datetime' => $payload['collected_datetime'],
            'physical_data' => $payload['physical_data'],
        ]);

        QuestOrderScreen::updateOrCreate(
            [
                'quest_order_id' => $order->id,
                'screen_type' => $screenType,
            ],
            $attributes
        );

        $this->syncParentSummary($order);

        Log::info('Quest OrderStatus applied to screen', [
            'order_id' => $order->id,
            'quest_order_id' => $order->quest_order_id,
            'screen_type' => $screenType,
            'status' => $payload['order_status_id'],
        ]);
    }

    public function applyResult(QuestOrder $order, array $payload, string $rawXml): void
    {
        $screenType = $payload['screen_type'] ?: 'drug';
        $existing = QuestOrderScreen::where('quest_order_id', $order->id)
            ->where('screen_type', $screenType)
            ->first();

        $incomingAt = $payload['order_result_datetime'];
        if (
            $existing
            && $existing->order_result_datetime
            && $incomingAt
            && $incomingAt->lt($existing->order_result_datetime)
        ) {
            Log::info('Quest OrderResult skipped (out of order)', [
                'order_id' => $order->id,
                'quest_order_id' => $order->quest_order_id,
                'screen_type' => $screenType,
                'incoming' => (string) $incomingAt,
                'stored' => (string) $existing->order_result_datetime,
            ]);

            return;
        }

        $attributes = [
            'order_result' => $payload['order_result_id'],
            'order_result_datetime' => $incomingAt,
            'result_raw_xml' => $this->truncateRawXml($rawXml),
        ];

        $this->mergeNonEmpty($attributes, $existing, [
            'specimen_id' => $payload['specimen_id'] ?: null,
            'lab_accession_number' => $payload['lab_accession_number'] ?: null,
            'collected_datetime' => $payload['collected_datetime'],
            'physical_data' => $payload['physical_data'],
        ]);

        QuestOrderScreen::updateOrCreate(
            [
                'quest_order_id' => $order->id,
                'screen_type' => $screenType,
            ],
            $attributes
        );

        $this->syncParentSummary($order);

        Log::info('Quest OrderResult applied to screen', [
            'order_id' => $order->id,
            'quest_order_id' => $order->quest_order_id,
            'screen_type' => $screenType,
            'result' => $payload['order_result_id'],
        ]);
    }

    public function syncParentSummary(QuestOrder $order): void
    {
        $order->load('screens');
        $screens = $order->screens;

        if ($screens->isEmpty()) {
            return;
        }

        $primary = $screens->firstWhere('screen_type', 'drug') ?? $screens->first();

        $latestStatus = $screens->filter(fn ($s) => $s->order_status)->sortByDesc('order_status_datetime')->first();
        $latestResult = $screens->filter(fn ($s) => $s->order_result)->sortByDesc('order_result_datetime')->first();

        $order->update([
            'order_status' => $latestStatus?->order_status ?? $primary?->order_status,
            'order_status_screen_type' => $latestStatus?->screen_type ?? $primary?->screen_type,
            'order_status_datetime' => $latestStatus?->order_status_datetime ?? $primary?->order_status_datetime,
            'order_status_updated_at' => $latestStatus ? now() : $order->order_status_updated_at,
            'order_result' => $latestResult?->order_result ?? $primary?->order_result,
            'order_result_screen_type' => $latestResult?->screen_type ?? $primary?->screen_type,
            'order_result_datetime' => $latestResult?->order_result_datetime ?? $primary?->order_result_datetime,
            'order_result_updated_at' => $latestResult ? now() : $order->order_result_updated_at,
            'specimen_id' => $primary?->specimen_id ?? $order->specimen_id,
            'lab_accession_number' => $primary?->lab_accession_number ?? $order->lab_accession_number,
            'collected_datetime' => $primary?->collected_datetime ?? $order->collected_datetime,
            'physical_data' => $primary?->physical_data ?? $order->physical_data,
        ]);
    }

    public function isResultAvailable(QuestOrder $order, ?string $screenType = 'drug'): bool
    {
        $screen = $this->resolveScreen($order, $screenType);

        if ($screen?->order_result) {
            return true;
        }

        $status = $screen?->order_status ?? $order->order_status;

        return in_array(strtoupper((string) $status), ['ATLAB', 'PENDINGMRO', 'COLLECTED'], true);
    }

    public function resolveScreen(QuestOrder $order, ?string $screenType = 'drug'): ?QuestOrderScreen
    {
        $order->loadMissing('screens');

        return $order->screens->firstWhere('screen_type', $screenType ?? 'drug')
            ?? $order->screens->firstWhere('screen_type', 'drug')
            ?? $order->screens->first();
    }

    /**
     * Only overwrite specimen/accession/collected/physical when the inbound message
     * supplies a non-empty value (spec marks those fields as not always provided).
     */
    private function mergeNonEmpty(array &$attributes, ?QuestOrderScreen $existing, array $candidates): void
    {
        foreach ($candidates as $key => $value) {
            if ($value === null || $value === '' || $value === []) {
                if ($existing && $existing->{$key} !== null) {
                    $attributes[$key] = $existing->{$key};
                }
                continue;
            }

            $attributes[$key] = $value;
        }
    }

    private function truncateRawXml(string $rawXml): string
    {
        return substr($rawXml, 0, 65535);
    }
}
