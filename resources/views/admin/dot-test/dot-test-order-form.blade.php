@extends('layouts.admin.master')

@section('content')
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=DM+Sans:wght@300;400;500&display=swap"
        rel="stylesheet">

    <style>
        /* ── Design tokens ── */
        :root {
            --aq-primary: #1a56db;
            --aq-primary-dark: #1044b3;
            --aq-primary-light: #e8f0fe;
            --aq-primary-glow: rgba(26, 86, 219, .15);
            --aq-success: #059669;
            --aq-danger: #e11d48;
            --aq-surface: #ffffff;
            --aq-surface-2: #f8faff;
            --aq-border: #e2e8f8;
            --aq-text: #0f172a;
            --aq-muted: #64748b;
            --aq-light: #94a3b8;
            --aq-shadow-sm: 0 1px 3px rgba(15, 23, 42, .06), 0 1px 2px rgba(15, 23, 42, .04);
            --aq-shadow-md: 0 4px 16px rgba(15, 23, 42, .08), 0 2px 6px rgba(15, 23, 42, .05);
            --aq-shadow-lg: 0 20px 60px rgba(15, 23, 42, .12), 0 8px 24px rgba(15, 23, 42, .07);
            --aq-radius: 14px;
            --aq-radius-sm: 9px;
            --aq-font-head: 'Sora', sans-serif;
            --aq-font-body: 'DM Sans', sans-serif;
        }

        body,
        .aq-wrap * {
            font-family: var(--aq-font-body);
        }

        /* ── Page background ── */
        .aq-page {
            background: linear-gradient(160deg, #f0f5ff 0%, #fafbff 50%, #f0f9ff 100%);
            min-height: 100vh;
            padding: 2rem 0 4rem;
        }

        /* ── Outer card ── */
        .aq-card {
            background: var(--aq-surface);
            border-radius: 20px;
            box-shadow: var(--aq-shadow-lg);
            border: 1px solid var(--aq-border);
            overflow: hidden;
        }

        /* ── Header ── */
        .aq-header {
            background: linear-gradient(135deg, #1a56db 0%, #0e3fa3 60%, #0c2f80 100%);
            padding: 2.25rem 2.5rem 2rem;
            position: relative;
            overflow: hidden;
        }

        .aq-header::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .05);
        }

        .aq-header::after {
            content: '';
            position: absolute;
            bottom: -40px;
            left: 30%;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: rgba(6, 182, 212, .1);
        }

        .aq-header .aq-pill {
            background: rgba(255, 255, 255, .15);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255, 255, 255, .2);
            color: #fff;
            font-family: var(--aq-font-head);
            font-size: .7rem;
            font-weight: 500;
            letter-spacing: .06em;
            text-transform: uppercase;
            padding: .35rem .9rem;
            border-radius: 100px;
            display: inline-block;
            margin-bottom: .85rem;
        }

        .aq-header h4 {
            font-family: var(--aq-font-head);
            font-size: 1.55rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: .35rem;
            line-height: 1.3;
        }

        .aq-header p {
            color: rgba(255, 255, 255, .7);
            font-size: .9rem;
            margin: 0;
        }

        .aq-header-icon {
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

        .aq-step-badge {
            background: rgba(255, 255, 255, .15);
            border: 1px solid rgba(255, 255, 255, .25);
            color: #fff;
            font-family: var(--aq-font-head);
            font-size: .78rem;
            font-weight: 700;
            padding: .4rem 1rem;
            border-radius: 100px;
            white-space: nowrap;
        }

        /* ── Stepper ── */
        .aq-stepper-wrap {
            padding: 1.75rem 2.5rem 0;
        }

        .aq-stepper {
            display: flex;
            align-items: center;
        }

        .aq-stepper-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }

        .aq-stepper-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 20px;
            left: 50%;
            right: -50%;
            height: 2px;
            background: var(--aq-border);
            z-index: 0;
        }

        .aq-stepper-item.completed:not(:last-child)::after {
            background: var(--aq-primary);
        }

        .aq-stepper-item.active:not(:last-child)::after {
            background: linear-gradient(90deg, var(--aq-primary), var(--aq-border));
        }

        .aq-dot {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--aq-surface-2);
            border: 2px solid var(--aq-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--aq-font-head);
            font-weight: 700;
            font-size: .85rem;
            color: var(--aq-light);
            z-index: 1;
            position: relative;
            transition: all .3s;
        }

        .aq-stepper-item.completed .aq-dot {
            background: var(--aq-primary);
            border-color: var(--aq-primary);
            color: #fff;
        }

        .aq-stepper-item.active .aq-dot {
            background: #fff;
            border-color: var(--aq-primary);
            color: var(--aq-primary);
            box-shadow: 0 0 0 4px var(--aq-primary-glow);
        }

        .aq-dot-label {
            margin-top: .5rem;
            font-size: .75rem;
            font-weight: 500;
            color: var(--aq-light);
            font-family: var(--aq-font-head);
        }

        .aq-stepper-item.completed .aq-dot-label,
        .aq-stepper-item.active .aq-dot-label {
            color: var(--aq-primary);
            font-weight: 600;
        }

        /* ── Body ── */
        .aq-body {
            padding: 2rem 2.5rem 2.5rem;
        }

        /* ── Section blocks ── */
        .aq-section {
            border: 1px solid var(--aq-border);
            border-radius: var(--aq-radius);
            overflow: hidden;
            margin-bottom: 1.75rem;
            background: var(--aq-surface);
            transition: box-shadow .2s;
        }

        .aq-section:focus-within {
            box-shadow: 0 0 0 3px var(--aq-primary-glow);
            border-color: rgba(26, 86, 219, .3);
        }

        .aq-section-head {
            background: var(--aq-primary-light);
            padding: .9rem 1.5rem;
            display: flex;
            align-items: center;
            gap: .7rem;
            border-bottom: 1px solid rgba(26, 86, 219, .1);
        }

        .aq-section-head .iw {
            width: 30px;
            height: 30px;
            background: var(--aq-primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: .75rem;
            flex-shrink: 0;
        }

        .aq-section-head h6 {
            font-family: var(--aq-font-head);
            font-size: .9rem;
            font-weight: 700;
            color: var(--aq-primary-dark);
            margin: 0;
        }

        .aq-section-body {
            padding: 1.5rem;
        }

        /* ── Form controls ── */
        .aq-label {
            font-family: var(--aq-font-head);
            font-size: .8rem;
            font-weight: 600;
            color: var(--aq-text);
            letter-spacing: .01em;
            margin-bottom: .4rem;
            display: block;
        }

        .aq-req {
            color: var(--aq-danger);
            margin-left: 2px;
        }

        .aq-opt {
            font-size: .68rem;
            background: #f1f5f9;
            color: var(--aq-muted);
            border-radius: 4px;
            padding: 1px 6px;
            font-weight: 500;
            margin-left: 6px;
            vertical-align: middle;
        }

        .aq-ctrl {
            width: 100%;
            border: 1.5px solid var(--aq-border);
            border-radius: var(--aq-radius-sm);
            padding: .65rem 1rem;
            font-size: .88rem;
            font-family: var(--aq-font-body);
            color: var(--aq-text);
            background: var(--aq-surface);
            transition: border-color .2s, box-shadow .2s, background .2s;
            box-shadow: var(--aq-shadow-sm);
            outline: none;
            -webkit-appearance: none;
            appearance: none;
        }

        .aq-ctrl::placeholder {
            color: var(--aq-light);
        }

        .aq-ctrl:focus {
            border-color: var(--aq-primary);
            box-shadow: 0 0 0 3.5px var(--aq-primary-glow);
            background: #fafcff;
        }

        .aq-ctrl.is-invalid {
            border-color: var(--aq-danger);
        }

        .aq-ctrl.is-valid {
            border-color: var(--aq-success);
        }

        select.aq-ctrl {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2394a3b8' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
        }

        textarea.aq-ctrl {
            resize: vertical;
            min-height: 90px;
        }

        .aq-feedback {
            font-size: .77rem;
            color: var(--aq-danger);
            font-weight: 500;
            margin-top: .3rem;
        }

        .aq-hint {
            font-size: .76rem;
            color: var(--aq-muted);
            margin-top: .3rem;
            display: flex;
            align-items: center;
            gap: .3rem;
        }

        /* icon prefix */
        .aq-iw {
            position: relative;
        }

        .aq-iw .aq-icon {
            position: absolute;
            left: .9rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--aq-light);
            font-size: .82rem;
            pointer-events: none;
        }

        .aq-iw .aq-ctrl {
            padding-left: 2.4rem;
        }

        .aq-iw textarea.aq-ctrl {
            padding-left: 2.4rem;
            padding-top: .7rem;
        }

        /* ── Alert ── */
        .aq-alert {
            border-radius: var(--aq-radius-sm);
            padding: .9rem 1.1rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: .7rem;
            font-size: .875rem;
        }

        .aq-alert-danger {
            background: rgba(225, 29, 72, .04);
            color: #9f1239;
            border: 1px solid rgba(225, 29, 72, .2);
        }

        /* ── Submit ── */
        .aq-btn-submit {
            background: linear-gradient(135deg, #1a56db 0%, #0e3fa3 100%);
            border: none;
            border-radius: 12px;
            font-family: var(--aq-font-head);
            font-weight: 700;
            font-size: 1rem;
            color: #fff;
            padding: .9rem 3rem;
            transition: all .25s;
            box-shadow: 0 4px 20px rgba(26, 86, 219, .35);
            display: inline-flex;
            align-items: center;
            gap: .6rem;
            cursor: pointer;
        }

        .aq-btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(26, 86, 219, .45);
            color: #fff;
        }

        .aq-btn-submit:active {
            transform: translateY(0);
        }

        /* ── Select2 overrides ── */
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            border: 1.5px solid var(--aq-border) !important;
            border-radius: var(--aq-radius-sm) !important;
            height: auto !important;
            padding: .65rem 1rem !important;
            font-size: .88rem !important;
            font-family: var(--aq-font-body) !important;
            box-shadow: var(--aq-shadow-sm) !important;
            transition: border-color .2s, box-shadow .2s !important;
        }

        .select2-container--default .select2-selection--single:focus-within,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--aq-primary) !important;
            box-shadow: 0 0 0 3.5px var(--aq-primary-glow) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5 !important;
            padding: 0 !important;
            color: var(--aq-text) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
            right: .75rem !important;
        }

        .select2-dropdown {
            border-radius: var(--aq-radius-sm) !important;
            border: 1.5px solid var(--aq-border) !important;
            box-shadow: var(--aq-shadow-md) !important;
            overflow: hidden;
        }

        .select2-search--dropdown .select2-search__field {
            border-radius: 6px !important;
            border: 1.5px solid var(--aq-border) !important;
            padding: .5rem .75rem !important;
            font-size: .85rem;
        }

        .select2-container--default .select2-results__option--highlighted {
            background: var(--aq-primary) !important;
        }

        .select2-site-result {
            padding: 8px 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .select2-site-result .site-name {
            font-weight: 600;
            margin-bottom: 2px;
            color: var(--aq-text);
        }

        .select2-site-result .site-code {
            color: var(--aq-muted);
            font-size: .82em;
        }

        /* ── Responsive ── */
        @media (max-width:768px) {
            .aq-header {
                padding: 1.75rem 1.5rem 1.5rem;
            }

            .aq-body {
                padding: 1.5rem 1.25rem 2rem;
            }

            .aq-stepper-wrap {
                padding: 1.5rem 1.25rem 0;
            }

            .aq-dot-label {
                display: none;
            }

            .aq-btn-submit {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <div class="aq-page">
        <div class="container-fluid px-3 px-md-4">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">

                    <div class="aq-card">

                        {{-- ── HEADER ── --}}
                        <div class="aq-header">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <span class="aq-pill">Quest Diagnostics · Admin</span>
                                    <h4>DOT Test Order Form</h4>
                                    <p>Schedule {{ $portfolio->title }} for {{ $employee->first_name }}
                                        {{ $employee->last_name }}</p>
                                </div>
                                <div class="d-flex flex-column align-items-end gap-2 flex-shrink-0">
                                    <div class="aq-header-icon d-none d-sm-flex">
                                        <i class="fas fa-flask"></i>
                                    </div>
                                    <span class="aq-step-badge">Step 2 of 3</span>
                                </div>
                            </div>
                        </div>

                        {{-- ── STEPPER ── --}}
                        <div class="aq-stepper-wrap">
                            <div class="aq-stepper">
                                <div class="aq-stepper-item completed">
                                    <div class="aq-dot"><i class="fas fa-check" style="font-size:.75rem;"></i></div>
                                    <div class="aq-dot-label">Payment</div>
                                </div>
                                <div class="aq-stepper-item active">
                                    <div class="aq-dot">2</div>
                                    <div class="aq-dot-label">Test Information</div>
                                </div>
                                <div class="aq-stepper-item">
                                    <div class="aq-dot">3</div>
                                    <div class="aq-dot-label">Confirmation</div>
                                </div>
                            </div>
                        </div>

                        {{-- ── BODY ── --}}
                        <div class="aq-body">

                            @if (session('error'))
                                <div class="aq-alert aq-alert-danger" role="alert">
                                    <i class="fas fa-exclamation-circle mt-1"></i>
                                    <div>{{ session('error') }}</div>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('admin.dot-test.submit-order') }}" id="questOrderForm"
                                novalidate>
                                @csrf
                                <input type="hidden" name="portfolio_id" value="{{ $portfolio->id }}">
                                <input type="hidden" name="payment_intent_id"
                                    value="{{ $paymentData['payment_intent_id'] }}">
                                @if (config('app.env') === 'production')
                                    <input type="hidden" name="lab_account"
                                        value="{{ $employee->clientProfile->account_no }}">
                                @else
                                    <input type="hidden" name="lab_account"
                                        value="{{ config('services.quest.dot_lab_account') }}">
                                @endif
                                <input type="hidden" name="is_physical"
                                    value="{{ $isPhysical = str_contains(strtolower($portfolio->title), 'physical') ? 'true' : 'false' }}">
                                <input type="hidden" name="is_ebat"
                                    value="{{ str_contains(strtolower($portfolio->title), 'ebat') ? 'true' : 'false' }}">
                                <input type="hidden" name="unit_codes" value="{{ $portfolio->code }}">
                                <input type="hidden" name="test_type" value="dot">
                                <input type="hidden" name="response_url" value="{{ url('/api/quest/order-status') }}">

                                {{-- ════ PERSONAL INFORMATION ════ --}}
                                <div class="aq-section">
                                    <div class="aq-section-head">
                                        <div class="iw"><i class="fas fa-user"></i></div>
                                        <h6>Personal Information</h6>
                                    </div>
                                    <div class="aq-section-body">
                                        <div class="row g-3">

                                            {{-- First Name --}}
                                            <div class="col-md-6">
                                                <label class="aq-label">First Name <span class="aq-req">*</span></label>
                                                <div class="aq-iw">
                                                    <i class="fas fa-user aq-icon"></i>
                                                    <input type="text" name="first_name"
                                                        class="aq-ctrl @error('first_name') is-invalid @enderror"
                                                        value="{{ old('first_name', $employee->first_name) }}"
                                                        placeholder="e.g. John" required maxlength="20">
                                                </div>
                                                @error('first_name')
                                                    <div class="aq-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Last Name --}}
                                            <div class="col-md-6">
                                                <label class="aq-label">Last Name <span class="aq-req">*</span></label>
                                                <div class="aq-iw">
                                                    <i class="fas fa-user aq-icon"></i>
                                                    <input type="text" name="last_name"
                                                        class="aq-ctrl @error('last_name') is-invalid @enderror"
                                                        value="{{ old('last_name', $employee->last_name) }}"
                                                        placeholder="e.g. Doe" required maxlength="25">
                                                </div>
                                                @error('last_name')
                                                    <div class="aq-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Middle Name --}}
                                            <div class="col-md-6 mt-2">
                                                <label class="aq-label">Middle Name <span
                                                        class="aq-opt">Optional</span></label>
                                                <input type="text" name="middle_name"
                                                    class="aq-ctrl @error('middle_name') is-invalid @enderror"
                                                    value="{{ old('middle_name', $employee->middle_name) }}"
                                                    placeholder="e.g. Michael" maxlength="20">
                                                @error('middle_name')
                                                    <div class="aq-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Primary ID --}}
                                            <div class="col-md-6 mt-2">
                                                <label class="aq-label">Primary ID <span class="aq-req">*</span></label>
                                                <div class="aq-iw">
                                                    <i class="fas fa-id-card aq-icon"></i>
                                                    <input type="text" name="primary_id"
                                                        class="aq-ctrl @error('primary_id') is-invalid @enderror"
                                                        value="{{ old('primary_id', $employee->employee_id) }}"
                                                        placeholder="Driver's license or ID" required maxlength="25">
                                                </div>
                                                @error('primary_id')
                                                    <div class="aq-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Email --}}
                                            <div class="col-md-6 mt-2">
                                                <label class="aq-label">Email Address <span
                                                        class="aq-req">*</span></label>
                                                <div class="aq-iw">
                                                    <i class="fas fa-envelope aq-icon"></i>
                                                    <input type="email" name="email"
                                                        class="aq-ctrl @error('email') is-invalid @enderror"
                                                        value="{{ old('email', $employee->email) }}"
                                                        placeholder="you@example.com" required maxlength="254">
                                                </div>
                                                @error('email')
                                                    <div class="aq-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Primary ID Type (Physical only) --}}
                                            @if ($isPhysical)
                                                <div class="col-md-6 mt-2">
                                                    <label class="aq-label">Primary ID Type <span
                                                            class="aq-opt">Optional</span></label>
                                                    <select name="primary_id_type"
                                                        class="aq-ctrl @error('primary_id_type') is-invalid @enderror">
                                                        <option value="">Select ID Type</option>
                                                        <option value="DL" @selected(old('primary_id_type') == 'DL')>Driver's
                                                            License</option>
                                                        <option value="OTHER" @selected(old('primary_id_type') == 'OTHER')>Other
                                                            Government ID</option>
                                                    </select>
                                                    @error('primary_id_type')
                                                        <div class="aq-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            @endif

                                            {{-- Date of Birth --}}
                                            <div class="col-md-6 mt-2">
                                                <label class="aq-label">Date of Birth <span
                                                        class="aq-opt">Optional</span></label>
                                                <div class="aq-iw">
                                                    <i class="fas fa-calendar aq-icon"></i>
                                                    <input type="date" name="dob"
                                                        class="aq-ctrl @error('dob') is-invalid @enderror"
                                                        value="{{ old('dob', $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('Y-m-d') : '') }}"
                                                        max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                                </div>
                                                @error('dob')
                                                    <div class="aq-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Primary Phone --}}
                                            <div class="col-md-6 mt-2">
                                                <label class="aq-label">Primary Phone <span
                                                        class="aq-req">*</span></label>
                                                <div class="aq-iw">
                                                    <i class="fas fa-phone aq-icon"></i>
                                                    <input type="tel" name="primary_phone"
                                                        class="aq-ctrl @error('primary_phone') is-invalid @enderror"
                                                        value="{{ old('primary_phone', $employee->phone) }}"
                                                        placeholder="(555) 000-0000" required>
                                                </div>
                                                @error('primary_phone')
                                                    <div class="aq-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Secondary Phone --}}
                                            <div class="col-md-6 mt-2">
                                                <label class="aq-label">Secondary Phone <span
                                                        class="aq-opt">Optional</span></label>
                                                <div class="aq-iw">
                                                    <i class="fas fa-phone-alt aq-icon"></i>
                                                    <input type="tel" name="secondary_phone"
                                                        class="aq-ctrl @error('secondary_phone') is-invalid @enderror"
                                                        value="{{ old('secondary_phone') }}"
                                                        placeholder="(555) 000-0000">
                                                </div>
                                                @error('secondary_phone')
                                                    <div class="aq-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Zip Code (Physical only) --}}
                                            @if ($isPhysical)
                                                <div class="col-md-6 mt-2">
                                                    <label class="aq-label">Zip Code <span
                                                            class="aq-opt">Optional</span></label>
                                                    <div class="aq-iw">
                                                        <i class="fas fa-map-pin aq-icon"></i>
                                                        <input type="text" name="zip_code"
                                                            class="aq-ctrl @error('zip_code') is-invalid @enderror"
                                                            value="{{ old('zip_code') }}" placeholder="For site search">
                                                    </div>
                                                    @error('zip_code')
                                                        <div class="aq-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>

                                {{-- ════ DOT TEST INFORMATION ════ --}}
                                <div class="aq-section">
                                    <div class="aq-section-head">
                                        <div class="iw"><i class="fas fa-vial"></i></div>
                                        <h6>DOT Test Information</h6>
                                    </div>
                                    <div class="aq-section-body">
                                        <div class="row g-3">

                                            {{-- Test Type --}}
                                            <div class="col-md-6 mt-2">
                                                <label class="aq-label">Test Type <span class="aq-req">*</span></label>
                                                <select name="dot_test" id="dot_test"
                                                    class="aq-ctrl @error('dot_test') is-invalid @enderror" required>
                                                    <option value="T" @selected(old('dot_test', 'T') == 'T')>DOT Test</option>
                                                    <option value="F" @selected(old('dot_test') == 'F')>Non-DOT Test
                                                    </option>
                                                </select>
                                                @error('dot_test')
                                                    <div class="aq-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Collection Site --}}
                                            <div class="col-md-6 mt-2">
                                                <label class="aq-label">Collection Site</label>
                                                <select name="collection_site_id" id="collection_site_id"
                                                    class="aq-ctrl select2-collection-sites">
                                                    <option value="">Select a collection site…</option>
                                                </select>
                                                @error('collection_site_id')
                                                    <div class="aq-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- DOT Testing Authority (conditional) --}}
                                            <div class="col-md-6 mt-2" id="testingAuthorityField">
                                                <label class="aq-label">DOT Testing Authority <span
                                                        class="aq-req">*</span></label>
                                                <select name="testing_authority" id="testing_authority"
                                                    class="aq-ctrl @error('testing_authority') is-invalid @enderror"
                                                    required>
                                                    <option value="">Select Authority</option>
                                                    <option value="FMCSA" @selected(old('testing_authority') == 'FMCSA')>FMCSA</option>
                                                    <option value="PHMSA" @selected(old('testing_authority') == 'PHMSA')>PHMSA</option>
                                                    <option value="FAA" @selected(old('testing_authority') == 'FAA')>FAA</option>
                                                    <option value="FTA" @selected(old('testing_authority') == 'FTA')>FTA</option>
                                                    <option value="FRA" @selected(old('testing_authority') == 'FRA')>FRA</option>
                                                    <option value="USCG" @selected(old('testing_authority') == 'USCG')>USCG</option>
                                                </select>
                                                @error('testing_authority')
                                                    <div class="aq-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Reason for Test (Physical) --}}
                                            @if ($isPhysical)
                                                <div class="col-md-6 mt-2">
                                                    <label class="aq-label">Reason for Test <span
                                                            class="aq-req">*</span></label>
                                                    <select name="reason_for_test_id"
                                                        class="aq-ctrl @error('reason_for_test_id') is-invalid @enderror"
                                                        required>
                                                        <option value="1" @selected(old('reason_for_test_id', '1') == '1')>Pre-Employment
                                                        </option>
                                                        <option value="2" @selected(old('reason_for_test_id') == '2')>Post Accident
                                                        </option>
                                                        <option value="3" @selected(old('reason_for_test_id') == '3')>Random
                                                        </option>
                                                        <option value="5" @selected(old('reason_for_test_id') == '5')>Reasonable
                                                            Suspicion/Cause</option>
                                                        <option value="6" @selected(old('reason_for_test_id') == '6')>Return to Duty
                                                        </option>
                                                        <option value="23" @selected(old('reason_for_test_id') == '23')>Follow-Up
                                                        </option>
                                                        <option value="99" @selected(old('reason_for_test_id') == '99')>Other</option>
                                                    </select>
                                                    @error('reason_for_test_id')
                                                        <div class="aq-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            @endif

                                            {{-- Physical Reason (Non-physical) --}}
                                            @if (!$isPhysical)
                                                <div class="col-md-6 mt-2">
                                                    <label class="aq-label">Physical Reason <span
                                                            class="aq-req">*</span></label>
                                                    <select name="physical_reason_for_test_id"
                                                        class="aq-ctrl @error('physical_reason_for_test_id') is-invalid @enderror"
                                                        required>
                                                        <option value="">Select Physical Reason</option>
                                                        <option value="NC">New Certification</option>
                                                        <option value="RE">Recertification</option>
                                                        <option value="FU">Follow-Up</option>
                                                        <option value="OT">Other</option>
                                                        <option value="SA">Site Access</option>
                                                        <option value="PE">Pre-employment</option>
                                                        <option value="RD">Return to Duty</option>
                                                        <option value="SU">Surveillance</option>
                                                    </select>
                                                    @error('physical_reason_for_test_id')
                                                        <div class="aq-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            @endif

                                            {{-- Collection Type --}}
                                            <div class="col-md-6 mt-2">
                                                <label class="aq-label">Collection Type</label>
                                                <select name="observed_requested"
                                                    class="aq-ctrl @error('observed_requested') is-invalid @enderror">
                                                    <option value="N" @selected(old('observed_requested', 'N') == 'N')>Not Observed
                                                    </option>
                                                    <option value="Y" @selected(old('observed_requested') == 'Y')>Observed</option>
                                                </select>
                                                @error('observed_requested')
                                                    <div class="aq-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Specimen Type --}}
                                            <div class="col-md-6 mt-2">
                                                <label class="aq-label">Specimen Type</label>
                                                <select name="split_specimen_requested"
                                                    class="aq-ctrl @error('split_specimen_requested') is-invalid @enderror">
                                                    <option value="N" @selected(old('split_specimen_requested', 'N') == 'N')>Single Specimen
                                                    </option>
                                                    <option value="Y" @selected(old('split_specimen_requested') == 'Y')>Split Specimen
                                                    </option>
                                                </select>
                                                @error('split_specimen_requested')
                                                    <div class="aq-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Expiration Date --}}
                                            <div class="col-md-6 mt-2">
                                                <label class="aq-label">Order Expiration <span
                                                        class="aq-opt">Optional</span></label>
                                                <div class="aq-iw">
                                                    <i class="fas fa-clock aq-icon"></i>
                                                    <input type="datetime-local" name="end_datetime"
                                                        class="aq-ctrl @error('end_datetime') is-invalid @enderror"
                                                        value="{{ old('end_datetime') }}">
                                                </div>
                                                <div class="aq-hint"><i class="fas fa-info-circle"></i> For ePhysical,
                                                    must be within 7 days</div>
                                                @error('end_datetime')
                                                    <div class="aq-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Timezone --}}
                                            <div class="col-md-6 mt-2">
                                                <label class="aq-label">Timezone <span
                                                        class="aq-opt">Optional</span></label>
                                                <select name="end_datetime_timezone_id"
                                                    class="aq-ctrl @error('end_datetime_timezone_id') is-invalid @enderror">
                                                    <option value="">Select Timezone</option>
                                                    <option value="1" @selected(old('end_datetime_timezone_id') == '1')>Eastern Time
                                                    </option>
                                                    <option value="2" @selected(old('end_datetime_timezone_id') == '2')>Central Time
                                                    </option>
                                                    <option value="3" @selected(old('end_datetime_timezone_id') == '3')>Mountain Time
                                                    </option>
                                                    <option value="4" @selected(old('end_datetime_timezone_id') == '4')>Pacific Time
                                                    </option>
                                                    <option value="5" @selected(old('end_datetime_timezone_id') == '5')>Hawaii-Aleutian
                                                    </option>
                                                    <option value="6" @selected(old('end_datetime_timezone_id') == '6')>Alaskan Time
                                                    </option>
                                                    <option value="7" @selected(old('end_datetime_timezone_id') == '7')>Atlantic Time
                                                    </option>
                                                    <option value="8" @selected(old('end_datetime_timezone_id') == '8')>Guam Time</option>
                                                </select>
                                                <div class="aq-hint"><i class="fas fa-info-circle"></i> Required if
                                                    expiration date is set</div>
                                                @error('end_datetime_timezone_id')
                                                    <div class="aq-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- CSL --}}
                                            <div class="col-md-6 mt-2">
                                                <label class="aq-label">Client Site Location (CSL) <span
                                                        class="aq-opt">Optional</span></label>
                                                <input type="text" name="csl"
                                                    class="aq-ctrl @error('csl') is-invalid @enderror"
                                                    value="{{ old('csl', config('services.quest.default_csl')) }}"
                                                    placeholder="Enter CSL" maxlength="20">
                                                @error('csl')
                                                    <div class="aq-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- DER Contact Name --}}
                                            <div class="col-md-6 mt-2">
                                                <label class="aq-label">DER Contact Name <span
                                                        class="aq-opt">Optional</span></label>
                                                <div class="aq-iw">
                                                    <i class="fas fa-user-tie aq-icon"></i>
                                                    <input type="text" name="contact_name"
                                                        class="aq-ctrl @error('contact_name') is-invalid @enderror"
                                                        value="{{ old('contact_name', config('services.quest.default_contact_name')) }}"
                                                        placeholder="DER full name" maxlength="45">
                                                </div>
                                                @error('contact_name')
                                                    <div class="aq-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- DER Phone Number --}}
                                            <div class="col-md-6 mt-2">
                                                <label class="aq-label">DER Phone Number <span
                                                        class="aq-opt">Optional</span></label>
                                                <div class="aq-iw">
                                                    <i class="fas fa-phone aq-icon"></i>
                                                    <input type="tel" name="telephone_number"
                                                        class="aq-ctrl @error('telephone_number') is-invalid @enderror"
                                                        value="{{ old('telephone_number', config('services.quest.default_telephone')) }}"
                                                        placeholder="10-digit phone" maxlength="10">
                                                </div>
                                                @error('telephone_number')
                                                    <div class="aq-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Special Instructions --}}
                                            <div class="col-12 mt-2">
                                                <label class="aq-label">Special Instructions <span
                                                        class="aq-opt">Optional</span></label>
                                                <div class="aq-iw">
                                                    <i class="fas fa-sticky-note aq-icon"
                                                        style="top:1rem;transform:none;"></i>
                                                    <textarea name="order_comments" class="aq-ctrl @error('order_comments') is-invalid @enderror"
                                                        placeholder="Any special instructions for the collection site…" maxlength="250">{{ old('order_comments') }}</textarea>
                                                </div>
                                                @error('order_comments')
                                                    <div class="aq-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ── SUBMIT ── --}}
                                <div class="text-center pt-2">
                                    <button type="submit" class="aq-btn-submit" id="submitButton">
                                        <i class="fas fa-paper-plane"></i>
                                        <span id="submitButtonText">Submit DOT Test to Quest Diagnostics</span>
                                        <span id="submitButtonSpinner" class="spinner-border spinner-border-sm d-none"
                                            role="status" aria-hidden="true"></span>
                                    </button>
                                    <p class="mt-3 mb-0" style="font-size:.78rem;color:var(--aq-muted);">
                                        <i class="fas fa-lock me-1"></i> Data transmitted securely
                                    </p>
                                </div>

                            </form>
                        </div>{{-- /aq-body --}}
                    </div>{{-- /aq-card --}}

                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const dotTestSelect = document.getElementById('dot_test');
            const testingAuthorityField = document.getElementById('testingAuthorityField');
            const testingAuthoritySelect = document.getElementById('testing_authority');
            const form = document.getElementById('questOrderForm');
            const submitButton = document.getElementById('submitButton');
            const submitButtonText = document.getElementById('submitButtonText');
            const submitButtonSpinner = document.getElementById('submitButtonSpinner');

            // Function to enable/disable submit button
            function setSubmitButtonState(enabled, showSpinner = false) {
                if (submitButton) {
                    submitButton.disabled = !enabled;
                    submitButton.style.opacity = enabled ? '1' : '0.6';
                    submitButton.style.cursor = enabled ? 'pointer' : 'not-allowed';

                    if (showSpinner) {
                        submitButtonText.classList.add('d-none');
                        submitButtonSpinner.classList.remove('d-none');
                    } else {
                        submitButtonText.classList.remove('d-none');
                        submitButtonSpinner.classList.add('d-none');
                    }
                }
            }

            // ── DOT authority toggle ──
            function toggleTestingAuthority() {
                if (!testingAuthorityField || !testingAuthoritySelect) return;
                const isDOT = dotTestSelect.value === 'T';
                testingAuthorityField.style.display = isDOT ? 'block' : 'none';
                testingAuthoritySelect.required = isDOT;
                if (!isDOT) testingAuthoritySelect.classList.remove('is-invalid');
            }

            if (dotTestSelect) {
                dotTestSelect.value = 'T';
                toggleTestingAuthority();
                dotTestSelect.addEventListener('change', toggleTestingAuthority);
            }

            // ── Physical expiry validation ──
            const endDateTime = document.querySelector('input[name="end_datetime"]');
            const isPhysical = {{ $isPhysical ? 'true' : 'false' }};
            if (endDateTime && isPhysical) {
                endDateTime.addEventListener('change', function() {
                    const sel = new Date(this.value);
                    const max = new Date(Date.now() + 168 * 3600 * 1000);
                    if (sel > max) {
                        alert('For physical tests, the expiration date must be within 7 days.');
                        this.value = '';
                    }
                });
            }

            // Clear validation on input
            function clearValidationOnInput() {
                form.querySelectorAll('input, select, textarea').forEach(el => {
                    el.addEventListener('input', function() {
                        this.classList.remove('is-invalid');
                        // Re-enable submit button when user starts fixing errors
                        if (submitButton && submitButton.disabled) {
                            setSubmitButtonState(true, false);
                        }
                    });

                    el.addEventListener('change', function() {
                        this.classList.remove('is-invalid');
                        if (submitButton && submitButton.disabled) {
                            setSubmitButtonState(true, false);
                        }
                    });
                });
            }

            // ── Form validation with proper submit handling ──
            if (form) {
                form.addEventListener('submit', function(e) {
                    let valid = true;
                    const errors = [];

                    // Clear previous errors
                    form.querySelectorAll('.is-invalid').forEach(f => f.classList.remove('is-invalid'));

                    // Validate DOT Testing Authority
                    const isDOT = document.getElementById('dot_test').value === 'T';
                    if (isDOT && testingAuthoritySelect && !testingAuthoritySelect.value) {
                        valid = false;
                        testingAuthoritySelect.classList.add('is-invalid');
                        errors.push('DOT Testing Authority is required');
                    }

                    // Validate required fields (skip specific fields)
                    const skipFields = ['contact_name', 'telephone_number'];
                    form.querySelectorAll('[required]').forEach(field => {
                        if (skipFields.includes(field.name)) return;
                        if (!field.value || !field.value.trim()) {
                            valid = false;
                            field.classList.add('is-invalid');
                            const section = field.closest('.col-md-6, .col-12');
                            const label = section?.querySelector('.aq-label')?.textContent?.trim();
                            if (label) {
                                const cleanLabel = label.replace('*', '').replace('Optional', '')
                                    .trim();
                                errors.push(cleanLabel + ' is required');
                            } else if (field.name) {
                                errors.push(field.name.replace(/_/g, ' ') + ' is required');
                            }
                        }
                    });

                    // Email format validation
                    const emailField = document.querySelector('input[name="email"]');
                    if (emailField && emailField.value && !emailField.value.match(
                            /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/)) {
                        valid = false;
                        emailField.classList.add('is-invalid');
                        errors.push('Valid email address is required');
                    }

                    // Phone format validation (basic)
                    const phoneField = document.querySelector('input[name="primary_phone"]');
                    if (phoneField && phoneField.value && !phoneField.value.match(/^[\d\s\-\(\)\+]+$/)) {
                        valid = false;
                        phoneField.classList.add('is-invalid');
                        errors.push('Valid phone number is required');
                    }



                    if (!valid) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Disable submit button to prevent multiple clicks
                        setSubmitButtonState(false, false);

                        // Scroll to first error
                        const first = form.querySelector('.is-invalid');
                        if (first) {
                            first.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }

                        // Show error message
                        if (errors.length) {
                            const errorMessage = 'Please fix the following errors:\n\n• ' + [...new Set(
                                errors)].join('\n• ');
                            alert(errorMessage);
                        }

                        // Re-enable submit button after a short delay so user can fix errors
                        setTimeout(() => {
                            setSubmitButtonState(true, false);
                        }, 100);

                        return false;
                    }

                    // If validation passes, disable button and show spinner to prevent double submission
                    setSubmitButtonState(false, true);

                    // Allow form to submit
                    return true;
                });

                // Clear validation on input
                clearValidationOnInput();

                // Additional: Reset form state if there are server-side errors
                @if ($errors->any())
                    // If there are server validation errors, ensure submit button is enabled
                    setSubmitButtonState(true, false);

                    // Scroll to first server error
                    setTimeout(() => {
                        const firstError = document.querySelector('.is-invalid');
                        if (firstError) {
                            firstError.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }
                    }, 100);
                @endif
            }

            // Collection site validation on change
            const collectionSiteSelect = document.getElementById('collection_site_id');
            if (collectionSiteSelect) {
                collectionSiteSelect.addEventListener('change', function() {
                    this.classList.remove('is-invalid');
                    if (submitButton && submitButton.disabled) {
                        setSubmitButtonState(true, false);
                    }
                });
            }
        });

        // ── Select2 with proper validation handling ──
        $(document).ready(function() {
            const $collectionSelect = $('.select2-collection-sites');

            $collectionSelect.select2({
                placeholder: 'Search by name, address, city, or zip…',
                allowClear: true,
                minimumInputLength: 2,
                width: '100%',
                ajax: {
                    url: '{{ route('collection-sites.search') }}',
                    type: 'GET',
                    dataType: 'json',
                    delay: 500,
                    data: function(p) {
                        return {
                            q: p.term,
                            page: p.page || 1
                        };
                    },
                    processResults: function(data, p) {
                        p.page = p.page || 1;
                        return {
                            results: data.map(s => ({
                                id: s.collection_site_code,
                                text: s.text + ' (' + s.collection_site_code + ')',
                                collection_site_code: s.collection_site_code,
                                original_data: s
                            })),
                            pagination: {
                                more: (p.page * 50) < 1000
                            }
                        };
                    },
                    cache: true
                },
                templateResult: function(site) {
                    if (site.loading) return $('<span>Searching…</span>');
                    return $(
                        '<div class="select2-site-result">' +
                        '<div class="site-name">' + site.text + '</div>' +
                        '<div class="site-code">Code: ' + (site.collection_site_code || '') +
                        '</div>' +
                        '</div>'
                    );
                },
                escapeMarkup: m => m
            });

            // Clear Select2 validation on change
            $collectionSelect.on('change', function() {
                $(this).removeClass('is-invalid');
                const submitBtn = document.getElementById('submitButton');
                if (submitBtn && submitBtn.disabled) {
                    const setSubmitButtonState = window.setSubmitButtonState || function() {};
                    // The button state function is defined in the outer scope
                    if (typeof window.setSubmitButtonState === 'function') {
                        window.setSubmitButtonState(true, false);
                    }
                }
            });
        });
    </script>
@endpush
