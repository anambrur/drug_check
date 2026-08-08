<?php

namespace App\Console\Commands;

use App\Models\Admin\SelectionProtocol;
use App\Services\RandomSelection\RandomSelectionSchedule;
use App\Services\RandomSelectionService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RunDueRandomSelections extends Command
{
    protected $signature = 'random-selection:run-due
                            {--date= : Run as if today were this date (Y-m-d)}
                            {--dry-run : List due protocols without executing}';

    protected $description = 'Execute active automatic random-selection protocols that are due';

    public function handle(RandomSelectionService $service, RandomSelectionSchedule $schedule): int
    {
        if (!config('random_selection.enabled', true) && !$this->option('date') && !$this->option('dry-run')) {
            $this->warn('Random selection schedule is disabled via config.');
            return self::SUCCESS;
        }

        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))->startOfDay()
            : now()->startOfDay();

        $protocols = SelectionProtocol::query()
            ->with(['clients', 'extraTests', 'subSelections', 'test'])
            ->where('is_active', true)
            ->where('automatic', true)
            ->get();

        $due = $protocols->filter(fn (SelectionProtocol $protocol) => $schedule->isDueToday($protocol, $date));

        if ($due->isEmpty()) {
            $this->info('No protocols due for ' . $date->toDateString() . '.');
            return self::SUCCESS;
        }

        $this->info($due->count() . ' protocol(s) due for ' . $date->toDateString() . '.');

        if ($this->option('dry-run')) {
            foreach ($due as $protocol) {
                $this->line("- [{$protocol->id}] {$protocol->name}");
            }
            return self::SUCCESS;
        }

        $failures = 0;

        foreach ($due as $protocol) {
            try {
                $results = $service->executeProtocol($protocol, 'scheduled');
                $primaryCount = $results['primary']->count();
                $message = "Executed protocol #{$protocol->id} ({$protocol->name}): {$primaryCount} primary";
                if (!empty($results['warning'])) {
                    $message .= ' — ' . $results['warning'];
                    $this->warn($message);
                } else {
                    $this->info($message);
                }
                Log::info($message);
            } catch (\Throwable $e) {
                $failures++;
                $error = "Failed protocol #{$protocol->id} ({$protocol->name}): " . $e->getMessage();
                $this->error($error);
                Log::error($error);
            }
        }

        return $failures > 0 ? self::FAILURE : self::SUCCESS;
    }
}
