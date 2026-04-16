@extends('layouts.admin.master')

@section('content')
{{-- ─────────────────────────────── STAT CARDS ──────────────────────────── --}}
<div class="row">
    <div class="col-xl-3 col-md-6 box-margin">
        <div class="card card-inverse-success h-100">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3"><i class="fa fa-dollar-sign fa-2x text-success"></i></div>
                <div>
                    <p class="mb-0 text-muted font-13">Total Revenue</p>
                    <h4 class="mb-0 font-weight-bold">${{ number_format($stats['total_revenue'] / 100, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 box-margin">
        <div class="card card-inverse-info h-100">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3"><i class="fa fa-calendar fa-2x text-info"></i></div>
                <div>
                    <p class="mb-0 text-muted font-13">Today's Revenue</p>
                    <h4 class="mb-0 font-weight-bold">${{ number_format($stats['today_revenue'] / 100, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 box-margin">
        <div class="card card-inverse-warning h-100">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3"><i class="fa fa-check-circle fa-2x text-warning"></i></div>
                <div>
                    <p class="mb-0 text-muted font-13">Succeeded</p>
                    <h4 class="mb-0 font-weight-bold">{{ number_format($stats['succeeded_count']) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 box-margin">
        <div class="card card-inverse-danger h-100">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3"><i class="fa fa-undo fa-2x text-danger"></i></div>
                <div>
                    <p class="mb-0 text-muted font-13">Refunded</p>
                    <h4 class="mb-0 font-weight-bold">{{ number_format($stats['refunded_count']) }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ─────────────────────────────── FILTER BAR ──────────────────────────── --}}
<div class="row">
    <div class="col-12 box-margin">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.payments.index') }}" class="row align-items-end g-2">
                    <div class="col-md-3">
                        <label class="font-13 text-muted mb-1">Search</label>
                        <input type="text" name="search" value="{{ $search }}"
                               class="form-control form-control-sm"
                               placeholder="Name, email, PI ID…">
                    </div>
                    <div class="col-md-2">
                        <label class="font-13 text-muted mb-1">Status</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="">All Statuses</option>
                            @foreach($statusOptions as $opt)
                                <option value="{{ $opt }}" @selected($status === $opt)>
                                    {{ ucfirst(str_replace('_', ' ', $opt)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="font-13 text-muted mb-1">From</label>
                        <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="font-13 text-muted mb-1">To</label>
                        <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fa fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-sm ml-2">
                            <i class="fa fa-times"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ──────────────────────────── TRANSACTIONS TABLE ─────────────────────── --}}
<div class="row">
    <div class="col-12 box-margin">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="card-title mb-0">
                        <i class="fa fa-credit-card mr-2"></i>Stripe Transactions
                    </h6>
                    <span class="badge badge-pill badge-secondary">
                        {{ $payments->total() }} {{ Str::plural('record', $payments->total()) }}
                    </span>
                </div>

                @if($payments->count())
                    <div class="table-responsive">
                        <table class="table table-hover table-striped font-13 w-100">
                            <thead class="thead-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Test</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Payment Intent</th>
                                    <th>Paid At</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                <tr>
                                    <td class="text-nowrap">
                                        {{ $payment->created_at->format('M d, Y') }}<br>
                                        <small class="text-muted">{{ $payment->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <span class="font-weight-medium">{{ $payment->customer_name ?: '—' }}</span><br>
                                        <small class="text-muted">{{ $payment->customer_email ?: '' }}</small>
                                    </td>
                                    <td>{{ $payment->test_name ?: '—' }}</td>
                                    <td class="font-weight-bold text-nowrap">
                                        {{ $payment->formatted_amount }}
                                        <small class="text-muted">{{ strtoupper($payment->currency) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-pill {{ $payment->status_badge_class }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                        @if($payment->refunded_amount)
                                            <br><small class="text-warning">
                                                Refunded ${{ number_format($payment->refunded_amount / 100, 2) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <code class="font-11" title="{{ $payment->stripe_payment_intent_id }}">
                                            {{ Str::limit($payment->stripe_payment_intent_id, 24) }}
                                        </code>
                                    </td>
                                    <td class="text-nowrap">
                                        {{ $payment->paid_at?->format('M d, Y H:i') ?? '—' }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.payments.show', $payment->id) }}"
                                           class="btn btn-xs btn-primary" title="View Details">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">
                            Showing {{ $payments->firstItem() }}–{{ $payments->lastItem() }}
                            of {{ $payments->total() }}
                        </small>
                        {{ $payments->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No transactions found matching your criteria.</p>
                        @if($search || $status || $dateFrom || $dateTo)
                            <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-secondary">
                                Clear Filters
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
