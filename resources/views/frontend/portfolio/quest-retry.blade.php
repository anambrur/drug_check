@extends('layouts.frontend.master2')

@section('content')
    <div class="pf-show-page svc-page ch-page" style="padding: 6rem 0 4rem;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="pf-card">
                        <div class="pf-header">
                            <span class="pill">Quest Submission</span>
                            <h4>Update Order &amp; Resubmit</h4>
                            <p>Your payment was successful, but we could not submit your order to Quest Diagnostics. Review and update the details below, then resubmit.</p>
                        </div>
                        <div class="pf-body">
                            @if (session('error'))
                                <div class="pf-alert pf-alert-danger mb-3">
                                    <i class="fas fa-exclamation-circle mt-1"></i>
                                    <div>{{ session('error') }}</div>
                                </div>
                            @endif

                            @if ($application->quest_submission_error)
                                <div class="pf-alert pf-alert-danger mb-3">
                                    <i class="fas fa-exclamation-triangle mt-1"></i>
                                    <div>
                                        <strong>Quest rejected this order:</strong> {{ $application->quest_submission_error }}
                                    </div>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="pf-alert pf-alert-danger mb-3">
                                    <i class="fas fa-exclamation-circle mt-1"></i>
                                    <div>
                                        <ul class="mb-0 ps-3">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            <div class="pf-section mb-4">
                                <div class="pf-section-head">
                                    <div class="icon-wrap"><i class="fas fa-receipt"></i></div>
                                    <h6>Payment Summary</h6>
                                </div>
                                <div class="pf-section-body">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4">Test</dt>
                                        <dd class="col-sm-8">{{ $application->portfolio->title ?? 'N/A' }}</dd>
                                        <dt class="col-sm-4">Donor</dt>
                                        <dd class="col-sm-8">{{ $application->first_name }} {{ $application->last_name }}</dd>
                                        <dt class="col-sm-4">Email</dt>
                                        <dd class="col-sm-8">{{ $application->email }}</dd>
                                        <dt class="col-sm-4">Amount Paid</dt>
                                        <dd class="col-sm-8">{{ $application->formatted_amount }}</dd>
                                    </dl>
                                </div>
                            </div>

                            <div class="pf-alert pf-alert-success mb-4">
                                <i class="fas fa-info-circle mt-1"></i>
                                <div>
                                    If Quest reported that the <strong>collection site cannot support this order</strong>, clear the collection site field or search for a different location before resubmitting. No additional payment is required.
                                </div>
                            </div>

                            <form method="POST" action="{{ route('frontend.portfolio-test.resubmit', $application->id) }}">
                                @csrf

                                @include('quest.partials.order-fields', [
                                    'questDefaults' => $questDefaults,
                                    'questIsPhysical' => $questIsPhysical,
                                    'questIsEbat' => $questIsEbat,
                                ])

                                <div class="text-center pt-3">
                                    <button type="submit" class="pf-btn-submit">
                                        <i class="fas fa-redo"></i>
                                        Update &amp; Resubmit to Quest
                                    </button>
                                </div>
                            </form>

                            <p class="pf-secure mt-3 text-center">
                                <a href="{{ route('default-portfolio-detail-show', ['portfolio_slug' => $application->portfolio->portfolio_slug]) }}">
                                    Return to portfolio page
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    @include('quest.partials.order-fields-scripts', [
        'questIsPhysical' => $questIsPhysical,
        'initialCollectionSite' => $initialCollectionSite ?? null,
    ])
@endsection
