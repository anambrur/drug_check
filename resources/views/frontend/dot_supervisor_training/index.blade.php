@extends('layouts.frontend.master2')

@section('content')

@php
    $dstTitle = $dot_supervisor_training?->title ?? 'DOT Supervisor Training';
    $dstIntro = $dot_supervisor_training?->short_description
        ?? 'Professional DOT-compliant supervisor training to help your team meet FMCSA requirements with confidence.';
    $hasDescription = !empty($dot_supervisor_training?->description);
@endphp

<div class="dst-page ch-page">
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
                    <i class="fas fa-user-graduate" aria-hidden="true"></i>
                    Supervisor Training
                </span>
                <h1 class="rc-hero-title rc-hero-item rc-hero-item--2">{{ $dstTitle }}</h1>
                <div class="rc-hero-desc rc-hero-item rc-hero-item--3">@php echo html_entity_decode($dstIntro); @endphp</div>

                @if ($hasDescription)
                    <div class="dst-hero-actions rc-hero-item rc-hero-item--4">
                        <a href="#dot-training-content" class="pf-btn-submit dst-hero-btn text-decoration-none">
                            <i class="fas fa-book-open" aria-hidden="true"></i> View Training Details
                        </a>
                        <a href="#dot-training-highlights" class="dst-hero-btn-outline text-decoration-none">
                            <i class="fas fa-shield-alt" aria-hidden="true"></i> Why It Matters
                        </a>
                    </div>
                @endif
            </div>

            @if ($hasDescription)
                <nav class="rc-stepper rc-hero-item rc-hero-item--4 dst-stepper" aria-label="Page sections" id="dst-stepper">
                    <div class="rc-step rc-step--active rc-step--current" data-step="1" data-target="#dot-training-highlights">
                        <span class="rc-step-num">1</span>
                        <span class="rc-step-label">Overview</span>
                    </div>
                    <div class="rc-step-line" aria-hidden="true"><span class="rc-step-line-fill" data-line="1"></span></div>
                    <div class="rc-step" data-step="2" data-target="#dot-training-content">
                        <span class="rc-step-num">2</span>
                        <span class="rc-step-label">Details</span>
                    </div>
                </nav>
            @endif
        </div>
    </section>

    {{-- Highlights strip --}}
    <section class="plan-section dst-section" id="dot-training-highlights">
        <div class="container">
            <div class="rc-section-head text-center rc-animate mb-4 mb-lg-5">
                <p class="section-eyebrow">FMCSA Compliance</p>
                <h2>Built for Supervisors &amp; Safety Leaders</h2>
                <p class="sub">Equip your team with the knowledge required to maintain DOT drug and alcohol program compliance.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4 rc-animate" style="--rc-delay: 0s;">
                    <div class="plan-card card dst-highlight-card h-100" style="--plan-accent: var(--main-color); border-top-color: var(--main-color) !important;">
                        <div class="plan-card-glow" aria-hidden="true"></div>
                        <div class="card-body text-center">
                            <div class="plan-icon-wrap mx-auto" style="background: var(--main-color);"><i class="fas fa-clipboard-check" aria-hidden="true"></i></div>
                            <h4>Regulatory Compliance</h4>
                            <p class="range mb-0">Covers DOT/FMCSA supervisor training requirements for reasonable suspicion and program awareness.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 rc-animate" style="--rc-delay: .08s;">
                    <div class="plan-card card dst-highlight-card h-100" style="--plan-accent: #059669; border-top-color: #059669 !important;">
                        <div class="plan-card-glow" aria-hidden="true"></div>
                        <div class="card-body text-center">
                            <div class="plan-icon-wrap mx-auto" style="background: #059669;"><i class="fas fa-users" aria-hidden="true"></i></div>
                            <h4>Practical Guidance</h4>
                            <p class="range mb-0">Clear, actionable content designed for fleet managers, DERs, and front-line supervisors.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 rc-animate" style="--rc-delay: .16s;">
                    <div class="plan-card card dst-highlight-card h-100" style="--plan-accent: #8b5cf6; border-top-color: #8b5cf6 !important;">
                        <div class="plan-card-glow" aria-hidden="true"></div>
                        <div class="card-body text-center">
                            <div class="plan-icon-wrap mx-auto" style="background: #8b5cf6;"><i class="fas fa-certificate" aria-hidden="true"></i></div>
                            <h4>Professional Delivery</h4>
                            <p class="range mb-0">Structured training materials presented in a clear, premium learning experience.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Main CMS content --}}
    @if ($hasDescription)
        <section class="dst-section dst-section--alt" id="dot-training-content">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10 rc-animate">
                        <div class="pf-card dst-content-card">
                            <div class="pf-header">
                                <div class="d-flex align-items-start justify-content-between gap-3">
                                    <div>
                                        <span class="pill">Training Program</span>
                                        <h4>{{ $dstTitle }}</h4>
                                        <p>Complete supervisor training information and program details below.</p>
                                    </div>
                                    <div class="pf-header-icon d-none d-sm-flex" aria-hidden="true">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="pf-body">
                                <article class="dst-prose">
                                    @php echo html_entity_decode($dot_supervisor_training->description); @endphp
                                </article>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @else
        <section class="dst-section dst-section--alt" id="dot-training-content">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 rc-animate">
                        <div class="pf-card dst-content-card text-center">
                            <div class="pf-body py-5">
                                <div class="dst-empty-icon mx-auto mb-3" aria-hidden="true"><i class="fas fa-book-open"></i></div>
                                <h3 class="dst-empty-title">Training Content Coming Soon</h3>
                                <p class="ch-text-muted mb-0">Program details will be published here shortly. Please check back or contact our team for more information.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Trust strip --}}
    <section class="dst-trust-strip" aria-label="Training credentials">
        <div class="container">
            <div class="dst-trust-inner rc-animate">
                <span><i class="fas fa-shield-alt" aria-hidden="true"></i> DOT Compliant</span>
                <span><i class="fas fa-truck" aria-hidden="true"></i> FMCSA Standards</span>
                <span><i class="fas fa-user-tie" aria-hidden="true"></i> Supervisor Ready</span>
                <span><i class="fas fa-check-circle" aria-hidden="true"></i> Professional Training</span>
            </div>
        </div>
    </section>
