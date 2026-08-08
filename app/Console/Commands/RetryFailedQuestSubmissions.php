<?php

namespace App\Console\Commands;

use App\Models\PortfolioTestApplication;
use App\Services\QuestOrderSubmissionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RetryFailedQuestSubmissions extends Command
{
    protected $signature = 'quest:retry-failed-submissions
        {--minutes=15 : Only retry applications older than this many minutes}
        {--limit=50 : Maximum number of applications to retry per run}
        {--alert-after=3 : Log an alert when quest_submission_error mentions repeated failure count}';

    protected $description = 'Retry paid portfolio test applications that failed or never reached Quest submission';

    public function handle(QuestOrderSubmissionService $submissionService): int
    {
        $minutes = max(1, (int) $this->option('minutes'));
        $limit = max(1, (int) $this->option('limit'));
        $cutoff = now()->subMinutes($minutes);

        $applications = PortfolioTestApplication::query()
            ->where('payment_status', 'completed')
            ->whereIn('quest_submission_status', ['pending', 'failed'])
            ->where(function ($query) {
                $query->whereNull('quest_order_id')->orWhere('quest_order_id', '');
            })
            ->where('updated_at', '<=', $cutoff)
            ->orderBy('id')
            ->limit($limit)
            ->get();

        if ($applications->isEmpty()) {
            $this->info('No stuck Quest submissions to retry.');

            return self::SUCCESS;
        }

        $succeeded = 0;
        $failed = 0;

        foreach ($applications as $application) {
            $this->line("Retrying application #{$application->id}…");

            $result = $submissionService->submitFromApplication($application);
            $application->refresh();

            if ($result['success'] ?? false) {
                $succeeded++;
                $this->info("  submitted as Quest order {$result['quest_order_id']}");
                continue;
            }

            $failed++;
            $error = $result['error'] ?? 'Unknown error';
            $this->error("  failed: {$error}");

            $attemptNote = 'retry_failed_at=' . now()->toIso8601String();
            $previous = (string) ($application->quest_submission_error ?? '');
            $attemptCount = substr_count($previous, 'retry_failed_at=') + 1;

            $application->update([
                'quest_submission_error' => trim($previous . "\n{$attemptNote}: {$error}"),
            ]);

            if ($attemptCount >= (int) $this->option('alert-after')) {
                Log::critical('Quest submission still failing after repeated retries', [
                    'application_id' => $application->id,
                    'attempts' => $attemptCount,
                    'error' => $error,
                ]);
            }
        }

        $this->info("Done. Succeeded: {$succeeded}, Failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
