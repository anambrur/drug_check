<?php

namespace Tests\Unit\Services\RandomSelection;

use App\Models\Admin\SelectionProtocol;
use App\Services\RandomSelection\RandomSelectionSchedule;
use Carbon\Carbon;
use Tests\TestCase;

class RandomSelectionScheduleTest extends TestCase
{
    protected RandomSelectionSchedule $schedule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->schedule = new RandomSelectionSchedule();
    }

    public function test_monthly_matches_configured_day(): void
    {
        $protocol = new SelectionProtocol([
            'is_active' => true,
            'automatic' => true,
            'selection_period' => 'MONTHLY',
            'monthly_selection_day' => 15,
        ]);

        $this->assertTrue($this->schedule->matchesFrequencyDate($protocol, Carbon::parse('2026-03-15')));
        $this->assertFalse($this->schedule->matchesFrequencyDate($protocol, Carbon::parse('2026-03-16')));
    }

    public function test_yearly_matches_january_first(): void
    {
        $protocol = new SelectionProtocol([
            'is_active' => true,
            'automatic' => true,
            'selection_period' => 'YEARLY',
        ]);

        $this->assertTrue($this->schedule->matchesFrequencyDate($protocol, Carbon::parse('2026-01-01')));
        $this->assertFalse($this->schedule->matchesFrequencyDate($protocol, Carbon::parse('2026-01-02')));
    }

    public function test_quarterly_matches_quarter_starts(): void
    {
        $protocol = new SelectionProtocol([
            'is_active' => true,
            'automatic' => true,
            'selection_period' => 'QUARTERLY',
        ]);

        foreach (['2026-01-01', '2026-04-01', '2026-07-01', '2026-10-01'] as $date) {
            $this->assertTrue($this->schedule->matchesFrequencyDate($protocol, Carbon::parse($date)), $date);
        }

        $this->assertFalse($this->schedule->matchesFrequencyDate($protocol, Carbon::parse('2026-02-01')));
    }

    public function test_manual_matches_listed_dates(): void
    {
        $protocol = new SelectionProtocol([
            'is_active' => true,
            'automatic' => true,
            'selection_period' => 'MANUAL',
            'manual_dates' => ['2026-05-10', '2026-08-20'],
        ]);

        $this->assertTrue($this->schedule->matchesFrequencyDate($protocol, Carbon::parse('2026-05-10')));
        $this->assertTrue($this->schedule->matchesFrequencyDate($protocol, Carbon::parse('2026-08-20')));
        $this->assertFalse($this->schedule->matchesFrequencyDate($protocol, Carbon::parse('2026-05-11')));
    }

    public function test_inactive_or_non_automatic_is_not_due(): void
    {
        $inactive = new SelectionProtocol([
            'is_active' => false,
            'automatic' => true,
            'selection_period' => 'YEARLY',
        ]);

        $manualOnly = new SelectionProtocol([
            'is_active' => true,
            'automatic' => false,
            'selection_period' => 'YEARLY',
        ]);

        $this->assertFalse($this->schedule->isDueToday($inactive, Carbon::parse('2026-01-01')));
        $this->assertFalse($this->schedule->isDueToday($manualOnly, Carbon::parse('2026-01-01')));
    }

    public function test_next_scheduled_date_for_monthly(): void
    {
        $protocol = new SelectionProtocol([
            'is_active' => true,
            'automatic' => true,
            'selection_period' => 'MONTHLY',
            'monthly_selection_day' => 10,
        ]);

        $next = $this->schedule->nextScheduledDate($protocol, Carbon::parse('2026-03-11'));
        $this->assertSame('2026-04-10', $next->toDateString());
    }
}
