<?php

namespace App\Jobs;

use App\Services\CollectionSiteImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportCollectionSitesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;

    public int $tries = 1;

    public function __construct(
        public string $filePath,
        public ?int $userId = null
    ) {}

    public function handle(CollectionSiteImportService $importService): void
    {
        $originalTimeLimit = ini_get('max_execution_time');

        try {
            set_time_limit(1800);
            ini_set('max_execution_time', '1800');
            ini_set('memory_limit', '1024M');

            cache()->put('collection_site_import_in_progress', true);
            cache()->put('collection_site_import_stage', 'reading');
            cache()->forget('collection_site_import_error');

            $startedAt = now()->toDateTimeString();

            cache()->put('collection_site_import_stats', [
                'processed' => 0,
                'skipped' => 0,
                'total' => 0,
                'started_at' => $startedAt,
                'finished_at' => null,
                'user_id' => $this->userId,
                'file' => basename($this->filePath),
            ]);

            $stats = $importService->import($this->filePath);

            cache()->put('collection_site_import_stage', 'done');
            cache()->put('collection_site_import_stats', array_merge($stats, [
                'started_at' => $startedAt,
                'user_id' => $this->userId,
                'file' => basename($this->filePath),
            ]));

            Log::info('Collection site import completed', $stats);
        } catch (\Throwable $e) {
            cache()->put('collection_site_import_stage', 'failed');
            cache()->put('collection_site_import_error', $e->getMessage());

            Log::error('Collection site import failed: ' . $e->getMessage(), [
                'file' => $this->filePath,
                'user_id' => $this->userId,
            ]);

            throw $e;
        } finally {
            cache()->put('collection_site_import_in_progress', false);

            if ($originalTimeLimit !== false) {
                @set_time_limit((int) $originalTimeLimit);
                @ini_set('max_execution_time', (string) $originalTimeLimit);
            }
        }
    }
}
