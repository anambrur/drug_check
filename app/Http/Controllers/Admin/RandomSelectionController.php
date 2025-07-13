<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Admin\DotAgency;
use App\Models\Admin\TestAdmin;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\ClientProfile;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Admin\SelectionEvent;
use App\Models\Admin\SelectionProtocol;
use App\Services\RandomSelectionService;
use Illuminate\Support\Facades\Validator;

class RandomSelectionController extends Controller
{
    public function index()
    {
        $protocols = SelectionProtocol::with(['client', 'test'])
            ->orderBy('created_at', 'desc')
            ->get();
        // dd($protocols);
        return view('admin.random_selection.index', compact('protocols'));
    }

    public function create()
    {
        $clients = ClientProfile::orderBy('id', 'desc')->get();
        $tests = TestAdmin::orderBy('id', 'desc')->get();
        $dotAgencies = DotAgency::orderBy('id', 'desc')->get();
        return view('admin.random_selection.create', compact('clients', 'tests', 'dotAgencies'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->merge([
                'exclude_previously_selected' => $request->has('exclude_previously_selected'),
                'automatic' => $request->has('automatic'),
                'calculate_pool_average' => $request->has('calculate_pool_average'),
                'is_active' => $request->has('is_active')
            ]);

            // Validate the main form data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'client_id' => 'required|exists:client_profiles,id',
                'test_id' => 'required|exists:test_admins,id',
                'group' => 'required|in:DOT,NON_DOT,DOT_AGENCY,ALL',
                'dot_agency_id' => 'required_if:group,DOT_AGENCY|exists:dot_agencies,id',
                'department_filter' => 'nullable|string|max:255',
                'shift_filter' => 'nullable|string|max:255',
                'exclude_previously_selected' => 'boolean',
                'selection_requirement_type' => 'required|in:NUMBER,PERCENTAGE',
                'selection_requirement_value' => 'required|integer|min:1',
                'selection_period' => 'required|in:YEARLY,QUARTERLY,MONTHLY,MANUAL',
                'monthly_selection_day' => 'required_if:selection_period,MONTHLY|integer|min:1|max:28',
                'manual_dates' => 'required_if:selection_period,MANUAL|array',
                'manual_dates.*' => 'required_if:selection_period,MANUAL|date',
                'alternates_type' => 'nullable|in:NUMBER,PERCENTAGE',
                'alternates_value' => 'nullable|integer|min:0',
                'automatic' => 'boolean',
                'calculate_pool_average' => 'boolean',
                'is_active' => 'boolean',
                'extra_tests' => 'sometimes|array',
                'extra_tests.*' => 'sometimes|exists:test_admins,id',
                'sub_selections' => 'sometimes|array|max:3',
                'sub_selections.*.test_id' => 'required_with:sub_selections|exists:test_admins,id',
                'sub_selections.*.requirement_type' => 'required_with:sub_selections|in:NUMBER,PERCENTAGE',
                'sub_selections.*.requirement_value' => 'required_with:sub_selections|integer|min:1',
            ]);

            // Any error checking
            if ($validator->fails()) {
                DB::rollBack();
                toastr()->error($validator->errors()->first(), 'content.error');
                return back();
            }

            $input = $request->all();

            $protocol = SelectionProtocol::create([
                'name' => $input['name'],
                'client_id' => $input['client_id'],
                'test_id' => $input['test_id'],
                'group' => $input['group'],
                'dot_agency_id' => $input['group'] === 'DOT_AGENCY' ? $input['dot_agency_id'] : null,
                'department_filter' => $input['department_filter'],
                'shift_filter' => $input['shift_filter'],
                'exclude_previously_selected' => $request->has('exclude_previously_selected'),
                'selection_requirement_type' => $input['selection_requirement_type'],
                'selection_requirement_value' => $input['selection_requirement_value'],
                'selection_period' => $input['selection_period'],
                'monthly_selection_day' => $input['selection_period'] === 'MONTHLY' ? $input['monthly_selection_day'] : null,
                'manual_dates' => $input['selection_period'] === 'MANUAL' ? json_encode($input['manual_dates']) : null,
                'alternates_type' => $input['alternates_value'] > 0 ? $input['alternates_type'] : null,
                'alternates_value' => $input['alternates_value'] > 0 ? $input['alternates_value'] : 0,
                'automatic' => $request->has('automatic'),
                'calculate_pool_average' => $request->has('calculate_pool_average'),
                'is_active' => $request->has('is_active')
            ]);

            // Add extra tests
            if (!empty($input['extra_tests'])) {
                foreach ($input['extra_tests'] as $testId) {
                    $protocol->extraTests()->create(['test_id' => $testId]);
                }
            }

            // Add sub-selections
            if (!empty($input['sub_selections'])) {
                foreach ($input['sub_selections'] as $sub) {
                    $protocol->subSelections()->create([
                        'test_id' => $sub['test_id'],
                        'requirement_type' => $sub['requirement_type'],
                        'requirement_value' => $sub['requirement_value']
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('random-selection.index')->with('success', 'Protocol created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating selection protocol: ' . $e->getMessage());
            toastr()->error('An error occurred while creating the protocol. Please try again.', 'content.error');
            return back()->withInput();
        }
    }


    public function execute(SelectionProtocol $protocol, RandomSelectionService $service)
    {
        try {
            $results = $service->executeProtocol($protocol);
            return view('admin.random_selection.results', [
                'protocol' => $protocol,
                'event' => $results['event'],
                'primary' => $results['primary'],
                'extra' => $results['extra'],
                'sub' => $results['sub'],
                'alternates' => $results['alternates']
            ]);
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    public function executions(SelectionProtocol $protocol)
    {
        $executions = $protocol->selectionEvents()
            ->with(['selectedEmployees.employee', 'selectedEmployees.test'])
            ->orderBy('selection_date', 'desc')
            ->paginate(10);

        return view('admin.random_selection.executions', [
            'protocol' => $protocol,
            'executions' => $executions
        ]);
    }


    public function viewResults(SelectionEvent $event)
    {
        $event->load([
            'protocol',
            'selectedEmployees.employee',
            'selectedEmployees.test'
        ]);

        // Group selections by type
        $selections = [
            'primary' => collect(),
            'extra' => collect(),
            'sub' => collect(),
            'alternate' => collect()
        ];

        foreach ($event->selectedEmployees as $selection) {
            $selections[strtolower($selection->selection_type)]->push($selection);
        }

        return view('admin.random_selection.execution_results', [
            'event' => $event,
            'protocol' => $event->protocol,
            'primary' => $selections['primary'],
            'extra' => $selections['extra'],
            'sub' => $selections['sub'],
            'alternates' => $selections['alternate']
        ]);
    }


    public function edit($id)
    {
        $protocol = SelectionProtocol::with(['extraTests', 'subSelections'])->findOrFail($id);

        // Initialize empty collections if relationships are null
        if (!$protocol->extraTests) {
            $protocol->setRelation('extraTests', collect());
        }
        if (!$protocol->subSelections) {
            $protocol->setRelation('subSelections', collect());
        }
        $clients = ClientProfile::all();
        $tests = TestAdmin::all();
        return view('admin.random_selection.edit', compact('protocol', 'clients', 'tests'));
    }


    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $request->merge([
                'exclude_previously_selected' => $request->has('exclude_previously_selected'),
                'automatic' => $request->has('automatic'),
                'calculate_pool_average' => $request->has('calculate_pool_average'),
                'is_active' => $request->has('is_active')
            ]);

            // Validate the main form data
            $validator = Validator::make($request->all(), [
                'client_id' => 'required|exists:client_profiles,id',
                'test_id' => 'required|exists:test_admins,id',
                'group' => 'required|in:DOT,NON_DOT,DOT_AGENCY,ALL',
                'dot_agency_id' => 'required_if:group,DOT_AGENCY|exists:dot_agencies,id',
                'department_filter' => 'nullable|string|max:255',
                'shift_filter' => 'nullable|string|max:255',
                'exclude_previously_selected' => 'boolean',
                'selection_requirement_type' => 'required|in:NUMBER,PERCENTAGE',
                'selection_requirement_value' => 'required|integer|min:1',
                'selection_period' => 'required|in:YEARLY,QUARTERLY,MONTHLY,MANUAL',
                'monthly_selection_day' => 'required_if:selection_period,MONTHLY|integer|min:1|max:28',
                'manual_dates' => 'required_if:selection_period,MANUAL|array',
                'manual_dates.*' => 'required_if:selection_period,MANUAL|date',
                'alternates_type' => 'nullable|in:NUMBER,PERCENTAGE',
                'alternates_value' => 'nullable|integer|min:0',
                'automatic' => 'boolean',
                'calculate_pool_average' => 'boolean',
                'is_active' => 'boolean',
                'extra_tests' => 'sometimes|array',
                'extra_tests.*' => 'sometimes|exists:test_admins,id',
                'sub_selections' => 'sometimes|array|max:3',
                'sub_selections.*.test_id' => 'required_with:sub_selections|exists:test_admins,id',
                'sub_selections.*.requirement_type' => 'required_with:sub_selections|in:NUMBER,PERCENTAGE',
                'sub_selections.*.requirement_value' => 'required_with:sub_selections|integer|min:1',
            ]);

            // Error checking
            if ($validator->fails()) {
                DB::rollBack();
                toastr()->error($validator->errors()->first(), 'content.error');
                return back();
            }

            $input = $request->all();

            // Find the protocol
            $protocol = SelectionProtocol::findOrFail($id);

            // Update the protocol
            $protocol->update([
                'client_id' => $input['client_id'],
                'test_id' => $input['test_id'],
                'group' => $input['group'],
                'dot_agency_id' => $input['group'] === 'DOT_AGENCY' ? $input['dot_agency_id'] : null,
                'department_filter' => $input['department_filter'],
                'shift_filter' => $input['shift_filter'],
                'exclude_previously_selected' => $request->has('exclude_previously_selected'),
                'selection_requirement_type' => $input['selection_requirement_type'],
                'selection_requirement_value' => $input['selection_requirement_value'],
                'selection_period' => $input['selection_period'],
                'monthly_selection_day' => $input['selection_period'] === 'MONTHLY' ? $input['monthly_selection_day'] : null,
                'manual_dates' => $input['selection_period'] === 'MANUAL' ? json_encode($input['manual_dates']) : null,
                'alternates_type' => $input['alternates_value'] > 0 ? $input['alternates_type'] : null,
                'alternates_value' => $input['alternates_value'] > 0 ? $input['alternates_value'] : 0,
                'automatic' => $request->has('automatic'),
                'calculate_pool_average' => $request->has('calculate_pool_average'),
                'is_active' => $request->has('is_active')
            ]);

            // Handle extra tests
            $protocol->extraTests()->delete(); // Remove existing
            if (!empty($input['extra_tests'])) {
                foreach ($input['extra_tests'] as $testId) {
                    $protocol->extraTests()->create(['test_id' => $testId]);
                }
            }

            // Handle sub-selections
            $protocol->subSelections()->delete(); // Remove existing
            if (!empty($input['sub_selections'])) {
                foreach ($input['sub_selections'] as $sub) {
                    $protocol->subSelections()->create([
                        'test_id' => $sub['test_id'],
                        'requirement_type' => $sub['requirement_type'],
                        'requirement_value' => $sub['requirement_value']
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('random-selection.index')
                ->with('success', 'Protocol updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating selection protocol ID ' . $id . ': ' . $e->getMessage());

            toastr()->error('An error occurred while updating the protocol. Please try again.', 'content.error');
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        // Retrieve a model
        $selection_protocol = SelectionProtocol::find($id);

        // Delete record
        $selection_protocol->delete();

        // Set a success toast, with a title
        toastr()->success('content.deleted_successfully', 'content.success');

        return redirect()->route('random-selection.index');
    }
}
