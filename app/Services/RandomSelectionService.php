<?php

namespace App\Services;

use RuntimeException;
use App\Models\Admin\Employee;
use App\Models\Admin\TestAdmin;
use App\Models\Admin\ResultPanel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Admin\ResultRecording;
use App\Models\Admin\SelectedEmployee;
use App\Models\Admin\SelectionProtocol;
use App\Mail\EmployeeSelectedNotification;

class RandomSelectionService
{
    public function secureRand($min, $max)
    {
        if ($min > $max) {
            list($min, $max) = array($max, $min);
        }

        $diff = $max - $min;
        if ($diff < 0 || $diff > 0x7FFFFFFF) {
            throw new RuntimeException("Bad range");
        }

        $bytes = random_bytes(4);
        if ($bytes === false || strlen($bytes) != 4) {
            throw new RuntimeException("Unable to get 4 bytes");
        }

        $ary = unpack("Nint", $bytes);
        $val = $ary['int'] & 0x7FFFFFFF;
        $fp = (float) $val / 2147483647.0;
        return floor($fp * ($diff + 1)) + $min;
    }

    public function executeProtocol(SelectionProtocol $protocol)
    {
        return DB::transaction(function () use ($protocol) {
            // Get all client IDs from the protocol
            $clientIds = $protocol->clients->pluck('id')->toArray();

            // Get the FULL employee pool from MULTIPLE clients
            $fullPoolQuery = Employee::whereIn('client_profile_id', $clientIds)
                ->where('status', 'active');

            // Apply group filters
            $this->applyGroupFilters($fullPoolQuery, $protocol);

            // Apply department/shift filters
            if ($protocol->department_filter) {
                $fullPoolQuery->where('department', $protocol->department_filter);
            }

            if ($protocol->shift_filter) {
                $fullPoolQuery->where('shift', $protocol->shift_filter);
            }

            // Get the FULL pool size BEFORE exclusions
            $fullEmployeePool = $fullPoolQuery->get();
            $fullPoolSize = $fullEmployeePool->count();

            if ($fullPoolSize === 0) {
                throw new \Exception("No employees match the selection criteria");
            }

            // Calculate IDEAL selection count from FULL pool size
            $idealSelectionCount = $protocol->selection_requirement_type === 'PERCENTAGE'
                ? ceil($fullPoolSize * ($protocol->selection_requirement_value / 100))
                : $protocol->selection_requirement_value;

            // NOW apply exclusion filter to get available pool
            $availablePool = $fullEmployeePool;

            if ($protocol->exclude_previously_selected) {
                $exclusionDate = $this->getExclusionDate($protocol->selection_period);

                // Get recently tested employees based on selection_event date, not created_at
                $recentlyTested = SelectedEmployee::whereIn('employee_id', $fullEmployeePool->pluck('id'))
                    ->whereHas('selectionEvent', function ($query) use ($exclusionDate) {
                        $query->where('selection_date', '>', $exclusionDate);
                    })
                    ->pluck('employee_id')
                    ->toArray();

                $availablePool = $fullEmployeePool->reject(function ($employee) use ($recentlyTested) {
                    return in_array($employee->id, $recentlyTested);
                });
            }

            $availablePoolSize = $availablePool->count();

            // DYNAMIC ADJUSTMENT: Use whatever is available
            // Select minimum between ideal count and available pool
            $actualSelectionCount = min($idealSelectionCount, $availablePoolSize);

            // Only throw error if NO employees are available at all
            if ($availablePoolSize === 0) {
                throw new \Exception(
                    "No employees available for selection. All {$fullPoolSize} employees have been recently tested. " .
                        "Please wait for the exclusion period to expire or disable the 'exclude previously selected' option."
                );
            }

            // Log a warning if we're selecting fewer than ideal
            if ($actualSelectionCount < $idealSelectionCount) {
                Log::warning("Random Selection Partial: Protocol '{$protocol->name}' (ID: {$protocol->id}) - " .
                    "Selecting {$actualSelectionCount} of {$idealSelectionCount} employees. " .
                    "Available: {$availablePoolSize}, Full pool: {$fullPoolSize}");
            }

            // Create the selection event
            $event = $protocol->selectionEvents()->create([
                'selection_date' => now(),
                'pool_size' => $fullPoolSize,
                'selection_pool' => $availablePool->pluck('id'),
                'status' => 'PENDING'
            ]);

            // Execute primary selections with DYNAMIC count
            $primarySelections = $this->makeSelections(
                $availablePool,
                $actualSelectionCount,
                $event,
                $protocol->test_id,
                'PRIMARY'
            );

            // Execute extra test selections
            $extraSelections = collect();
            foreach ($protocol->extraTests as $extraTest) {
                $extraSelections = $extraSelections->merge(
                    $this->makeSelections(
                        $availablePool,
                        $actualSelectionCount,
                        $event,
                        $extraTest->test_id,
                        'EXTRA'
                    )
                );
            }

            // Execute sub-selections
            $subSelections = collect();
            foreach ($protocol->subSelections as $sub) {
                $subPool = $primarySelections->pluck('employee');
                $subPoolSize = $subPool->count();

                if ($subPoolSize > 0) {
                    $idealSubCount = $sub->requirement_type === 'PERCENTAGE'
                        ? ceil($subPoolSize * ($sub->requirement_value / 100))
                        : $sub->requirement_value;

                    // Dynamic adjustment for sub-selections too
                    $actualSubCount = min($idealSubCount, $subPoolSize);

                    $subSelections = $subSelections->merge(
                        $this->makeSelections(
                            $subPool,
                            $actualSubCount,
                            $event,
                            $sub->test_id,
                            'SUB'
                        )
                    );
                }
            }

            // Execute alternate selections
            $alternates = collect();
            if ($protocol->alternates_value > 0) {
                $remainingPool = $availablePool->diff($primarySelections->pluck('employee'));
                $remainingPoolSize = $remainingPool->count();

                if ($remainingPoolSize > 0) {
                    $idealAlternateCount = $protocol->alternates_type === 'PERCENTAGE'
                        ? ceil($fullPoolSize * ($protocol->alternates_value / 100))
                        : $protocol->alternates_value;

                    // Dynamic adjustment for alternates
                    $actualAlternateCount = min($idealAlternateCount, $remainingPoolSize);

                    $alternates = $this->makeSelections(
                        $remainingPool,
                        $actualAlternateCount,
                        $event,
                        $protocol->test_id,
                        'ALTERNATE'
                    );
                }
            }

            // Update event status
            $event->update(['status' => 'COMPLETED']);

            // Return results with warning if partial selection occurred
            return [
                'event' => $event,
                'primary' => $primarySelections,
                'extra' => $extraSelections,
                'sub' => $subSelections,
                'alternates' => $alternates,
                'warning' => $actualSelectionCount < $idealSelectionCount
                    ? "Only {$actualSelectionCount} of {$idealSelectionCount} employees were selected due to availability constraints."
                    : null
            ];
        });
    }

