<?php

namespace App\Services;

use App\Mail\EmployeeSelectedNotification;
use App\Models\Admin\Employee;
use App\Models\Admin\ResultPanel;
use App\Models\Admin\ResultRecording;
use App\Models\Admin\SelectedEmployee;
use App\Models\Admin\SelectionEvent;
use App\Models\Admin\SelectionOfflineList;
use App\Models\Admin\SelectionOfflineListConsumption;
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
    public const ALTERNATE_MODE_IMMEDIATE = 'immediate';

    public const ALTERNATE_MODE_ON_DEMAND = 'on_demand';

    public const ALTERNATE_MODE_OFFLINE_LIST = 'offline_list';

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
     *     offline_list: ?SelectionOfflineList,
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
        $alternateMode = $this->resolveAlternateMode($protocol);

        $result = DB::transaction(function () use ($protocol, $trigger, $alternateMode, &$pendingEmails) {
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
                'selection_pool' => $this->donorIdsFromEmployees($pool),
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
                $subPool = $primarySelections->pluck('employee')->filter()->values();
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
            if ($alternateMode === self::ALTERNATE_MODE_IMMEDIATE && (int) $protocol->alternates_value > 0) {
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

                    if ($protocol->randomize_alternate_print_order !== false) {
                        $this->assignRandomizedPrintOrder($alternates);
                    }
                }
            }

            $offlineList = null;
            if ($alternateMode === self::ALTERNATE_MODE_OFFLINE_LIST) {
                $offlineList = $this->createOfflineList($event, $protocol, $availablePool['full_pool']);
            }

            $event->update(['status' => 'COMPLETED']);

            return [
                'event' => $event->fresh([
                    'protocol',
                    'selectedEmployees.employee.clientProfile',
                    'selectedEmployees.test',
                    'offlineList',
                ]),
                'primary' => $primarySelections,
                'extra' => $extraSelections,
                'sub' => $subSelections,
                'alternates' => $alternates,
                'offline_list' => $offlineList,
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

        $this->assertUniqueDonorIds($fullEmployeePool);

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

        if ($event->protocol?->randomize_alternate_print_order !== false) {
            $groups['alternates'] = $groups['alternates']
                ->sortBy(fn (SelectedEmployee $selection) => $selection->print_order ?? $selection->id)
                ->values();
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

    /**
     * Mark a primary selection excused/refused and draw an on-demand alternate (mode b).
     */
    public function markExcusedOrRefused(SelectedEmployee $original, string $reason): SelectedEmployee
    {
        $reason = strtolower($reason);
        if (!in_array($reason, ['excused', 'refused'], true)) {
            throw new RuntimeException('Replacement reason must be excused or refused.');
        }

        return DB::transaction(function () use ($original, $reason) {
            $original->loadMissing([
                'selectionEvent.protocol.clients',
                'employee',
                'replacementAlternate',
                'resultRecording',
            ]);

            $event = $original->selectionEvent;
            $protocol = $event?->protocol;

            if (!$event || !$protocol) {
                throw new RuntimeException('Selection event or protocol not found.');
            }

            if ($this->resolveAlternateMode($protocol) !== self::ALTERNATE_MODE_ON_DEMAND) {
                throw new RuntimeException('This protocol is not configured for on-demand alternates.');
            }

            if ($original->selection_type !== 'PRIMARY') {
                throw new RuntimeException('Only primary selections can be excused or refused for replacement.');
            }

            if ($original->is_excused || $original->is_refused || $original->replacementAlternate) {
                throw new RuntimeException('This selection already has a replacement or has already been marked.');
            }

            $alternate = $this->selectOnDemandAlternate($original, $reason);

            $original->update([
                'is_excused' => $reason === 'excused',
                'is_refused' => $reason === 'refused',
                'status' => $reason,
                'replacement_reason' => $reason,
            ]);

            if ($original->resultRecording) {
                $original->resultRecording->update(['status' => $reason]);
            }

            return $alternate;
        });
    }

    public function selectOnDemandAlternate(SelectedEmployee $original, string $reason): SelectedEmployee
    {
        $original->loadMissing(['selectionEvent.protocol', 'employee']);
        $event = $original->selectionEvent;
        $protocol = $event->protocol;

        $fullPool = $this->fullPoolQuery($protocol)->get();
        $this->assertUniqueDonorIds($fullPool);

        $alreadySelectedIds = $event->selectedEmployees()->pluck('employee_id')->all();
        $remaining = $fullPool->reject(
            fn (Employee $employee) => in_array($employee->id, $alreadySelectedIds, true)
        )->values();

        if ($remaining->isEmpty()) {
            throw new RuntimeException('No employees remain available for an on-demand alternate.');
        }

        $companyId = $original->employee?->client_profile_id;
        $sameCompany = $remaining->filter(
            fn (Employee $employee) => $companyId && $employee->client_profile_id === $companyId
        )->values();

        $drawPool = $sameCompany->isNotEmpty() ? $sameCompany : $remaining;
        $pendingEmails = [];

        $selections = $this->makeSelections(
            $drawPool,
            1,
            $event,
            $original->test_id,
            'ALTERNATE',
            $pendingEmails,
            [
                'alternate_replaces_id' => $original->id,
                'replacement_reason' => $reason,
            ]
        );

        $alternate = $selections->first();
        if (!$alternate) {
            throw new RuntimeException('Failed to select an on-demand alternate.');
        }

        $this->dispatchPendingEmails($pendingEmails);

        return $alternate->fresh(['employee.clientProfile', 'test', 'alternateReplaces']);
    }

    public function createOfflineList(
        SelectionEvent $event,
        SelectionProtocol $protocol,
        Collection $fullPool
    ): SelectionOfflineList {
        $donorIds = $this->donorIdsFromEmployees($fullPool);
        $shuffled = $this->secureShuffle($donorIds);

        return SelectionOfflineList::create([
            'selection_event_id' => $event->id,
            'selection_protocol_id' => $protocol->id,
            'shuffled_donor_ids' => $shuffled,
            'cursor' => 0,
            'is_single_use' => true,
            'printed_at' => null,
        ]);
    }

    public function markOfflineListPrinted(SelectionOfflineList $list): SelectionOfflineList
    {
        if ($list->printed_at && $list->is_single_use) {
            return $list;
        }

        $list->update(['printed_at' => now()]);

        return $list->fresh();
    }

    /**
     * Consume the next unused donor from the offline shuffled list as an alternate replacement.
     */
    public function consumeNextFromOfflineList(
        SelectionOfflineList $list,
        ?SelectedEmployee $replaces = null
    ): SelectedEmployee {
        return DB::transaction(function () use ($list, $replaces) {
            $list = SelectionOfflineList::query()->lockForUpdate()->findOrFail($list->id);
            $list->loadMissing(['event.protocol.clients', 'event.selectedEmployees']);

            $event = $list->event;
            $protocol = $event->protocol;
            $shuffled = array_values($list->shuffled_donor_ids ?? []);
            $cursor = (int) $list->cursor;

            if ($cursor >= count($shuffled)) {
                throw new RuntimeException('This offline list has been fully consumed.');
            }

            $clientIds = $protocol->clients->pluck('id')->all();
            if ($clientIds === [] && $protocol->client_id) {
                $clientIds = [$protocol->client_id];
            }

            $alreadySelectedIds = $event->selectedEmployees()->pluck('employee_id')->all();
            $employee = null;
            $listIndex = $cursor;

            while ($listIndex < count($shuffled)) {
                $donorId = (string) $shuffled[$listIndex];
                $candidate = Employee::query()
                    ->where('employee_id', $donorId)
                    ->whereIn('client_profile_id', $clientIds)
                    ->where('status', 'active')
                    ->first();

                if ($candidate && !in_array($candidate->id, $alreadySelectedIds, true)) {
                    $employee = $candidate;
                    break;
                }

                $listIndex++;
            }

            if (!$employee) {
                $list->update(['cursor' => count($shuffled)]);
                throw new RuntimeException('No unused employees remain on this offline list.');
            }

            $drawPool = array_slice($shuffled, 0);
            $pendingEmails = [];
            $extra = [
                'donor_id' => (string) $employee->employee_id,
                'draw_pool' => $drawPool,
                'pool_range_max' => max(0, count($drawPool) - 1),
                'random_number' => $listIndex,
                'alternate_replaces_id' => $replaces?->id,
                'replacement_reason' => $replaces
                    ? ($replaces->is_refused ? 'refused' : ($replaces->is_excused ? 'excused' : 'offline_list'))
                    : 'offline_list',
            ];

            $selection = $event->selectedEmployees()->create(array_merge([
                'employee_id' => $employee->id,
                'test_id' => $protocol->test_id,
                'selection_protocol_id' => $protocol->id,
                'selection_type' => 'ALTERNATE',
                'status' => 'pending',
            ], $extra));

            $selection->setRelation('employee', $employee);
            $selection->setRelation('test', TestAdmin::find($protocol->test_id));
            $this->createInitialResultRecording($selection, $employee, $protocol->test_id, $event);

            if ($replaces) {
                $reason = $replaces->is_refused
                    ? 'refused'
                    : ($replaces->is_excused ? 'excused' : 'excused');

                $replaces->update([
                    'is_excused' => $reason === 'excused',
                    'is_refused' => $reason === 'refused',
                    'status' => $reason,
                    'replacement_reason' => $reason,
                ]);

                $replaces->loadMissing('resultRecording');
                if ($replaces->resultRecording) {
                    $replaces->resultRecording->update(['status' => $reason]);
                }
            }

            SelectionOfflineListConsumption::create([
                'selection_offline_list_id' => $list->id,
                'list_index' => $listIndex,
                'donor_id' => (string) $employee->employee_id,
                'employee_id' => $employee->id,
                'selected_employee_id' => $selection->id,
                'replaces_selected_employee_id' => $replaces?->id,
                'consumed_at' => now(),
            ]);

            $list->update(['cursor' => $listIndex + 1]);

            return $selection->fresh(['employee.clientProfile', 'test']);
        });
    }

    public function resolveAlternateMode(SelectionProtocol $protocol): string
    {
        $mode = $protocol->alternate_mode ?: self::ALTERNATE_MODE_IMMEDIATE;

        return in_array($mode, [
            self::ALTERNATE_MODE_IMMEDIATE,
            self::ALTERNATE_MODE_ON_DEMAND,
            self::ALTERNATE_MODE_OFFLINE_LIST,
        ], true) ? $mode : self::ALTERNATE_MODE_IMMEDIATE;
    }

    /**
     * @param  Collection<int, Employee>  $employees
     * @return array<int, string>
     */
    public function donorIdsFromEmployees(Collection $employees): array
    {
        return $employees->values()->map(
            fn (Employee $employee) => (string) $employee->employee_id
        )->all();
    }

    /**
     * @param  array<int, mixed>  $items
     * @return array<int, mixed>
     */
    public function secureShuffle(array $items): array
    {
        $items = array_values($items);
        $count = count($items);

        for ($i = $count - 1; $i > 0; $i--) {
            $j = $this->secureRand(0, $i);
            [$items[$i], $items[$j]] = [$items[$j], $items[$i]];
        }

        return $items;
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
     */
    protected function assertUniqueDonorIds(Collection $pool): void
    {
        $donorIds = $pool->map(fn (Employee $employee) => (string) $employee->employee_id);
        $duplicates = $donorIds->duplicates()->unique()->values();

        if ($duplicates->isNotEmpty()) {
            throw new RuntimeException(
                'Duplicate DonorIDs found in the selection pool: ' . $duplicates->implode(', ') .
                '. Each employee in the pool must have a unique DonorID before a random selection can run.'
            );
        }
    }

    /**
     * @param  Collection<int, Employee>  $pool
     * @param  array<int, array{selection: SelectedEmployee, employee: Employee, protocol: SelectionProtocol}>  $pendingEmails
     * @param  array<string, mixed>  $extraAttributes
     * @return Collection<int, SelectedEmployee>
     */
    protected function makeSelections(
        Collection $pool,
        int $count,
        SelectionEvent $event,
        int $testId,
        string $type,
        array &$pendingEmails,
        array $extraAttributes = []
    ): Collection {
        $selections = collect();
        $poolArray = $pool->values()->all();
        $poolSize = count($poolArray);
        $actualCount = min($count, $poolSize);

        if ($actualCount === 0) {
            return $selections;
        }

        $drawPool = $this->donorIdsFromEmployees(collect($poolArray));
        $poolRangeMax = max(0, $poolSize - 1);
        $selectedNumbers = [];

        for ($x = 0; $x < $actualCount; $x++) {
            do {
                $randomNumber = $this->secureRand(0, $poolSize - 1);
            } while (isset($selectedNumbers[$randomNumber]));

            $selectedNumbers[$randomNumber] = true;
            $employee = $poolArray[$randomNumber];

            $selection = $event->selectedEmployees()->create(array_merge([
                'employee_id' => $employee->id,
                'donor_id' => (string) $employee->employee_id,
                'test_id' => $testId,
                'selection_protocol_id' => $event->selection_protocol_id,
                'selection_type' => $type,
                'random_number' => $randomNumber,
                'draw_pool' => $drawPool,
                'pool_range_max' => $poolRangeMax,
                'status' => 'pending',
            ], $extraAttributes));

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

    /**
     * @param  Collection<int, SelectedEmployee>  $alternates
     */
    protected function assignRandomizedPrintOrder(Collection $alternates): void
    {
        if ($alternates->isEmpty()) {
            return;
        }

        $ids = $alternates->pluck('id')->all();
        $shuffledIds = $this->secureShuffle($ids);

        foreach ($shuffledIds as $order => $id) {
            SelectedEmployee::whereKey($id)->update(['print_order' => $order + 1]);
            $match = $alternates->firstWhere('id', $id);
            if ($match) {
                $match->print_order = $order + 1;
            }
        }
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
