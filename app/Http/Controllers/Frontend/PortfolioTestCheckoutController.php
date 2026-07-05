<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\PortfolioTestCheckoutRequest;
use App\Mail\DotApplicationReceived;
use App\Mail\PaymentConfirmation;
use App\Models\Admin\ContactInfoWidget;
use App\Models\Admin\Employee;
use App\Models\Admin\Portfolio;
use App\Models\Admin\QuestOrder;
use App\Models\PortfolioTestApplication;
use App\Services\PortfolioTestApplicationService;
use App\Services\QuestOrderSubmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

class PortfolioTestCheckoutController extends Controller
{
    public function __construct(
        private readonly PortfolioTestApplicationService $applicationService,
        private readonly QuestOrderSubmissionService $questSubmissionService
    ) {
        $this->middleware('auth');
    }

    public function checkout(PortfolioTestCheckoutRequest $request)
    {
        $validated = $request->validated();
        $portfolio = Portfolio::findOrFail((int) $validated['portfolio_id']);
        $testType = $validated['test_type'];
        $employee = null;

        if ($testType === 'dot') {
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
        $application = PortfolioTestApplication::with('portfolio')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $sessionId = $request->query('session_id');
        if ($sessionId
            && $application->stripe_checkout_session_id === $sessionId
            && $application->payment_status === 'pending'
        ) {
            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                $session = StripeSession::retrieve($sessionId);
                if ($session->payment_status === 'paid') {
                    $this->applicationService->markPaymentCompleted(
                        $application,
                        $session->payment_intent ?? null
                    );
                    $application->refresh();

                    if ($application->isNonDot()) {
                        $this->sendNonDotConfirmationEmails($application);
                    }
                }
            } catch (\Exception $e) {
                // Webhook will reconcile if this check fails.
            }
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
        $application = PortfolioTestApplication::with(['portfolio', 'employee'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        if ($application->payment_status !== 'completed') {
            abort(403, 'Payment has not been completed.');
        }

        if ($application->isQuestSubmitted()) {
            return redirect()->route('quest.order-success', [
                'quest_order_id' => $application->quest_order_id,
                'reference_test_id' => QuestOrder::where('quest_order_id', $application->quest_order_id)->value('reference_test_id'),
            ]);
        }

        return view('frontend.portfolio.quest-retry', [
            'application' => $application,
        ] + getFrontendData());
    }

    public function resubmit(Request $request, int $id)
    {
        $application = PortfolioTestApplication::with('portfolio')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        if ($application->payment_status !== 'completed') {
            return back()->with('error', 'Payment has not been completed.');
        }

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
        $testType = $validated['test_type'];

        return [
            'test_type' => $testType,
            'portfolio_id' => $portfolio->id,
            'user_id' => Auth::id(),
            'employee_id' => $testType === 'dot' ? (int) $validated['employee_id'] : null,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'middle_name' => $this->nullIfEmpty($validated['middle_name'] ?? null),
            'primary_id' => $validated['primary_id'],
            'primary_id_type' => $this->nullIfEmpty($validated['primary_id_type'] ?? null),
            'dob' => $this->nullIfEmpty($validated['dob'] ?? null),
            'email' => $validated['email'],
            'phone' => $this->nullIfEmpty($validated['primary_phone'] ?? null),
            'secondary_phone' => $this->nullIfEmpty($validated['secondary_phone'] ?? null),
            'zip_code' => $this->nullIfEmpty($validated['zip_code'] ?? null),
            'dot_test' => $validated['dot_test'],
            'testing_authority' => $this->nullIfEmpty($validated['testing_authority'] ?? null),
            'reason_for_test_id' => $this->nullInt($validated['reason_for_test_id'] ?? null),
            'physical_reason_for_test_id' => $this->nullIfEmpty($validated['physical_reason_for_test_id'] ?? null),
            'collection_site_id' => $this->nullIfEmpty($validated['collection_site_id'] ?? null),
            'end_datetime' => $this->nullIfEmpty($validated['end_datetime'] ?? null),
            'end_datetime_timezone_id' => $this->nullInt($validated['end_datetime_timezone_id'] ?? null),
            'observed_requested' => $this->nullIfEmpty($validated['observed_requested'] ?? null) ?? 'N',
            'split_specimen_requested' => $this->nullIfEmpty($validated['split_specimen_requested'] ?? null) ?? 'N',
            'csl' => $this->nullIfEmpty($validated['csl'] ?? null),
            'contact_name' => $this->nullIfEmpty($validated['contact_name'] ?? null),
            'telephone_number' => $this->nullIfEmpty($validated['telephone_number'] ?? null),
            'order_comments' => $this->nullIfEmpty($validated['order_comments'] ?? null),
            'amount' => $amountCents,
            'status' => 'Pending Payment',
            'payment_status' => 'pending',
            'quest_submission_status' => 'pending',
        ];
    }

    private function nullIfEmpty(mixed $value): mixed
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return $value;
    }

    private function nullInt(mixed $value): ?int
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return (int) $value;
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
                'payment_intent_data' => [
                    'metadata' => [
                        'portfolio_test_application_id' => (string) $application->id,
                        'portfolio_id' => (string) $portfolio->id,
                        'test_type' => $application->test_type,
                        'user_id' => (string) $application->user_id,
                    ],
                ],
                'success_url' => route('frontend.portfolio-test.success', ['id' => $application->id])
                    . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('default-portfolio-detail-show', ['portfolio_slug' => $portfolio->portfolio_slug]),
                'metadata' => [
                    'portfolio_test_application_id' => (string) $application->id,
                    'portfolio_id' => (string) $portfolio->id,
                    'test_type' => $application->test_type,
                    'user_id' => (string) $application->user_id,
                ],
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

    private function sendNonDotConfirmationEmails(PortfolioTestApplication $application): void
    {
        $portfolio = $application->portfolio;
        $price = number_format($application->amount / 100, 2, '.', '');

        $mailData = [
            'portfolio_id' => $application->portfolio_id,
            'first_name' => $application->first_name,
            'last_name' => $application->last_name,
            'email' => $application->email,
            'phone' => $application->phone,
            'address' => $application->address,
            'date' => $application->date,
            'gender' => $application->gender,
            'preferred_location' => $application->preferred_location,
            'employee_name' => $application->employee_name,
            'company_name' => $application->company_name,
            'accounting_email' => $application->accounting_email,
            'reason_for_testing' => $application->reason_for_testing,
            'payment_intent_id' => $application->stripe_payment_intent_id,
            'test_name' => $portfolio->title ?? 'Test',
            'code' => $portfolio->code,
            'lab_account' => $portfolio->lab_account,
            'country' => $application->country,
            'price' => '$' . $price,
        ];

        try {
            $adminEmail = ContactInfoWidget::pluck('email')->first();
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new DotApplicationReceived(
                    $mailData,
                    $application->reason_for_testing ?? 'No message provided.'
                ));
            }
        } catch (\Exception $e) {
            // Non-blocking — payment already succeeded.
        }

        try {
            if ($application->email) {
                Mail::to($application->email)->send(new PaymentConfirmation(
                    $mailData,
                    $portfolio->title ?? 'Test',
                    $price
                ));
            }
        } catch (\Exception $e) {
            // Non-blocking.
        }
    }
}
