<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\QuestDocType;
use App\Http\Controllers\Controller;
use App\Models\Admin\CollectionSite;
use App\Models\Admin\Portfolio;
use App\Models\Admin\QuestOrder;
use App\Models\PortfolioTestApplication;
use App\Services\Quest\QuestDocumentDownloadService;
use App\Services\Quest\QuestOrderLifecycleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QuestOrderController extends Controller
{
    public function __construct(
        private readonly QuestOrderLifecycleService $lifecycle,
        private readonly QuestDocumentDownloadService $documentService,
    ) {}

    public function showOrderForm(Request $request)
    {
        $applicationId = $request->session()->get('portfolio_test_application_id');

        if ($applicationId) {
            $application = PortfolioTestApplication::where('user_id', Auth::id())->find($applicationId);
            if ($application && $application->payment_status === 'completed') {
                return redirect()->route('frontend.portfolio-test.retry', $application->id);
            }
        }

        return redirect()->route('page-index')->with('error', 'Please complete your test order from the portfolio page.');
    }

    public function orderSuccess(Request $request, string $questOrderId)
    {
        $order = QuestOrder::with('screens')
            ->where('quest_order_id', $questOrderId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('quest.order-success', array_merge(getFrontendData(), [
            'questOrderId' => $order->quest_order_id,
            'referenceTestId' => $order->reference_test_id,
            'order' => $order,
        ]));
    }

    public function downloadDocument(Request $request, string $questOrderId, string $docType)
    {
        if (!in_array($docType, QuestDocType::values(), true)) {
            return back()->with('error', 'Invalid document type requested.');
        }

        $order = $this->findUserOrder($questOrderId);
        $enum = QuestDocType::from($docType);

        try {
            return $this->documentService->downloadDocument($order, $enum);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function downloadResult(Request $request, string $questOrderId, ?string $screenType = 'drug')
    {
        $order = $this->findUserOrder($questOrderId);

        try {
            return $this->documentService->downloadForScreen($order, $screenType ?? 'drug');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function downloadMroLetter(Request $request, string $questOrderId)
    {
        $order = $this->findUserOrder($questOrderId);

        try {
            return $this->documentService->downloadMroLetter($order);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function openPortal(Request $request, ?string $questOrderId = null, ?string $referenceTestId = null)
    {
        $questOrderId = $questOrderId ?: $request->input('quest_order_id', '');
        $referenceTestId = $referenceTestId ?: $request->input('reference_test_id', '');

        if (empty($questOrderId) && empty($referenceTestId)) {
            return back()->with('error', 'Quest Order ID or Reference Test ID is required.');
        }

        $this->assertUserOwnsQuestOrder($questOrderId ?: null, $referenceTestId ?: null);

        $order = QuestOrder::where('quest_order_id', $questOrderId)
            ->orWhere('reference_test_id', $referenceTestId)
            ->first();

        if (!$order) {
            return back()->with('error', 'Order not found.');
        }

        try {
            $result = $this->lifecycle->getOrderDetails($order);

            if ($result['status'] === 'SUCCESS' && !empty($result['display_url'])) {
                return redirect()->away($result['display_url']);
            }

            return back()->with('error', 'Failed to retrieve order: ' . ($result['error']['detail'] ?? 'Unknown error.'));
        } catch (\RuntimeException $e) {
            Log::error('Quest GetOrderDetails failed', ['message' => $e->getMessage()]);

            return back()->with('error', 'Failed to retrieve order details. Please try again.');
        }
    }

    public function showOrderDetails()
    {
        return redirect()->route('quest.order-details.form')
            ->with('error', 'Please use the order lookup form or open the Quest portal from your order confirmation page.');
    }

    public function getOrderDetailsForm()
    {
        return view('quest.order-details-form', getFrontendData());
    }

    public function dotTest(?int $portfolioId = null)
    {
        $portfolio = Portfolio::findOrFail($portfolioId);

        return redirect()
            ->route('default-portfolio-detail-show', [
                'portfolio_slug' => $portfolio->portfolio_slug,
            ])
            ->with('info', 'Complete your DOT test order details on this page, then proceed to secure checkout. Your order will be submitted to Quest Diagnostics automatically after payment.');
    }

    public function showDotOrderForm(Request $request, string $reference)
    {
        $application = PortfolioTestApplication::with('portfolio')
            ->where('user_id', Auth::id())
            ->where(function ($query) use ($reference) {
                if (ctype_digit($reference)) {
                    $query->where('id', (int) $reference);
                } else {
                    $query->where('stripe_payment_intent_id', $reference);
                }
            })
            ->first();

        if (!$application) {
            return redirect()
                ->route('page-index')
                ->with('error', 'Order not found. Please start a new test from the portfolio page.');
        }

        if ($application->isQuestSubmitted() && $application->quest_order_id) {
            $questOrder = QuestOrder::where('quest_order_id', $application->quest_order_id)->first();

            return redirect()->route('quest.order-success', [
                'quest_order_id' => $application->quest_order_id,
                'reference_test_id' => $questOrder?->reference_test_id ?? '',
            ]);
        }

        if ($application->payment_status === 'completed') {
            return redirect()->route('frontend.portfolio-test.retry', $application->id);
        }

        if ($application->portfolio) {
            return redirect()
                ->route('default-portfolio-detail-show', [
                    'portfolio_slug' => $application->portfolio->portfolio_slug,
                ])
                ->with('info', 'Please complete your order details and checkout from the portfolio page.');
        }

        return redirect()->route('page-index')->with('error', 'Please complete your test order from the portfolio page.');
    }

    public function searchCollectionSites(Request $request)
    {
        $searchTerm = trim($request->get('q', ''));

        if (strlen($searchTerm) < 2) {
            return response()->json([]);
        }

        try {
            $like = '%' . $searchTerm . '%';
            $sites = CollectionSite::where(function ($q) use ($like) {
                $q->where('name', 'LIKE', $like)
                    ->orWhere('address_1', 'LIKE', $like)
                    ->orWhere('city', 'LIKE', $like)
                    ->orWhere('state', 'LIKE', $like)
                    ->orWhere('zip_code', 'LIKE', $like);
            })
                ->orderBy('name')
                ->limit(30)
                ->get(['id', 'collection_site_code', 'name', 'address_1', 'city', 'state', 'zip_code']);

            return response()->json(
                $sites->map(fn ($site) => [
                    'id' => $site->id,
                    'collection_site_code' => $site->collection_site_code,
                    'text' => $this->formatSiteLabel($site),
                ])
            );
        } catch (\Throwable $e) {
            Log::error('Collection site search failed', ['message' => $e->getMessage()]);

            return response()->json([], 500);
        }
    }

    private function findUserOrder(string $questOrderId): QuestOrder
    {
        return QuestOrder::with('screens')
            ->where('quest_order_id', $questOrderId)
            ->where('user_id', auth()->id())
            ->firstOrFail();
    }

    private function assertUserOwnsQuestOrder(?string $questOrderId, ?string $referenceTestId): void
    {
        $userId = auth()->id();

        $owned = QuestOrder::where('user_id', $userId)
            ->where(function ($q) use ($questOrderId, $referenceTestId) {
                if ($questOrderId) {
                    $q->where('quest_order_id', $questOrderId);
                }
                if ($referenceTestId) {
                    $q->orWhere('reference_test_id', $referenceTestId);
                }
            })
            ->exists();

        if (!$owned && $questOrderId) {
            $owned = PortfolioTestApplication::where('user_id', $userId)
                ->where('quest_order_id', $questOrderId)
                ->exists();
        }

        abort_unless($owned, 403, 'You are not authorized to view this order.');
    }

    private function formatSiteLabel(CollectionSite $site): string
    {
        $parts = array_filter([$site->name, implode(', ', array_filter([$site->address_1, $site->city, $site->state, $site->zip_code]))]);

        return implode(' — ', $parts);
    }
}
