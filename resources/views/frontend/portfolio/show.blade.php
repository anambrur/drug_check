@extends('layouts.frontend.master2')

@section('content')

    @php
        $priceDisplay = preg_replace('/[^0-9.]/', '', (string) $portfolio->price);
        $priceFormatted = number_format((float) $priceDisplay, 2);
        $isDotTesting = !$isNonDot;
        $pfHeroIntro = $isNonDot
            ? 'Review full test details, transparent pricing, and schedule your workplace test through our secure checkout.'
            : 'Review DOT-compliant test information, pricing, and schedule testing for your employees through our secure platform.';
        $revealLoginPanel = $errors->any() || session('error') || session('status');
        $questIsPhysical = str_contains(strtolower($portfolio->title ?? ''), 'physical');
        $questIsEbat = str_contains(strtolower($portfolio->title ?? ''), 'ebat');
        $questDefaults = [
            'first_name' => old('first_name'),
            'last_name' => old('last_name'),
            'email' => old('email', auth()->user()->email ?? ''),
            'primary_phone' => old('primary_phone'),
        ];
    @endphp

    <div class="pf-show-page svc-page ch-page" id="portfolio-single-page">

        {{-- Scroll progress --}}
        <div class="rc-scroll-progress" id="rc-scroll-progress" aria-hidden="true"><span></span></div>

        {{-- Hero (replaces breadcrumb) --}}
        <section class="rc-hero pf-show-hero" style="padding-top: 8.5rem !important;">
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
                        <i class="fas {{ $isDotTesting ? 'fa-truck' : 'fa-flask' }}" aria-hidden="true"></i>
                        {{ $portfolio->category_name }}
                    </span>
                    <h1 class="rc-hero-title rc-hero-item rc-hero-item--2">{{ $portfolio->title }}</h1>
                    <div class="rc-hero-desc rc-hero-item rc-hero-item--3">{{ $pfHeroIntro }}</div>

                    <div class="svc-hero-stats rc-hero-item rc-hero-item--4">
                        <span class="svc-stat-pill">
                            <i class="fas fa-tag" aria-hidden="true"></i>
                            ${{ $priceFormatted }}
                        </span>
                        @if (!empty($portfolio->code))
                            <span class="svc-stat-pill">
                                <i class="fas fa-barcode" aria-hidden="true"></i>
                                {{ $portfolio->code }}
                            </span>
                        @endif
                        <span class="svc-stat-pill">
                            <i class="fas fa-shield-alt" aria-hidden="true"></i>
                            Secure Checkout
                        </span>
                    </div>
                </div>

                <nav class="rc-stepper rc-hero-item rc-hero-item--4 pf-show-stepper" aria-label="Test scheduling steps" id="pf-stepper">
                    <div class="rc-step rc-step--active rc-step--current" data-step="1" role="button" tabindex="0" aria-label="Go to service details">
                        <span class="rc-step-num">1</span>
                        <span class="rc-step-label">Learn</span>
                    </div>
                    <div class="rc-step-line" aria-hidden="true"><span class="rc-step-line-fill rc-step-line-fill--done" data-line="1"></span></div>
                    <div class="rc-step rc-step--active" data-step="2" role="button" tabindex="0" aria-label="Go to pricing">
                        <span class="rc-step-num">2</span>
                        <span class="rc-step-label">Pricing</span>
                    </div>
                    <div class="rc-step-line" aria-hidden="true"><span class="rc-step-line-fill" data-line="2"></span></div>
                    <div class="rc-step" data-step="3" role="button" tabindex="0" aria-label="{{ auth()->check() ? 'Go to scheduling' : 'Go to sign in' }}">
                        <span class="rc-step-num">3</span>
                        <span class="rc-step-label">{{ auth()->check() ? 'Order' : 'Sign In' }}</span>
                    </div>
                </nav>
            </div>
        </section>

        {{-- Service Introduction --}}
        @isset($portfolio_content)
            <section class="pf-show-intro" id="pf-step-learn" aria-labelledby="pf-intro-heading">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-xl-7 pf-show-action-col">
                            <div class="rc-section-head text-center rc-animate mb-4 mb-lg-5">
                                <p class="section-eyebrow">Step 1</p>
                                <h2 id="pf-intro-heading">About {{ $portfolio->title }}</h2>
                                <p class="sub">Review the service details, understand what's included, and see transparent pricing before you continue.</p>
                            </div>

                            <div class="summary-card pf-show-desc-card rc-animate">
                                <div class="summary-card-head">
                                    <h5><i class="fas fa-book-open me-2" aria-hidden="true"></i>{{ $portfolio->title }}</h5>
                                </div>
                                <div class="pf-body pf-show-prose-wrap pt-3">
                                    <article class="dst-prose">@php echo html_entity_decode($portfolio_content->description); @endphp</article>
                                </div>
                            </div>

                            <ul class="pf-show-benefits rc-animate" aria-label="Service highlights">
                                <li><i class="fas fa-network-wired" aria-hidden="true"></i> Quest Diagnostics collection network</li>
                                <li><i class="fas fa-shield-alt" aria-hidden="true"></i> Secure Stripe checkout</li>
                                <li><i class="fas fa-user-check" aria-hidden="true"></i> Tracked under your account</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
        @endisset

        {{-- 3. Pricing Section --}}
        <section class="pf-show-pricing" id="pf-step-pricing" aria-labelledby="pf-pricing-heading">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-xl-7 pf-show-action-col">
                        <div class="rc-section-head text-center rc-animate mb-4">
                            <p class="section-eyebrow">Step 2</p>
                            <h2 id="pf-pricing-heading">Transparent Pricing</h2>
                            <p class="sub">One clear price for {{ $portfolio->title }} — no hidden fees at checkout.</p>
                        </div>

                        <div class="summary-card pf-show-pricing-card rc-animate">
                            <div class="summary-card-head">
                                <h5><i class="fas fa-tag me-2" aria-hidden="true"></i>{{ $portfolio->title }}</h5>
                            </div>
                            <div class="driver-block">
                                <div class="d-flex align-items-end justify-content-between gap-3 flex-wrap">
                                    <div>
                                        <div class="pf-pricing-label">Total test fee</div>
                                        <div class="pf-pricing-amount">${{ $priceFormatted }}</div>
                                    </div>
                                    <span class="pf-pricing-badge">{{ $portfolio->category_name }}</span>
                                </div>
                            </div>
                            <ul class="pf-pricing-details">
                                <li>
                                    <span class="fee-label"><i class="fas fa-vial" aria-hidden="true"></i> Service</span>
                                    <span class="fee-val">{{ $portfolio->title }}</span>
                                </li>
                                @if (!empty($portfolio->code))
                                    <li>
                                        <span class="fee-label"><i class="fas fa-barcode" aria-hidden="true"></i> Unit code</span>
                                        <span class="fee-val">{{ $portfolio->code }}</span>
                                    </li>
                                @endif
                                <li>
                                    <span class="fee-label"><i class="fas fa-credit-card" aria-hidden="true"></i> Payment</span>
                                    <span class="fee-val">Secure Stripe Checkout</span>
                                </li>
                            </ul>
                            <div class="pf-pricing-note">
                                <i class="fas fa-info-circle" aria-hidden="true"></i>
                                Price is calculated server-side at checkout. You will complete scheduling after payment.
                            </div>
                            @guest
                                <button type="button" class="pf-btn-submit pf-show-scroll-cta" data-reveal-login="true">
                                    <i class="fas fa-arrow-down"></i>
                                    Continue to Sign In
                                </button>
                            @else
                                <a href="#application-form" class="pf-btn-submit pf-show-scroll-cta">
                                    <i class="fas fa-arrow-down"></i>
                                    {{ $isNonDot ? 'Continue to Application' : 'Continue to Employee Selection' }}
                                </a>
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 4. Call-to-Action / Login / Application --}}
        <section class="pf-show-cta" id="application-form" aria-labelledby="pf-cta-heading">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-xl-7 pf-show-action-col">
                        <div class="rc-section-head text-center rc-animate mb-4">
                            <p class="section-eyebrow">Step 3</p>
                            <h2 id="pf-cta-heading">{{ auth()->check() ? 'Ready to Schedule?' : 'When You\'re Ready' }}</h2>
                            <p class="sub">
                                @guest
                                    Review the service details and pricing above, then sign in when you are ready to proceed.
                                @else
                                    {{ $isNonDot ? 'Complete your application details below, then proceed to secure checkout.' : 'Select the employee who will take this test, then proceed to checkout.' }}
                                @endguest
                            </p>
                        </div>

                        @guest
                            {{-- Teaser: shown first; login form hidden until user opts in --}}
                            <div id="pf-login-teaser" class="pf-card pf-show-teaser rc-animate {{ $revealLoginPanel ? 'd-none' : '' }}">
                                <div class="pf-body text-center py-4 px-3">
                                    <div class="pf-show-teaser-icon mx-auto mb-3" aria-hidden="true">
                                        <i class="fas fa-user-lock"></i>
                                    </div>
                                    <h3 class="pf-show-teaser-title">Ready to schedule this test?</h3>
                                    <p class="pf-show-teaser-text">Sign in to apply for {{ $portfolio->title }}, complete checkout, and track your order under your account.</p>
                                    <button type="button" class="pf-btn-submit pf-show-reveal-login" data-reveal-login="true">
                                        <i class="fas fa-sign-in-alt"></i>
                                        Sign In to Continue
                                    </button>
                                </div>
                            </div>

                            <div id="pf-login-panel" class="pf-show-login-panel {{ $revealLoginPanel ? 'is-visible' : '' }}">
                            <div class="pf-card rc-animate">
                                <div class="pf-header">
                                    <div class="d-flex align-items-start justify-content-between gap-3">
                                        <div>
                                            <span class="pill">Account Required</span>
                                            <h4>Sign in to schedule your test</h4>
                                            <p>Login is required to apply for {{ $portfolio->title }} and track your tests.</p>
                                        </div>
                                        <div class="pf-header-icon d-none d-sm-flex">
                                            <i class="fas fa-user-lock"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="pf-body">
                                    @if (session('status'))
                                        <div class="pf-alert pf-alert-success mb-3" role="alert">
                                            <i class="fas fa-check-circle mt-1"></i>
                                            <div>{{ session('status') }}</div>
                                        </div>
                                    @endif
                                    @if (session('error'))
                                        <div class="pf-alert pf-alert-danger mb-3" role="alert">
                                            <i class="fas fa-exclamation-circle mt-1"></i>
                                            <div>{{ session('error') }}</div>
                                        </div>
                                    @endif
                                    @if ($errors->any())
                                        <div class="pf-alert pf-alert-danger mb-3" role="alert">
                                            <i class="fas fa-exclamation-triangle mt-1"></i>
                                            <div>
                                                <strong>Login failed:</strong>
                                                <ul class="mt-1 mb-0 ps-3">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="pf-alert pf-alert-info mb-4" role="status">
                                        <i class="fas fa-info-circle mt-1"></i>
                                        <div>After signing in you will return to this page to complete your {{ $isNonDot ? 'application' : 'employee selection' }} and checkout.</div>
                                    </div>

                                    <form method="POST" action="{{ route('portfolio.login.submit', $portfolio->id) }}">
                                        @csrf

                                        <div class="pf-section">
                                            <div class="pf-section-head">
                                                <div class="icon-wrap"><i class="fas fa-envelope"></i></div>
                                                <h6>Your Credentials</h6>
                                            </div>
                                            <div class="pf-section-body">
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label class="pf-label" for="login_email">Email Address <span class="pf-req">*</span></label>
                                                        <div class="pf-icon-wrap">
                                                            <i class="fas fa-envelope pf-icon"></i>
                                                            <input type="email" name="email" id="login_email"
                                                                class="pf-control @error('email') is-invalid @enderror"
                                                                value="{{ old('email') }}" required autofocus
                                                                placeholder="you@example.com">
                                                        </div>
                                                        @error('email')
                                                            <div class="pf-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="pf-label" for="login_password">Password <span class="pf-req">*</span></label>
                                                        <div class="pf-icon-wrap">
                                                            <i class="fas fa-lock pf-icon"></i>
                                                            <input type="password" name="password" id="login_password"
                                                                class="pf-control @error('password') is-invalid @enderror"
                                                                required placeholder="Enter your password">
                                                        </div>
                                                        @error('password')
                                                            <div class="pf-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="pf-terms-label d-flex align-items-center gap-2 mb-0">
                                                            <input type="checkbox" name="remember" id="remember" style="width:18px;height:18px;accent-color:var(--pf-primary);">
                                                            Remember me on this device
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="submit" class="pf-btn-submit">
                                            <i class="fas fa-sign-in-alt"></i>
                                            Sign In &amp; Continue
                                        </button>

                                        <div class="text-center mt-3">
                                            @if (Route::has('password.request'))
                                                <a href="{{ route('password.request') }}" class="pf-login-link">Forgot your password?</a>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            </div>
                            </div>{{-- /pf-login-panel --}}
                        @else
                            <div class="pf-card rc-animate">
                                <div class="pf-header">
                                    <div class="d-flex align-items-start justify-content-between gap-3">
                                        <div>
                                            <span class="pill">{{ $isNonDot ? 'Non-DOT Testing' : 'DOT Testing' }}</span>
                                            <h4>{{ $isNonDot ? 'Apply for' : 'Schedule' }} {{ $portfolio->title }}</h4>
                                            <p>Complete your order details below, then proceed to secure Stripe checkout. Your order will be submitted to Quest Diagnostics automatically after payment.</p>
                                        </div>
                                        <div class="pf-header-icon d-none d-sm-flex">
                                            <i class="fas {{ $isNonDot ? 'fa-clipboard-list' : 'fa-users' }}"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="pf-body pb-0">
                                    <div id="pf-form-errors" class="pf-alert pf-alert-danger d-none mb-3" role="alert">
                                        <i class="fas fa-exclamation-triangle mt-1"></i>
                                        <div id="pf-form-errors-body"></div>
                                    </div>
                                    @if (session('success'))
                                        <div class="pf-alert pf-alert-success mb-3" role="alert">
                                            <i class="fas fa-check-circle mt-1"></i>
                                            <div>{{ session('success') }}</div>
                                        </div>
                                    @endif
                                    @if (session('info'))
                                        <div class="pf-alert pf-alert-success mb-3" role="alert">
                                            <i class="fas fa-info-circle mt-1"></i>
                                            <div>{{ session('info') }}</div>
                                        </div>
                                    @endif
                                    @if (session('error'))
                                        <div class="pf-alert pf-alert-danger mb-3" role="alert">
                                            <i class="fas fa-exclamation-circle mt-1"></i>
                                            <div>{{ session('error') }}</div>
                                        </div>
                                    @endif
                                </div>

                                <div class="pf-body pt-2">
                                    <form id="portfolio-checkout-form">
                                        @csrf
                                        <input type="hidden" name="portfolio_id" value="{{ $portfolio->id }}">
                                        <input type="hidden" name="test_type" value="{{ $isNonDot ? 'non_dot' : 'dot' }}">

                                        @include('quest.partials.order-fields', [
                                            'questDefaults' => $questDefaults,
                                            'questIsPhysical' => $questIsPhysical,
                                            'questIsEbat' => $questIsEbat,
                                        ])

                                        <div class="pf-price-display mb-4">
                                            <div>
                                                <div class="label">Total Amount Due</div>
                                                <div style="font-size:.75rem;color:var(--pf-muted);margin-top:2px;">{{ $portfolio->title }}</div>
                                            </div>
                                            <div class="amount">${{ $priceFormatted }}</div>
                                        </div>

                                        @if (!$isNonDot && $employees->isEmpty())
                                            {{-- checkout disabled when no employees --}}
                                        @else
                                            <div class="pf-terms mb-4">
                                                <input type="checkbox" id="terms-check" required>
                                                <label class="pf-terms-label" for="terms-check">
                                                    I agree to the <a href="{{ route('frontend.terms-and-conditions') }}" target="_blank" rel="noopener">Terms and Conditions</a>
                                                    and <a href="{{ route('frontend.privacy-policy') }}" target="_blank" rel="noopener">Privacy Policy</a>.
                                                </label>
                                            </div>

                                            <button type="button" id="portfolio-checkout-btn" class="pf-btn-submit">
                                                <i class="fas fa-lock"></i>
                                                Continue to Checkout — ${{ $priceFormatted }}
                                            </button>
                                            <p class="pf-secure"><i class="fas fa-shield-alt"></i> Secure payment via Stripe Checkout</p>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        </section>

        {{-- Trust strip --}}
        <div class="dst-trust-strip svc-trust-strip">
            <div class="container">
                <div class="dst-trust-inner">
                    <span><i class="fas fa-shield-alt" aria-hidden="true"></i> Certified Labs</span>
                    <span><i class="fas fa-clock" aria-hidden="true"></i> Fast Turnaround</span>
                    <span><i class="fas fa-lock" aria-hidden="true"></i> Confidential Results</span>
                    <span><i class="fas fa-headset" aria-hidden="true"></i> Expert Support</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Stripe Checkout loader --}}
    <div id="loader-overlay" role="dialog" aria-modal="true" aria-labelledby="pf-loader-title" aria-hidden="true">
        <div class="rc-loader-card">
            <div class="rc-loader-spinner" aria-hidden="true">
                <div class="rc-loader-ring"></div>
                <div class="rc-loader-ring rc-loader-ring--inner"></div>
            </div>
            <h4 class="fw-bold" id="pf-loader-title" style="font-family:var(--pf-font-head);">Generating Secure Stripe Checkout…</h4>
            <p class="rc-loader-sub">Please wait, redirecting shortly.</p>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function initScrollAnimations() {
                var items = document.querySelectorAll('.pf-show-page .rc-animate');
                if (!items.length) return;

                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    items.forEach(function (el) { el.classList.add('rc-visible'); });
                    return;
                }

                if (!('IntersectionObserver' in window)) {
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

            function revealLoginPanel() {
                var teaser = document.getElementById('pf-login-teaser');
                var panel = document.getElementById('pf-login-panel');
                var cta = document.getElementById('application-form');
                if (teaser) teaser.classList.add('d-none');
                if (panel) panel.classList.add('is-visible');
                if (cta) {
                    cta.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    window.setTimeout(function () {
                        var email = document.getElementById('login_email');
                        if (email) email.focus();
                    }, 450);
                }
            }

            function updateStepper(activeStep) {
                var stepper = document.getElementById('pf-stepper');
                if (!stepper) return;

                stepper.querySelectorAll('.rc-step').forEach(function (step) {
                    var n = parseInt(step.dataset.step, 10);
                    step.classList.toggle('rc-step--active', n <= activeStep);
                    step.classList.toggle('rc-step--current', n === activeStep);
                });

                stepper.querySelectorAll('.rc-step-line-fill').forEach(function (line) {
                    var n = parseInt(line.dataset.line, 10);
                    line.classList.toggle('rc-step-line-fill--done', n < activeStep);
                });
            }

            function initStepperClicks() {
                var stepper = document.getElementById('pf-stepper');
                if (!stepper) return;

                var targets = {
                    1: '#pf-step-learn',
                    2: '#pf-step-pricing',
                    3: '#application-form',
                };

                function goToStep(n) {
                    var selector = targets[n];
                    var el = selector ? document.querySelector(selector) : null;
                    if (n === 3 && document.getElementById('pf-login-teaser')) {
                        revealLoginPanel();
                        updateStepper(3);
                        return;
                    }
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                    updateStepper(n);
                }

                stepper.querySelectorAll('.rc-step').forEach(function (step) {
                    step.addEventListener('click', function () {
                        goToStep(parseInt(step.dataset.step, 10));
                    });
                    step.addEventListener('keydown', function (e) {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            goToStep(parseInt(step.dataset.step, 10));
                        }
                    });
                });
            }

            function initStepperOnScroll() {
                var intro = document.getElementById('pf-step-learn');
                var pricing = document.getElementById('pf-step-pricing');
                var cta = document.getElementById('application-form');

                if (intro) {
                    new IntersectionObserver(function (entries) {
                        entries.forEach(function (entry) {
                            if (entry.isIntersecting) updateStepper(1);
                        });
                    }, { threshold: 0.2 }).observe(intro);
                }

                if (pricing) {
                    new IntersectionObserver(function (entries) {
                        entries.forEach(function (entry) {
                            if (entry.isIntersecting) updateStepper(2);
                        });
                    }, { threshold: 0.25 }).observe(pricing);
                }

                if (cta) {
                    new IntersectionObserver(function (entries) {
                        entries.forEach(function (entry) {
                            if (entry.isIntersecting) updateStepper(3);
                        });
                    }, { threshold: 0.15 }).observe(cta);
                }
            }

            initScrollAnimations();
            initScrollProgress();
            initStepperClicks();
            initStepperOnScroll();
            updateStepper(1);

            document.querySelectorAll('[data-reveal-login="true"]').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    revealLoginPanel();
                });
            });

            document.querySelectorAll('a.pf-show-scroll-cta').forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    var target = document.getElementById('application-form');
                    if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            });

            if (window.location.hash === '#application-form' || window.location.hash === '#sign-in') {
                revealLoginPanel();
            }

            const loader = document.getElementById('loader-overlay');
            const errBox = document.getElementById('pf-form-errors');
            const errBody = document.getElementById('pf-form-errors-body');

            function showErrors(messages) {
                if (!errBox || !errBody) return;
                errBody.innerHTML = '';
                (messages || []).forEach(function (msg) {
                    errBody.innerHTML += '<div><i class="fas fa-exclamation-circle me-2"></i>' + msg + '</div>';
                });
                errBox.classList.remove('d-none');
                errBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            function startCheckout(url, form) {
                if (!form.reportValidity()) return;
                if (loader) {
                    loader.classList.add('show');
                    loader.setAttribute('aria-hidden', 'false');
                }
                if (errBox) errBox.classList.add('d-none');

                fetch(url, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.success && data.redirect_url) {
                        window.location.href = data.redirect_url;
                        return;
                    }
                    if (loader) {
                        loader.classList.remove('show');
                        loader.setAttribute('aria-hidden', 'true');
                    }
                    showErrors(data.errors || ['An unexpected error occurred. Please try again.']);
                })
                .catch(function () {
                    if (loader) {
                        loader.classList.remove('show');
                        loader.setAttribute('aria-hidden', 'true');
                    }
                    showErrors(['Unable to connect to the server. Please check your internet and try again.']);
                });
            }

            const checkoutBtn = document.getElementById('portfolio-checkout-btn');
            const checkoutForm = document.getElementById('portfolio-checkout-form');
            if (checkoutBtn && checkoutForm) {
                checkoutBtn.addEventListener('click', function () {
                    const terms = document.getElementById('terms-check');
                    if (terms && !terms.checked) {
                        showErrors(['You must agree to the Terms and Conditions.']);
                        return;
                    }
                    const checkoutUrl = @json($isNonDot ? route('frontend.portfolio-test.checkout.non-dot') : route('frontend.portfolio-test.checkout.dot'));
                    startCheckout(checkoutUrl, checkoutForm);
                });
            }
        });
    </script>

    @include('quest.partials.order-fields-scripts', [
        'questIsPhysical' => $questIsPhysical,
    ])

@endsection
