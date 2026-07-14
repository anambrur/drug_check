@extends('layouts.frontend.master2')

@section('content')

@php
    $enrollmentId = str_pad($enrollment->id, 6, '0', STR_PAD_LEFT);
    $companyLabel = $enrollment->company_name;
    if (!empty($enrollment->dba_name)) {
        $companyLabel .= ' (DBA: ' . $enrollment->dba_name . ')';
    }
@endphp

<div class="pf-show-page rc-success-page ch-page" id="rc-success-page">

    <div class="rc-scroll-progress" id="rc-scroll-progress" aria-hidden="true"><span></span></div>

    {{-- Hero --}}
    <section class="rc-hero pf-show-hero rc-success-hero" style="padding-top: 8.5rem !important;">
        <div class="rc-hero-bg" aria-hidden="true">
            <div class="rc-hero-orb rc-hero-orb--1"></div>
            <div class="rc-hero-orb rc-hero-orb--2"></div>
            <div class="rc-hero-grid"></div>
            <div class="rc-particles">
                <span></span><span></span><span></span><span></span><span></span><span></span>
            </div>
        </div>
        <div class="container position-relative">
            <div class="rc-hero-content text-center">
                <span class="rc-badge rc-hero-item rc-hero-item--1">
                    <i class="fas fa-check-circle" aria-hidden="true"></i>
                    Payment Confirmed
                </span>
                <h1 class="rc-hero-title rc-hero-item rc-hero-item--2">Enrollment Completed!</h1>
                <div class="rc-hero-desc rc-hero-item rc-hero-item--3">
                    Thank you. Your paid enrollment in the Random Consortium has been registered successfully.
                    A secure confirmation email has been dispatched to <strong>{{ $enrollment->email }}</strong>.
                </div>

                <div class="svc-hero-stats rc-hero-item rc-hero-item--4">
                    <span class="svc-stat-pill">
                        <i class="fas fa-hashtag" aria-hidden="true"></i>
                        #{{ $enrollmentId }}
                    </span>
                    <span class="svc-stat-pill">
                        <i class="fas fa-truck" aria-hidden="true"></i>
                        {{ $enrollment->selected_plan }}
                    </span>
                    <span class="svc-stat-pill">
                        <i class="fas fa-credit-card" aria-hidden="true"></i>
                        {{ $enrollment->formatted_amount }}
                    </span>
                </div>
            </div>

            <nav class="rc-stepper rc-hero-item rc-hero-item--4 pf-show-stepper" aria-label="Enrollment progress">
                <div class="rc-step rc-step--active">
                    <span class="rc-step-num"><i class="fas fa-check" aria-hidden="true"></i></span>
                    <span class="rc-step-label">Choose Plan</span>
                </div>
                <div class="rc-step-line" aria-hidden="true"><span class="rc-step-line-fill rc-step-line-fill--done"></span></div>
                <div class="rc-step rc-step--active">
                    <span class="rc-step-num"><i class="fas fa-check" aria-hidden="true"></i></span>
                    <span class="rc-step-label">Your Details</span>
                </div>
                <div class="rc-step-line" aria-hidden="true"><span class="rc-step-line-fill rc-step-line-fill--done"></span></div>
                <div class="rc-step rc-step--active rc-step--current">
                    <span class="rc-step-num"><i class="fas fa-check" aria-hidden="true"></i></span>
                    <span class="rc-step-label">Complete</span>
                </div>
            </nav>
        </div>
    </section>

    {{-- Next steps + receipt --}}
    <section class="pf-show-intro rc-success-body" aria-labelledby="rc-success-heading">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-7 pf-show-action-col">

                    <div class="rc-section-head text-center rc-animate mb-4 mb-lg-5">
                        <p class="section-eyebrow">You're All Set</p>
                        <h2 id="rc-success-heading">What Happens Next?</h2>
                        <p class="sub">Our team will finish setup and send your consortium certificate within 24 business hours.</p>
                    </div>

                    <div class="summary-card pf-show-desc-card rc-animate mb-4">
                        <div class="summary-card-head">
                            <h5><i class="fas fa-list-ol me-2" aria-hidden="true"></i>Next Steps</h5>
                        </div>
                        <div class="pf-body pt-3">
                            <ol class="rc-success-steps mb-0">
                                <li>
                                    <span class="rc-success-step-num" aria-hidden="true">1</span>
                                    <div>
                                        <strong>Review</strong>
                                        <p class="mb-0">Our support representatives will review your consortium details.</p>
                                    </div>
                                </li>
                                <li>
                                    <span class="rc-success-step-num" aria-hidden="true">2</span>
                                    <div>
                                        <strong>Certificate Generation</strong>
                                        <p class="mb-0">Your DOT driver enrollment certificate will be generated.</p>
                                    </div>
                                </li>
                                <li>
                                    <span class="rc-success-step-num" aria-hidden="true">3</span>
                                    <div>
                                        <strong>Delivery</strong>
                                        <p class="mb-0">You will receive your consortium certificate and official credentials via email within 24 business hours.</p>
                                    </div>
                                </li>
                            </ol>
                        </div>
                    </div>

                    <div class="summary-card pf-show-pricing-card rc-animate">
                        <div class="summary-card-head">
                            <h5><i class="fas fa-receipt me-2" aria-hidden="true"></i>Transaction Details</h5>
                        </div>
                        <div class="driver-block">
                            <div class="d-flex align-items-end justify-content-between gap-3 flex-wrap">
                                <div>
                                    <div class="pf-pricing-label">Total paid</div>
                                    <div class="pf-pricing-amount">{{ $enrollment->formatted_amount }}</div>
                                </div>
                                <span class="pf-pricing-badge">#{{ $enrollmentId }}</span>
                            </div>
                        </div>

                        <ul class="pf-pricing-details">
                            <li>
                                <span class="fee-label"><i class="fas fa-building" aria-hidden="true"></i> Company</span>
                                <span class="fee-val">{{ $companyLabel }}</span>
                            </li>
                            <li>
                                <span class="fee-label"><i class="fas fa-id-card" aria-hidden="true"></i> USDOT Number</span>
                                <span class="fee-val">{{ $enrollment->dot_number }}</span>
                            </li>
                            <li>
                                <span class="fee-label"><i class="fas fa-layer-group" aria-hidden="true"></i> Selected Plan</span>
                                <span class="fee-val">{{ $enrollment->selected_plan }}</span>
                            </li>
                            <li>
                                <span class="fee-label"><i class="fas fa-users" aria-hidden="true"></i> Drivers Registered</span>
                                <span class="fee-val">{{ $enrollment->driver_count }} driver(s)</span>
                            </li>
                            <li>
                                <span class="fee-label"><i class="fas fa-envelope" aria-hidden="true"></i> Email</span>
                                <span class="fee-val">{{ $enrollment->email }}</span>
                            </li>

                            @if ($pricing)
                                @foreach ($pricing->fees as $fee)
                                    <li>
                                        <span class="fee-label">
                                            <i class="fas fa-tag" aria-hidden="true"></i>
                                            {{ $fee->fee_label }}
                                            @if ($fee->fee_type == 'per_driver')
                                                (x{{ $enrollment->driver_count }})
                                            @endif
                                        </span>
                                        <span class="fee-val">
                                            @if ($fee->fee_type == 'per_driver')
                                                ${{ number_format(($fee->fee_amount_in_dollars * $enrollment->driver_count), 2) }}
                                            @else
                                                ${{ number_format($fee->fee_amount_in_dollars, 2) }}
                                            @endif
                                        </span>
                                    </li>
                                @endforeach
                            @endif

                            <li class="rc-success-total">
                                <span class="fee-label"><i class="fas fa-credit-card" aria-hidden="true"></i> Total Paid (USD)</span>
                                <span class="fee-val">{{ $enrollment->formatted_amount }}</span>
                            </li>
                        </ul>

                        <div class="pf-pricing-note">
                            <i class="fas fa-info-circle" aria-hidden="true"></i>
                            A confirmation receipt has also been emailed to {{ $enrollment->email }}.
                        </div>

                        <div class="d-flex flex-wrap gap-2 justify-content-center mt-3">
                            <a href="{{ url('/') }}" class="pf-btn-submit pf-show-scroll-cta text-decoration-none">
                                <i class="fas fa-home" aria-hidden="true"></i>
                                Return to Home
                            </a>
                        </div>
                    </div>

                    <ul class="pf-show-benefits rc-animate" aria-label="Enrollment reminders">
                        <li><i class="fas fa-envelope-open-text" aria-hidden="true"></i> Confirmation email sent</li>
                        <li><i class="fas fa-shield-alt" aria-hidden="true"></i> Secure Stripe payment</li>
                        <li><i class="fas fa-clock" aria-hidden="true"></i> Certificate within 24 hrs</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
(function () {
    function initScrollAnimations() {
        var items = document.querySelectorAll('.rc-success-page .rc-animate');
        if (!items.length) return;

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) {
            items.forEach(function (el) { el.classList.add('rc-visible'); });
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('rc-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -20px 0px' });

        items.forEach(function (el) {
            observer.observe(el);
            var rect = el.getBoundingClientRect();
            if (rect.top < window.innerHeight && rect.bottom > 0) {
                el.classList.add('rc-visible');
                observer.unobserve(el);
            }
        });
    }

    function initScrollProgress() {
        var bar = document.querySelector('#rc-scroll-progress span');
        if (!bar) return;
        window.addEventListener('scroll', function () {
            var doc = document.documentElement;
            var pct = (doc.scrollTop / (doc.scrollHeight - doc.clientHeight)) * 100;
            bar.style.width = Math.min(100, Math.max(0, pct)) + '%';
        }, { passive: true });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initScrollAnimations();
        initScrollProgress();
    });
})();
</script>

@endsection
