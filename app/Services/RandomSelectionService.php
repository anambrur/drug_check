<?php

namespace App\Services;

use App\Mail\EmployeeSelectedNotification;
use App\Models\Admin\Employee;
use App\Models\Admin\ResultPanel;
use App\Models\Admin\ResultRecording;
use App\Models\Admin\SelectedEmployee;
use App\Models\Admin\SelectionEvent;
use App\Models\Admin\SelectionProtocol;
use App\Models\Admin\TestAdmin;
use App\Services\RandomSelection\RandomSelectionSchedule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class RandomSelectionService
{
    public function __construct(
        protected RandomSelectionSchedule $schedule
    ) {
    }

    public function schedule(): RandomSelectionSchedule
    {
        return $this->schedule;
    }

    public function secureRand(int $min, int $max): int
    {
        if ($min > $max) {
            [$min, $max] = [$max, $min];
        }

        $diff = $max - $min;
        if ($diff < 0 || $diff > 0x7FFFFFFF) {
            throw new RuntimeException('Bad range');
        }

        $bytes = random_bytes(4);
        if ($bytes === false || strlen($bytes) !== 4) {
            throw new RuntimeException('Unable to get 4 bytes');
        }

        $ary = unpack('Nint', $bytes);
        $val = $ary['int'] & 0x7FFFFFFF;
        $fp = (float) $val / 2147483647.0;

        return (int) (floor($fp * ($diff + 1)) + $min);
    }

    /**
     * @return array{
     *     event: SelectionEvent,
     *     primary: Collection,
     *     extra: Collection,
     *     sub: Collection,
     *     alternates: Collection,
     *     warning: ?string
     * }
     */
    public function executeProtocol(SelectionProtocol $protocol, string $trigger = 'manual'): array
    {
        $protocol->loadMissing(['clients', 'extraTests', 'subSelections', 'test']);

        if (!$protocol->is_active) {
            throw new RuntimeException('This protocol is inactive and cannot be executed.');
        }

        $pendingEmails = [];

        $result = DB::transaction(function () use ($protocol, $trigger, &$pendingEmails) {
            $availablePool = $this->buildAvailablePool($protocol);
            $fullPoolSize = $availablePool['full_pool_size'];
            $pool = $availablePool['available_pool'];
            $availablePoolSize = $pool->count();
            $idealSelectionCount = $availablePool['ideal_count'];
            $actualSelectionCount = min($idealSelectionCount, $availablePoolSize);

            if ($availablePoolSize === 0) {
                throw new RuntimeException(
                    "No employees available for selection. All {$fullPoolSize} employees have been recently tested. " .
                    "Please wait for the exclusion period to expire or disable the 'exclude previously selected' option."
                );
            }

            if ($actualSelectionCount < $idealSelectionCount) {
                Log::warning(
                    "Random Selection Partial: Protocol '{$protocol->name}' (ID: {$protocol->id}) - " .
                    "Selecting {$actualSelectionCount} of {$idealSelectionCount} employees. " .
                    "Available: {$availablePoolSize}, Full pool: {$fullPoolSize}"
                );
            }

            $event = $protocol->selectionEvents()->create([
                'selection_date' => now(),
                'pool_size' => $fullPoolSize,
                'selection_pool' => $pool->pluck('id')->values()->all(),
                'status' => 'PENDING',
                'trigger' => in_array($trigger, ['manual', 'scheduled'], true) ? $trigger : 'manual',
            ]);
            $event->setRelation('protocol', $protocol);

            $primarySelections = $this->makeSelections(
                $pool,
                $actualSelectionCount,
                $event,
                $protocol->test_id,
                'PRIMARY',
                $pendingEmails
            );

            $extraSelections = collect();
            foreach ($protocol->extraTests as $extraTest) {
                $extraSelections = $extraSelections->merge(
                    $this->makeSelections(
                        $pool,
                        $actualSelectionCount,
                        $event,
                        $extraTest->test_id,
                        'EXTRA',
                        $pendingEmails
                    )
                );
            }

            $subSelections = collect();
            foreach ($protocol->subSelections as $sub) {
                $subPool = $primarySelections->pluck('employee')->filter();
                $subPoolSize = $subPool->count();

                if ($subPoolSize === 0) {
                    continue;
                }

                $idealSubCount = $sub->requirement_type === 'PERCENTAGE'
                    ? (int) ceil($subPoolSize * ($sub->requirement_value / 100))
                    : (int) $sub->requirement_value;

                $subSelections = $subSelections->merge(
                    $this->makeSelections(
                        $subPool,
                        min($idealSubCount, $subPoolSize),
                        $event,
                        $sub->test_id,
                        'SUB',
                        $pendingEmails
                    )
                );
            }

            $alternates = collect();
            if ((int) $protocol->alternates_value > 0) {
                $primaryEmployeeIds = $primarySelections->pluck('employee_id')->all();
                $remainingPool = $pool->reject(fn ($employee) => in_array($employee->id, $primaryEmployeeIds, true))->values();
                $remainingPoolSize = $remainingPool->count();

                if ($remainingPoolSize > 0) {
                    $idealAlternateCount = $protocol->alternates_type === 'PERCENTAGE'
                        ? (int) ceil($fullPoolSize * ($protocol->alternates_value / 100))
                        : (int) $protocol->alternates_value;

                    $alternates = $this->makeSelections(
                        $remainingPool,
                        min($idealAlternateCount, $remainingPoolSize),
                        $event,
                        $protocol->test_id,
                        'ALTERNATE',
                        $pendingEmails
                    );
                }
            }

            $event->update(['status' => 'COMPLETED']);

            return [
                'event' => $event->fresh(['protocol', 'selectedEmployees.employee.clientProfile', 'selectedEmployees.test']),
                'primary' => $primarySelections,
                'extra' => $extraSelections,
                'sub' => $subSelections,
                'alternates' => $alternates,
                'warning' => $actualSelectionCount < $idealSelectionCount
                    ? "Only {$actualSelectionCount} of {$idealSelectionCount} employees were selected due to availability constraints."
                    : null,
            ];
        });

        $this->dispatchPendingEmails($pendingEmails);

        return $result;
    }

    public function currentPoolSize(SelectionProtocol $protocol): int
    {
        return $this->fullPoolQuery($protocol)->count();
    }

    /**
     * @return array{full_pool: Collection, full_pool_size: int, available_pool: Collection, ideal_count: int}
     */
    public function buildAvailablePool(SelectionProtocol $protocol): array
    {
        $fullEmployeePool = $this->fullPoolQuery($protocol)->get();
        $fullPoolSize = $fullEmployeePool->count();

        if ($fullPoolSize === 0) {
            throw new RuntimeException('No employees match the selection criteria');
        }

        $idealSelectionCount = $protocol->selection_requirement_type === 'PERCENTAGE'
            ? (int) ceil($fullPoolSize * ($protocol->selection_requirement_value / 100))
            : (int) $protocol->selection_requirement_value;

        $availablePool = $fullEmployeePool;

        if ($protocol->exclude_previously_selected) {
            $exclusionDate = $this->getExclusionDate($protocol->selection_period);

            $recentlySelected = SelectedEmployee::query()
                ->whereIn('employee_id', $fullEmployeePool->pluck('id'))
                ->whereHas('selectionEvent', function ($query) use ($protocol, $exclusionDate) {
                    $query->where('selection_protocol_id', $protocol->id)
                        ->where('selection_date', '>', $exclusionDate);
                })
                ->pluck('employee_id')
                ->all();

            $availablePool = $fullEmployeePool->reject(
                fn ($employee) => in_array($employee->id, $recentlySelected, true)
            )->values();
        }

        return [
            'full_pool' => $fullEmployeePool,
            'full_pool_size' => $fullPoolSize,
            'available_pool' => $availablePool,
            'ideal_count' => $idealSelectionCount,
        ];
    }

    public function groupSelectionsByType(SelectionEvent $event): array
    {
        $event->loadMissing(['selectedEmployees.employee.clientProfile', 'selectedEmployees.test']);

        $groups = [
            'primary' => collect(),
            'extra' => collect(),
            'sub' => collect(),
            'alternates' => collect(),
        ];

        foreach ($event->selectedEmployees as $selection) {
            $key = match ($selection->selection_type) {
                'PRIMARY' => 'primary',
                'EXTRA' => 'extra',
                'SUB' => 'sub',
                'ALTERNATE' => 'alternates',
                default => null,
            };

            if ($key !== null) {
                $groups[$key]->push($selection);
            }
        }

        return $groups;
    }

    public function selectionTypeCounts(SelectionEvent $event): array
    {
        $counts = [
            'primary' => 0,
            'extra' => 0,
            'sub' => 0,
            'alternate' => 0,
            'total' => 0,
        ];

        foreach ($event->selectedEmployees as $selection) {
            $counts['total']++;
            match ($selection->selection_type) {
                'PRIMARY' => $counts['primary']++,
                'EXTRA' => $counts['extra']++,
                'SUB' => $counts['sub']++,
                'ALTERNATE' => $counts['alternate']++,
                default => null,
            };
        }

        return $counts;
    }

    protected function fullPoolQuery(SelectionProtocol $protocol)
    {
        $protocol->loadMissing('clients');
        $clientIds = $protocol->clients->pluck('id')->all();

        if ($clientIds === [] && $protocol->client_id) {
            $clientIds = [$protocol->client_id];
        }

        $query = Employee::query()
            ->whereIn('client_profile_id', $clientIds)
            ->where('status', 'active');

        $this->applyGroupFilters($query, $protocol);

        if ($protocol->department_filter) {
            $query->where('department', $protocol->department_filter);
        }

        if ($protocol->shift_filter) {
            $query->where('shift', $protocol->shift_filter);
        }

        return $query;
    }

    protected function applyGroupFilters($query, SelectionProtocol $protocol): void
    {
        match ($protocol->group) {
            'DOT' => $query->where('dot', 'yes'),
            'NON_DOT' => $query->where('dot', 'no'),
            'FMCSA' => $query->where('dot', 'FMCSA'),
            'FRA' => $query->where('dot', 'FRA'),
            'FTA' => $query->where('dot', 'FTA'),
            'FAA' => $query->where('dot', 'FAA'),
            'PHMSA' => $query->where('dot', 'PHMSA'),
            'RSPA' => $query->where('dot', 'RSPA'),
            'USCG' => $query->where('dot', 'USCG'),
            'ALL' => $query->whereIn('dot', ['yes', 'no', '', 'FMCSA', 'FRA', 'FTA', 'FAA', 'PHMSA', 'RSPA', 'USCG']),
            default => null,
        };
    }

    protected function getExclusionDate(string $selectionPeriod)
    {
        return match ($selectionPeriod) {
            'YEARLY', 'MANUAL' => now()->subYear(),
            'QUARTERLY' => now()->subMonths(3),
            'MONTHLY' => now()->subMonth(),
            default => now()->subYear(),
        };
    }

    /**
     * @param  Collection<int, Employee>  $pool
     * @param  array<int, array{selection: SelectedEmployee, employee: Employee, protocol: SelectionProtocol}>  $pendingEmails
     * @return Collection<int, SelectedEmployee>
     */
    protected function makeSelections(
        Collection $pool,
        int $count,
        SelectionEvent $event,
        int $testId,
        string $type,
        array &$pendingEmails
    ): Collection {
        $selections = collect();
        $poolArray = $pool->values()->all();
        $poolSize = count($poolArray);
        $actualCount = min($count, $poolSize);

        if ($actualCount === 0) {
            return $selections;
        }

        $selectedNumbers = [];

        for ($x = 0; $x < $actualCount; $x++) {
            do {
                $randomNumber = $this->secureRand(0, $poolSize - 1);
            } while (isset($selectedNumbers[$randomNumber]));

            $selectedNumbers[$randomNumber] = true;
            $employee = $poolArray[$randomNumber];

            $selection = $event->selectedEmployees()->create([
                'employee_id' => $employee->id,
                'test_id' => $testId,
                'selection_protocol_id' => $event->selection_protocol_id,
                'selection_type' => $type,
                'random_number' => $randomNumber,
                'status' => 'pending',
            ]);

            $selection->setRelation('employee', $employee);
            $selection->setRelation('test', TestAdmin::find($testId));

            $this->createInitialResultRecording($selection, $employee, $testId, $event);

            if ($type === 'PRIMARY' && $employee->email && $event->protocol?->is_email_send) {
                $pendingEmails[] = [
                    'selection' => $selection,
                    'employee' => $employee,
                    'protocol' => $event->protocol,
                ];
            }

            $selections->push($selection);
        }

        return $selections;
    }

    protected function createInitialResultRecording(
        SelectedEmployee $selection,
        Employee $employee,
        int $testId,
        SelectionEvent $event
    ): ResultRecording {
        $testAdmin = TestAdmin::with('panel')->findOrFail($testId);

        $result = ResultRecording::create([
            'company_id' => $employee->client_profile_id,
            'employee_id' => $employee->id,
            'test_admin_id' => $testId,
            'selection_event_id' => $event->id,
            'selected_employee_id' => $selection->id,
            'reason_for_test' => 'Random Selection',
            'collection_datetime' => now(),
            'date_of_collection' => now()->format('Y-m-d'),
            'time_of_collection' => now()->format('H:i'),
            'status' => 'pending',
            'note' => 'Automatically created from random selection',
        ]);

        foreach ($testAdmin->panel as $panel) {
            ResultPanel::create([
                'result_id' => $result->id,
                'panel_id' => $panel->id,
                'drug_name' => $panel->drug_name,
                'drug_code' => $panel->drug_code,
                'result' => null,
                'cut_off_level' => $panel->cut_off_level,
                'conf_level' => $panel->conf_level,
            ]);
        }

        return $result;
    }

    /**
     * @param  array<int, array{selection: SelectedEmployee, employee: Employee, protocol: SelectionProtocol}>  $pendingEmails
     */
    protected function dispatchPendingEmails(array $pendingEmails): void
    {
        foreach ($pendingEmails as $item) {
            try {
                Mail::to($item['employee']->email)
                    ->send(new EmployeeSelectedNotification($item['employee'], $item['protocol']));

                $item['selection']->update([
                    'notification_sent' => true,
                    'notification_sent_at' => now(),
                ]);
            } catch (\Throwable $e) {
                Log::error('Failed to send notification to ' . $item['employee']->email . ': ' . $e->getMessage());
                $item['selection']->update(['notification_sent' => false]);
            }
        }
    }
}
