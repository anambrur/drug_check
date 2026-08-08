@extends('layouts.admin.master')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <a href="{{ route($listRoute) }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left mr-2"></i> Back to Listing
        </a>
    </div>

    <div class="col-lg-8 box-margin">
        <div class="card card-body border-0 shadow-sm">
            <h4 class="card-title pb-3 border-bottom d-flex justify-content-between align-items-center">
                <span>
                    {{ $application->isDot() ? 'DOT' : 'Non-DOT' }} Testing Order
                </span>
                <span class="badge badge-secondary font-14">#{{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}</span>
            </h4>

            <div class="table-responsive">
                <table class="table table-striped table-bordered mt-3">
                    <tbody>
                        <tr>
                            <th style="width: 35%">Applicant Name</th>
                            <td>{{ $application->applicantDisplayName() }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>
                                @if($application->email)
                                    <a href="mailto:{{ $application->email }}">{{ $application->email }}</a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td>{{ $application->phone ?: '—' }}</td>
                        </tr>
                        <tr>
                            <th>Company</th>
                            <td>{{ $application->resolveCompanyName() }}</td>
                        </tr>
                        <tr>
                            <th>Accounting Email</th>
                            <td>{{ $application->accounting_email ?: '—' }}</td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td>{{ $application->address ?: '—' }}</td>
                        </tr>
                        <tr>
                            <th>Preferred Location</th>
                            <td>{{ $application->preferred_location ?: '—' }}</td>
                        </tr>
                        <tr>
                            <th>Reason for Testing</th>
                            <td>{{ $application->reason_for_testing ?: '—' }}</td>
                        </tr>
                        <tr>
                            <th>Guest Checkout</th>
                            <td>{{ $application->is_guest ? 'Yes' : 'No' }}</td>
                        </tr>
                        @if($application->user)
                        <tr>
                            <th>Account User</th>
                            <td>{{ $application->user->name }} ({{ $application->user->email }})</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4 box-margin">
        <div class="card card-body border-0 shadow-sm mb-3">
            <h5 class="card-title border-bottom pb-2">Order Summary</h5>
            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <th>Test</th>
                    <td class="text-right">{{ $application->portfolio->title ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Amount</th>
                    <td class="text-right font-weight-bold text-primary">{{ $application->formatted_amount }}</td>
                </tr>
                <tr>
                    <th>Payment</th>
                    <td class="text-right">
                        @if($application->payment_status === 'completed')
                            <span class="badge badge-success">Completed</span>
                        @else
                            <span class="badge badge-warning">{{ ucfirst($application->payment_status ?? 'pending') }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td class="text-right">{{ $application->status ?: '—' }}</td>
                </tr>
                <tr>
                    <th>Quest</th>
                    <td class="text-right">
                        @if($application->quest_submission_status === 'submitted')
                            <span class="badge badge-success">Submitted</span>
                        @elseif($application->quest_submission_status === 'failed')
                            <span class="badge badge-danger">Failed</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($application->quest_submission_status ?? 'pending') }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Created</th>
                    <td class="text-right">{{ $application->created_at?->format('m/d/Y g:i A') }}</td>
                </tr>
                <tr>
                    <th>Updated</th>
                    <td class="text-right">{{ $application->updated_at?->format('m/d/Y g:i A') }}</td>
                </tr>
            </table>

            @if($application->stripe_payment_intent_id)
                <hr>
                <small class="text-muted d-block">Payment Intent</small>
                <code class="font-12">{{ $application->stripe_payment_intent_id }}</code>
            @endif

            @if($application->quest_order_id)
                <hr>
                <small class="text-muted d-block">Quest Order ID</small>
                <code class="font-12">{{ $application->quest_order_id }}</code>
                @if($questOrder)
                    <div class="mt-2">
                        <a href="{{ route('quest-order.show', $questOrder->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-external-link-alt mr-1"></i> Open Lab / Quest Order
                        </a>
                    </div>
                @endif
            @endif

            @if($application->quest_submission_error)
                <hr>
                <small class="text-danger d-block font-weight-bold">Quest Error</small>
                <p class="font-12 text-danger mb-0">{{ $application->quest_submission_error }}</p>
            @endif

            @if($application->payment_status === 'completed' && !$application->isQuestSubmitted())
                <hr>
                <form method="POST" action="{{ route('admin.orders.applications.resubmit', $application->id) }}"
                      onsubmit="return confirm('Resubmit this order to Quest? No new payment is taken.');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger btn-block">
                        <i class="fa fa-redo mr-1"></i> Resubmit to Quest
                    </button>
                </form>
                <small class="text-muted d-block mt-1">Submits the saved order details again. No additional payment is taken.</small>
            @endif
        </div>
    </div>
</div>
@endsection
