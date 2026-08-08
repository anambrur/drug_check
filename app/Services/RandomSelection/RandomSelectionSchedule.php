<?php

namespace App\Services\RandomSelection;

use App\Models\Admin\SelectionProtocol;
use Carbon\Carbon;

class RandomSelectionSchedule
{
    public function isDueToday(SelectionProtocol $protocol, ?Carbon $date = null): bool
    {
        $date = ($date ?? now())->copy()->startOfDay();

        if (!$protocol->is_active || !$protocol->automatic) {
            return false;
        }

        if ($protocol->exists && $this->hasRunOnDate($protocol, $date)) {
            return false;
        }

        return $this->matchesFrequencyDate($protocol, $date);
    }

    public function matchesFrequencyDate(SelectionProtocol $protocol, Carbon $date): bool
    {
        $date = $date->copy()->startOfDay();

        return match ($protocol->selection_period) {
            'YEARLY' => $date->month === 1 && $date->day === 1,
            'QUARTERLY' => in_array($date->month, [1, 4, 7, 10], true) && $date->day === 1,
            'MONTHLY' => (int) $protocol->monthly_selection_day === $date->day,
            'MANUAL' => $this->matchesManualDate($protocol, $date),
            default => false,
        };
    }

    public function hasRunOnDate(SelectionProtocol $protocol, Carbon $date): bool
    {
        if (!$protocol->exists) {
            return false;
        }

        return $protocol->selectionEvents()
            ->whereDate('selection_date', $date->toDateString())
            ->whereIn('status', ['PENDING', 'COMPLETED'])
            ->exists();
    }

    public function nextScheduledDate(SelectionProtocol $protocol, ?Carbon $from = null): ?Carbon
    {
        if (!$protocol->is_active || !$protocol->automatic) {
            return null;
        }

        $from = ($from ?? now())->copy()->startOfDay();

        return match ($protocol->selection_period) {
            'YEARLY' => $this->nextYearly($from),
            'QUARTERLY' => $this->nextQuarterly($from),
            'MONTHLY' => $this->nextMonthly($from, (int) ($protocol->monthly_selection_day ?: 1)),
            'MANUAL' => $this->nextManualDate($protocol, $from),
            default => null,
        };
    }

    public function frequencyLabel(SelectionProtocol $protocol): string
    {
        return match ($protocol->selection_period) {
            'YEARLY' => 'Yearly (Jan 1)',
            'QUARTERLY' => 'Quarterly (Jan/Apr/Jul/Oct 1)',
            'MONTHLY' => 'Monthly (day ' . ($protocol->monthly_selection_day ?: '—') . ')',
            'MANUAL' => 'Manual dates',
            default => (string) $protocol->selection_period,
        };
    }

    protected function matchesManualDate(SelectionProtocol $protocol, Carbon $date): bool
    {
        foreach ($this->manualDates($protocol) as $manualDate) {
            if ($manualDate->isSameDay($date)) {
                return true;
            }
        }

        return false;
    }

    protected function nextManualDate(SelectionProtocol $protocol, Carbon $from): ?Carbon
    {
        $upcoming = collect($this->manualDates($protocol))
            ->filter(fn (Carbon $date) => $date->greaterThanOrEqualTo($from))
            ->sortBy(fn (Carbon $date) => $date->timestamp)
            ->first();

        return $upcoming?->copy();
    }

    /**
     * @return array<int, Carbon>
     */
    protected function manualDates(SelectionProtocol $protocol): array
    {
        $raw = $protocol->manual_dates;

        if (is_string($raw)) {
            $raw = json_decode($raw, true) ?: [];
        }

        if (!is_array($raw)) {
            return [];
        }

        return collect($raw)
            ->filter()
            ->map(fn ($value) => Carbon::parse($value)->startOfDay())
            ->values()
            ->all();
    }

    protected function nextYearly(Carbon $from): Carbon
    {
        $candidate = $from->copy()->month(1)->day(1);
        if ($candidate->lessThan($from)) {
            $candidate->addYear();
        }

        return $candidate;
    }

    protected function nextQuarterly(Carbon $from): Carbon
    {
        foreach ([1, 4, 7, 10] as $month) {
            $candidate = $from->copy()->month($month)->day(1);
            if ($candidate->greaterThanOrEqualTo($from)) {
                return $candidate;
            }
        }

        return $from->copy()->addYear()->month(1)->day(1);
    }

    protected function nextMonthly(Carbon $from, int $day): Carbon
    {
        $day = max(1, min(28, $day));
        $candidate = $from->copy()->day($day);

        if ($candidate->lessThan($from)) {
            $candidate->addMonthNoOverflow()->day($day);
        }

        return $candidate;
    }
}
