<?php

namespace App\Services;

use RuntimeException;
use App\Models\admin\Employee;
use App\Models\Admin\TestAdmin;
use App\Models\Admin\ResultPanel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Admin\ResultRecording;
use App\Models\Admin\SelectedEmployee;
use App\Models\Admin\SelectionProtocol;

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
            // Get the employee pool
            $poolQuery = Employee::where('client_profile_id', $protocol->client_id)->where('status', 'active');

            // Apply group filters
            if ($protocol->group === 'DOT') {
                $poolQuery->where('is_dot', true);
            } elseif ($protocol->group === 'NON_DOT') {
                $poolQuery->where('is_dot', false);
            } elseif ($protocol->group === 'DOT_AGENCY') {
                $poolQuery->where('dot_agency_id', $protocol->dot_agency_id);
            }

            // Apply department/shift filters
            if ($protocol->department_filter) {
                $poolQuery->where('department', $protocol->department_filter);
            }

            if ($protocol->shift_filter) {
                $poolQuery->where('shift', $protocol->shift_filter);
            }

            // Exclude previously selected if configured
            if ($protocol->exclude_previously_selected) {
                $recentlyTested = SelectedEmployee::whereIn('employee_id', $poolQuery->pluck('id'))
                    ->where('created_at', '>', now()->subYear())
                    ->pluck('employee_id');
                $poolQuery->whereNotIn('id', $recentlyTested);
            }

            $employeePool = $poolQuery->get();
            $poolSize = $employeePool->count();


            if ($poolSize === 0) {
                throw new \Exception("No employees match the selection criteria");
            }

            // Calculate how many to select
            $selectionCount = $protocol->selection_requirement_type === 'PERCENTAGE'
                ? ceil($poolSize * ($protocol->selection_requirement_value / 100))
                : $protocol->selection_requirement_value;

            // Create the selection event
            $event = $protocol->selectionEvents()->create([
                'selection_date' => now(),
                'pool_size' => $poolSize,
                'selection_pool' => $employeePool->pluck('id'),
                'status' => 'PENDING'
            ]);

            // Execute primary selections
            $primarySelections = $this->makeSelections(
                $employeePool,
                $selectionCount,
                $event,
                $protocol->test_id,
                'PRIMARY'
            );

            // Execute extra test selections
            $extraSelections = collect();
            foreach ($protocol->extraTests as $extraTest) {
                $extraSelections = $extraSelections->merge(
                    $this->makeSelections(
                        $employeePool,
                        $selectionCount,
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
                $subCount = $sub->requirement_type === 'PERCENTAGE'
                    ? ceil($subPool->count() * ($sub->requirement_value / 100))
                    : $sub->requirement_value;

                $subSelections = $subSelections->merge(
                    $this->makeSelections(
                        $subPool,
                        $subCount,
                        $event,
                        $sub->test_id,
                        'SUB'
                    )
                );
            }

            // Execute alternate selections
            $alternates = collect();
            if ($protocol->alternates_value > 0) {
                $alternateCount = $protocol->alternates_type === 'PERCENTAGE'
                    ? ceil($poolSize * ($protocol->alternates_value / 100))
                    : $protocol->alternates_value;

                $alternates = $this->makeSelections(
                    $employeePool->diff($primarySelections->pluck('employee')),
                    $alternateCount,
                    $event,
                    $protocol->test_id,
                    'ALTERNATE'
                );
            }

            return [
                'event' => $event,
                'primary' => $primarySelections,
                'extra' => $extraSelections,
                'sub' => $subSelections,
                'alternates' => $alternates
            ];
        });
    }

    protected function makeSelections($pool, $count, $event, $testId, $type)
    {
        $selections = collect();
        $poolArray = $pool->values()->all();
        $poolSize = count($poolArray);
        $selectedNumbers = [];

        for ($x = 0; $x < $count && $x < $poolSize; $x++) {
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

            $selections->push($selection);
        }

        return $selections;
    }


    protected function createInitialResultRecording($selection, $employee, $testId, $event)
    {
        return DB::transaction(function () use ($selection, $employee, $testId, $event) {
            try {
                $testAdmin = TestAdmin::with('panel')->findOrFail($testId);

                // Create the main result recording
                $result = ResultRecording::create([
                    'company_id' => $event->protocol->client_id,
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

                // Create panel results for each panel associated with the test
                foreach ($testAdmin->panel as $panel) {
                    ResultPanel::create([
                        'result_id' => $result->id,
                        'panel_id' => $panel->id,
                        'drug_name' => $panel->drug_name,
                        'drug_code' => $panel->drug_code,
                        'result' => null, // Initially null until results are entered
                        'cut_off_level' => $panel->cut_off_level,
                        'conf_level' => $panel->conf_level,
                    ]);
                }

                return $result;
            } catch (\Exception $e) {
                Log::error("Error creating initial result recording: " . $e->getMessage());
                throw $e; // Re-throw for handling at higher level
            }
        });
    }
}
