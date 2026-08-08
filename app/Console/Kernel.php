<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('quest:retry-failed-submissions')
            ->everyFiveMinutes()
            ->withoutOverlapping();

        if (config('random_selection.enabled', true)) {
            $schedule->command('random-selection:run-due')
                ->dailyAt(config('random_selection.run_at', '01:00'))
                ->withoutOverlapping();
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
