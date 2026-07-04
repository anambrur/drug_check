@extends('layouts.frontend.master2')

@section('content')

@php
    $siteMainColor = (isset($color_option) && $color_option->color_option != 0)
        ? $color_option->main_color
        : '#ff4500';
    $chTitle = $clearing_house?->title ?? 'FMCSA Clearinghouse';
    $chIntro = $clearing_house?->short_description
        ?? 'Understand FMCSA Clearinghouse requirements, C/TPA compliance, query guidelines, and step-by-step registration.';
@endphp

<div class="ch-page">
    {{-- Scroll progress --}}
    <div class="rc-scroll-progress" id="rc-scroll-progress" aria-hidden="true"><span></span></div>

    {{-- Hero --}}
    <section class="rc-hero">
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
                    <i class="fas fa-database" aria-hidden="true"></i>
                    FMCSA Compliance
                </span>
                <h1 class="rc-hero-title rc-hero-item rc-hero-item--2">{{ $chTitle }}</h1>
                <div class="rc-hero-desc rc-hero-item rc-hero-item--3">@php echo html_entity_decode($chIntro); @endphp</div>
            </div>

            <nav class="rc-stepper rc-hero-item rc-hero-item--4 ch-stepper" aria-label="Page sections" id="ch-stepper">
                <div class="rc-step rc-step--active rc-step--current" data-step="1" data-target="#ch-overview">
                    <span class="rc-step-num">1</span>
                    <span class="rc-step-label">Overview</span>
                </div>
                <div class="rc-step-line" aria-hidden="true"><span class="rc-step-line-fill" data-line="1"></span></div>
                <div class="rc-step" data-step="2" data-target="#ch-ctpa">
                    <span class="rc-step-num">2</span>
                    <span class="rc-step-label">C/TPA</span>
                </div>
                <div class="rc-step-line" aria-hidden="true"><span class="rc-step-line-fill" data-line="2"></span></div>
                <div class="rc-step" data-step="3" data-target="#ch-queries">
                    <span class="rc-step-num">3</span>
                    <span class="rc-step-label">Queries</span>
                </div>
                <div class="rc-step-line" aria-hidden="true"><span class="rc-step-line-fill" data-line="3"></span></div>
                <div class="rc-step" data-step="4" data-target="#ch-registration">
                    <span class="rc-step-num">4</span>
                    <span class="rc-step-label">Register</span>
                </div>
            </nav>
        </div>
    </section>

    {{-- Overview --}}
    <section class="plan-section ch-section" id="ch-overview">
        <div class="container">
            <div class="row g-4 g-lg-5 align-items-center">
                <div class="col-lg-6 rc-animate">
                    <div class="ch-content-block">
                        <p class="section-eyebrow">Overview</p>
                        <h2>What is the FMCSA Clearinghouse?</h2>
                        <p class="ch-lead">
                            The FMCSA Drug and Alcohol Clearinghouse is a secure, online database that provides real-time
                            information about commercial drivers' drug and alcohol violations. It is maintained by the
                            Federal Motor Carrier Safety Administration (FMCSA) and ensures that drivers who violate the
                            DOT's drug and alcohol program rules complete the Return-to-Duty (RTD) process before operating
                            commercial vehicles again.
                        </p>
                        <p class="ch-text-muted mb-0">
                            The Clearinghouse improves road safety and helps employers make informed hiring decisions by
                            accessing drivers' compliance history.
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 rc-animate rc-animate-delay-1">
                    <div class="pf-card ch-info-card">
                        <div class="pf-header ch-info-card-head">
                            <div class="d-flex align-items-start gap-3">
                                <div class="pf-header-icon flex-shrink-0" aria-hidden="true"><i class="fas fa-users-cog"></i></div>
                                <div>
                                    <span class="pill">Requirements</span>
                                    <h4 class="mb-1">Who is Required to Use it?</h4>
                                    <p class="mb-0">Applies to all employers and drivers operating commercial motor vehicles under FMCSA regulations.</p>
                                </div>
                            </div>
                        </div>
                        <div class="pf-body">
                            <ul class="ch-check-list">
                                <li><i class="fas fa-check-circle" aria-hidden="true"></i> CDL Drivers</li>
                                <li><i class="fas fa-check-circle" aria-hidden="true"></i> Employers of CDL drivers (incl. owner-operators)</li>
                                <li><i class="fas fa-check-circle" aria-hidden="true"></i> C/TPAs (Consortium/Third-Party Administrators)</li>
                                <li><i class="fas fa-check-circle" aria-hidden="true"></i> Medical Review Officers (MROs)</li>
                                <li><i class="fas fa-check-circle" aria-hidden="true"></i> Substance Abuse Professionals (SAPs)</li>
                            </ul>
                            <div class="pf-alert ch-alert-note mb-0" role="note">
                                <i class="fas fa-info-circle mt-1" aria-hidden="true"></i>
                                <span>Registration is mandatory for all of the above under FMCSA guidelines.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- C/TPA --}}
    <section class="ch-section ch-section--alt" id="ch-ctpa">
        <div class="container">
            <div class="row g-4 g-lg-5 align-items-start">
                <div class="col-lg-6 order-lg-2 rc-animate">
                    <div class="ch-content-block">
                        <p class="section-eyebrow">Compliance Simplified</p>
                        <h2>What is a C/TPA?</h2>
                        <p class="ch-lead">
                            A C/TPA (Consortium/Third-Party Administrator) is a professional service agent that helps
                            employers manage all aspects of the DOT drug and alcohol testing program. This includes managing
                            testing, compliance, Clearinghouse queries, Return-to-Duty monitoring, and more.
                        </p>
                        <div class="pf-alert ch-alert-info" role="note">
                            <i class="fas fa-lightbulb mt-1" aria-hidden="true"></i>
                            <span><strong>For small companies and owner-operators</strong>, selecting a C/TPA is required under DOT rules.</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1 rc-animate rc-animate-delay-1">
                    <div class="pf-card">
                        <div class="pf-header">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <span class="pill">Your Partner</span>
                                    <h4>What Does a C/TPA Do?</h4>
                                    <p>At <strong>Skyros Drug Checks Inc</strong>, we act as your designated C/TPA, managing compliance with all FMCSA Clearinghouse requirements.</p>
                                </div>
                                <div class="pf-header-icon d-none d-sm-flex" aria-hidden="true"><i class="fas fa-handshake"></i></div>
                            </div>
                        </div>
                        <div class="pf-body">
                            <ul class="ch-feature-list">
                                <li class="ch-feature-item">
                                    <div class="ch-feature-icon" style="--ch-accent: {{ $siteMainColor }};"><i class="fas fa-search" aria-hidden="true"></i></div>
                                    <div>
                                        <h5>Managing Clearinghouse Queries</h5>
                                        <p>Full and Limited queries for hiring and annual checks.</p>
                                    </div>
                                </li>
                                <li class="ch-feature-item">
                                    <div class="ch-feature-icon" style="--ch-accent: #059669;"><i class="fas fa-sync-alt" aria-hidden="true"></i></div>
                                    <div>
                                        <h5>Initiating Return-to-Duty (RTD)</h5>
                                        <p>Managing notifications and process requirements.</p>
                                    </div>
                                </li>
                                <li class="ch-feature-item mb-0">
                                    <div class="ch-feature-icon" style="--ch-accent: #8b5cf6;"><i class="fas fa-file-contract" aria-hidden="true"></i></div>
                                    <div>
                                        <h5>Audit-Ready Records</h5>
                                        <p>Maintaining compliance records, monitoring follow-up testing, and registration &amp; onboarding assistance.</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Query Guidelines --}}
    <section class="plan-section ch-section" id="ch-queries">
        <div class="container">
            <div class="rc-section-head text-center rc-animate mb-4 mb-lg-5">
                <p class="section-eyebrow">Frequency</p>
                <h2>Query Guidelines &amp; Requirements</h2>
                <p class="sub">The two primary types of FMCSA required queries and when you must utilize them.</p>
            </div>

            <div class="pf-card rc-animate">
                <div class="row g-0">
                    <div class="col-md-6 ch-query-col">
                        <div class="ch-query-block">
                            <div class="ch-query-head">
                                <div class="ch-query-icon" style="--ch-accent: {{ $siteMainColor }};"><i class="fas fa-user-check" aria-hidden="true"></i></div>
                                <h3>Full Query</h3>
                            </div>
                            <p class="ch-text-muted">Provides complete details of any drug and alcohol program violations directly from the FMCSA clearinghouse.</p>
                            <p class="ch-label">When is it required?</p>
                            <ul class="ch-check-list ch-check-list--compact">
                                <li><i class="fas fa-check" aria-hidden="true"></i> Before hiring a new CDL driver</li>
                                <li><i class="fas fa-check" aria-hidden="true"></i> When a limited query returns "information found"</li>
                                <li><i class="fas fa-check" aria-hidden="true"></i> During the Return-to-Duty (RTD) process</li>
                                <li><i class="fas fa-check" aria-hidden="true"></i> During follow-up testing programs</li>
                            </ul>
                            <div class="ch-freq-badge">
                                <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                                <div>
                                    <strong>Frequency</strong>
                                    <span>At hiring + as needed</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 ch-query-col ch-query-col--divider">
                        <div class="ch-query-block">
                            <div class="ch-query-head">
                                <div class="ch-query-icon" style="--ch-accent: #06b6d4;"><i class="fas fa-search-plus" aria-hidden="true"></i></div>
                                <h3>Limited Query</h3>
                            </div>
                            <p class="ch-text-muted">Checks whether any records exist for a driver without showing full details. A faster way to monitor compliance.</p>
                            <p class="ch-label">When is it required?</p>
                            <ul class="ch-check-list ch-check-list--compact">
                                <li><i class="fas fa-check" aria-hidden="true"></i> Annually for all existing drivers</li>
                            </ul>
                            <div class="ch-freq-badge">
                                <i class="fas fa-calendar-check" aria-hidden="true"></i>
                                <div>
                                    <strong>Frequency</strong>
                                    <span>1 Limited Query annually per driver</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Registration --}}
    <section class="ch-section ch-section--alt" id="ch-registration">
        <div class="container">
            <div class="rc-section-head text-center rc-animate mb-4 mb-lg-5">
                <p class="section-eyebrow">Step by Step</p>
                <h2>Clearinghouse Registration</h2>
                <p class="sub">Follow these simple steps to register in the Clearinghouse, or let Skyros Drug Checks Inc assist you in achieving seamless compliance.</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6 rc-animate" style="--rc-delay: 0s;">
                    <div class="plan-card card ch-reg-card h-100" style="--plan-accent: {{ $siteMainColor }}; border-top-color: {{ $siteMainColor }} !important;">
                        <div class="plan-card-glow" aria-hidden="true"></div>
                        <div class="card-body">
                            <div class="plan-icon-wrap mx-auto" style="background: {{ $siteMainColor }};"><i class="fas fa-id-card" aria-hidden="true"></i></div>
                            <h4 class="text-center">For Drivers</h4>
                            <div class="plan-divider" aria-hidden="true"></div>
                            <ol class="ch-steps-list">
                                <li>Visit <a href="https://clearinghouse.fmcsa.dot.gov" target="_blank" rel="noopener noreferrer">clearinghouse.fmcsa.dot.gov</a></li>
                                <li>Click "Register" and select <strong>Driver</strong></li>
                                <li>Sign in or create an account using Login.gov</li>
                                <li>Enter CDL details and personal information</li>
                                <li>Complete registration</li>
                                <li>Use this account to provide consent for Full Queries by employers</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 rc-animate" style="--rc-delay: .08s;">
                    <div class="plan-card card ch-reg-card h-100" style="--plan-accent: #059669; border-top-color: #059669 !important;">
                        <div class="plan-card-glow" aria-hidden="true"></div>
                        <div class="card-body">
                            <div class="plan-icon-wrap mx-auto" style="background: #059669;"><i class="fas fa-building" aria-hidden="true"></i></div>
                            <h4 class="text-center">For Employers</h4>
                            <div class="plan-divider" aria-hidden="true"></div>
                            <ol class="ch-steps-list">
                                <li>Visit <a href="https://clearinghouse.fmcsa.dot.gov" target="_blank" rel="noopener noreferrer">clearinghouse.fmcsa.dot.gov</a></li>
                                <li>Click "Register" and select <strong>Employer</strong></li>
                                <li>Sign in or create an account using Login.gov</li>
                                <li>Enter company details, DOT number, and business information</li>
                                <li>If you're an owner-operator, you must designate a C/TPA</li>
                                <li>Purchase a Query Plan and complete setup</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 rc-animate" style="--rc-delay: .16s;">
                    <div class="plan-card card ch-reg-card ch-reg-card--featured h-100 active" style="--plan-accent: {{ $siteMainColor }}; border-top-color: {{ $siteMainColor }} !important;">
                        <div class="plan-card-glow" aria-hidden="true"></div>
                        <div class="card-body">
                            <div class="plan-icon-wrap mx-auto ch-reg-icon-light" style="background: #fff; color: var(--main-color);"><i class="fas fa-handshake" aria-hidden="true"></i></div>
                            <h4 class="text-center text-white">Designate a C/TPA</h4>
                            <div class="plan-divider ch-divider-light" aria-hidden="true"></div>
                            <ol class="ch-steps-list ch-steps-list--light">
                                <li>Log in to your Clearinghouse Employer Dashboard</li>
                                <li>Click on <strong>"Designate C/TPA"</strong></li>
                                <li>Search or enter our details:<br>
                                    <span class="ch-cta-badge">Skyros Drug Checks Inc</span>
                                </li>
                                <li>Confirm selection</li>
                                <li>You're now connected and compliant!</li>
                            </ol>
                            <div class="ch-reg-footer">
                                <i class="fas fa-check-circle" aria-hidden="true"></i> Fully Compliant FMCSA C/TPA
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Downloadable Resources --}}
    @if (
        ($clearing_house?->driver_pdf && count(json_decode($clearing_house->driver_pdf, true) ?? []) > 0) ||
            ($clearing_house?->employer_pdf && count(json_decode($clearing_house->employer_pdf, true) ?? []) > 0))
        <section class="plan-section ch-section" id="ch-resources">
            <div class="container">
                <div class="rc-section-head text-center rc-animate mb-4 mb-lg-5">
                    <p class="section-eyebrow">Downloads</p>
                    <h2>Resources &amp; Guides</h2>
                </div>

                <div class="row g-4">
                    @if ($clearing_house->driver_pdf)
                        @php $driver_pdfs = json_decode($clearing_house->driver_pdf, true) ?? []; @endphp
                        @if (count($driver_pdfs) > 0)
                            <div class="col-md-6 rc-animate">
                                <div class="pf-card ch-resource-card h-100">
                                    <div class="pf-header ch-resource-head">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="pf-header-icon" aria-hidden="true"><i class="fas fa-id-card"></i></div>
                                            <h4 class="mb-0">Driver Resources</h4>
                                        </div>
                                    </div>
                                    <div class="pf-body pt-3">
                                        <ul class="ch-pdf-list">
                                            @foreach ($driver_pdfs as $pdf)
                                                <li>
                                                    @php
                                                        $filenameWithoutTimestamp = substr($pdf, strpos($pdf, '-') + 1);
                                                        $cleanName = pathinfo($filenameWithoutTimestamp, PATHINFO_FILENAME);
                                                        $formattedName = ucwords(str_replace('-', ' ', $cleanName));
                                                    @endphp
                                                    <a href="{{ asset('uploads/pdf/driver_pdf/' . $pdf) }}" target="_blank" rel="noopener noreferrer" class="ch-pdf-link">
                                                        <span class="ch-pdf-icon" aria-hidden="true"><i class="fas fa-file-pdf"></i></span>
                                                        <span class="ch-pdf-meta">
                                                            <strong>{{ $formattedName }}</strong>
                                                            <small><i class="fas fa-download" aria-hidden="true"></i> Download</small>
                                                        </span>
                                                        <span class="ch-pdf-arrow" aria-hidden="true"><i class="fas fa-arrow-right"></i></span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    @if ($clearing_house->employer_pdf)
                        @php $employer_pdfs = json_decode($clearing_house->employer_pdf, true) ?? []; @endphp
                        @if (count($employer_pdfs) > 0)
                            <div class="col-md-6 rc-animate rc-animate-delay-1">
                                <div class="pf-card ch-resource-card h-100">
                                    <div class="pf-header ch-resource-head">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="pf-header-icon" aria-hidden="true"><i class="fas fa-building"></i></div>
                                            <h4 class="mb-0">Employer Resources</h4>
                                        </div>
                                    </div>
                                    <div class="pf-body pt-3">
                                        <ul class="ch-pdf-list">
                                            @foreach ($employer_pdfs as $pdf)
                                                <li>
                                                    @php
                                                        $filenameWithoutTimestamp = substr($pdf, strpos($pdf, '-') + 1);
                                                        $cleanName = pathinfo($filenameWithoutTimestamp, PATHINFO_FILENAME);
                                                        $formattedName = ucwords(str_replace('-', ' ', $cleanName));
                                                    @endphp
                                                    <a href="{{ asset('uploads/pdf/employer_pdf/' . $pdf) }}" target="_blank" rel="noopener noreferrer" class="ch-pdf-link">
                                                        <span class="ch-pdf-icon" aria-hidden="true"><i class="fas fa-file-pdf"></i></span>
                                                        <span class="ch-pdf-meta">
                                                            <strong>{{ $formattedName }}</strong>
                                                            <small><i class="fas fa-download" aria-hidden="true"></i> Download</small>
                                                        </span>
                                                        <span class="ch-pdf-arrow" aria-hidden="true"><i class="fas fa-arrow-right"></i></span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </section>
    @endif
