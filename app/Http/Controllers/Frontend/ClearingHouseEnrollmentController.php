<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Admin\ClearingHouse;
use App\Models\Admin\ClearingHousePlan;
use App\Models\ClearingHouseEnrollment;
use App\Services\ClearingHouseEnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

class ClearingHouseEnrollmentController extends Controller
{
    /**
     * Display the frontend Clearing House page (education + enrollment).
     */
    public function index()
    {
        $clearing_house = ClearingHouse::first();

        $plans = ClearingHousePlan::active()->ordered()->with('fees')->get();

        $pricingJson = $plans->map(function ($plan) {
            return [
                'name' => $plan->name,
                'slug' => $plan->slug,
                'min_drivers' => $plan->min_drivers,
                'max_drivers' => $plan->max_drivers,
                'fees' => $plan->fees->map(function ($fee) {
                    return [
                        'fee_key' => $fee->fee_key,
                        'fee_label' => $fee->fee_label,
                        'fee_amount' => $fee->fee_amount_in_dollars,
                        'fee_type' => $fee->fee_type,
                    ];
                }),
            ];
        })->toJson();

        $pricing = $plans->first();

        return view('frontend.clearing_house.index', array_merge(
            getFrontendData(),
            compact('clearing_house', 'plans', 'pricing', 'pricingJson')
        ));
    }

    /**
     * Handle enrollment form submission and create Stripe Checkout Session.
     */
    public function enroll(Request $request)
    {
        $activePlans = ClearingHousePlan::active()->with('fees')->get();
        $planNames = $activePlans->pluck('name')->toArray();

        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'dba_name' => 'nullable|string|max:255',
            'dot_number' => 'required|string|max:255',
            'mc_number' => 'nullable|string|max:255',
            'ein_number' => 'required|string|max:255',
            'company_phone' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:255',
            'is_owner_operator' => 'required|in:0,1,true,false',
            'clearinghouse_registered' => 'required|in:yes,no,unsure',
            'authorize_conduct_queries' => 'nullable|boolean',
            'authorize_report_violations' => 'nullable|boolean',
            'authorize_report_rtd' => 'nullable|boolean',
            'acknowledge_designate_ctpa' => 'accepted',
            'acknowledge_query_plan' => 'accepted',
            'selected_plan' => 'required|in:' . implode(',', $planNames),
            'driver_count' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $validator->after(function ($validator) use ($request) {
            $queries = filter_var($request->input('authorize_conduct_queries'), FILTER_VALIDATE_BOOLEAN);
            $violations = filter_var($request->input('authorize_report_violations'), FILTER_VALIDATE_BOOLEAN);
            $rtd = filter_var($request->input('authorize_report_rtd'), FILTER_VALIDATE_BOOLEAN);

            if (!$queries && !$violations && !$rtd) {
                $validator->errors()->add(
                    'authorize_conduct_queries',
                    'Select at least one C/TPA authorization (Conduct Queries, Report Violations, or Report RTD).'
                );
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all(),
            ], 422);
        }

        $planName = $request->input('selected_plan');
        $driverCount = (int) $request->input('driver_count');

        $planRecord = $activePlans->firstWhere('name', $planName);
        if (!$planRecord) {
            return response()->json(['success' => false, 'errors' => ['Selected plan is invalid.']], 422);
        }

        if ($planRecord->min_drivers !== null && $driverCount < $planRecord->min_drivers) {
            return response()->json([
                'success' => false,
                'errors' => ["The selected plan requires at least {$planRecord->min_drivers} driver(s)."],
            ], 422);
        }
        if ($planRecord->max_drivers !== null && $driverCount > $planRecord->max_drivers) {
            return response()->json([
                'success' => false,
                'errors' => ["The selected plan allows at most {$planRecord->max_drivers} driver(s)."],
            ], 422);
        }

        $totalCents = $planRecord->calculateTotal($driverCount);

        $authorizeQueries = filter_var($request->input('authorize_conduct_queries'), FILTER_VALIDATE_BOOLEAN);
        $authorizeViolations = filter_var($request->input('authorize_report_violations'), FILTER_VALIDATE_BOOLEAN);
        $authorizeRtd = filter_var($request->input('authorize_report_rtd'), FILTER_VALIDATE_BOOLEAN);
        $isOwnerOperator = filter_var($request->input('is_owner_operator'), FILTER_VALIDATE_BOOLEAN);

        $enrollment = ClearingHouseEnrollment::create([
            'company_name' => $request->input('company_name'),
            'dba_name' => $request->input('dba_name'),
            'dot_number' => $request->input('dot_number'),
            'mc_number' => $request->input('mc_number'),
            'ein_number' => $request->input('ein_number'),
            'company_phone' => $request->input('company_phone'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'job_title' => $request->input('job_title'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address_line_1' => $request->input('address_line_1'),
            'address_line_2' => $request->input('address_line_2'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'zip_code' => $request->input('zip_code'),
            'is_owner_operator' => $isOwnerOperator,
            'clearinghouse_registered' => $request->input('clearinghouse_registered'),
            'authorize_conduct_queries' => $authorizeQueries,
            'authorize_report_violations' => $authorizeViolations,
            'authorize_report_rtd' => $authorizeRtd,
            'acknowledge_designate_ctpa' => true,
            'acknowledge_query_plan' => true,
            'selected_plan' => $planName,
            'driver_count' => $driverCount,
            'notes' => $request->input('notes'),
            'amount' => $totalCents,
            'status' => 'Pending Payment',
            'payment_status' => 'pending',
        ]);

        $stripeSecret = config('services.stripe.secret');
        if (!$stripeSecret) {
            return response()->json(['success' => false, 'errors' => ['Stripe is not configured in services.stripe.secret']], 500);
        }

        Stripe::setApiKey($stripeSecret);

        $lineItems = [];
        foreach ($planRecord->fees as $fee) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $fee->fee_label,
                        'description' => $fee->fee_type === 'per_driver'
                            ? 'Fee applied per driver enrolled'
                            : 'Flat fee applied to plan',
                    ],
                    'unit_amount' => $fee->fee_amount,
                ],
                'quantity' => $fee->fee_type === 'per_driver' ? $driverCount : 1,
            ];
        }

