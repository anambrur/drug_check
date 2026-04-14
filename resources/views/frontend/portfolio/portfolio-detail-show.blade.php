<!--// Portfolio Single Section Start //-->
<section class="section" id="portfolio-single-page">
    <div class="container">
        @isset($portfolio_content)
            <div class="portfolio-single-inner custom-blog-img">
                <h4>{{ $portfolio->title }}</h4>
                <div class="author-meta">
                    <a href="#"><span class="far fa-bookmark"></span>{{ $portfolio->category_name }}</a>
                </div>
                <p>@php echo html_entity_decode($portfolio_content->description); @endphp </p>
            </div>
        @endisset

        <hr>

        {{-- ════════════════════════════════════════════
        NON-DOT: Full Application + Payment Form
        ════════════════════════════════════════════ --}}
        @if ($portfolio->category_name == 'Non DOT Testing')

            {{-- ── Google Fonts (same as Quest form) ── --}}
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link
                href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=DM+Sans:wght@300;400;500&display=swap"
                rel="stylesheet">

            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


            <style>
                /* ── Design tokens (shared with Quest form) ── */
                :root {
                    --pf-primary: #1a56db;
                    --pf-primary-dark: #1044b3;
                    --pf-primary-light: #e8f0fe;
                    --pf-primary-glow: rgba(26, 86, 219, .15);
                    --pf-accent: #06b6d4;
                    --pf-success: #059669;
                    --pf-danger: #e11d48;
                    --pf-surface: #ffffff;
                    --pf-surface-2: #f8faff;
                    --pf-border: #e2e8f8;
                    --pf-text: #0f172a;
                    --pf-muted: #64748b;
                    --pf-light: #94a3b8;
                    --pf-shadow-sm: 0 1px 3px rgba(15, 23, 42, .06), 0 1px 2px rgba(15, 23, 42, .04);
                    --pf-shadow-md: 0 4px 16px rgba(15, 23, 42, .08), 0 2px 6px rgba(15, 23, 42, .05);
                    --pf-shadow-lg: 0 20px 60px rgba(15, 23, 42, .12), 0 8px 24px rgba(15, 23, 42, .07);
                    --pf-radius: 14px;
                    --pf-radius-sm: 9px;
                    --pf-font-head: 'Sora', sans-serif;
                    --pf-font-body: 'DM Sans', sans-serif;
                }

                /* ── Page wrapper ── */
                #application-form {

                    font-family: var(--pf-font-body);
                }

                /* ── Outer card ── */
                .pf-card {
                    background: var(--pf-surface);
                    border-radius: 20px;
                    box-shadow: var(--pf-shadow-lg);
                    border: 1px solid var(--pf-border);
                    overflow: hidden;
                }

                /* ── Header ── */
                .pf-header {
                    background: linear-gradient(135deg, #1a56db 0%, #0e3fa3 60%, #0c2f80 100%);
                    padding: 2.25rem 2.5rem 2rem;
                    position: relative;
                    overflow: hidden;
                }

                .pf-header::before {
                    content: '';
                    position: absolute;
                    top: -60px;
                    right: -60px;
                    width: 220px;
                    height: 220px;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, .05);
                }

                .pf-header::after {
                    content: '';
                    position: absolute;
                    bottom: -40px;
                    left: 30%;
                    width: 160px;
                    height: 160px;
                    border-radius: 50%;
                    background: rgba(6, 182, 212, .1);
                }

                .pf-header .pill {
                    background: rgba(255, 255, 255, .15);
                    backdrop-filter: blur(6px);
                    border: 1px solid rgba(255, 255, 255, .2);
                    color: #fff;
                    font-family: var(--pf-font-head);
                    font-size: .7rem;
                    font-weight: 500;
                    letter-spacing: .06em;
                    text-transform: uppercase;
                    padding: .35rem .9rem;
                    border-radius: 100px;
                    display: inline-block;
                    margin-bottom: .85rem;
                }

                .pf-header h4 {
                    font-family: var(--pf-font-head);
                    font-size: 1.55rem;
                    font-weight: 700;
                    color: #fff;
                    margin-bottom: .35rem;
                    line-height: 1.3;
                }

                .pf-header p {
                    color: rgba(255, 255, 255, .7);
                    font-size: .9rem;
                    margin: 0;
                }

                .pf-header-icon {
                    width: 48px;
                    height: 48px;
                    background: rgba(255, 255, 255, .12);
                    border-radius: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.3rem;
                    color: #fff;
                    flex-shrink: 0;
                }

                /* ── Body ── */
                .pf-body {
                    padding: 2rem 2.5rem 2.5rem;
                }

                /* ── Section blocks ── */
                .pf-section {
                    border: 1px solid var(--pf-border);
                    border-radius: var(--pf-radius);
                    overflow: hidden;
                    margin-bottom: 1.75rem;
                    background: var(--pf-surface);
                    transition: box-shadow .2s;
                }

                .pf-section:focus-within {
                    box-shadow: 0 0 0 3px var(--pf-primary-glow);
                    border-color: rgba(26, 86, 219, .3);
                }

                .pf-section-head {
                    background: var(--pf-primary-light);
                    padding: .9rem 1.5rem;
                    display: flex;
                    align-items: center;
                    gap: .7rem;
                    border-bottom: 1px solid rgba(26, 86, 219, .1);
                }

                .pf-section-head .icon-wrap {
                    width: 30px;
                    height: 30px;
                    background: var(--pf-primary);
                    border-radius: 8px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #fff;
                    font-size: .75rem;
                    flex-shrink: 0;
                }

                .pf-section-head h6 {
                    font-family: var(--pf-font-head);
                    font-size: .9rem;
                    font-weight: 700;
                    color: var(--pf-primary-dark);
                    margin: 0;
                }

                .pf-section-body {
                    padding: 1.5rem;
                }

                /* ── Controls ── */
                .pf-label {
                    font-family: var(--pf-font-head);
                    font-size: .8rem;
                    font-weight: 600;
                    color: var(--pf-text);
                    letter-spacing: .01em;
                    margin-bottom: .4rem;
                    display: block;
                }

                .pf-req {
                    color: var(--pf-danger);
                    margin-left: 2px;
                }

                .pf-opt {
                    font-size: .68rem;
                    background: #f1f5f9;
                    color: var(--pf-muted);
                    border-radius: 4px;
                    padding: 1px 6px;
                    font-weight: 500;
                    margin-left: 6px;
                    vertical-align: middle;
                }

                .pf-control {
                    width: 100%;
                    border: 1.5px solid var(--pf-border);
                    border-radius: var(--pf-radius-sm);
                    padding: .65rem 1rem;
                    font-size: .88rem;
                    font-family: var(--pf-font-body);
                    color: var(--pf-text);
                    background: var(--pf-surface);
                    transition: border-color .2s, box-shadow .2s, background .2s;
                    box-shadow: var(--pf-shadow-sm);
                    outline: none;
                    -webkit-appearance: none;
                    appearance: none;
                }

                .pf-control::placeholder {
                    color: var(--pf-light);
                }

                .pf-control:focus {
                    border-color: var(--pf-primary);
                    box-shadow: 0 0 0 3.5px var(--pf-primary-glow);
                    background: #fafcff;
                }

                .pf-control.is-invalid {
                    border-color: var(--pf-danger);
                }

                .pf-control.is-valid {
                    border-color: var(--pf-success);
                }

                select.pf-control {
                    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2394a3b8' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
                    background-repeat: no-repeat;
                    background-position: right 1rem center;
                    padding-right: 2.5rem;
                }

                textarea.pf-control {
                    resize: vertical;
                    min-height: 90px;
                }

                .pf-feedback {
                    font-size: .77rem;
                    color: var(--pf-danger);
                    font-weight: 500;
                    margin-top: .3rem;
                }

                .pf-hint {
                    font-size: .76rem;
                    color: var(--pf-muted);
                    margin-top: .3rem;
                    display: flex;
                    align-items: center;
                    gap: .3rem;
                }

                /* icon prefix */
                .pf-icon-wrap {
                    position: relative;
                }

                .pf-icon-wrap .pf-icon {
                    position: absolute;
                    left: .9rem;
                    top: 50%;
                    transform: translateY(-50%);
                    color: var(--pf-light);
                    font-size: .82rem;
                    pointer-events: none;
                }

                .pf-icon-wrap .pf-control {
                    padding-left: 2.4rem;
                }

                /* readonly price */
                .pf-price-display {
                    background: linear-gradient(135deg, var(--pf-primary-light), #e0ecff);
                    border: 1.5px solid rgba(26, 86, 219, .2);
                    border-radius: var(--pf-radius-sm);
                    padding: 1rem 1.5rem;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                }

                .pf-price-display .label {
                    font-family: var(--pf-font-head);
                    font-size: .82rem;
                    font-weight: 600;
                    color: var(--pf-muted);
                }

                .pf-price-display .amount {
                    font-family: var(--pf-font-head);
                    font-size: 1.6rem;
                    font-weight: 700;
                    color: var(--pf-primary);
                }

                /* gender radios */
                .pf-radio-group {
                    display: flex;
                    flex-wrap: wrap;
                    gap: .75rem;
                }

                .pf-radio-item {
                    display: flex;
                    align-items: center;
                    gap: .5rem;
                    border: 1.5px solid var(--pf-border);
                    border-radius: var(--pf-radius-sm);
                    padding: .55rem 1.1rem;
                    cursor: pointer;
                    transition: all .2s;
                    font-family: var(--pf-font-head);
                    font-size: .85rem;
                    font-weight: 500;
                    color: var(--pf-muted);
                    user-select: none;
                }

                .pf-radio-item input[type="radio"] {
                    accent-color: var(--pf-primary);
                    width: 15px;
                    height: 15px;
                }

                .pf-radio-item:has(input:checked) {
                    border-color: var(--pf-primary);
                    background: var(--pf-primary-light);
                    color: var(--pf-primary);
                }

                /* ── Payment panel ── */
                .pf-payment-panel {
                    background: #f8faff;
                    border: 1.5px solid var(--pf-border);
                    border-radius: var(--pf-radius-sm);
                    padding: 1.5rem;
                }

                .pf-card-icons {
                    display: flex;
                    align-items: center;
                    gap: .5rem;
                    flex-wrap: wrap;
                }

                .pf-card-icons i {
                    font-size: 1.6rem;
                }

                /* Stripe element wrapper */
                .pf-stripe-wrap {
                    border: 1.5px solid var(--pf-border);
                    border-radius: var(--pf-radius-sm);
                    padding: .7rem 1rem;
                    background: #fff;
                    box-shadow: var(--pf-shadow-sm);
                    transition: border-color .2s, box-shadow .2s;
                }

                .pf-stripe-wrap.StripeElement--focus {
                    border-color: var(--pf-primary);
                    box-shadow: 0 0 0 3.5px var(--pf-primary-glow);
                }

                .pf-stripe-wrap.StripeElement--invalid {
                    border-color: var(--pf-danger);
                }

                /* country select override */
                #country.pf-control {
                    height: auto;
                }

                /* ── Alert ── */
                .pf-alert {
                    border-radius: var(--pf-radius-sm);
                    border: none;
                    font-size: .875rem;
                    padding: .9rem 1.1rem;
                    margin-bottom: 1.25rem;
                    display: flex;
                    align-items: flex-start;
                    gap: .7rem;
                }

                .pf-alert-danger {
                    background: rgba(225, 29, 72, .05);
                    color: #9f1239;
                    border: 1px solid rgba(225, 29, 72, .2);
                }

                .pf-alert-success {
                    background: rgba(5, 150, 105, .05);
                    color: #065f46;
                    border: 1px solid rgba(5, 150, 105, .2);
                }

                .pf-alert-info {
                    background: rgba(26, 86, 219, .05);
                    color: var(--pf-primary-dark);
                    border: 1px solid rgba(26, 86, 219, .2);
                }

                /* ── Terms checkbox ── */
                .pf-terms {
                    border: 1.5px solid var(--pf-border);
                    border-radius: var(--pf-radius-sm);
                    padding: 1rem 1.25rem;
                    display: flex;
                    align-items: flex-start;
                    gap: .75rem;
                    background: var(--pf-surface-2);
                }

                .pf-terms input[type="checkbox"] {
                    width: 18px;
                    height: 18px;
                    accent-color: var(--pf-primary);
                    flex-shrink: 0;
                    margin-top: 2px;
                }

                .pf-terms-label {
                    font-size: .85rem;
                    color: var(--pf-muted);
                    line-height: 1.5;
                }

                .pf-terms-label a {
                    color: var(--pf-primary);
                    font-weight: 600;
                    text-decoration: none;
                }

                .pf-terms-label a:hover {
                    text-decoration: underline;
                }

                /* ── Submit button ── */
                .pf-btn-submit {
                    background: linear-gradient(135deg, #1a56db 0%, #0e3fa3 100%);
                    border: none;
                    border-radius: 12px;
                    width: 100%;
                    font-family: var(--pf-font-head);
                    font-weight: 700;
                    font-size: 1rem;
                    color: #fff;
                    padding: 1rem 2rem;
                    transition: all .25s;
                    box-shadow: 0 4px 20px rgba(26, 86, 219, .35);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: .6rem;
                    cursor: pointer;
                }

                .pf-btn-submit:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 30px rgba(26, 86, 219, .45);
                    color: #fff;
                }

                .pf-btn-submit:active {
                    transform: translateY(0);
                }

                .pf-btn-submit:disabled {
                    opacity: .7;
                    cursor: not-allowed;
                    transform: none;
                }

                /* ── Security badge ── */
                .pf-secure {
                    font-size: .78rem;
                    color: var(--pf-muted);
                    display: flex;
                    align-items: center;
                    gap: .4rem;
                    justify-content: center;
                    margin-top: .75rem;
                }

                .pf-secure i {
                    color: #059669;
                }

                /* ── Success modal ── */
                .pf-success-icon {
                    width: 80px;
                    height: 80px;
                    border-radius: 50%;
                    background: #059669;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 1.25rem;
                    font-size: 2rem;
                    color: #fff;
                }

                /* ── Login card ── */
                .pf-login-card {
                    border: 1px solid var(--pf-border);
                    border-radius: var(--pf-radius);
                    overflow: hidden;
                    box-shadow: var(--pf-shadow-md);
                    background: var(--pf-surface);
                }

                /* ── Responsive ── */
                @media (max-width:768px) {
                    .pf-header {
                        padding: 1.75rem 1.5rem 1.5rem;
                    }

                    .pf-body {
                        padding: 1.5rem 1.25rem 2rem;
                    }

                    .pf-btn-submit {
                        font-size: .9rem;
                        padding: .85rem 1.5rem;
                    }
                }
            </style>

            <section id="application-form">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="">
                            <div class="pf-card">

                                {{-- Header --}}
                                <div class="pf-header">
                                    <div class="d-flex align-items-start justify-content-between gap-3">
                                        <div>
                                            <span class="pill">Non-DOT Testing</span>
                                            <h4>Apply for {{ $portfolio->title }}</h4>
                                            <p>Complete your details and payment to schedule your test</p>
                                        </div>
                                        <div class="pf-header-icon d-none d-sm-flex">
                                            <i class="fas fa-clipboard-list"></i>
                                        </div>
                                    </div>
                                </div>

                                {{-- Alerts --}}
                                <div class="pf-body pb-0">
                                    @if (session('success'))
                                        <div class="pf-alert pf-alert-success" role="alert">
                                            <i class="fas fa-check-circle mt-1"></i>
                                            <div>{{ session('success') }}</div>
                                        </div>
                                    @endif
                                    @if (session('error'))
                                        <div class="pf-alert pf-alert-danger" role="alert">
                                            <i class="fas fa-exclamation-circle mt-1"></i>
                                            <div>{{ session('error') }}</div>
                                        </div>
                                    @endif
                                    @if ($errors->any())
                                        <div class="pf-alert pf-alert-danger" role="alert">
                                            <i class="fas fa-exclamation-triangle mt-1"></i>
                                            <div>
                                                <strong>Please fix these errors:</strong>
                                                <ul class="mt-1 mb-0 ps-3">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="pf-body pt-2">
                                    <form id="payment-form" action="{{ route('send.mail_dot') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="portfolio_id" id="portfolio_id"
                                            value="{{ $portfolio->id }}">
                                        <input type="hidden" name="payment_intent_id" id="payment_intent_id">
                                        <input type="hidden" name="test_name" id="test_name"
                                            value="{{ $portfolio->title }}">
                                        <input type="hidden" name="code" value="{{ $portfolio->code }}">
                                        <input type="hidden" name="lab_account" value="{{ $portfolio->lab_account }}">

                                        {{-- ── 1. Personal Information ── --}}
                                        <div class="pf-section">
                                            <div class="pf-section-head">
                                                <div class="icon-wrap"><i class="fas fa-user"></i></div>
                                                <h6>Personal Information</h6>
                                            </div>
                                            <div class="pf-section-body">
                                                <div class="row g-3">

                                                    {{-- First Name --}}
                                                    <div class="col-md-6">
                                                        <label class="pf-label" for="first_name">First Name <span
                                                                class="pf-req">*</span></label>
                                                        <div class="pf-icon-wrap">
                                                            <i class="fas fa-user pf-icon"></i>
                                                            <input type="text" id="first_name" name="first_name"
                                                                class="pf-control @error('first_name') is-invalid @enderror"
                                                                value="{{ old('first_name') }}" placeholder="e.g. John"
                                                                required>
                                                        </div>
                                                        @error('first_name')
                                                            <div class="pf-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- Last Name --}}
                                                    <div class="col-md-6">
                                                        <label class="pf-label" for="last_name">Last Name <span
                                                                class="pf-req">*</span></label>
                                                        <div class="pf-icon-wrap">
                                                            <i class="fas fa-user pf-icon"></i>
                                                            <input type="text" id="last_name" name="last_name"
                                                                class="pf-control @error('last_name') is-invalid @enderror"
                                                                value="{{ old('last_name') }}" placeholder="e.g. Doe"
                                                                required>
                                                        </div>
                                                        @error('last_name')
                                                            <div class="pf-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- Email --}}
                                                    <div class="col-md-6">
                                                        <label class="pf-label" for="email">Email Address <span
                                                                class="pf-req">*</span></label>
                                                        <div class="pf-icon-wrap">
                                                            <i class="fas fa-envelope pf-icon"></i>
                                                            <input type="email" id="email" name="email"
                                                                class="pf-control @error('email') is-invalid @enderror"
                                                                value="{{ old('email') }}" placeholder="you@example.com"
                                                                required>
                                                        </div>
                                                        @error('email')
                                                            <div class="pf-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- Phone --}}
                                                    <div class="col-md-6">
                                                        <label class="pf-label" for="phone">Phone Number <span
                                                                class="pf-req">*</span></label>
                                                        <div class="pf-icon-wrap">
                                                            <i class="fas fa-phone pf-icon"></i>
                                                            <input type="tel" id="phone" name="phone"
                                                                class="pf-control @error('phone') is-invalid @enderror"
                                                                value="{{ old('phone') }}" placeholder="(555) 000-0000"
                                                                required>
                                                        </div>
                                                        @error('phone')
                                                            <div class="pf-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- Address --}}
                                                    <div class="col-12">
                                                        <label class="pf-label" for="address">Street Address <span
                                                                class="pf-opt">Optional</span></label>
                                                        <div class="pf-icon-wrap">
                                                            <i class="fas fa-map-marker-alt pf-icon"></i>
                                                            <input type="text" id="address" name="address"
                                                                class="pf-control @error('address') is-invalid @enderror"
                                                                value="{{ old('address') }}"
                                                                placeholder="123 Main St, City, State">
                                                        </div>
                                                        @error('address')
                                                            <div class="pf-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- Gender --}}
                                                    <div class="col-12">
                                                        <label class="pf-label">Gender <span class="pf-req">*</span></label>
                                                        <div class="pf-radio-group">
                                                            <label class="pf-radio-item">
                                                                <input type="radio" name="gender" value="Male"
                                                                    @checked(old('gender') == 'Male') required>
                                                                <i class="fas fa-mars"></i> Male
                                                            </label>
                                                            <label class="pf-radio-item">
                                                                <input type="radio" name="gender" value="Female"
                                                                    @checked(old('gender') == 'Female')>
                                                                <i class="fas fa-venus"></i> Female
                                                            </label>
                                                            <label class="pf-radio-item">
                                                                <input type="radio" name="gender" value="Other"
                                                                    @checked(old('gender') == 'Other')>
                                                                <i class="fas fa-genderless"></i> Other
                                                            </label>
                                                        </div>
                                                        @error('gender')
                                                            <div class="pf-feedback mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        {{-- ── 2. Testing Information ── --}}
                                        <div class="pf-section">
                                            <div class="pf-section-head">
                                                <div class="icon-wrap"><i class="fas fa-vial"></i></div>
                                                <h6>Testing Information</h6>
                                            </div>
                                            <div class="pf-section-body">
                                                <div class="row g-3">

                                                    {{-- Preferred Test Date --}}
                                                    <div class="col-md-6">
                                                        <label class="pf-label" for="date">Preferred Test Date
                                                            <span class="pf-opt">Optional</span></label>
                                                        <div class="pf-icon-wrap">
                                                            <i class="fas fa-calendar pf-icon"></i>

                                                            <input type="text" id="date" name="date"
                                                                class="pf-control @error('date') is-invalid @enderror"
                                                                value="{{ old('date', date('m-d-Y')) }}"
                                                                placeholder="MM-DD-YYYY" readonly>
                                                        </div>
                                                        @error('date')
                                                            <div class="pf-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- Preferred Location --}}
                                                    <div class="col-md-6">
                                                        <label class="pf-label" for="preferred_location">Preferred
                                                            Location <span class="pf-opt">Optional</span></label>
                                                        <div class="pf-icon-wrap">
                                                            <i class="fas fa-map-pin pf-icon"></i>
                                                            <input type="text" id="preferred_location"
                                                                name="preferred_location"
                                                                class="pf-control @error('preferred_location') is-invalid @enderror"
                                                                value="{{ old('preferred_location') }}"
                                                                placeholder="City or zip code">
                                                        </div>
                                                        @error('preferred_location')
                                                            <div class="pf-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- Employer Name --}}
                                                    <div class="col-md-6">
                                                        <label class="pf-label" for="employee_name">Employer Name
                                                            <span class="pf-req">*</span></label>
                                                        <div class="pf-icon-wrap">
                                                            <i class="fas fa-user-tie pf-icon"></i>
                                                            <input type="text" id="employee_name" name="employee_name"
                                                                class="pf-control @error('employee_name') is-invalid @enderror"
                                                                value="{{ old('employee_name') }}"
                                                                placeholder="Enter employer name" required>
                                                        </div>
                                                        @error('employee_name')
                                                            <div class="pf-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- Company Name --}}
                                                    <div class="col-md-6">
                                                        <label class="pf-label" for="company_name">Company Name <span
                                                                class="pf-opt">Optional</span></label>
                                                        <div class="pf-icon-wrap">
                                                            <i class="fas fa-building pf-icon"></i>
                                                            <input type="text" id="company_name" name="company_name"
                                                                class="pf-control @error('company_name') is-invalid @enderror"
                                                                value="{{ old('company_name') }}"
                                                                placeholder="Enter company name">
                                                        </div>
                                                        @error('company_name')
                                                            <div class="pf-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- Accounting Email --}}
                                                    <div class="col-md-6">
                                                        <label class="pf-label" for="accounting_email">Accounting
                                                            Email
                                                            <span class="pf-opt">Optional</span></label>
                                                        <div class="pf-icon-wrap">
                                                            <i class="fas fa-envelope pf-icon"></i>
                                                            <input type="email" id="accounting_email"
                                                                name="accounting_email"
                                                                class="pf-control @error('accounting_email') is-invalid @enderror"
                                                                value="{{ old('accounting_email') }}"
                                                                placeholder="accounts@company.com">
                                                        </div>
                                                        @error('accounting_email')
                                                            <div class="pf-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- Reason for Testing --}}
                                                    <div class="col-md-6">
                                                        <label class="pf-label" for="reason_for_testing">Reason for
                                                            Testing <span class="pf-req">*</span></label>
                                                        <select id="reason_for_testing" name="reason_for_testing"
                                                            class="pf-control @error('reason_for_testing') is-invalid @enderror"
                                                            required>
                                                            <option value="" disabled selected>Select a reason
                                                            </option>
                                                            <option value="Follow Up Test"
                                                                @selected(old('reason_for_testing') == 'Follow Up Test')>
                                                                Follow Up Test</option>
                                                            <option value="Pre Employment"
                                                                @selected(old('reason_for_testing') == 'Pre Employment')>Pre
                                                                Employment</option>
                                                            <option value="Random"
                                                                @selected(old('reason_for_testing') == 'Random')>Random
                                                            </option>
                                                            <option value="Return to Duty"
                                                                @selected(old('reason_for_testing') == 'Return to Duty')>
                                                                Return to Duty</option>
                                                            <option value="Post Accident"
                                                                @selected(old('reason_for_testing') == 'Post Accident')>
                                                                Post
                                                                Accident</option>
                                                            <option value="Promotion"
                                                                @selected(old('reason_for_testing') == 'Promotion')>
                                                                Promotion</option>
                                                            <option value="Reasonable Cause/Suspicion"
                                                                @selected(old('reason_for_testing') == 'Reasonable Cause/Suspicion')>Reasonable Cause/Suspicion
                                                            </option>
                                                            <option value="Other"
                                                                @selected(old('reason_for_testing') == 'Other')>Other
                                                            </option>
                                                        </select>
                                                        @error('reason_for_testing')
                                                            <div class="pf-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- Total Amount --}}
                                                    <div class="col-12">
                                                        <div class="pf-price-display">
                                                            <div>
                                                                <div class="label">Total Amount Due</div>
                                                                <div
                                                                    style="font-size:.75rem;color:var(--pf-muted);margin-top:2px;">
                                                                    {{ $portfolio->title }}
                                                                </div>
                                                            </div>
                                                            <div class="amount">${{ $portfolio->price }}</div>
                                                        </div>
                                                        <input type="hidden" name="price" id="price"
                                                            value="${{ $portfolio->price }}">
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        {{-- ── 3. Payment Details ── --}}
                                        <div class="pf-section">
                                            <div class="pf-section-head">
                                                <div class="icon-wrap"><i class="fas fa-credit-card"></i></div>
                                                <h6>Payment Details</h6>
                                            </div>
                                            <div class="pf-section-body">
                                                <div class="pf-payment-panel">

                                                    {{-- Accepted cards --}}
                                                    <div class="d-flex align-items-center gap-3 mb-4 pb-3"
                                                        style="border-bottom:1px solid var(--pf-border);">
                                                        <span
                                                            style="font-family:var(--pf-font-head);font-size:.78rem;font-weight:600;color:var(--pf-muted);">Accepted</span>
                                                        <div class="pf-card-icons">
                                                            <i class="fab fa-cc-visa" style="color:#1a1f71;"></i>
                                                            <i class="fab fa-cc-mastercard" style="color:#eb001b;"></i>
                                                            <i class="fab fa-cc-amex" style="color:#016fd0;"></i>
                                                            <i class="fab fa-cc-discover" style="color:#ff6000;"></i>
                                                            <i class="fab fa-cc-jcb" style="color:#0b4ea2;"></i>
                                                            <i class="fab fa-cc-diners-club" style="color:#0079be;"></i>
                                                        </div>
                                                    </div>

                                                    <div class="row g-3">

                                                        {{-- Cardholder Name --}}
                                                        <div class="col-12">
                                                            <label class="pf-label" for="cardholder-name">Cardholder
                                                                Name
                                                                <span class="pf-req">*</span></label>
                                                            <div class="pf-icon-wrap">
                                                                <i class="fas fa-id-badge pf-icon"></i>
                                                                <input type="text" id="cardholder-name" class="pf-control"
                                                                    placeholder="Name as it appears on card" required>
                                                            </div>
                                                        </div>

                                                        {{-- Card Number --}}
                                                        <div class="col-12">
                                                            <label class="pf-label">Card Number <span
                                                                    class="pf-req">*</span></label>
                                                            <div id="card-number" class="pf-stripe-wrap"></div>
                                                        </div>

                                                        {{-- Expiry + CVC --}}
                                                        <div class="col-md-4">
                                                            <label class="pf-label">Expiration Date <span
                                                                    class="pf-req">*</span></label>
                                                            <div id="card-expiry" class="pf-stripe-wrap"></div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="pf-label">CVC <span
                                                                    class="pf-req">*</span></label>
                                                            <div id="card-cvc" class="pf-stripe-wrap"></div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="pf-label">ZIP Code <span
                                                                    class="pf-req">*</span></label>
                                                            <div id="postal-code" class="pf-stripe-wrap"></div>
                                                        </div>

                                                        {{-- Country --}}
                                                        <div class="col-12">
                                                            <label class="pf-label" for="country">Country <span
                                                                    class="pf-opt">Optional</span></label>
                                                            <select id="country" name="country" class="pf-control">
                                                                <option value="" selected disabled>Select Country
                                                                </option>
                                                            </select>
                                                        </div>

                                                        {{-- Stripe errors --}}
                                                        <div class="col-12">
                                                            <div id="card-errors" role="alert"></div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- ── Terms ── --}}
                                        <div class="pf-terms mb-4">
                                            <input type="checkbox" id="terms-check" required>
                                            <label class="pf-terms-label" for="terms-check">
                                                I agree to the
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and
                                                    Conditions</a>
                                                and
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy
                                                    Policy</a>.
                                                By submitting, I authorize this charge to my card.
                                            </label>
                                        </div>

                                        {{-- ── Submit ── --}}
                                        <button type="button" id="pay-button" class="pf-btn-submit">
                                            <i class="fas fa-lock"></i>
                                            <span id="pay-button-text">Pay & Schedule Test —
                                                ${{ $portfolio->price }}</span>
                                            <span id="pay-button-loader"
                                                class="spinner-border spinner-border-sm d-none ms-1"></span>
                                        </button>
                                        <p class="pf-secure">
                                            <i class="fas fa-shield-alt"></i>
                                            256-bit SSL encrypted — your payment is fully secured
                                        </p>

                                    </form>
                                </div>{{-- /pf-body --}}
                            </div>{{-- /pf-card --}}
                        </div>
                    </div>
                </div>
            </section>

            {{-- Payment Success --}}
            <div class="modal fade" id="paymentSuccessModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content"
                        style="border-radius:var(--pf-radius);border:1px solid var(--pf-border);text-align:center;">
                        <div class="modal-body p-5">
                            <div class="pf-success-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <h4
                                style="font-family:var(--pf-font-head);font-weight:700;color:var(--pf-text);margin-bottom:.75rem;">
                                Payment Successful!</h4>
                            <p style="color:var(--pf-muted);font-size:.9rem;">Thank you! Your test has been scheduled.
                                Check your email for confirmation.</p>
                            <button type="button" class="pf-btn-submit mt-3" style="width:auto;padding:.75rem 2.5rem;"
                                data-bs-dismiss="modal">Continue</button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- ── DOT: Login Required ── --}}
            <!-- DOT Testing - Show login form -->
            <section class="section" id="application-form">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="card shadow-sm">
                                <div class="card-body p-5">
                                    <h2 class="text-center mb-4">Login Required for DOT Testing</h2>

                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        DOT testing requires user authentication for security and compliance purposes.
                                    </div>

                                    <!-- Status Messages -->
                                    @if (session('status'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <i class="fas fa-check-circle me-2"></i>
                                            {{ session('status') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                    @endif

                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong>Login failed:</strong>
                                            <ul class="mt-2 mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                    @endif

                                    <!-- Use custom login route with portfolio ID -->
                                    <form method="POST" action="{{ route('portfolio.login.submit', $portfolio->id) }}">
                                        @csrf

                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" name="email" value="{{ old('email') }}"
                                                class="form-control @error('email') is-invalid @enderror" id="email"
                                                required autofocus>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" name="password" required
                                                class="form-control @error('password') is-invalid @enderror" id="password">
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                            <label class="form-check-label" for="remember">Remember me</label>
                                        </div>

                                        <button type="submit" class="btn btn-primary w-100 py-2">
                                            <i class="fas fa-sign-in-alt me-2"></i> Login & Continue Application
                                        </button>

                                        <div class="text-center mt-3">
                                            @if (Route::has('password.request'))
                                                <a href="{{ route('password.request') }}" class="text-decoration-none">
                                                    Forgot your password?
                                                </a>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    </div>
</section>
<!--// Portfolio Single Section End //-->


<!-- Stripe.js -->
<script src="https://js.stripe.com/v3/"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        flatpickr("#date", {
            dateFormat: "m-d-Y",   // US format: MM-DD-YYYY
            defaultDate: "today",
            minDate: "today",
            allowInput: false
        });
        const form = document.getElementById('payment-form');
        const payButton = document.getElementById('pay-button');
        const payButtonText = document.getElementById('pay-button-text');
        const payButtonLoader = document.getElementById('pay-button-loader');
        const errorContainer = document.getElementById('card-errors');
        if (!form) return; // Only init on Non-DOT page

        const portfolioId = document.getElementById('portfolio_id')?.value || '';

        // ── Stripe Init ──
        const stripe = Stripe("{{ config('services.stripe.public') }}");
        const elements = stripe.elements();

        const elStyle = {
            base: {
                color: '#0f172a',
                fontFamily: "'DM Sans', sans-serif",
                fontSmoothing: 'antialiased',
                fontSize: '15px',
                '::placeholder': {
                    color: '#94a3b8'
                }
            },
            invalid: {
                color: '#e11d48',
                iconColor: '#e11d48'
            }
        };

        const cardNumber = elements.create('cardNumber', {
            style: elStyle,
            showIcon: true,
            placeholder: '4242 4242 4242 4242'
        });
        const cardExpiry = elements.create('cardExpiry', {
            style: elStyle
        });
        const cardCvc = elements.create('cardCvc', {
            style: elStyle
        });
        const postalCode = elements.create('postalCode', {
            style: elStyle
        });

        cardNumber.mount('#card-number');
        cardExpiry.mount('#card-expiry');
        cardCvc.mount('#card-cvc');
        postalCode.mount('#postal-code');

        // Stripe validation errors
        [cardNumber, cardExpiry, cardCvc, postalCode].forEach(el => {
            el.on('change', ev => {
                if (ev.error) showError(ev.error.message);
                else clearError();
            });
        });

        // ── Validation ──
        function validateForm() {
            let valid = true;
            clearError();

            ['first_name', 'last_name', 'email', 'phone', 'employee_name', 'reason_for_testing'].forEach(id => {
                const f = document.getElementById(id);
                if (!f) return;
                if (!f.value.trim()) {
                    f.classList.add('is-invalid');
                    valid = false;
                } else f.classList.remove('is-invalid');
            });

            const email = document.getElementById('email');
            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
                email.classList.add('is-invalid');
                valid = false;
            }

            const dateField = document.getElementById('date');
            if (dateField?.value) {
                // Parse MM-DD-YYYY from Flatpickr
                const parts = dateField.value.split('-');
                const selectedDate = new Date(parts[2], parts[0] - 1, parts[1]); // Y, M-1, D
                const today = new Date();
                today.setHours(0, 0, 0, 0); // strip time for fair comparison

                if (selectedDate < today) {
                    dateField.classList.add('is-invalid');
                    showError('Preferred test date cannot be in the past.');
                    valid = false;
                }
            }

            if (!document.querySelector('input[name="gender"]:checked')) {
                showError('Please select your gender.');
                valid = false;
            }

            if (!document.getElementById('terms-check')?.checked) {
                showError('You must agree to the Terms and Conditions.');
                valid = false;
            }

            return valid;
        }

        // ── Pay click ──
        payButton.addEventListener('click', async () => {
            if (!validateForm()) return;
            setLoading(true);

            try {
                const res = await fetch('/create-payment-intent', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .content
                    },
                    body: JSON.stringify({
                        portfolio_id: portfolioId,
                        country: document.getElementById('country')?.value || null
                    })
                });

                const data = await res.json();
                if (!res.ok) throw new Error(data.error || 'Payment failed');

                const {
                    paymentIntent,
                    error
                } = await stripe.confirmCardPayment(data.client_secret, {
                    payment_method: {
                        card: cardNumber,
                        billing_details: {
                            name: document.getElementById('cardholder-name').value,
                            email: document.getElementById('email').value,
                            phone: document.getElementById('phone').value,
                            address: {
                                line1: document.getElementById('address')?.value || '',
                                postal_code: '',
                                country: document.getElementById('country').value
                            }
                        }
                    }
                });

                if (error) throw error;

                document.getElementById('payment_intent_id').value = paymentIntent.id;
                payButton.classList.add('pf-btn-success');
                payButtonText.textContent = 'Payment Successful!';
                setTimeout(() => form.submit(), 900);

            } catch (err) {
                showError(err.message || 'An error occurred during payment.');
                setLoading(false);
            }
        });

        // ── Helpers ──
        function setLoading(on) {
            payButton.disabled = on;
            payButtonText.textContent = on ? 'Processing…' : `Pay & Schedule Test — ${{ $portfolio->price }}`;
            payButtonLoader.classList.toggle('d-none', !on);
        }

        function showError(msg) {
            errorContainer.innerHTML =
                `<div class="pf-alert pf-alert-danger"><i class="fas fa-exclamation-circle mt-1"></i><div>${msg}</div></div>`;
            errorContainer.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        function clearError() {
            errorContainer.innerHTML = '';
        }

        // Remove is-invalid on input
        form.querySelectorAll('input, select, textarea').forEach(el => {
            el.addEventListener('input', () => el.classList.remove('is-invalid'));
        });

        // ── Load countries ──
        fetch('https://restcountries.com/v3.1/all?fields=name,cca2')
            .then(r => r.json())
            .then(list => {
                const sel = document.getElementById('country');
                if (!sel) return;
                list.sort((a, b) => a.name.common.localeCompare(b.name.common))
                    .forEach(c => {
                        const o = document.createElement('option');
                        o.value = c.cca2;
                        o.textContent = c.name.common;
                        sel.appendChild(o);
                    });
            })
            .catch(() => {
                // Country is optional; ignore failures.
            });
    });
</script>