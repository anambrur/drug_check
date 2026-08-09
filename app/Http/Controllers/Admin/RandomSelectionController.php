<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SelectionProtocolRequest;
use App\Models\Admin\DotAgency;
use App\Models\Admin\SelectedEmployee;
use App\Models\Admin\SelectionEvent;
use App\Models\Admin\SelectionOfflineList;
use App\Models\Admin\SelectionProtocol;
use App\Models\Admin\TestAdmin;
use App\Models\Admin\ClientProfile;
use App\Services\RandomSelectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RandomSelectionController extends Controller
{
    public function __construct(
        protected RandomSelectionService $selectionService
    ) {
    }

    public function index()
    {
        $protocols = SelectionProtocol::with(['clients', 'test'])
            ->withMax('selectionEvents as last_run_at', 'selection_date')
            ->orderByDesc('created_at')
            ->get();

        $schedule = $this->selectionService->schedule();

        return view('admin.random_selection.index', compact('protocols', 'schedule'));
    }

    public function create()
    {
        $clients = ClientProfile::orderByDesc('id')->get();
        $tests = TestAdmin::orderByDesc('id')->get();
        $dotAgencies = DotAgency::orderByDesc('id')->get();

        return view('admin.random_selection.create', compact('clients', 'tests', 'dotAgencies'));
    }

    public function store(SelectionProtocolRequest $request)
    {
        DB::beginTransaction();

        try {
            $protocol = SelectionProtocol::create($request->protocolAttributes());
            $protocol->clients()->attach($request->input('client_ids'));

            foreach ((array) $request->input('extra_tests', []) as $testId) {
                $protocol->extraTests()->create(['test_id' => $testId]);
            }

            foreach ((array) $request->input('sub_selections', []) as $sub) {
                $protocol->subSelections()->create([
                    'test_id' => $sub['test_id'],
                    'requirement_type' => $sub['requirement_type'],
                    'requirement_value' => $sub['requirement_value'],
                ]);
            }

            DB::commit();

            return redirect()->route('random-selection.index')
                ->with('success', 'Protocol created successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error creating selection protocol: ' . $e->getMessage());
            toastr()->error('An error occurred while creating the protocol. Please try again.', 'content.error');

            return back()->withInput();
        }
    }

    public function execute(SelectionProtocol $protocol)
    {
        try {
            $results = $this->selectionService->executeProtocol($protocol, 'manual');

            if (!empty($results['warning'])) {
                toastr()->warning($results['warning'], 'Partial Selection');
            } else {
                toastr()->success('Protocol executed successfully.', 'Success');
            }

            return redirect()->route('random-selection.results.view', $results['event']->id);
        } catch (\Throwable $e) {
            Log::error('Random Selection Error: ' . $e->getMessage());
            toastr()->error($e->getMessage(), 'Error');

            return back();
        }
    }

    public function executions(SelectionProtocol $protocol)
    {
        $protocol->load(['clients', 'test']);

        $executions = $protocol->selectionEvents()
            ->with('selectedEmployees')
            ->orderByDesc('selection_date')
            ->paginate(10);

        $executions->getCollection()->transform(function (SelectionEvent $execution) {
            $execution->setAttribute(
                'type_counts',
                $this->selectionService->selectionTypeCounts($execution)
            );

            return $execution;
        });

        $schedule = $this->selectionService->schedule();
        $currentPoolSize = $this->selectionService->currentPoolSize($protocol);
        $clientIds = $protocol->clients->pluck('id');
        $totalActiveEmployees = \App\Models\Admin\Employee::query()
            ->where('status', 'active')
            ->whereIn('client_profile_id', $clientIds->isNotEmpty()
                ? $clientIds
                : [$protocol->client_id])
            ->count();
        $lastRun = $protocol->selectionEvents()->latest('selection_date')->first();
        $nextRun = $schedule->nextScheduledDate($protocol);

        return view('admin.random_selection.executions', [
            'protocol' => $protocol,
            'executions' => $executions,
            'schedule' => $schedule,
            'currentPoolSize' => $currentPoolSize,
            'totalActiveOnClients' => $totalActiveEmployees,
            'lastRun' => $lastRun,
            'nextRun' => $nextRun,
        ]);
    }

    public function viewResults(SelectionEvent $event)
    {
        $event->load([
            'protocol.clients',
            'protocol.test',
            'selectedEmployees.employee.clientProfile',
            'selectedEmployees.test',
            'selectedEmployees.replacementAlternate.employee',
            'selectedEmployees.alternateReplaces.employee',
            'offlineList.consumptions',
        ]);

        $groups = $this->selectionService->groupSelectionsByType($event);
        $counts = $this->selectionService->selectionTypeCounts($event);
        $emailsSent = $event->selectedEmployees->where('notification_sent', true)->count();
        $alternateMode = $this->selectionService->resolveAlternateMode($event->protocol);

        return view('admin.random_selection.execution_results', [
            'event' => $event,
            'protocol' => $event->protocol,
            'primary' => $groups['primary'],
            'extra' => $groups['extra'],
            'sub' => $groups['sub'],
            'alternates' => $groups['alternates'],
            'counts' => $counts,
            'emailsSent' => $emailsSent,
            'offlineList' => $event->offlineList,
            'alternateMode' => $alternateMode,
            'warning' => null,
        ]);
    }

    public function markExcusedOrRefused(Request $request, SelectedEmployee $selection)
    {
        $request->validate([
            'reason' => 'required|in:excused,refused',
        ]);

        try {
            $alternate = $this->selectionService->markExcusedOrRefused($selection, $request->input('reason'));
            toastr()->success(
                'Selection marked ' . $request->input('reason') . '. Alternate DonorID ' .
                ($alternate->donor_id ?: $alternate->employee?->employee_id) . ' was selected.',
                'Success'
            );

            return redirect()->route('random-selection.results.view', $selection->selection_event_id);
        } catch (\Throwable $e) {
            Log::error('On-demand alternate error: ' . $e->getMessage());
            toastr()->error($e->getMessage(), 'Error');

            return back();
        }
    }

    public function printOfflineList(SelectionEvent $event)
    {
        $event->load(['protocol', 'offlineList']);
        $list = $event->offlineList;

        if (!$list) {
            toastr()->error('No offline list exists for this selection run.', 'Error');

            return back();
        }

        $this->selectionService->markOfflineListPrinted($list);
        $list->load('consumptions');

        $employeesByDonor = \App\Models\Admin\Employee::query()
            ->with('clientProfile')
            ->whereIn('employee_id', $list->shuffled_donor_ids ?? [])
            ->get()
            ->keyBy(fn ($employee) => (string) $employee->employee_id);

        return view('admin.random_selection.offline_list_print', [
            'event' => $event,
            'protocol' => $event->protocol,
            'list' => $list->fresh(),
            'employeesByDonor' => $employeesByDonor,
        ]);
    }

    public function consumeOfflineList(Request $request, SelectionOfflineList $list)
    {
        $request->validate([
            'replaces_selected_employee_id' => 'nullable|exists:selected_employees,id',
        ]);

        try {
            $replaces = null;
            if ($request->filled('replaces_selected_employee_id')) {
                $replaces = SelectedEmployee::findOrFail($request->input('replaces_selected_employee_id'));
                if ((int) $replaces->selection_event_id !== (int) $list->selection_event_id) {
                    throw new \RuntimeException('Replacement selection must belong to the same run.');
                }
                if (!$replaces->is_excused && !$replaces->is_refused) {
                    $replaces->update([
                        'is_excused' => true,
                        'status' => 'excused',
                        'replacement_reason' => 'excused',
                    ]);
                }
            }

            $selection = $this->selectionService->consumeNextFromOfflineList($list, $replaces);
            toastr()->success(
                'Consumed offline list index ' . $selection->random_number .
                ' (DonorID ' . $selection->donor_id . ').',
                'Success'
            );

            return redirect()->route('random-selection.results.view', $list->selection_event_id);
        } catch (\Throwable $e) {
            Log::error('Offline list consume error: ' . $e->getMessage());
            toastr()->error($e->getMessage(), 'Error');

            return back();
        }
    }

    public function edit($id)
    {
        $protocol = SelectionProtocol::with(['extraTests', 'subSelections', 'clients'])->findOrFail($id);
        $clients = ClientProfile::orderByDesc('id')->get();
        $tests = TestAdmin::orderByDesc('id')->get();

        return view('admin.random_selection.edit', compact('protocol', 'clients', 'tests'));
    }

    public function update(SelectionProtocolRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $protocol = SelectionProtocol::findOrFail($id);
            $protocol->update($request->protocolAttributes());
            $protocol->clients()->sync($request->input('client_ids'));

            $protocol->extraTests()->delete();
            foreach ((array) $request->input('extra_tests', []) as $testId) {
                $protocol->extraTests()->create(['test_id' => $testId]);
            }

            $protocol->subSelections()->delete();
            foreach ((array) $request->input('sub_selections', []) as $sub) {
                $protocol->subSelections()->create([
                    'test_id' => $sub['test_id'],
                    'requirement_type' => $sub['requirement_type'],
                    'requirement_value' => $sub['requirement_value'],
                ]);
            }

            DB::commit();

            return redirect()->route('random-selection.index')
                ->with('success', 'Protocol updated successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error updating selection protocol ID ' . $id . ': ' . $e->getMessage());
            toastr()->error('An error occurred while updating the protocol. Please try again.', 'content.error');

            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        $protocol = SelectionProtocol::findOrFail($id);
        $protocol->delete();

        toastr()->success('content.deleted_successfully', 'content.success');

        return redirect()->route('random-selection.index');
    }
}
