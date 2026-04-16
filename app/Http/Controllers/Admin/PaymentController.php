<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\StripeWebhookEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Paginated, filtered list of all payments.
     */
    public function index(Request $request)
    {
        $search   = $request->input('search');
        $status   = $request->input('status');
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        $payments = Payment::query()
            ->when($search, fn ($q) => $q->where(fn ($q) =>
                $q->where('customer_name',             'like', "%{$search}%")
                  ->orWhere('customer_email',           'like', "%{$search}%")
                  ->orWhere('stripe_payment_intent_id', 'like', "%{$search}%")
                  ->orWhere('test_name',                'like', "%{$search}%")
            ))
            ->when($status,   fn ($q) => $q->where('status', $status))
            ->when($dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Summary stats for top cards
        $stats = [
            'total_revenue'   => Payment::where('status', 'succeeded')->sum('amount'),
            'today_revenue'   => Payment::where('status', 'succeeded')
                                        ->whereDate('paid_at', today())
                                        ->sum('amount'),
            'succeeded_count' => Payment::where('status', 'succeeded')->count(),
            'refunded_count'  => Payment::where('status', 'refunded')->count(),
            'pending_count'   => Payment::whereIn('status', ['processing', 'requires_payment_method'])->count(),
            'failed_count'    => Payment::whereIn('status', ['canceled', 'requires_payment_method'])->count(),
        ];

        // Revenue trend — last 7 days
        $revenueTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $revenueTrend[$date] = Payment::where('status', 'succeeded')
                ->whereDate('paid_at', $date)
                ->sum('amount');
        }

        $statusOptions = ['succeeded', 'processing', 'refunded', 'canceled', 'requires_payment_method'];

        return view('admin.payments.index', compact(
            'payments', 'stats', 'revenueTrend', 'statusOptions',
            'search', 'status', 'dateFrom', 'dateTo'
        ));
    }

    /**
     * Full detail for one payment + chronological webhook event timeline.
     */
    public function show(Payment $payment)
    {
        $webhookEvents = StripeWebhookEvent::where('payment_intent_id', $payment->stripe_payment_intent_id)
            ->orderBy('stripe_created')
            ->get();

        return view('admin.payments.show', compact('payment', 'webhookEvents'));
    }
}
