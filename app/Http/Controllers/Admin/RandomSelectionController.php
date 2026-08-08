<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SelectionProtocolRequest;
use App\Models\Admin\DotAgency;
use App\Models\Admin\SelectionEvent;
use App\Models\Admin\SelectionProtocol;
use App\Models\Admin\TestAdmin;
use App\Models\Admin\ClientProfile;
use App\Services\RandomSelectionService;
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
        ]);

        $groups = $this->selectionService->groupSelectionsByType($event);
        $counts = $this->selectionService->selectionTypeCounts($event);
        $emailsSent = $event->selectedEmployees->where('notification_sent', true)->count();

        return view('admin.random_selection.execution_results', [
            'event' => $event,
            'protocol' => $event->protocol,
            'primary' => $groups['primary'],
            'extra' => $groups['extra'],
            'sub' => $groups['sub'],
            'alternates' => $groups['alternates'],
            'counts' => $counts,
            'emailsSent' => $emailsSent,
            'warning' => null,
        ]);
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