</div>

{{-- Legacy form (preserved, inactive) --}}
<style>
    .contact-form-wrap2 {
        background: #fff;
        margin: 250px !important;
    }

    @media (max-width: 576px) {
        .contact-form-wrap2 {
            background: #fff;
            margin: 20px !important;
        }
    }

    @media (max-width: 768px) {
        .contact-form-wrap2 {
            background: #fff;
            margin: 100px !important;
        }
    }

    @media (max-width: 992px) {
        .contact-form-wrap2 {
            background: #fff;
            margin: 100px !important;
        }
    }
</style>

{{-- <section class="">
        <div class="container m-auto ">
            <div class="sidebar-widgets contact-form-wrap2 ">
                <h5 class="inner-header-title">Please fill out the information below
                    Information</h5>
                <div class="contact-form-wrap p-3">
                    <form id="payment-form" action="{{ route('send.mail_form') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_intent_id" id="payment_intent_id">

                        <div class="row align-self-center ">
                            <div class="col-md-12 d-flex">
                                <div class="contact-form-group">
                                    <input type="text" class="form-control" name="first_name" placeholder="First name"
                                        required>
                                    <div class="form-validate-icons">
                                        <span></span>
                                    </div>
                                </div>
                                <div class="contact-form-group">
                                    <input type="text" class="form-control" name="last_name" placeholder="Last name"
                                        required>
                                    <div class="form-validate-icons">
                                        <span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 d-flex">
                                <div class="contact-form-group">
                                    <input type="email" class="form-control" name="email"
                                        placeholder="{{ __('frontend.email') }}" required>
                                    <div class="form-validate-icons">
                                        <span></span>
                                    </div>
                                </div>
                                <div class="contact-form-group">
                                    <input type="text" class="form-control" name="phone"
                                        placeholder="{{ __('frontend.phone') }}" required>
                                    <div class="form-validate-icons">
                                        <span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 d-flex">
                                <div class="contact-form-group">
                                    <input type="text" class="form-control" name="address"
                                        placeholder="{{ __('frontend.address') }}" required>
                                    <div class="form-validate-icons">
                                        <span></span>
                                    </div>
                                </div>

                            </div>
                            <h5 class="inner-header-title">Company Information</h5>
                            <div class="col-md-12 d-flex">
                                <div class="contact-form-group">
                                    <input type="text" class="form-control" name="Company_name"
                                        placeholder="Company name" required>
                                    <div class="form-validate-icons">
                                        <span></span>
                                    </div>
                                </div>
                                <div class="contact-form-group">
                                    <input type="text" class="form-control" name="Accounting_Email"
                                        placeholder="Accounting Email" required>
                                    <div class="form-validate-icons">
                                        <span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 d-flex">
                                <div class="contact-form-group">
                                    <input type="text" class="form-control" name="company_address"
                                        placeholder="{{ __('frontend.address') }}" required>
                                    <div class="form-validate-icons">
                                        <span></span>
                                    </div>
                                </div>

                            </div>
                            <!-- check list -->
                            <h5 class="inner-header-title">Select Service</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]" value="Choice Package"
                                    id="choicePackage">
                                <label class="form-check-label" for="choicePackage">
                                    CHOICE PACKAGE: InstaCrim National Criminal
                                    Database Search; National Sex Offender Search; OFAC/Terrorist Watchlist; SSN +
                                    Alias Name Check + Address History Search
                                    ----------$19.95 (1-3 Business Day Turn Around) ----------Recomended
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]" value="Premier Package"
                                    id="premierPackage">
                                <label class="form-check-label" for="premierPackage">
                                    PREMIER PACKAGE: InstaCrim National Criminal Database Search;
                                    National Sex Offender Search; OFAC/Terrorist Watchlist; Current County Criminal Records
                                    Search*
                                    applicable Court access fees may apply; SSN +
                                    Alias Name Check + Address History Search ----------$19.95 (1-3 Business Day Turn
                                    Around) ----------Recomended
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    value="County Criminal Records" id="countyCriminal">
                                <label class="form-check-label" for="countyCriminal">
                                    COUNTY CRIMINAL COURT RECORDS SEARCH:
                                    A name and date of birth search of county criminal records for a given jurisdiction.
                                    Includes all available, reportable criminal records, including felonies, misdemeanors,
                                    and serious traffic violations. Jurisdictions that fall within territories covered
                                    under the All County Criminal Record Search will be substituted for the
                                    All County Criminal Record Search a
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    value="Federal Court Records" id="federalCourt">
                                <label class="form-check-label" for="federalCourt">
                                    FEDERAL COURT RECORDS SEARCH: A name and date of birth search of federal
                                    court records for an applicant. Includes all available, reportable Federal records,
                                    including criminal records, bankruptcies,
                                    tax liens and other records from the district and appellate courts.
                                    ----------$4.75 (1 Business Day Turn Around)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    value="Education Verification" id="educationVerification">
                                <label class="form-check-label" for="educationVerification">
                                    EDUCATION VERIFICATION: A name and date of
                                    birth verification of an applicant's education history.
                                    Can be applied to both secondary and postsecondary schools and includes
                                    the applicant's date of graduation, degree obtained, and re-entry status.
                                    ---------- $7.00 + Access Fees (Instant - 3 Business Day Turn Around)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    value="Employment Verification" id="employmentVerification">
                                <label class="form-check-label" for="employmentVerification">
                                    EMPLOYMENT VERIFICATION: A name and date of birth verification of an applicant's
                                    previous employment verification. Includes a verification of the applicant's dates of
                                    hire, re-hire status,
                                    position held, and reason for departure. ---------- $7.00 + Access Fees (Instant - 3
                                    Business Day Turn Around)
                                </label>
                            </div>


                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    value="Personal Reference Verification" id="personalReference">
                                <label class="form-check-label" for="personalReference">
                                    PERSONAL REFERENCE VERIFICATION: A personal reference verification for
                                    an applicant based on their personal history. Can be tailored to address and identify
                                    key characteristics that your organization supports, such as trustworthiness,
                                    timeliness, personal temperament and overall competencies.
                                    ----------$7.00 (Instant - 3 Business Day Turn Around)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                                <label class="form-check-label" for="flexCheckChecked">
                                    PREVIOUS VOLUNTEER VERIFICATION: A previous volunteer verification
                                    for an applicant based on their past volunteer experiences.
                                    Includes a confirmation of the applicant's actual participation with that organization,
                                    including duties or responsibilities, time with the organization,
                                    known incidents, and ability to volunteer in the future.
                                    ----------$7.00 (Instant - 3 Business Day Turn Around)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    value="Professional License Verification" id="licenseVerification">
                                <label class="form-check-label" for="licenseVerification">
                                    PROFESSIONAL LICENSE VERIFICATION: Includes A Professional
                                    License Verification search that validates information provided by your candidate or
                                    prospective connection against national, state, or municipal databases. This includes
                                    their
                                    first name, last name, date of birth,
                                    sex, and any other necessary identifying characteristics.
                                    ----------$7.00 (Instant - 3 Business Day Turn Around)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                                <label class="form-check-label" for="flexCheckChecked">
                                    PROFESSIONAL REFERENCE VERIFICATION: A professional
                                    reference verification for an applicant based on their previous
                                    professional history. Can be tailored to address and identify key
                                    characteristics that your organization supports, such as timeliness, creativity,
                                    completion of projects, and overall competencies.
                                    ----------$7.00 (Instant - 3 Business Day Turn Around)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]" value="SSN Trace"
                                    id="ssnTrace">
                                <label class="form-check-label" for="ssnTrace">
                                    SSN TRACE: A name, date of birth and social security number check for an applicant to
                                    confirm that the
                                    social provided is valid and matches to the applicant's information.
                                    ----------$4.00 (Instant Turn Around)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    value="Pre-employment Credit Report" id="creditReport">
                                <label class="form-check-label" for="creditReport">
                                    PRE-EMPLOYMENT CREDIT REPORT: A name, date of birth and social
                                    security number based retrieval of all known credit history for an applicant. Includes
                                    all known tax liens, collections, open accounts, accounts under bad debt collection,
                                    inquiries and date of birth confirmation. Restricted in
                                    many jurisdictions to applicants with specific fiduciary responsibilities.
                                    ----------$7.25 (Instant Turn Arou
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    value="international_credit" id="internationalCredit">
                                <label class="form-check-label" for="internationalCredit">
                                    INTERNATIONAL CREDIT REPORT: An identity based credit report for
                                    an applicant from a country outside of the United States. Includes all
                                    available records for that country's credit system
                                    ----------$Price upon request (Varies by Country)
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    value="international_criminal" id="internationalCriminal">
                                <label class="form-check-label" for="internationalCriminal">
                                    INTERNATIONAL CRIMINAL RECORDS SEARCH: A name, date of birth and,
                                    where available, identity based criminal record search for countries outside of the
                                    United States. Includes all
                                    available records for that country's criminal justice system.
                                    ----------$Price upon request (Varies by Country)
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    value="international_education" id="internationalEducation">
                                <label class="form-check-label" for="internationalEducation">
                                    INTERNATIONAL EDUCATION VERIFICATION: A name, date of birth and,
                                    where available, identity based education verification for institutions located outside
                                    of the United States.
                                    Includes all available records for that institution's system.
                                    ----------$Price upon request (Varies by Country)
                                </label>
                            </div>

                            <div class="form-check pb-3">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    value="Motor Vehicle Report" id="motorVehicle">
                                <label class="form-check-label" for="motorVehicle">
                                    MOTOR VEHICLE REPORT: A name, date of birth and driver's license number
                                    search for all available traffic citations and serious violations for an applicant in a
                                    given state.
                                    Includes tickets, DUI, DWI, revocations, suspensions,
                                    date of expiration, state of issuance. ----------$4.00 + State and Search Fee (Varies by
                                    State)
                                </label>
                            </div>




                            <div class="col-md-12 text-center">
                                <div class="form-alerts">
                                    @error('name')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                    @error('email')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                    @error('phone')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                    @error('message')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                    @if (session('success'))
                                        <span class="error">{{ __(session('success')) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <h5 class="inner-header-title p-3">Terms and Conditions</h5>
                        <p>By checking the box below, you have agreed to the <a href="#">Terms and Conditions</a></p>
                        <div class="form-check p-3">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                            <label class="form-check-label" for="flexCheckChecked">
                                I agree to the service agreement
                            </label>
                        </div>
                        <div class="contact-btn-left m-5">
                            <button type="submit" id="contactBtn" class="primary-btn">
                                <span class="text">Apply</span>
                                <span class="icon"><i class="fa fa-arrow-right"></i></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section> --}}

<script>
(function () {
    function initScrollAnimations() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.querySelectorAll('.dst-page .rc-animate').forEach(function (el) {
                el.classList.add('rc-visible');
            });
            return;
        }
        if (!('IntersectionObserver' in window)) {
            document.querySelectorAll('.dst-page .rc-animate').forEach(function (el) {
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
        document.querySelectorAll('.dst-page .rc-animate').forEach(function (el) {
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
        document.querySelectorAll('#dst-stepper .rc-step').forEach(function (el) {
            var n = parseInt(el.dataset.step, 10);
            el.classList.toggle('rc-step--active', n <= step);
            el.classList.toggle('rc-step--current', n === step);
        });
        document.querySelectorAll('#dst-stepper .rc-step-line-fill').forEach(function (line) {
            var n = parseInt(line.dataset.line, 10);
            line.classList.toggle('rc-step-line-fill--done', n < step);
        });
    }

    function initStepper() {
        var stepper = document.getElementById('dst-stepper');
        if (!stepper) return;

        var sections = [
            { step: 1, el: document.getElementById('dot-training-highlights') },
            { step: 2, el: document.getElementById('dot-training-content') }
        ];

        document.querySelectorAll('#dst-stepper .rc-step').forEach(function (stepEl) {
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

    function initHighlightCards() {
        document.querySelectorAll('.dst-highlight-card').forEach(function (card) {
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
        initHighlightCards();
        updateStepper(1);
    });
})();
</script>

@endsection
