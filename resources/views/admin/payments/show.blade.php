@extends('layouts.admin.master')

@section('content')
<div class="row">
    <div class="col-12 box-margin">

        {{-- ── Back + Header ──────────────────────────────────────────────── --}}
        <div class="d-flex align-items-center mb-3">
            <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-secondary mr-3">
                <i class="fa fa-arrow-left mr-1"></i> Back
            </a>
            <h5 class="mb-0">
                Transaction Detail
                <small class="text-muted font-13 ml-2">
                    <code>{{ $payment->stripe_payment_intent_id }}</code>
                </small>
            </h5>
        </div>

        <div class="row">

            {{-- ── Left Column: Payment Info ──────────────────────────────── --}}
            <div class="col-lg-8">

                {{-- Status Banner --}}
                <div class="card mb-3">
                    <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <span class="badge badge-pill {{ $payment->status_badge_class }} font-14 px-3 py-2">
                                {{ ucfirst($payment->status) }}
                            </span>
                            @if($payment->refunded_amount)
                                <span class="badge badge-pill badge-warning font-14 px-3 py-2 ml-2">
                                    Refunded ${{ number_format($payment->refunded_amount / 100, 2) }}
                                </span>
                            @endif
                        </div>
                        <div class="text-right">
                            <h3 class="mb-0 font-weight-bold text-dark">{{ $payment->formatted_amount }}</h3>
                            <small class="text-muted">{{ strtoupper($payment->currency) }}</small>
                        </div>
                    </div>
                </div>

                {{-- Customer Info --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0"><i class="fa fa-user mr-2 text-primary"></i>Customer</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-4 text-muted font-13">Name</div>
                            <div class="col-sm-8">{{ $payment->customer_name ?: '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-sm-4 text-muted font-13">Email</div>
                            <div class="col-sm-8">{{ $payment->customer_email ?: '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-sm-4 text-muted font-13">Phone</div>
                            <div class="col-sm-8">{{ $payment->customer_phone ?: '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-sm-4 text-muted font-13">Country</div>
                            <div class="col-sm-8">{{ $payment->country ? strtoupper($payment->country) : '—' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Payment Details --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0"><i class="fa fa-credit-card mr-2 text-primary"></i>Payment Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted font-13">Payment Intent ID</div>
                            <div class="col-sm-8"><code>{{ $payment->stripe_payment_intent_id }}</code></div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted font-13">Charge ID</div>
                            <div class="col-sm-8"><code>{{ $payment->stripe_charge_id ?: '—' }}</code></div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted font-13">Test / Product</div>
                            <div class="col-sm-8">{{ $payment->test_name ?: '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted font-13">Description</div>
                            <div class="col-sm-8">{{ $payment->description ?: '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted font-13">App Tag</div>
                            <div class="col-sm-8">{{ $payment->app_tag ?: '—' }}</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted font-13">Environment</div>
                            <div class="col-sm-8">
                                @if($payment->app_env)
                                    <span class="badge badge-pill {{ $payment->app_env === 'production' ? 'badge-danger' : 'badge-warning' }}">
                                        {{ $payment->app_env }}
                                    </span>
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                        @if($payment->failure_message)
                        <hr class="my-2">
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted font-13">Failure Message</div>
                            <div class="col-sm-8 text-danger">{{ $payment->failure_message }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Raw Stripe Payload --}}
                @if($payment->stripe_payment_intent)
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 d-flex justify-content-between align-items-center">
                            <span><i class="fa fa-code mr-2 text-secondary"></i>Raw Stripe Object</span>
                            <button class="btn btn-xs btn-outline-secondary"
                                    type="button" data-toggle="collapse"
                                    data-target="#rawPayload">Toggle</button>
                        </h6>
                    </div>
                    <div class="collapse" id="rawPayload">
                        <div class="card-body p-0">
                            <pre class="mb-0 p-3 font-11" style="background:#f8f9fa;max-height:400px;overflow:auto;">{{ json_encode($payment->stripe_payment_intent, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                </div>
                @endif

            </div>{{-- /col-lg-8 --}}

            {{-- ── Right Column: Timestamps + Webhook Timeline ────────────── --}}
            <div class="col-lg-4">

                {{-- Timestamps --}}
                <div class="card mb-3">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0"><i class="fa fa-clock mr-2 text-primary"></i>Timestamps</h6>
                    </div>
                    <div class="card-body">
                        <p class="font-13 mb-1"><span class="text-muted">Created:</span><br>
                            <strong>{{ $payment->created_at->format('M d, Y H:i:s') }}</strong></p>
                        <p class="font-13 mb-1"><span class="text-muted">Paid At:</span><br>
                            <strong>{{ $payment->paid_at?->format('M d, Y H:i:s') ?? '—' }}</strong></p>
                        @if($payment->refunded_at)
                        <p class="font-13 mb-1"><span class="text-muted">Refunded At:</span><br>
                            <strong>{{ $payment->refunded_at->format('M d, Y H:i:s') }}</strong></p>
                        @endif
                        <p class="font-13 mb-0"><span class="text-muted">Last Updated:</span><br>
                            <strong>{{ $payment->updated_at->format('M d, Y H:i:s') }}</strong></p>
                    </div>
                </div>

                {{-- Webhook Event Timeline --}}
                <div class="card">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0">
                            <i class="fa fa-stream mr-2 text-primary"></i>
                            Webhook Timeline
                            <span class="badge badge-pill badge-secondary ml-1">{{ $webhookEvents->count() }}</span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        @if($webhookEvents->count())
                            <ul class="list-group list-group-flush">
                                @foreach($webhookEvents as $event)
                                <li class="list-group-item px-3 py-2">
                                    <div class="d-flex align-items-start">
                                        <span class="mt-1 mr-2 text-{{ match(true) {
                                            str_contains($event->type, 'succeeded') => 'success',
                                            str_contains($event->type, 'failed') || str_contains($event->type, 'canceled') => 'danger',
                                            str_contains($event->type, 'refund') => 'warning',
                                            default => 'info'
                                        } }}">
                                            <i class="fa fa-circle" style="font-size:8px;"></i>
                                        </span>
                                        <div>
                                            <p class="mb-0 font-13 font-weight-medium">{{ $event->type }}</p>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::createFromTimestamp($event->stripe_created)->format('M d, Y H:i:s') }}
                                            </small>
                                            @if($event->livemode === false)
                                                <span class="badge badge-warning badge-pill ml-1" style="font-size:9px;">test</span>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-4 text-muted font-13">
                                <i class="fa fa-inbox fa-2x mb-2"></i>
                                <p class="mb-0">No webhook events recorded for this payment.</p>
                                <small>Events will appear here after the next webhook from Stripe.</small>
                            </div>
                        @endif
                    </div>
                </div>

            </div>{{-- /col-lg-4 --}}

        </div>{{-- /row --}}
    </div>{{-- /col-12 --}}
</div>{{-- /row --}}
@endsection
