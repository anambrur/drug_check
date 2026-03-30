@extends('layouts.admin.master')

@section('content')
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=DM+Sans:wght@300;400;500&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --ap-primary: #1a56db;
            --ap-primary-dark: #1044b3;
            --ap-primary-light: #e8f0fe;
            --ap-primary-glow: rgba(26, 86, 219, .15);
            --ap-success: #059669;
            --ap-danger: #e11d48;
            --ap-surface: #ffffff;
            --ap-surface-2: #f8faff;
            --ap-border: #e2e8f8;
            --ap-text: #0f172a;
            --ap-muted: #64748b;
            --ap-light: #94a3b8;
            --ap-shadow-sm: 0 1px 3px rgba(15, 23, 42, .06), 0 1px 2px rgba(15, 23, 42, .04);
            --ap-shadow-md: 0 4px 16px rgba(15, 23, 42, .08), 0 2px 6px rgba(15, 23, 42, .05);
            --ap-shadow-lg: 0 20px 60px rgba(15, 23, 42, .12), 0 8px 24px rgba(15, 23, 42, .07);
            --ap-radius: 14px;
            --ap-radius-sm: 9px;
            --ap-font-head: 'Sora', sans-serif;
            --ap-font-body: 'DM Sans', sans-serif;
        }

        body {
            font-family: var(--ap-font-body);
        }

        /* ── Page background ── */
        .ap-page {
            background: linear-gradient(160deg, #f0f5ff 0%, #fafbff 50%, #f0f9ff 100%);
            min-height: 100vh;
            padding: 2rem 0 4rem;
        }

        /* ── Outer card ── */
        .ap-card {
            background: var(--ap-surface);
            border-radius: 20px;
            box-shadow: var(--ap-shadow-lg);
            border: 1px solid var(--ap-border);
            overflow: hidden;
        }

        /* ── Header ── */
        .ap-header {
            background: linear-gradient(135deg, #1a56db 0%, #0e3fa3 60%, #0c2f80 100%);
            padding: 2.25rem 2.5rem 2rem;
            position: relative;
            overflow: hidden;
        }

        .ap-header::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .05);
        }

        .ap-header::after {
            content: '';
            position: absolute;
            bottom: -40px;
            left: 30%;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: rgba(6, 182, 212, .1);
        }

        .ap-pill {
            background: rgba(255, 255, 255, .15);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255, 255, 255, .2);
            color: #fff;
            font-family: var(--ap-font-head);
            font-size: .7rem;
            font-weight: 500;
            letter-spacing: .06em;
            text-transform: uppercase;
            padding: .35rem .9rem;
            border-radius: 100px;
            display: inline-block;
            margin-bottom: .85rem;
        }

        .ap-header h4 {
            font-family: var(--ap-font-head);
            font-size: 1.55rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: .35rem;
            line-height: 1.3;
        }

        .ap-header p {
            color: rgba(255, 255, 255, .7);
            font-size: .9rem;
            margin: 0;
        }

        .ap-header-icon {
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

        .ap-step-badge {
            background: rgba(255, 255, 255, .15);
            border: 1px solid rgba(255, 255, 255, .25);
            color: #fff;
            font-family: var(--ap-font-head);
            font-size: .78rem;
            font-weight: 700;
            padding: .4rem 1rem;
            border-radius: 100px;
            white-space: nowrap;
        }

        /* ── Stepper ── */
        .ap-stepper-wrap {
            padding: 1.75rem 2.5rem 0;
        }

        .ap-stepper {
            display: flex;
            align-items: center;
        }

        .ap-stepper-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }

        .ap-stepper-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 20px;
            left: 50%;
            right: -50%;
            height: 2px;
            background: var(--ap-border);
            z-index: 0;
        }

        .ap-stepper-item.active:not(:last-child)::after {
            background: linear-gradient(90deg, var(--ap-primary), var(--ap-border));
        }

        .ap-dot {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--ap-surface-2);
            border: 2px solid var(--ap-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--ap-font-head);
            font-weight: 700;
            font-size: .85rem;
            color: var(--ap-light);
            z-index: 1;
            position: relative;
            transition: all .3s;
        }

        .ap-stepper-item.active .ap-dot {
            background: #fff;
            border-color: var(--ap-primary);
            color: var(--ap-primary);
            box-shadow: 0 0 0 4px var(--ap-primary-glow);
        }

        .ap-dot-label {
            margin-top: .5rem;
            font-size: .75rem;
            font-weight: 500;
            color: var(--ap-light);
            font-family: var(--ap-font-head);
        }

        .ap-stepper-item.active .ap-dot-label {
            color: var(--ap-primary);
            font-weight: 600;
        }

        /* ── Body ── */
        .ap-body {
            padding: 2rem 2.5rem 2.5rem;
        }

        /* ── Section blocks ── */
        .ap-section {
            border: 1px solid var(--ap-border);
            border-radius: var(--ap-radius);
            overflow: hidden;
            margin-bottom: 1.75rem;
            background: var(--ap-surface);
            transition: box-shadow .2s;
        }

        .ap-section:focus-within {
            box-shadow: 0 0 0 3px var(--ap-primary-glow);
            border-color: rgba(26, 86, 219, .3);
        }

        .ap-section-head {
            background: var(--ap-primary-light);
            padding: .9rem 1.5rem;
            display: flex;
            align-items: center;
            gap: .7rem;
            border-bottom: 1px solid rgba(26, 86, 219, .1);
        }

        .ap-section-head .iw {
            width: 30px;
            height: 30px;
            background: var(--ap-primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: .75rem;
            flex-shrink: 0;
        }

        .ap-section-head h6 {
            font-family: var(--ap-font-head);
            font-size: .9rem;
            font-weight: 700;
            color: var(--ap-primary-dark);
            margin: 0;
        }

        .ap-section-body {
            padding: 1.5rem;
        }

        /* ── Form controls ── */
        .ap-label {
            font-family: var(--ap-font-head);
            font-size: .8rem;
            font-weight: 600;
            color: var(--ap-text);
            letter-spacing: .01em;
            margin-bottom: .4rem;
            display: block;
        }

        .ap-req {
            color: var(--ap-danger);
            margin-left: 2px;
        }

        .ap-ctrl {
            width: 100%;
            border: 1.5px solid var(--ap-border);
            border-radius: var(--ap-radius-sm);
            padding: .65rem 1rem;
            font-size: .88rem;
            font-family: var(--ap-font-body);
            color: var(--ap-text);
            background: var(--ap-surface);
            transition: border-color .2s, box-shadow .2s, background .2s;
            box-shadow: var(--ap-shadow-sm);
            outline: none;
            -webkit-appearance: none;
            appearance: none;
        }

        .ap-ctrl::placeholder {
            color: var(--ap-light);
        }

        .ap-ctrl:focus {
            border-color: var(--ap-primary);
            box-shadow: 0 0 0 3.5px var(--ap-primary-glow);
            background: #fafcff;
        }

        .ap-ctrl.is-invalid {
            border-color: var(--ap-danger);
        }

        .ap-ctrl.is-valid {
            border-color: var(--ap-success);
        }

        select.ap-ctrl {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2394a3b8' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
        }

        .ap-feedback {
            font-size: .77rem;
            color: var(--ap-danger);
            font-weight: 500;
            margin-top: .3rem;
        }

        .ap-hint {
            font-size: .76rem;
            color: var(--ap-muted);
            margin-top: .3rem;
            display: flex;
            align-items: center;
            gap: .3rem;
        }

        /* icon prefix */
        .ap-iw {
            position: relative;
        }

        .ap-iw .ap-icon {
            position: absolute;
            left: .9rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--ap-light);
            font-size: .82rem;
            pointer-events: none;
        }

        .ap-iw .ap-ctrl {
            padding-left: 2.4rem;
        }

        /* ── Order summary card ── */
        .ap-summary {
            background: linear-gradient(135deg, var(--ap-primary-light), #e0ecff);
            border: 1.5px solid rgba(26, 86, 219, .18);
            border-radius: var(--ap-radius-sm);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.75rem;
        }

        .ap-summary-title {
            font-family: var(--ap-font-head);
            font-size: .78rem;
            font-weight: 700;
            color: var(--ap-primary-dark);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 1rem;
        }

        .ap-summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .4rem 0;
            border-bottom: 1px solid rgba(26, 86, 219, .1);
        }

        .ap-summary-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .ap-summary-row .lbl {
            font-size: .83rem;
            color: var(--ap-muted);
        }

        .ap-summary-row .val {
            font-family: var(--ap-font-head);
            font-size: .88rem;
            font-weight: 600;
            color: var(--ap-text);
        }

        .ap-summary-row .val.price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--ap-primary);
        }

        /* ── Payment panel ── */
        .ap-payment-panel {
            background: #f8faff;
            border: 1.5px solid var(--ap-border);
            border-radius: var(--ap-radius-sm);
            padding: 1.5rem;
        }

        .ap-card-icons {
            display: flex;
            align-items: center;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .ap-card-icons i {
            font-size: 1.6rem;
        }

        /* Stripe wrapper */
        .ap-stripe-wrap {
            border: 1.5px solid var(--ap-border);
            border-radius: var(--ap-radius-sm);
            padding: .7rem 1rem;
            background: #fff;
            box-shadow: var(--ap-shadow-sm);
            transition: border-color .2s, box-shadow .2s;
        }

        .ap-stripe-wrap.StripeElement--focus {
            border-color: var(--ap-primary);
            box-shadow: 0 0 0 3.5px var(--ap-primary-glow);
        }

        .ap-stripe-wrap.StripeElement--invalid {
            border-color: var(--ap-danger);
        }

        /* ── Alert ── */
        .ap-alert {
            border-radius: var(--ap-radius-sm);
            padding: .9rem 1.1rem;
            display: flex;
            align-items: flex-start;
            gap: .7rem;
            font-size: .875rem;
        }

        .ap-alert-danger {
            background: rgba(225, 29, 72, .04);
            color: #9f1239;
            border: 1px solid rgba(225, 29, 72, .2);
        }

        /* ── Terms ── */
        .ap-terms {
            border: 1.5px solid var(--ap-border);
            border-radius: var(--ap-radius-sm);
            padding: 1rem 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            background: var(--ap-surface-2);
            margin-bottom: 1.5rem;
        }

        .ap-terms input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--ap-primary);
            flex-shrink: 0;
            margin-top: 2px;
        }

        .ap-terms-label {
            font-size: .85rem;
            color: var(--ap-muted);
            line-height: 1.5;
        }

        .ap-terms-label a {
            color: var(--ap-primary);
            font-weight: 600;
            text-decoration: none;
        }

        .ap-terms-label a:hover {
            text-decoration: underline;
        }

        /* ── Submit ── */
        .ap-btn-submit {
            background: linear-gradient(135deg, #1a56db 0%, #0e3fa3 100%);
            border: none;
            border-radius: 12px;
            width: 100%;
            font-family: var(--ap-font-head);
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

        .ap-btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(26, 86, 219, .45);
            color: #fff;
        }

        .ap-btn-submit:active {
            transform: translateY(0);
        }

        .ap-btn-submit:disabled {
            opacity: .7;
            cursor: not-allowed;
            transform: none;
        }

        .ap-secure {
            font-size: .78rem;
            color: var(--ap-muted);
            display: flex;
            align-items: center;
            gap: .4rem;
            justify-content: center;
            margin-top: .75rem;
        }

        .ap-secure i {
            color: #059669;
        }

        /* ── Success modal ── */
        .ap-success-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #059669;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            font-size: 1.8rem;
            color: #fff;
        }

        /* ── Responsive ── */
        @media (max-width:768px) {
            .ap-header {
                padding: 1.75rem 1.5rem 1.5rem;
            }

            .ap-body {
                padding: 1.5rem 1.25rem 2rem;
            }

            .ap-stepper-wrap {
                padding: 1.5rem 1.25rem 0;
            }

            .ap-dot-label {
                display: none;
            }

            .ap-btn-submit {
                font-size: .9rem;
                padding: .85rem 1.5rem;
            }
        }
    </style>

    <div class="ap-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="">

                    <div class="ap-card">

                        {{-- ── HEADER ── --}}
                        <div class="ap-header">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <span class="ap-pill">Quest Diagnostics · Admin</span>
                                    <h4>Complete Your Payment</h4>
                                    <p>Secure payment for {{ $portfolio->title }}</p>
                                </div>
                                <div class="d-flex flex-column align-items-end gap-2 flex-shrink-0">
                                    <div class="ap-header-icon d-none d-sm-flex">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <span class="ap-step-badge">Step 1 of 3</span>
                                </div>
                            </div>
                        </div>

                        {{-- ── STEPPER ── --}}
                        <div class="ap-stepper-wrap">
                            <div class="ap-stepper">
                                <div class="ap-stepper-item active">
                                    <div class="ap-dot">1</div>
                                    <div class="ap-dot-label">Payment</div>
                                </div>
                                <div class="ap-stepper-item">
                                    <div class="ap-dot">2</div>
                                    <div class="ap-dot-label">Test Details</div>
                                </div>
                                <div class="ap-stepper-item">
                                    <div class="ap-dot">3</div>
                                    <div class="ap-dot-label">Confirmation</div>
                                </div>
                            </div>
                        </div>

                        {{-- ── BODY ── --}}
                        <div class="ap-body">

                            {{-- ── Employee selector ── --}}
                            <div class="ap-section">
                                <div class="ap-section-head">
                                    <div class="iw"><i class="fas fa-user"></i></div>
                                    <h6>Select Employee</h6>
                                </div>
                                <div class="ap-section-body">
                                    <label class="ap-label" for="employee_id">Employee <span class="ap-req">*</span></label>
                                    <div class="ap-iw">
                                        <i class="fas fa-users ap-icon"></i>
                                        <select class="ap-ctrl" name="employee_id" id="employee_id" required>
                                            <option value="" selected disabled>Choose an employee…</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}">
                                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="ap-hint"><i class="fas fa-info-circle"></i> Select the employee who will
                                        take this test</div>
                                </div>
                            </div>

                            {{-- ── Order summary ── --}}
                            <div class="ap-summary">
                                <div class="ap-summary-title"><i class="fas fa-receipt me-2"></i>Order Summary</div>
                                <div class="ap-summary-row">
                                    <span class="lbl">Test Type</span>
                                    <span class="val">{{ $portfolio->title }}</span>
                                </div>
                                <div class="ap-summary-row">
                                    <span class="lbl">Unit Code</span>
                                    <span class="val">{{ $portfolio->code }}</span>
                                </div>
                                <div class="ap-summary-row">
                                    <span class="lbl">Total Amount</span>
                                    <span class="val price">${{ number_format($portfolio->price, 2) }}</span>
                                </div>
                            </div>

                            <form id="payment-form" method="POST">
                                @csrf
                                <input type="hidden" name="portfolio_id" value="{{ $portfolio->id }}">
                                <input type="hidden" name="payment_intent_id" id="payment_intent_id">
                                <input type="hidden" name="test_name" id="test_name" value="{{ $portfolio->title }}">
                                <input type="hidden" id="price" value="{{ $portfolio->price * 100 }}">
                                <input type="hidden" name="employee_id" id="selected_employee_id">

                                {{-- ── Payment section ── --}}
                                <div class="ap-section">
                                    <div class="ap-section-head">
                                        <div class="iw"><i class="fas fa-credit-card"></i></div>
                                        <h6>Payment Information</h6>
                                    </div>
                                    <div class="ap-section-body">
                                        <div class="ap-payment-panel">

                                            {{-- Accepted cards --}}
                                            <div class="d-flex align-items-center gap-3 mb-4 pb-3"
                                                style="border-bottom:1px solid var(--ap-border);">
                                                <span
                                                    style="font-family:var(--ap-font-head);font-size:.78rem;font-weight:600;color:var(--ap-muted);">Accepted</span>
                                                <div class="ap-card-icons">
                                                    <i class="fab fa-cc-visa" style="color:#1a1f71;"></i>
                                                    <i class="fab fa-cc-mastercard" style="color:#eb001b;"></i>
                                                    <i class="fab fa-cc-amex" style="color:#016fd0;"></i>
                                                    <i class="fab fa-cc-discover" style="color:#ff6000;"></i>
                                                </div>
                                            </div>

                                            <div class="row g-3">

                                                {{-- Cardholder Name --}}
                                                <div class="col-12">
                                                    <label class="ap-label" for="cardholder-name">Cardholder Name <span
                                                            class="ap-req">*</span></label>
                                                    <div class="ap-iw">
                                                        <i class="fas fa-id-badge ap-icon"></i>
                                                        <input type="text" id="cardholder-name" class="ap-ctrl"
                                                            placeholder="Name as it appears on card" required>
                                                    </div>
                                                </div>

                                                {{-- Card Number --}}
                                                <div class="col-12 mt-2">
                                                    <label class="ap-label">Card Number <span
                                                            class="ap-req">*</span></label>
                                                    <div id="card-number" class="ap-stripe-wrap"></div>
                                                </div>

                                                {{-- Expiry + CVC --}}
                                                <div class="col-md-4 mt-2">
                                                    <label class="ap-label">Expiry <span class="ap-req">*</span></label>
                                                    <div id="card-expiry" class="ap-stripe-wrap"></div>
                                                </div>
                                                <div class="col-md-4 mt-2">
                                                    <label class="ap-label">CVC <span class="ap-req">*</span></label>
                                                    <div id="card-cvc" class="ap-stripe-wrap"></div>
                                                </div>
                                                <div class="col-md-4 mt-2">
                                                    <label class="ap-label">ZIP Code <span class="ap-req">*</span></label>
                                                    <div id="postal-code" class="ap-stripe-wrap"></div>
                                                </div>

                                                {{-- Country --}}
                                                <div class="col-12 mt-2">
                                                    <label class="ap-label" for="country">Country <span
                                                            class="ap-req">*</span></label>
                                                    <select id="country" name="country" class="ap-ctrl" required>
                                                        <option value="" selected disabled>Select Country</option>
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
                                <div class="ap-terms">
                                    <input type="checkbox" id="terms-check" required>
                                    <label class="ap-terms-label" for="terms-check">
                                        I agree to the
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms of
                                            Service</a>
                                        and authorize a charge of
                                        <strong
                                            style="color:var(--ap-primary);">${{ number_format($portfolio->price, 2) }}</strong>
                                        to my card.
                                    </label>
                                </div>

                                {{-- ── Submit ── --}}
                                <button type="button" id="pay-button" class="ap-btn-submit">
                                    <i class="fas fa-lock"></i>
                                    <span id="pay-button-text">Pay ${{ number_format($portfolio->price, 2) }} &amp;
                                        Continue</span>
                                    <span id="pay-button-loader"
                                        class="spinner-border spinner-border-sm d-none ms-1"></span>
                                </button>
                                <p class="ap-secure">
                                    <i class="fas fa-shield-alt"></i>
                                    256-bit SSL encrypted — you'll provide test details after payment
                                </p>

                            </form>
                        </div>{{-- /ap-body --}}
                    </div>{{-- /ap-card --}}

                </div>
            </div>
        </div>
    </div>

    {{-- ── Terms Modal ── --}}
    <div class="modal fade" id="termsModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content" style="border-radius:var(--ap-radius);border:1px solid var(--ap-border);">
                <div class="modal-header"
                    style="background:var(--ap-primary-light);border-bottom:1px solid rgba(26,86,219,.1);">
                    <h5 class="modal-title"
                        style="font-family:var(--ap-font-head);font-weight:700;color:var(--ap-primary-dark);">
                        <i class="fas fa-file-contract me-2"></i>Terms of Service
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="font-family:var(--ap-font-body);font-size:.88rem;color:var(--ap-muted);">
                    <h6 style="font-family:var(--ap-font-head);font-weight:700;color:var(--ap-text);">Payment Terms</h6>
                    <p>By completing this payment, you authorize us to charge your card for the test service. All payments
                        are final and non-refundable once the test process has been initiated.</p>
                    <h6 style="font-family:var(--ap-font-head);font-weight:700;color:var(--ap-text);margin-top:1.25rem;">
                        Service Terms</h6>
                    <p>After payment, you'll be redirected to complete the test information form. The test must be scheduled
                        within 30 days of payment.</p>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--ap-border);">
                    <button type="button"
                        style="background:linear-gradient(135deg,#1a56db,#0e3fa3);border:none;border-radius:9px;font-family:var(--ap-font-head);font-weight:700;font-size:.88rem;color:#fff;padding:.65rem 1.75rem;cursor:pointer;"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Success Modal ── --}}
    <div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content"
                style="border-radius:var(--ap-radius);border:1px solid var(--ap-border);text-align:center;">
                <div class="modal-body p-5">
                    <div class="ap-success-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <h5 style="font-family:var(--ap-font-head);font-weight:700;color:var(--ap-text);margin-bottom:.5rem;">
                        Payment Successful!</h5>
                    <p style="color:var(--ap-muted);font-size:.88rem;margin:0;">Redirecting to test details…</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Stripe ── --}}
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const stripe = Stripe("{{ env('STRIPE_PUBLIC') }}");
            const elements = stripe.elements();
            const form = document.getElementById('payment-form');
            const payButton = document.getElementById('pay-button');
            const payButtonText = document.getElementById('pay-button-text');
            const payButtonLoader = document.getElementById('pay-button-loader');
            const errorContainer = document.getElementById('card-errors');
            const employeeSelect = document.getElementById('employee_id');
            const selectedEmpInput = document.getElementById('selected_employee_id');
            const price = {{ $portfolio->price * 100 }};

            // ── Stripe elements ──
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

            // Focus / blur classes on wrappers
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

                if (!employeeSelect.value) {
                    employeeSelect.classList.add('is-invalid');
                    showError('Please select an employee.');
                    valid = false;
                } else {
                    employeeSelect.classList.remove('is-invalid');
                    employeeSelect.classList.add('is-valid');
                    selectedEmpInput.value = employeeSelect.value;
                }

                const name = document.getElementById('cardholder-name');
                if (!name.value.trim()) {
                    name.classList.add('is-invalid');
                    showError('Please enter the cardholder name.');
                    valid = false;
                } else {
                    name.classList.remove('is-invalid');
                }

                const country = document.getElementById('country');
                if (!country.value) {
                    country.classList.add('is-invalid');
                    showError('Please select a country.');
                    valid = false;
                } else {
                    country.classList.remove('is-invalid');
                }

                if (!document.getElementById('terms-check').checked) {
                    showError('Please agree to the Terms of Service.');
                    valid = false;
                }

                return valid;
            }

            // ── Pay click ──
            payButton.addEventListener('click', async () => {
                if (!validateForm()) return;
                setLoading(true);

                try {
                    const res = await fetch("{{ route('admin.dot-test.process-payment') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            portfolio_id: {{ $portfolio->id }},
                            price: price,
                            employee_id: employeeSelect.value
                        })
                    });

                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Payment processing failed');

                    const {
                        paymentIntent,
                        error
                    } = await stripe.confirmCardPayment(data.client_secret, {
                        payment_method: {
                            card: cardNumber,
                            billing_details: {
                                name: document.getElementById('cardholder-name').value,
                                address: {
                                    country: document.getElementById('country').value,
                                    postal_code: ''
                                }
                            }
                        }
                    });

                    if (error) throw error;

                    document.getElementById('payment_intent_id').value = paymentIntent.id;

                    const modal = new bootstrap.Modal(document.getElementById('successModal'));
                    modal.show();

                    setTimeout(() => {
                        window.location.href =
                            "{{ route('admin.dot-test.order-form', '') }}/" + paymentIntent.id;
                    }, 2000);

                } catch (err) {
                    showError(err.message || 'An error occurred during payment.');
                    setLoading(false);
                }
            });

            // ── Helpers ──
            function setLoading(on) {
                payButton.disabled = on;
                payButtonText.textContent = on ? 'Processing Payment…' :
                    'Pay ${{ number_format($portfolio->price, 2) }} & Continue';
                payButtonLoader.classList.toggle('d-none', !on);
            }

            function showError(msg) {
                errorContainer.innerHTML =
                    `<div class="ap-alert ap-alert-danger"><i class="fas fa-exclamation-circle mt-1"></i><div>${msg}</div></div>`;
                errorContainer.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }

            function clearError() {
                errorContainer.innerHTML = '';
            }

            // Clear invalid on change
            document.querySelectorAll('#employee_id, #cardholder-name, #country').forEach(el => {
                el.addEventListener('input', () => {
                    el.classList.remove('is-invalid');
                    clearError();
                });
                el.addEventListener('change', () => {
                    el.classList.remove('is-invalid');
                    clearError();
                });
            });
            document.getElementById('terms-check').addEventListener('change', clearError);

            // ── Load countries ──
            fetch('https://restcountries.com/v3.1/all?fields=name,cca2')
                .then(r => r.json())
                .then(list => {
                    const sel = document.getElementById('country');
                    list.sort((a, b) => a.name.common.localeCompare(b.name.common))
                        .forEach(c => {
                            const o = document.createElement('option');
                            o.value = c.cca2;
                            o.textContent = c.name.common;
                            sel.appendChild(o);
                        });
                    sel.value = 'US';
                })
                .catch(console.error);
        });
    </script>
@endsection