</div>

<script>
(function () {
    function initScrollAnimations() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.querySelectorAll('.ch-page .rc-animate').forEach(function (el) {
                el.classList.add('rc-visible');
            });
            return;
        }
        if (!('IntersectionObserver' in window)) {
            document.querySelectorAll('.ch-page .rc-animate').forEach(function (el) {
                el.classList.add('rc-visible');
            });
            return;
        }
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('rc-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
        document.querySelectorAll('.ch-page .rc-animate').forEach(function (el) {
            observer.observe(el);
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

    function updateStepper(step) {
        document.querySelectorAll('#ch-stepper .rc-step').forEach(function (el) {
            var n = parseInt(el.dataset.step, 10);
            el.classList.toggle('rc-step--active', n <= step);
            el.classList.toggle('rc-step--current', n === step);
        });
        document.querySelectorAll('#ch-stepper .rc-step-line-fill').forEach(function (line) {
            var n = parseInt(line.dataset.line, 10);
            line.classList.toggle('rc-step-line-fill--done', n < step);
        });
    }

    function initStepper() {
        var sections = [
            { step: 1, el: document.getElementById('ch-overview') },
            { step: 2, el: document.getElementById('ch-ctpa') },
            { step: 3, el: document.getElementById('ch-queries') },
            { step: 4, el: document.getElementById('ch-registration') }
        ];
        var resources = document.getElementById('ch-resources');
        if (resources) sections.push({ step: 4, el: resources });

        document.querySelectorAll('#ch-stepper .rc-step').forEach(function (stepEl) {
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
        }, { threshold: 0.25 });
        sections.forEach(function (s) { if (s.el) observer.observe(s.el); });
    }

    function initRegCardHover() {
        document.querySelectorAll('.ch-reg-card:not(.ch-reg-card--featured)').forEach(function (card) {
            card.addEventListener('mouseenter', function () {
                card.classList.add('rc-plan-pop');
            });
            card.addEventListener('animationend', function () {
                card.classList.remove('rc-plan-pop');
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initScrollAnimations();
        initScrollProgress();
        initStepper();
        initRegCardHover();
        updateStepper(1);
    });
})();
</script>

@endsection