        try {
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('frontend.clearing-house.success', ['id' => $enrollment->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('frontend.clearing-house') . '#ch-enroll',
                'metadata' => [
                    'clearing_house_enrollment_id' => $enrollment->id,
                ],
            ]);

            $enrollment->update([
                'stripe_checkout_session_id' => $session->id,
            ]);

            return response()->json([
                'success' => true,
                'redirect_url' => $session->url,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['Failed to initiate payment session: ' . $e->getMessage()],
            ], 500);
        }
    }

    /**
     * Display the success/receipt page for the customer.
     */
    public function success(Request $request, $id, ClearingHouseEnrollmentService $enrollmentService)
    {
        $enrollment = ClearingHouseEnrollment::findOrFail($id);

        $sessionId = $request->query('session_id');
        if ($sessionId && $enrollment->stripe_checkout_session_id === $sessionId && $enrollment->payment_status === 'pending') {
            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                $session = StripeSession::retrieve($sessionId);
                if ($session->payment_status === 'paid') {
                    $enrollmentService->finalizePaidEnrollment(
                        $enrollment,
                        is_string($session->payment_intent) ? $session->payment_intent : ($session->payment_intent->id ?? null)
                    );
                    $enrollment->refresh();
                }
            } catch (\Exception $e) {
                // Webhook will catch it
            }
        } elseif ($enrollment->payment_status === 'completed' && (
            !$enrollment->user_id
            || !$enrollment->company_notified_at
            || !$enrollment->admin_notified_at
        )) {
            try {
                $enrollmentService->finalizePaidEnrollment($enrollment);
                $enrollment->refresh();
            } catch (\Exception $e) {
                // Non-blocking for receipt page
            }
        }

        $pricing = ClearingHousePlan::where('name', $enrollment->selected_plan)->with('fees')->first()
            ?? ClearingHousePlan::first();

        return view('frontend.clearing_house.success', array_merge(
            getFrontendData(),
            compact('enrollment', 'pricing')
        ));
    }
}
