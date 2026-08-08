<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\DeniesCompanyAccess;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ClearingHousePlanRequest;
use App\Models\Admin\ClearingHousePlan;
use App\Models\Admin\ClearingHousePlanFee;
use App\Models\Admin\Favicon;
use App\Models\Admin\PanelImage;
use Illuminate\Support\Str;

class ClearingHousePlanController extends Controller
{
    use DeniesCompanyAccess;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->denyCompanyUsers();

            return $next($request);
        });
    }

    /**
     * Display a listing of all clearing house plans.
     */
    public function index()
    {
        $plans = ClearingHousePlan::orderBy('display_order', 'asc')->orderBy('name', 'asc')->get();
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();

        return view('admin.clearing_house_plans.index', compact('plans', 'favicon', 'panel_image'));
    }

    /**
     * Show the form for creating a new clearing house plan.
     */
    public function create()
    {
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();

        return view('admin.clearing_house_plans.create', compact('favicon', 'panel_image'));
    }

    /**
     * Store a newly created clearing house plan in storage.
     */
    public function store(ClearingHousePlanRequest $request)
    {
        $validated = $request->validated();

        $plan = ClearingHousePlan::create([
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
            ClearingHousePlanFee::create([
                'clearing_house_plan_id' => $plan->id,
                'fee_key' => $feeData['fee_key'],
                'fee_label' => $feeData['fee_label'],
                'fee_amount' => (int) round($feeData['fee_amount'] * 100),
                'fee_type' => $feeData['fee_type'],
                'display_order' => $feeData['display_order'],
            ]);
        }

        toastr()->success('Clearing house plan created successfully.', 'content.success');

        return redirect()->route('admin.clearing-house-plans.index');
    }

    /**
     * Show the form for editing the specified clearing house plan.
     */
    public function edit($id)
    {
        $plan = ClearingHousePlan::with('fees')->findOrFail($id);
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();

        return view('admin.clearing_house_plans.edit', compact('plan', 'favicon', 'panel_image'));
    }

    /**
     * Update the specified clearing house plan in storage.
     */
    public function update(ClearingHousePlanRequest $request, $id)
    {
        $plan = ClearingHousePlan::findOrFail($id);
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

        $plan->fees()->delete();

        foreach ($validated['fees'] as $feeData) {
            ClearingHousePlanFee::create([
                'clearing_house_plan_id' => $plan->id,
                'fee_key' => $feeData['fee_key'],
                'fee_label' => $feeData['fee_label'],
                'fee_amount' => (int) round($feeData['fee_amount'] * 100),
                'fee_type' => $feeData['fee_type'],
                'display_order' => $feeData['display_order'],
            ]);
        }

        toastr()->success('Clearing house plan updated successfully.', 'content.success');

        return redirect()->route('admin.clearing-house-plans.index');
    }

    public function toggleStatus($id)
    {
        $plan = ClearingHousePlan::findOrFail($id);
        $plan->is_active = !$plan->is_active;
        $plan->save();

        toastr()->success('Plan status updated successfully.', 'content.success');

        return back();
    }

    public function destroy($id)
    {
        $plan = ClearingHousePlan::findOrFail($id);
        $plan->delete();

        toastr()->success('Clearing house plan archived successfully.', 'content.success');

        return redirect()->route('admin.clearing-house-plans.index');
    }

    public function trashed()
    {
        $plans = ClearingHousePlan::onlyTrashed()->orderBy('name', 'asc')->get();
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();

        return view('admin.clearing_house_plans.trashed', compact('plans', 'favicon', 'panel_image'));
    }

    public function restore($id)
    {
        $plan = ClearingHousePlan::onlyTrashed()->findOrFail($id);
        $plan->restore();

        toastr()->success('Clearing house plan restored successfully.', 'content.success');

        return redirect()->route('admin.clearing-house-plans.index');
    }
}
