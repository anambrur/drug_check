<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Admin\ConsortiumPlan;
use App\Models\Admin\Favicon;
use App\Models\Admin\PanelImage;
use App\Models\Admin\RandomConsortium;
use App\Models\ConsortiumEnrollment;
use App\Services\ConsortiumEnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

class ConsortiumEnrollmentController extends Controller
{
    /**
     * Display the frontend Random Consortium page.
     */
    public function random_consortium()
    {
        $language = getLanguage();
        // Load the page text content
        $random_consortium = RandomConsortium::where('language_id', $language->id)->first();
        if (!$random_consortium) {
            $random_consortium = RandomConsortium::first();
        }

        $plans = ConsortiumPlan::active()->ordered()->with('fees')->get();

        // Pass JSON representation for the JS calculator
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
                })
            ];
        })->toJson();

        $pricing = $plans->first();

        return view('frontend.random_consortium.index', array_merge(getFrontendData(), compact('random_consortium', 'plans', 'pricing', 'pricingJson')));
    }

    /**
     * Handle the enrollment form submission and create Stripe Checkout Session.
     */
    public function enroll(Request $request)
    {
        $activePlans = ConsortiumPlan::active()->with('fees')->get();
        $planNames = $activePlans->pluck('name')->toArray();

        $validator = Validator::make($request->all(), [
            'company_name'   => 'required|string|max:255',
            'dba_name'       => 'nullable|string|max:255',
            'dot_number'     => 'required|string|max:255',
            'mc_number'      => 'nullable|string|max:255',
            'ein_number'     => 'nullable|string|max:255',
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'phone'          => 'required|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city'           => 'required|string|max:255',
            'state'          => 'required|string|max:255',
            'zip_code'       => 'required|string|max:255',
            'selected_plan'  => 'required|in:' . implode(',', $planNames),
            'driver_count'   => 'required|integer|min:1',
            'notes'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all()
            ], 422);
        }

        $planName = $request->input('selected_plan');
        $driverCount = (int) $request->input('driver_count');

        $planRecord = $activePlans->firstWhere('name', $planName);
        if (!$planRecord) {
            return response()->json(['success' => false, 'errors' => ['Selected plan is invalid.']], 422);
        }

        // Validate plan tier constraints dynamically
        if ($planRecord->min_drivers !== null && $driverCount < $planRecord->min_drivers) {
            return response()->json([
                'success' => false,
                'errors' => ["The selected plan requires at least {$planRecord->min_drivers} driver(s)."]
            ], 422);
        }
        if ($planRecord->max_drivers !== null && $driverCount > $planRecord->max_drivers) {
            return response()->json([
                'success' => false,
                'errors' => ["The selected plan allows at most {$planRecord->max_drivers} driver(s)."]
            ], 422);
        }

        // Calculate amount server-side in cents
        $totalCents = $planRecord->calculateTotal($driverCount);

        // Create enrollment record (Pending Payment)
        $enrollment = ConsortiumEnrollment::create([
            'company_name'    => $request->input('company_name'),
            'dba_name'        => $request->input('dba_name'),
            'dot_number'      => $request->input('dot_number'),
            'mc_number'       => $request->input('mc_number'),
            'ein_number'      => $request->input('ein_number'),
            'first_name'      => $request->input('first_name'),
            'last_name'       => $request->input('last_name'),
            'email'           => $request->input('email'),
            'phone'           => $request->input('phone'),
            'address_line_1'  => $request->input('address_line_1'),
            'address_line_2'  => $request->input('address_line_2'),
            'city'            => $request->input('city'),
            'state'           => $request->input('state'),
            'zip_code'        => $request->input('zip_code'),
            'selected_plan'   => $planName,
            'driver_count'    => $driverCount,
            'notes'           => $request->input('notes'),
            'amount'          => $totalCents,
            'status'          => 'Pending Payment',
            'payment_status'  => 'pending',
        ]);

        // Configure Stripe
        $stripeSecret = config('services.stripe.secret');
        if (!$stripeSecret) {
            return response()->json(['success' => false, 'errors' => ['Stripe is not configured in services.stripe.secret']], 500);
        }

        Stripe::setApiKey($stripeSecret);

        // Build itemized line items dynamically
        $lineItems = [];
        foreach ($planRecord->fees as $fee) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $fee->fee_label,
                        'description' => $fee->fee_type === 'per_driver' ? "Fee applied per driver enrolled" : "Flat fee applied to plan",
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
                'success_url' => route('frontend.random-consortium.success', ['id' => $enrollment->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('frontend.random-consortium'),
                'metadata' => [
                    'consortium_enrollment_id' => $enrollment->id,
                ],
            ]);

            $enrollment->update([
                'stripe_checkout_session_id' => $session->id,
            ]);

            return response()->json([
                'success' => true,
                'redirect_url' => $session->url
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['Failed to initiate payment session: ' . $e->getMessage()]
            ], 500);
        }
    }

    /**
     * Display the success/receipt page for the customer.
     */
    public function success(Request $request, $id, ConsortiumEnrollmentService $enrollmentService)
    {
        $enrollment = ConsortiumEnrollment::findOrFail($id);

        // If Stripe redirected, we verify the checkout session status
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
                // Keep the current local status if check fails, webhook will catch it
            }
        } elseif ($enrollment->payment_status === 'completed' && (!$enrollment->user_id || !$enrollment->notifications_sent_at)) {
            // Payment already marked complete (e.g. webhook first) — ensure account + emails exist
            try {
                $enrollmentService->finalizePaidEnrollment($enrollment);
                $enrollment->refresh();
            } catch (\Exception $e) {
                // Non-blocking for receipt page
            }
        }

        $pricing = ConsortiumPlan::where('name', $enrollment->selected_plan)->with('fees')->first() ?? ConsortiumPlan::first();

        return view('frontend.random_consortium.success', array_merge(getFrontendData(), compact('enrollment', 'pricing')));
    }
}