    /**
     * Apply group filters to query
     */
    protected function applyGroupFilters($query, SelectionProtocol $protocol)
    {
        if ($protocol->group === 'DOT') {
            $query->where('dot', 'yes');
        } elseif ($protocol->group === 'NON_DOT') {
            $query->where('dot', 'no');
        } elseif ($protocol->group === 'FMCSA') {
            $query->where('dot', 'FMCSA');
        } elseif ($protocol->group === 'FRA') {
            $query->where('dot', 'FRA');
        } elseif ($protocol->group === 'FTA') {
            $query->where('dot', 'FTA');
        } elseif ($protocol->group === 'FAA') {
            $query->where('dot', 'FAA');
        } elseif ($protocol->group === 'PHMSA') {
            $query->where('dot', 'PHMSA');
        } elseif ($protocol->group === 'RSPA') {
            $query->where('dot', 'RSPA');
        } elseif ($protocol->group === 'USCG') {
            $query->where('dot', 'USCG');
        } elseif ($protocol->group === 'ALL') {
            $query->whereIn('dot', ['yes', 'no', '', 'FMCSA', 'FRA', 'FTA', 'FAA', 'PHMSA', 'RSPA', 'USCG']);
        }
    }

    /**
     * Get the exclusion date based on selection period
     */
    protected function getExclusionDate($selectionPeriod)
    {
        switch ($selectionPeriod) {
            case 'YEARLY':
                return now()->subYear();
            case 'QUARTERLY':
                return now()->subMonths(3);
            case 'MONTHLY':
                return now()->subMonth();
            case 'MANUAL':
                return now()->subYear();
            default:
                return now()->subYear();
        }
    }

    protected function makeSelections($pool, $count, $event, $testId, $type)
    {
        $selections = collect();
        $poolArray = $pool->values()->all();
        $poolSize = count($poolArray);

        // Safety check: can't select more than available
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
                'selection_type' => $type,
                'random_number' => $randomNumber,
                'status' => 'pending'
            ]);

            // Create initial result recording
            $this->createInitialResultRecording($selection, $employee, $testId, $event);

            // Send notification email (only for primary selections)
            if ($type === 'PRIMARY' && $employee->email) {
                try {
                    Mail::to($employee->email)
                        ->queue(new EmployeeSelectedNotification($employee, $event->protocol));

                    $selection->update(['notification_sent' => true, 'notification_sent_at' => now()]);
                } catch (\Exception $e) {
                    Log::error("Failed to send notification to {$employee->email}: " . $e->getMessage());
                    $selection->update(['notification_sent' => false]);
                }
            }

            $selections->push($selection);
        }

        return $selections;
    }

    protected function createInitialResultRecording($selection, $employee, $testId, $event)
    {
        return DB::transaction(function () use ($selection, $employee, $testId, $event) {
            try {
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
                    'note' => 'Automatically created from random selection'
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
            } catch (\Exception $e) {
                Log::error("Error creating initial result recording: " . $e->getMessage());
                throw $e;
            }
        });
    }
}
