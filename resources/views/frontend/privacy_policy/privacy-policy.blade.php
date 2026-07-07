@extends('layouts.frontend.master2')

@section('content')

@php
    $pageTitle = 'Privacy Policy';
    $pageIntro = 'Learn how we collect, use, store, and protect your personal information when you use our services.';
    $hasContent = !empty($PrivacyPolicy?->content);
@endphp

<div class="pf-show-page legal-page ch-page" id="legal-privacy-page">

    <div class="rc-scroll-progress" id="rc-scroll-progress" aria-hidden="true"><span></span></div>

    <section class="rc-hero pf-show-hero legal-hero" style="padding-top: 8.5rem !important;">
        <div class="rc-hero-bg" aria-hidden="true">
            <div class="rc-hero-orb rc-hero-orb--1"></div>
            <div class="rc-hero-orb rc-hero-orb--2"></div>
            <div class="rc-hero-grid"></div>
            <div class="rc-particles">
                <span></span><span></span><span></span><span></span><span></span><span></span>
            </div>
        </div>
        <div class="container position-relative">
            @if (Auth::user())
                @can('background view')
                    <div class="click-icon d-md-none text-center mb-3">
                        <button class="custom-btn text-white" type="button">
                            <i class="fa fa-mobile-alt text-white" aria-hidden="true"></i> {{ __('content.touch') }}
                        </button>
                    </div>
                @endcan
            @endif

            <div class="rc-hero-content text-center">
                <span class="rc-badge rc-hero-item rc-hero-item--1">
                    <i class="fas fa-user-lock" aria-hidden="true"></i>
                    Privacy
                </span>
                <h1 class="rc-hero-title rc-hero-item rc-hero-item--2">{{ $pageTitle }}</h1>
                <div class="rc-hero-desc rc-hero-item rc-hero-item--3">{{ $pageIntro }}</div>

                <div class="svc-hero-stats rc-hero-item rc-hero-item--4">
                    <a href="{{ url('/') }}" class="svc-stat-pill text-decoration-none">
                        <i class="fas fa-home" aria-hidden="true"></i>
                        {{ __('frontend.home') }}
                    </a>
                    <span class="svc-stat-pill">
                        <i class="fas fa-database" aria-hidden="true"></i>
                        Data Practices
                    </span>
                    <span class="svc-stat-pill">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        Your Rights
                    </span>
                </div>

                @if ($hasContent)
                    <div class="legal-hero-actions rc-hero-item rc-hero-item--4">
                        <a href="#legal-document" class="pf-btn-submit legal-hero-btn text-decoration-none">
                            <i class="fas fa-book-open" aria-hidden="true"></i> Read Full Policy
                        </a>
                    </div>
                @endif
            </div>

            @if ($hasContent)
                <nav class="rc-stepper rc-hero-item rc-hero-item--4 legal-stepper" aria-label="Privacy page sections" id="legal-stepper">
                    <div class="rc-step rc-step--active rc-step--current" data-step="1" data-target="#legal-highlights">
                        <span class="rc-step-num">1</span>
                        <span class="rc-step-label">Overview</span>
                    </div>
                    <div class="rc-step-line" aria-hidden="true"><span class="rc-step-line-fill rc-step-line-fill--done" data-line="1"></span></div>
                    <div class="rc-step" data-step="2" data-target="#legal-document">
                        <span class="rc-step-num">2</span>
                        <span class="rc-step-label">Full Policy</span>
                    </div>
                </nav>
            @endif
        </div>
    </section>

    <section class="plan-section legal-section" id="legal-highlights">
        <div class="container">
            <div class="rc-section-head text-center rc-animate mb-4 mb-lg-5">
                <p class="section-eyebrow">Your Privacy Matters</p>
                <h2>How We Handle Your Data</h2>
                <p class="sub">A quick overview of our commitment to protecting personal and testing-related information.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4 rc-animate" style="--rc-delay: 0s;">
                    <div class="plan-card card legal-highlight-card h-100" style="--plan-accent: var(--main-color); border-top-color: var(--main-color) !important;">
                        <div class="plan-card-glow" aria-hidden="true"></div>
                        <div class="card-body text-center">
                            <div class="plan-icon-wrap mx-auto" style="background: var(--main-color);"><i class="fas fa-info-circle" aria-hidden="true"></i></div>
                            <h4>Information We Collect</h4>
                            <p class="range mb-0">Details what personal, employer, and testing data we collect when you register, order, or use our platform.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 rc-animate" style="--rc-delay: 0.08s;">
                    <div class="plan-card card legal-highlight-card h-100" style="--plan-accent: var(--main-color); border-top-color: var(--main-color) !important;">
                        <div class="plan-card-glow" aria-hidden="true"></div>
                        <div class="card-body text-center">
                            <div class="plan-icon-wrap mx-auto" style="background: var(--main-color);"><i class="fas fa-shield-alt" aria-hidden="true"></i></div>
                            <h4>How We Use Data</h4>
                            <p class="range mb-0">Explains how information is used to deliver services, process orders, and meet compliance requirements.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 rc-animate" style="--rc-delay: 0.16s;">
                    <div class="plan-card card legal-highlight-card h-100" style="--plan-accent: var(--main-color); border-top-color: var(--main-color) !important;">
                        <div class="plan-card-glow" aria-hidden="true"></div>
                        <div class="card-body text-center">
                            <div class="plan-icon-wrap mx-auto" style="background: var(--main-color);"><i class="fas fa-user-check" aria-hidden="true"></i></div>
                            <h4>Your Choices</h4>
                            <p class="range mb-0">Covers access, correction, security measures, and your rights regarding personal information.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($hasContent)
        <section class="pf-show-intro legal-content-section" id="legal-document" aria-labelledby="legal-privacy-heading">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-xl-7 pf-show-action-col">
                        @if (Auth::user())
                            @can('service view')
                                <div class="click-icon d-md-none text-center mb-3">
                                    <button class="custom-btn text-white" type="button">
                                        <i class="fa fa-mobile-alt text-white" aria-hidden="true"></i> {{ __('content.touch') }}
                                    </button>
                                </div>
                            @endcan
                        @endif

                        <div class="rc-section-head text-center rc-animate mb-4 mb-lg-5">
                            <p class="section-eyebrow">Full Document</p>
                            <h2 id="legal-privacy-heading">{{ $pageTitle }}</h2>
                            <p class="sub">Our complete privacy policy describing how your information is handled across our services.</p>
                        </div>

                        <div class="summary-card pf-show-desc-card rc-animate">
                            <div class="summary-card-head">
                                <h5><i class="fas fa-file-alt me-2" aria-hidden="true"></i>{{ $pageTitle }}</h5>
                            </div>
                            <div class="pf-body pf-show-prose-wrap pt-3">
                                <article class="dst-prose">@php echo html_entity_decode($PrivacyPolicy->content); @endphp</article>
                            </div>
                        </div>

                        <ul class="pf-show-benefits rc-animate" aria-label="Privacy reminders">
                            <li><i class="fas fa-lock" aria-hidden="true"></i> Secure data handling practices</li>
                            <li><i class="fas fa-eye" aria-hidden="true"></i> Transparent collection policies</li>
                            <li><i class="fas fa-envelope" aria-hidden="true"></i> Contact us for privacy requests</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    @endif
