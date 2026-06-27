<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\ConsortiumPlan;
use App\Models\Admin\ConsortiumPlanFee;
use App\Models\Admin\Favicon;
use App\Models\Admin\PanelImage;
use App\Http\Requests\Admin\ConsortiumPlanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ConsortiumPlanController extends Controller
{
    /**
     * Display a listing of all consortium plans.
     */
    public function index()
    {
        $plans = ConsortiumPlan::orderBy('display_order', 'asc')->orderBy('name', 'asc')->get();
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();

        return view('admin.consortium_plans.index', compact('plans', 'favicon', 'panel_image'));
    }

    /**
     * Show the form for creating a new consortium plan.
     */
    public function create()
    {
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();

        return view('admin.consortium_plans.create', compact('favicon', 'panel_image'));
    }

    /**
     * Store a newly created consortium plan in storage.
     */
    public function store(ConsortiumPlanRequest $request)
    {
        $validated = $request->validated();

        $plan = ConsortiumPlan::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'description' => $validated['description'],
            'min_drivers' => $validated['min_drivers'],
            'max_drivers' => $validated['max_drivers'],
            'is_active' => $validated['is_active'],
            'display_order' => $validated['display_order'],
            'created_by' => auth()->id(),
        ]);

        foreach ($validated['fees'] as $feeData) {
            ConsortiumPlanFee::create([
                'consortium_plan_id' => $plan->id,
                'fee_key' => $feeData['fee_key'],
                'fee_label' => $feeData['fee_label'],
                'fee_amount' => (int) round($feeData['fee_amount'] * 100), // convert dollars to cents
                'fee_type' => $feeData['fee_type'],
                'display_order' => $feeData['display_order'],
            ]);
        }

        toastr()->success('Consortium plan created successfully.', 'content.success');

        return redirect()->route('admin.consortium-plans.index');
    }

    /**
     * Show the form for editing the specified consortium plan.
     */
    public function edit($id)
    {
        $plan = ConsortiumPlan::with('fees')->findOrFail($id);
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();

        return view('admin.consortium_plans.edit', compact('plan', 'favicon', 'panel_image'));
    }

    /**
     * Update the specified consortium plan in storage.
     */
    public function update(ConsortiumPlanRequest $request, $id)
    {
        $plan = ConsortiumPlan::findOrFail($id);
        $validated = $request->validated();

        $plan->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'description' => $validated['description'],
            'min_drivers' => $validated['min_drivers'],
            'max_drivers' => $validated['max_drivers'],
            'is_active' => $validated['is_active'],
            'display_order' => $validated['display_order'],
            'updated_by' => auth()->id(),
        ]);

        // Simple delete-and-reinsert sync strategy for plan fees
        $plan->fees()->delete();

        foreach ($validated['fees'] as $feeData) {
            ConsortiumPlanFee::create([
                'consortium_plan_id' => $plan->id,
                'fee_key' => $feeData['fee_key'],
                'fee_label' => $feeData['fee_label'],
                'fee_amount' => (int) round($feeData['fee_amount'] * 100), // convert dollars to cents
                'fee_type' => $feeData['fee_type'],
                'display_order' => $feeData['display_order'],
            ]);
        }

        toastr()->success('Consortium plan updated successfully.', 'content.success');

        return redirect()->route('admin.consortium-plans.index');
    }

    /**
     * Toggle the active status of the specified consortium plan.
     */
    public function toggleStatus($id)
    {
        $plan = ConsortiumPlan::findOrFail($id);
        $plan->is_active = !$plan->is_active;
        $plan->save();

        toastr()->success('Plan status updated successfully.', 'content.success');

        return back();
    }

    /**
     * Remove the specified consortium plan from storage (Soft Delete).
     */
    public function destroy($id)
    {
        $plan = ConsortiumPlan::findOrFail($id);
        $plan->delete();

        toastr()->success('Consortium plan archived successfully.', 'content.success');

        return redirect()->route('admin.consortium-plans.index');
    }

    /**
     * Display a listing of soft-deleted plans.
     */
    public function trashed()
    {
        $plans = ConsortiumPlan::onlyTrashed()->orderBy('name', 'asc')->get();
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();

        return view('admin.consortium_plans.trashed', compact('plans', 'favicon', 'panel_image'));
    }

    /**
     * Restore the specified soft-deleted consortium plan.
     */
    public function restore($id)
    {
        $plan = ConsortiumPlan::onlyTrashed()->findOrFail($id);
        $plan->restore();

        toastr()->success('Consortium plan restored successfully.', 'content.success');

        return redirect()->route('admin.consortium-plans.index');
    }
}
