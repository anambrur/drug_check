@extends('layouts.frontend.master2')

@section('content')
    <div class="pf-show-page svc-page ch-page" style="padding: 6rem 0 4rem;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="pf-card">
                        <div class="pf-header">
                            <span class="pill">Quest Submission</span>
                            <h4>Order Submission Pending</h4>
                            <p>Your payment was successful, but we could not submit your order to Quest Diagnostics.</p>
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
                                    <div>{{ $application->quest_submission_error }}</div>
                                </div>
                            @endif

                            <div class="pf-section">
                                <div class="pf-section-head">
                                    <div class="icon-wrap"><i class="fas fa-clipboard-check"></i></div>
                                    <h6>Order Summary</h6>
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

                            <form method="POST" action="{{ route('frontend.portfolio-test.resubmit', $application->id) }}" class="text-center pt-3">
                                @csrf
                                <button type="submit" class="pf-btn-submit">
                                    <i class="fas fa-redo"></i>
                                    Retry Quest Submission
                                </button>
                            </form>

                            <p class="pf-secure mt-3">
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
@endsection