</div>

<script>
(function () {
    function initScrollAnimations() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.querySelectorAll('.legal-page .rc-animate').forEach(function (el) { el.classList.add('rc-visible'); });
            return;
        }
        if (!('IntersectionObserver' in window)) {
            document.querySelectorAll('.legal-page .rc-animate').forEach(function (el) { el.classList.add('rc-visible'); });
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
        document.querySelectorAll('.legal-page .rc-animate').forEach(function (el) { observer.observe(el); });
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

    function updateStepper(step) {
        document.querySelectorAll('#legal-stepper .rc-step').forEach(function (el) {
            var n = parseInt(el.dataset.step, 10);
            el.classList.toggle('rc-step--active', n <= step);
            el.classList.toggle('rc-step--current', n === step);
        });
        document.querySelectorAll('#legal-stepper .rc-step-line-fill').forEach(function (line) {
            var n = parseInt(line.dataset.line, 10);
            line.classList.toggle('rc-step-line-fill--done', n < step);
        });
    }

    function initStepper() {
        var stepper = document.getElementById('legal-stepper');
        if (!stepper) return;

        var sections = [
            { step: 1, el: document.getElementById('legal-highlights') },
            { step: 2, el: document.getElementById('legal-document') }
        ];

        stepper.querySelectorAll('.rc-step').forEach(function (stepEl) {
            stepEl.addEventListener('click', function () {
                var target = document.querySelector(stepEl.dataset.target);
                if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                updateStepper(parseInt(stepEl.dataset.step, 10));
            });
        });

        if (!('IntersectionObserver' in window)) return;
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                var match = sections.find(function (s) { return s.el === entry.target; });
                if (match) updateStepper(match.step);
            });
        }, { threshold: 0.2 });
        sections.forEach(function (s) { if (s.el) observer.observe(s.el); });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initScrollAnimations();
        initScrollProgress();
        initStepper();
        updateStepper(1);
    });
})();
</script>

@endsection
