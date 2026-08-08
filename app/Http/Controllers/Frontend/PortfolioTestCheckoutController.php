<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\PortfolioTestCheckoutRequest;
use App\Http\Requests\PortfolioTestResubmitRequest;
use App\Models\Admin\Employee;
use App\Models\Admin\Portfolio;
use App\Models\Admin\QuestOrder;
use App\Models\PortfolioTestApplication;
use App\Services\PortfolioTestApplicationService;
use App\Services\QuestOrderSubmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

class PortfolioTestCheckoutController extends Controller
{
    public function __construct(
        private readonly PortfolioTestApplicationService $applicationService,
        private readonly QuestOrderSubmissionService $questSubmissionService
    ) {
        $this->middleware('auth')->only(['checkoutDot']);
    }

    public function checkout(PortfolioTestCheckoutRequest $request)
    {
        $validated = $request->validated();
        $portfolio = Portfolio::findOrFail((int) $validated['portfolio_id']);
        $testType = $validated['test_type'];
        $employee = null;

        if ($testType === 'dot') {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['You must be signed in to schedule a DOT test.'],
                ], 403);
            }

            $employee = Employee::with('clientProfile')->findOrFail((int) $validated['employee_id']);
            if (!$this->userCanSelectEmployee($employee)) {
                return response()->json([
                    'success' => false,
                    'errors' => ['You are not authorized to select this employee.'],
                ], 403);
            }
        }

        try {
            $amountCents = $this->applicationService->calculateAmountCents($portfolio->price);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'errors' => [$e->getMessage()]], 422);
        }

        $application = PortfolioTestApplication::create($this->buildApplicationAttributes($validated, $portfolio, $amountCents));

        if ($application->is_guest) {
            PortfolioTestApplication::storeGuestSessionToken($application);
        }

        if ($testType === 'dot') {
            $internal = $this->applicationService->populateInternalFields($application, $portfolio, $employee);
        } else {
            $internal = $this->applicationService->populateInternalFields($application, $portfolio);
        }
        $application->update($internal);

        $description = ($testType === 'dot' ? 'DOT' : 'Non-DOT') . ' drug test — ' . $portfolio->title;

        return $this->createStripeCheckoutSession($application, $portfolio, [
            'name' => $portfolio->title,
            'description' => $description,
        ]);
    }

    public function checkoutDot(PortfolioTestCheckoutRequest $request)
    {
        return $this->checkout($request);
    }

    public function checkoutNonDot(PortfolioTestCheckoutRequest $request)
    {
        return $this->checkout($request);
    }

    public function success(Request $request, int $id)
    {
        $application = $this->findAuthorizedApplication($id);

        if (!$application) {
            abort(403);
        }

        $application->loadMissing('portfolio');

        $sessionId = $request->query('session_id');
        if ($sessionId && $application->stripe_checkout_session_id === $sessionId) {
            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                $session = StripeSession::retrieve($sessionId);
                if ($session->payment_status === 'paid') {
                    // Idempotent: marks payment if needed and sends any missing emails.
                    $this->applicationService->finalizePaidApplication(
                        $application,
                        $session->payment_intent ?? null
                    );
                    $application->refresh();
                }
            } catch (\Exception $e) {
                // Webhook will reconcile if this check fails.
            }
        }

        // If webhook already completed payment but emails are still pending, finish them.
        if ($application->payment_status === 'completed'
            && (!$application->customer_notified_at || !$application->admin_notified_at)
        ) {
            $this->applicationService->finalizePaidApplication($application);
            $application->refresh();
        }

        if ($application->payment_status !== 'completed') {
            return redirect()
                ->route('default-portfolio-detail-show', ['portfolio_slug' => $application->portfolio->portfolio_slug])
                ->with('error', 'Payment has not been completed yet. Please try again or contact support.');
        }

        return $this->submitQuestAndRedirect($application);
    }

    public function retry(int $id)
    {
        $application = $this->findAuthorizedApplication($id);

        if (!$application) {
            abort(403);
        }

        $application->load(['portfolio', 'employee']);

        if ($application->payment_status !== 'completed') {
            abort(403, 'Payment has not been completed.');
        }

        if ($application->isQuestSubmitted()) {
            return redirect()->route('quest.order-success', [
                'quest_order_id' => $application->quest_order_id,
                'reference_test_id' => QuestOrder::where('quest_order_id', $application->quest_order_id)->value('reference_test_id'),
            ]);
        }

        $portfolio = $application->portfolio;
        $flags = $this->applicationService->portfolioFlags($portfolio);

        return view('frontend.portfolio.quest-retry', [
            'application' => $application,
            'portfolio' => $portfolio,
            'isNonDot' => $application->isNonDot(),
            'questDefaults' => $this->applicationService->questDefaultsFromApplication($application),
            'employees' => $application->isDot() ? $this->applicationService->employeesForUser() : collect(),
            'questIsPhysical' => $flags['is_physical'],
            'questIsEbat' => $flags['is_ebat'],
            'initialCollectionSite' => $this->applicationService->collectionSiteSelectOption($application),
        ] + getFrontendData());
    }

    public function resubmit(PortfolioTestResubmitRequest $request, int $id)
    {
        $application = $this->findAuthorizedApplication($id);

        if (!$application) {
            abort(403);
        }

        $application->load(['portfolio', 'employee']);

        if ($application->payment_status !== 'completed') {
            return back()->with('error', 'Payment has not been completed.');
        }

        if ($application->isQuestSubmitted()) {
            return redirect()->route('quest.order-success', [
                'quest_order_id' => $application->quest_order_id,
                'reference_test_id' => QuestOrder::where('quest_order_id', $application->quest_order_id)->value('reference_test_id'),
            ]);
        }

        $validated = $request->validated();
        $employee = null;

        if ($application->isDot()) {
            $employee = Employee::with('clientProfile')->findOrFail((int) $validated['employee_id']);
            if (!$this->userCanSelectEmployee($employee)) {
                return back()->withErrors(['employee_id' => 'You are not authorized to select this employee.']);
            }
        }

        $application->update(array_merge(
            $this->applicationService->questAttributesFromValidated($validated),
            [
                'quest_submission_status' => 'pending',
                'quest_submission_error' => null,
            ]
        ));

        $application->refresh();
        $internal = $this->applicationService->populateInternalFields(
            $application,
            $application->portfolio,
            $employee
        );
        $application->update($internal);

        return $this->submitQuestAndRedirect($application);
    }

    private function submitQuestAndRedirect(PortfolioTestApplication $application)
    {
        $result = $this->questSubmissionService->submitFromApplication($application);

        if ($result['success']) {
            return redirect()->route('quest.order-success', [
                'quest_order_id' => $result['quest_order_id'],
                'reference_test_id' => $result['reference_test_id'],
            ])->with('success', 'Your test order has been submitted to Quest Diagnostics.');
        }

        return redirect()
            ->route('frontend.portfolio-test.retry', $application->id)
            ->with('error', $result['error'] ?? 'Quest order submission failed. Please try again.');
    }

    private function buildApplicationAttributes(array $validated, Portfolio $portfolio, int $amountCents): array
    {
        $isGuest = !Auth::check() && $validated['test_type'] === 'non_dot';
        $guestToken = $isGuest ? Str::random(64) : null;

        return array_merge(
            $this->applicationService->questAttributesFromValidated($validated),
            [
                'test_type' => $validated['test_type'],
                'portfolio_id' => $portfolio->id,
                'user_id' => $isGuest ? null : Auth::id(),
                'is_guest' => $isGuest,
                'guest_access_token' => $guestToken,
                'amount' => $amountCents,
                'status' => 'Pending Payment',
                'payment_status' => 'pending',
                'quest_submission_status' => 'pending',
            ]
        );
    }

    private function findAuthorizedApplication(int $id): ?PortfolioTestApplication
    {
        $application = PortfolioTestApplication::find($id);

        if (!$application) {
            return null;
        }

        if (Auth::check() && (int) $application->user_id === (int) Auth::id()) {
            return $application;
        }

        if ($application->is_guest && $application->isNonDot() && $application->guestSessionMatches()) {
            return $application;
        }

        return null;
    }

    private function createStripeCheckoutSession(
        PortfolioTestApplication $application,
        Portfolio $portfolio,
        array $product
    ) {
        $stripeSecret = config('services.stripe.secret');
        if (!$stripeSecret) {
            return response()->json([
                'success' => false,
                'errors' => ['Stripe is not configured.'],
            ], 500);
        }

        Stripe::setApiKey($stripeSecret);

        try {
            $customerName = trim(implode(' ', array_filter([
                $application->first_name,
                $application->last_name,
            ])));

            $paymentIntentMetadata = array_filter([
                'portfolio_test_application_id' => (string) $application->id,
                'portfolio_id' => (string) $portfolio->id,
                'test_type' => $application->test_type,
                'user_id' => $application->user_id ? (string) $application->user_id : null,
                'customer_name' => $customerName !== '' ? $customerName : null,
                'customer_email' => $application->email ? (string) $application->email : null,
                'customer_phone' => $application->phone ? (string) $application->phone : null,
                'test_name' => $portfolio->title ? (string) $portfolio->title : null,
                'country' => $application->country ? (string) $application->country : null,
            ], static fn ($value) => $value !== null && $value !== '');

            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $product['name'],
                            'description' => $product['description'],
                        ],
                        'unit_amount' => $application->amount,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'customer_email' => $application->email ?: null,
                'payment_intent_data' => [
                    'receipt_email' => $application->email ?: null,
                    'metadata' => $paymentIntentMetadata,
                ],
                'success_url' => route('frontend.portfolio-test.success', ['id' => $application->id])
                    . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('default-portfolio-detail-show', ['portfolio_slug' => $portfolio->portfolio_slug]),
                'metadata' => $paymentIntentMetadata,
            ]);

            $application->update([
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

    private function userCanSelectEmployee(Employee $employee): bool
    {
        $user = Auth::user();
        $role = $user->roles()->first();

        return match ($role?->name) {
            'super-admin' => $employee->status === 'active',
            'company' => $employee->status === 'active'
                && (int) $employee->clientProfile?->user_id === (int) $user->id,
            default => false,
        };
    }
}
