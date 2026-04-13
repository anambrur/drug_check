@extends('layouts.frontend.master2')

@section('content')
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=DM+Sans:wght@300;400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        :root {
            --primary: #1a56db;
            --primary-dark: #1044b3;
            --primary-light: #e8f0fe;
            --primary-glow: rgba(26, 86, 219, 0.15);
            --accent: #06b6d4;
            --success: #059669;
            --danger: #e11d48;
            --surface: #ffffff;
            --surface-2: #f8faff;
            --border: #e2e8f8;
            --text: #0f172a;
            --text-muted: #64748b;
            --text-light: #94a3b8;
            --shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.06), 0 1px 2px rgba(15, 23, 42, 0.04);
            --shadow-md: 0 4px 16px rgba(15, 23, 42, 0.08), 0 2px 6px rgba(15, 23, 42, 0.05);
            --shadow-lg: 0 20px 60px rgba(15, 23, 42, 0.12), 0 8px 24px rgba(15, 23, 42, 0.07);
            --radius: 14px;
            --radius-sm: 9px;
            --font-head: 'Sora', sans-serif;
            --font-body: 'DM Sans', sans-serif;
        }

        body {
            font-family: var(--font-body);
            color: var(--text);
        }

        /* ─── Page Background ─── */
        .quest-page-bg {
            background: linear-gradient(160deg, #f0f5ff 0%, #fafbff 50%, #f0f9ff 100%);
            min-height: 100vh;
            padding: 5rem 0 4rem;
        }

        /* ─── Card Wrapper ─── */
        .quest-card {
            background: var(--surface);
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        /* ─── Header ─── */
        .quest-header {
            background: linear-gradient(135deg, #1a56db 0%, #0e3fa3 60%, #0c2f80 100%);
            padding: 2.25rem 2.5rem 2rem;
            position: relative;
            overflow: hidden;
        }

        .quest-header::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
        }

        .quest-header::after {
            content: '';
            position: absolute;
            bottom: -40px;
            left: 30%;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: rgba(6, 182, 212, 0.1);
        }

        .quest-header .badge-pill {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            font-family: var(--font-head);
            font-size: 0.7rem;
            font-weight: 500;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 0.35rem 0.9rem;
            border-radius: 100px;
            display: inline-block;
            margin-bottom: 0.85rem;
        }

        .quest-header h4 {
            font-family: var(--font-head);
            font-size: 1.55rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.35rem;
            line-height: 1.3;
        }

        .quest-header p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            margin: 0;
        }

        .quest-header-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: #fff;
            flex-shrink: 0;
        }

        /* ─── Progress Stepper ─── */
        .stepper-wrap {
            padding: 2rem 2.5rem 0;
        }

        .stepper {
            display: flex;
            align-items: center;
            gap: 0;
        }

        .stepper-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }

        .stepper-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 20px;
            left: 50%;
            right: -50%;
            height: 2px;
            background: var(--border);
            z-index: 0;
        }

        .stepper-item.completed:not(:last-child)::after,
        .stepper-item.active:not(:last-child)::after {
            background: linear-gradient(90deg, var(--primary), var(--border));
        }

        .stepper-item.completed:not(:last-child)::after {
            background: var(--primary);
        }

        .stepper-dot {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--surface-2);
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-head);
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--text-light);
            z-index: 1;
            transition: all 0.3s;
            position: relative;
        }

        .stepper-item.completed .stepper-dot {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .stepper-item.active .stepper-dot {
            background: white;
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-glow);
        }

        .stepper-label {
            margin-top: 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text-light);
            font-family: var(--font-head);
        }

        .stepper-item.completed .stepper-label,
        .stepper-item.active .stepper-label {
            color: var(--primary);
            font-weight: 600;
        }

        /* ─── Form Body ─── */
        .quest-body {
            padding: 2rem 2.5rem 2.5rem;
        }

        /* ─── Section Block ─── */
        .form-section {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            margin-bottom: 1.75rem;
            background: var(--surface);
            transition: box-shadow 0.2s;
        }

        .form-section:focus-within {
            box-shadow: 0 0 0 3px var(--primary-glow);
            border-color: rgba(26, 86, 219, 0.3);
        }

        .form-section-head {
            background: var(--primary-light);
            padding: 0.9rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.7rem;
            border-bottom: 1px solid rgba(26, 86, 219, 0.1);
        }

        .form-section-head .icon-wrap {
            width: 30px;
            height: 30px;
            background: var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        .form-section-head h6 {
            font-family: var(--font-head);
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin: 0;
        }

        .form-section-body {
            padding: 1.5rem;
        }

        /* ─── Form Controls ─── */
        .form-label {
            font-family: var(--font-head);
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text);
            letter-spacing: 0.01em;
            margin-bottom: 0.4rem;
        }

        .req {
            color: var(--danger);
            margin-left: 2px;
        }

        .form-control,
        .form-select {
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 0.65rem 1rem;
            font-size: 0.88rem;
            font-family: var(--font-body);
            color: var(--text);
            background: var(--surface);
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            box-shadow: var(--shadow-sm);
        }

        .form-control::placeholder {
            color: var(--text-light);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3.5px var(--primary-glow);
            background: #fafcff;
            outline: none;
        }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: var(--danger);
            background-image: none;
        }

        .form-control.is-invalid:focus,
        .form-select.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.12);
        }

        .invalid-feedback {
            font-size: 0.77rem;
            color: var(--danger);
            font-weight: 500;
            margin-top: 0.3rem;
        }

        .form-text {
            font-size: 0.76rem;
            color: var(--text-muted);
            margin-top: 0.3rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        /* ─── Input Group Icon ─── */
        .input-icon-wrap {
            position: relative;
        }

        .input-icon-wrap .input-icon {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 0.82rem;
            pointer-events: none;
        }

        .input-icon-wrap .form-control,
        .input-icon-wrap .form-select {
            padding-left: 2.4rem;
        }

        /* ─── Alert ─── */
        .quest-alert {
            border: 1px solid rgba(225, 29, 72, 0.2);
            background: rgba(225, 29, 72, 0.04);
            border-radius: var(--radius-sm);
            padding: 0.9rem 1.1rem;
            font-size: 0.875rem;
            color: #9f1239;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.7rem;
        }

        /* ─── Submit Button ─── */
        .btn-submit {
            background: linear-gradient(135deg, #1a56db 0%, #0e3fa3 100%);
            border: none;
            border-radius: 12px;
            font-family: var(--font-head);
            font-weight: 700;
            font-size: 1rem;
            color: white;
            padding: 0.9rem 3rem;
            transition: all 0.25s;
            box-shadow: 0 4px 20px rgba(26, 86, 219, 0.35);
            letter-spacing: 0.01em;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(26, 86, 219, 0.45);
            color: white;
            background: linear-gradient(135deg, #1d5fe0 0%, #0f44b0 100%);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* ─── Floating label badge ─── */
        .optional-badge {
            font-size: 0.68rem;
            background: #f1f5f9;
            color: var(--text-muted);
            border-radius: 4px;
            padding: 1px 6px;
            font-weight: 500;
            margin-left: 6px;
            vertical-align: middle;
        }

        /* ─── Responsive ─── */
        @media (max-width: 768px) {
            .quest-header {
                padding: 1.75rem 1.5rem 1.5rem;
            }

            .quest-body {
                padding: 1.5rem 1.25rem 2rem;
            }

            .stepper-wrap {
                padding: 1.5rem 1.25rem 0;
            }

            .stepper-label {
                display: none;
            }

            .btn-submit {
                width: 100%;
                justify-content: center;
            }
        }

        /* ─── Select2 Custom ─── */
        .select2-container--default .select2-selection--single {
            border: 1.5px solid var(--border) !important;
            border-radius: var(--radius-sm) !important;
            height: auto !important;
            padding: 0.65rem 1rem !important;
            font-size: 0.88rem !important;
            font-family: var(--font-body) !important;
            box-shadow: var(--shadow-sm) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5 !important;
            padding: 0 !important;
            color: var(--text) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
            right: 0.75rem !important;
        }

        .select2-container--default .select2-results__option--highlighted {
            background: var(--primary) !important;
        }

        .select2-dropdown {
            border-radius: var(--radius-sm) !important;
            border: 1.5px solid var(--border) !important;
            box-shadow: var(--shadow-md) !important;
            overflow: hidden;
        }

        .select2-search--dropdown .select2-search__field {
            border-radius: 6px !important;
            border: 1.5px solid var(--border) !important;
            padding: 0.5rem 0.75rem !important;
            font-size: 0.85rem;
        }
    </style>

    <div class="quest-page-bg">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="">

                    <div class="quest-card">

                        {{-- ── HEADER ── --}}
                        <div class="quest-header">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <span class="badge-pill">Quest Diagnostics</span>
                                    <h4>{{ $portfolio->title }}</h4>
                                    <p>Complete the details below to submit your lab order</p>
                                </div>
                                <div class="quest-header-icon d-none d-sm-flex">
                                    <i class="fas fa-flask"></i>
                                </div>
                            </div>
                        </div>

                        {{-- ── STEPPER ── --}}
                        <div class="stepper-wrap">
                            <div class="stepper">
                                <div class="stepper-item completed">
                                    <div class="stepper-dot"><i class="fas fa-check" style="font-size:0.75rem;"></i></div>
                                    <div class="stepper-label">Payment</div>
                                </div>
                                <div class="stepper-item active">
                                    <div class="stepper-dot">2</div>
                                    <div class="stepper-label">Test Information</div>
                                </div>
                                <div class="stepper-item">
                                    <div class="stepper-dot">3</div>
                                    <div class="stepper-label">Confirmation</div>
                                </div>
                            </div>
                        </div>

                        {{-- ── BODY ── --}}
                        <div class="quest-body">

                            @if (session('error'))
                                <div class="quest-alert" role="alert">
                                    <i class="fas fa-exclamation-circle mt-1"></i>
                                    <div>{{ session('error') }}</div>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('quest.submit-order') }}" id="questOrderForm">
                                @csrf

                                <input type="hidden" name="portfolio_id" value="{{ $portfolio->id }}">
                                <input type="hidden" name="payment_intent_id"
                                    value="{{ $paymentData['payment_intent_id'] }}">
                                @if (config('app.env') === 'production')
                                    <input type="hidden" name="lab_account"
                                        value="{{ $paymentData['portfolio']->quest_lab_account }}">
                                @else
                                    <input type="hidden" name="lab_account" value="{{ config('services.quest.lab_account') }}">
                                @endif
                                <input type="hidden" name="is_physical"
                                    value="{{ str_contains(strtolower($portfolio->title), 'physical') ? 'true' : 'false' }}">
                                <input type="hidden" name="is_ebat"
                                    value="{{ str_contains(strtolower($portfolio->title), 'ebat') ? 'true' : 'false' }}">
                                <input type="hidden" name="unit_codes"
                                    value="{{ $paymentData['portfolio']->quest_unit_code }}">
                                <input type="hidden" name="test_type" value="non_dot">
                                <input type="hidden" name="response_url" value="{{ url('/api/quest/order-status') }}">

                                {{-- ════ PERSONAL INFORMATION ════ --}}
                                <div class="form-section">
                                    <div class="form-section-head">
                                        <div class="icon-wrap"><i class="fas fa-user"></i></div>
                                        <h6>Personal Information</h6>
                                    </div>
                                    <div class="form-section-body">
                                        <div class="row g-3">

                                            {{-- First Name --}}
                                            <div class="col-md-6">
                                                <label for="first_name" class="form-label">First Name <span
                                                        class="req">*</span></label>
                                                <div class="input-icon-wrap">
                                                    <i class="fas fa-user input-icon"></i>
                                                    <input type="text"
                                                        class="form-control @error('first_name') is-invalid @enderror"
                                                        name="first_name" id="first_name"
                                                        value="{{ old('first_name', $paymentData['first_name']) }}"
                                                        placeholder="e.g. John" required maxlength="20">
                                                </div>
                                                @error('first_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Last Name --}}
                                            <div class="col-md-6">
                                                <label for="last_name" class="form-label">Last Name <span
                                                        class="req">*</span></label>
                                                <div class="input-icon-wrap">
                                                    <i class="fas fa-user input-icon"></i>
                                                    <input type="text"
                                                        class="form-control @error('last_name') is-invalid @enderror"
                                                        name="last_name" id="last_name"
                                                        value="{{ old('last_name', $paymentData['last_name']) }}"
                                                        placeholder="e.g. Doe" required maxlength="25">
                                                </div>
                                                @error('last_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Middle Name --}}
                                            <div class="col-md-6">
                                                <label for="middle_name" class="form-label">Middle Name <span
                                                        class="optional-badge">Optional</span></label>
                                                <input type="text"
                                                    class="form-control @error('middle_name') is-invalid @enderror"
                                                    name="middle_name" id="middle_name" value="{{ old('middle_name') }}"
                                                    placeholder="e.g. Michael" maxlength="20">
                                                @error('middle_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Primary ID --}}
                                            <div class="col-md-6">
                                                <label for="primary_id" class="form-label">Driver's License / ID <span
                                                        class="req">*</span></label>
                                                <div class="input-icon-wrap">
                                                    <i class="fas fa-id-card input-icon"></i>
                                                    <input type="text"
                                                        class="form-control @error('primary_id') is-invalid @enderror"
                                                        name="primary_id" id="primary_id" value="{{ old('primary_id') }}"
                                                        placeholder="Enter ID number" required maxlength="25">
                                                </div>
                                                @error('primary_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Email --}}
                                            <div class="col-md-6">
                                                <label for="email" class="form-label">Email Address <span
                                                        class="req">*</span></label>
                                                <div class="input-icon-wrap">
                                                    <i class="fas fa-envelope input-icon"></i>
                                                    <input type="email"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        name="email" id="email"
                                                        value="{{ old('email', $paymentData['email']) }}"
                                                        placeholder="you@example.com" required maxlength="254">
                                                </div>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Primary ID Type (Physical only) --}}
                                            @if (str_contains(strtolower($paymentData['portfolio']->title), 'physical'))
                                                <div class="col-md-6">
                                                    <label for="primary_id_type" class="form-label">Primary ID Type <span
                                                            class="optional-badge">Optional</span></label>
                                                    <select class="form-select @error('primary_id_type') is-invalid @enderror"
                                                        name="primary_id_type" id="primary_id_type">
                                                        <option value="">Select ID Type</option>
                                                        <option value="DL" @selected(old('primary_id_type') == 'DL')>Driver's
                                                            License</option>
                                                        <option value="OTHER" @selected(old('primary_id_type') == 'OTHER')>Other
                                                            Government ID</option>
                                                    </select>
                                                    @error('primary_id_type')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            @endif

                                            {{-- Date of Birth --}}
                                            <div class="col-md-6">
                                                <label for="dob" class="form-label">Date of Birth <span
                                                        class="optional-badge">Optional</span></label>
                                                <div class="input-icon-wrap">
                                                    <i class="fas fa-calendar input-icon"></i>
                                                    <input type="text"
                                                        class="form-control datepicker @error('dob') is-invalid @enderror"
                                                        name="dob" id="dob" value="{{ old('dob') }}"
                                                        placeholder="MM/DD/YYYY" autocomplete="off">
                                                </div>
                                                @error('dob')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Primary Phone --}}
                                            <div class="col-md-6">
                                                <label for="primary_phone" class="form-label">Primary Phone <span
                                                        class="optional-badge">Optional</span></label>
                                                <div class="input-icon-wrap">
                                                    <i class="fas fa-phone input-icon"></i>
                                                    <input type="tel"
                                                        class="form-control @error('primary_phone') is-invalid @enderror"
                                                        name="primary_phone" id="primary_phone"
                                                        value="{{ old('primary_phone', $paymentData['phone']) }}"
                                                        placeholder="5550000000" pattern="[0-9]*" inputmode="numeric">
                                                </div>
                                                @error('primary_phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Secondary Phone --}}
                                            <div class="col-md-6">
                                                <label for="secondary_phone" class="form-label">Secondary Phone <span
                                                        class="optional-badge">Optional</span></label>
                                                <div class="input-icon-wrap">
                                                    <i class="fas fa-phone-alt input-icon"></i>
                                                    <input type="tel"
                                                        class="form-control @error('secondary_phone') is-invalid @enderror"
                                                        name="secondary_phone" id="secondary_phone"
                                                        value="{{ old('secondary_phone') }}" placeholder="5550000000" pattern="[0-9]*" inputmode="numeric">
                                                </div>
                                                @error('secondary_phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Zip Code (Physical only) --}}
                                            @if (str_contains(strtolower($paymentData['portfolio']->title), 'physical'))
                                                <div class="col-md-6">
                                                    <label for="zip_code" class="form-label">Zip Code <span
                                                            class="optional-badge">Optional</span></label>
                                                    <div class="input-icon-wrap">
                                                        <i class="fas fa-map-pin input-icon"></i>
                                                        <input type="text"
                                                            class="form-control @error('zip_code') is-invalid @enderror"
                                                            name="zip_code" id="zip_code" value="{{ old('zip_code') }}"
                                                            placeholder="e.g. 90210">
                                                    </div>
                                                    @error('zip_code')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>

                                {{-- ════ TEST INFORMATION ════ --}}
                                <div class="form-section">
                                    <div class="form-section-head">
                                        <div class="icon-wrap"><i class="fas fa-vial"></i></div>
                                        <h6>Test Information</h6>
                                    </div>
                                    <div class="form-section-body">
                                        <div class="row g-3">

                                            {{-- DOT Test --}}
                                            <div class="col-md-6">
                                                <label for="dot_test" class="form-label">Test Type <span
                                                        class="req">*</span></label>
                                                <select class="form-select @error('dot_test') is-invalid @enderror"
                                                    name="dot_test" id="dot_test" required>
                                                    <option value="F" @selected(old('dot_test', 'F') == 'F')>Non-DOT Test
                                                    </option>
                                                    <option value="T" @selected(old('dot_test') == 'T')>DOT Test</option>
                                                </select>
                                                @error('dot_test')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Collection Site --}}
                                            <div class="col-md-6">
                                                <label for="collection_site_id" class="form-label">Collection Site <span
                                                        class="optional-badge">Optional</span></label>
                                                <select name="collection_site_id" id="collection_site_id"
                                                    class="form-control select2-collection-sites">
                                                    <option value="">Select a collection site...</option>
                                                </select>
                                                @error('collection_site_id')
                                                    <div class="text-danger" style="font-size:0.77rem;margin-top:0.3rem;">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            {{-- DOT Authority (conditional) --}}
                                            <div class="col-md-6" id="testingAuthorityField" style="display:none;">
                                                <label for="testing_authority" class="form-label">DOT Testing Authority
                                                    <span class="req">*</span></label>
                                                <select class="form-select @error('testing_authority') is-invalid @enderror"
                                                    name="testing_authority" id="testing_authority">
                                                    <option value="">Select Authority</option>
                                                    <option value="FMCSA" @selected(old('testing_authority') == 'FMCSA')>FMCSA
                                                    </option>
                                                    <option value="PHMSA" @selected(old('testing_authority') == 'PHMSA')>PHMSA
                                                    </option>
                                                    <option value="FAA" @selected(old('testing_authority') == 'FAA')>FAA
                                                    </option>
                                                    <option value="FTA" @selected(old('testing_authority') == 'FTA')>FTA
                                                    </option>
                                                    <option value="FRA" @selected(old('testing_authority') == 'FRA')>FRA
                                                    </option>
                                                    <option value="USCG" @selected(old('testing_authority') == 'USCG')>USCG
                                                    </option>
                                                </select>
                                                @error('testing_authority')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Reason for Test (Non-physical) --}}
                                            @if (!str_contains(strtolower($paymentData['portfolio']->title), 'physical'))
                                                <div class="col-md-6">
                                                    <label for="reason_for_test_id" class="form-label">Reason for Test
                                                        <span class="req">*</span></label>
                                                    <select
                                                        class="form-select @error('reason_for_test_id') is-invalid @enderror"
                                                        name="reason_for_test_id" id="reason_for_test_id" required>
                                                        <option value="1" @selected(old('reason_for_test_id', '1') == '1')>
                                                            Pre-Employment
                                                        </option>
                                                        <option value="2" @selected(old('reason_for_test_id') == '2')>Post
                                                            Accident
                                                        </option>
                                                        <option value="3" @selected(old('reason_for_test_id') == '3')>Random
                                                        </option>
                                                        <option value="5" @selected(old('reason_for_test_id') == '5')>Reasonable
                                                            Suspicion / Cause</option>
                                                        <option value="6" @selected(old('reason_for_test_id') == '6')>Return to
                                                            Duty
                                                        </option>
                                                        <option value="23" @selected(old('reason_for_test_id') == '23')>Follow-Up
                                                        </option>
                                                        <option value="99" @selected(old('reason_for_test_id') == '99')>Other
                                                        </option>
                                                    </select>
                                                    @error('reason_for_test_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            @endif

                                            {{-- Physical Reason (Physical only) --}}
                                            @if (str_contains(strtolower($paymentData['portfolio']->title), 'physical'))
                                                <div class="col-md-6">
                                                    <label for="physical_reason_for_test_id" class="form-label">Physical
                                                        Reason <span class="req">*</span></label>
                                                    <select
                                                        class="form-select @error('physical_reason_for_test_id') is-invalid @enderror"
                                                        name="physical_reason_for_test_id" id="physical_reason_for_test_id"
                                                        required>
                                                        <option value="">Select Physical Reason</option>
                                                        <option value="NC" @selected(old('physical_reason_for_test_id') == 'NC')>
                                                            New
                                                            Certification</option>
                                                        <option value="RE" @selected(old('physical_reason_for_test_id') == 'RE')>
                                                            Recertification</option>
                                                        <option value="FU" @selected(old('physical_reason_for_test_id') == 'FU')>
                                                            Follow-Up
                                                        </option>
                                                        <option value="OT" @selected(old('physical_reason_for_test_id') == 'OT')>
                                                            Other</option>
                                                        <option value="SA" @selected(old('physical_reason_for_test_id') == 'SA')>
                                                            Site Access
                                                        </option>
                                                        <option value="PE" @selected(old('physical_reason_for_test_id') == 'PE')>
                                                            Pre-employment
                                                        </option>
                                                        <option value="RD" @selected(old('physical_reason_for_test_id') == 'RD')>
                                                            Return to Duty
                                                        </option>
                                                        <option value="SU" @selected(old('physical_reason_for_test_id') == 'SU')>
                                                            Surveillance
                                                        </option>
                                                    </select>
                                                    @error('physical_reason_for_test_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            @endif

                                            {{-- Expiration Date --}}
                                            <div class="col-md-6">
                                                <label for="end_datetime" class="form-label">Order Expiration <span
                                                        class="optional-badge">Optional</span></label>
                                                <div class="input-icon-wrap">
                                                    <i class="fas fa-clock input-icon"></i>
                                                    <input type="datetime-local"
                                                        class="form-control @error('end_datetime') is-invalid @enderror"
                                                        name="end_datetime" id="end_datetime"
                                                        value="{{ old('end_datetime') }}">
                                                </div>
                                                <div class="form-text"><i class="fas fa-info-circle"></i> For ePhysical,
                                                    must be within 7 days</div>
                                                @error('end_datetime')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Timezone --}}
                                            <div class="col-md-6">
                                                <label for="end_datetime_timezone_id" class="form-label">Timezone <span
                                                        class="optional-badge">Optional</span></label>
                                                <select
                                                    class="form-select @error('end_datetime_timezone_id') is-invalid @enderror"
                                                    name="end_datetime_timezone_id" id="end_datetime_timezone_id">
                                                    <option value="">Select Timezone</option>
                                                    <option value="1" @selected(old('end_datetime_timezone_id') == '1')>
                                                        Eastern Time
                                                    </option>
                                                    <option value="2" @selected(old('end_datetime_timezone_id') == '2')>
                                                        Central Time
                                                    </option>
                                                    <option value="3" @selected(old('end_datetime_timezone_id') == '3')>
                                                        Mountain Time
                                                    </option>
                                                    <option value="4" @selected(old('end_datetime_timezone_id') == '4')>
                                                        Pacific Time
                                                    </option>
                                                    <option value="5" @selected(old('end_datetime_timezone_id') == '5')>
                                                        Hawaii-Aleutian
                                                    </option>
                                                    <option value="6" @selected(old('end_datetime_timezone_id') == '6')>
                                                        Alaskan Time
                                                    </option>
                                                    <option value="7" @selected(old('end_datetime_timezone_id') == '7')>
                                                        Atlantic Time
                                                    </option>
                                                    <option value="8" @selected(old('end_datetime_timezone_id') == '8')>Guam
                                                        Time</option>
                                                </select>
                                                <div class="form-text"><i class="fas fa-info-circle"></i> Required if
                                                    expiration date is set</div>
                                                @error('end_datetime_timezone_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Collection Type --}}
                                            <div class="col-md-6">
                                                <label for="observed_requested" class="form-label">Collection Type</label>
                                                <select
                                                    class="form-select @error('observed_requested') is-invalid @enderror"
                                                    name="observed_requested" id="observed_requested">
                                                    <option value="N" @selected(old('observed_requested', 'N') == 'N')>Not
                                                        Observed
                                                    </option>
                                                    <option value="Y" @selected(old('observed_requested') == 'Y')>Observed
                                                    </option>
                                                </select>
                                                @error('observed_requested')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Specimen Type --}}
                                            <div class="col-md-6">
                                                <label for="split_specimen_requested" class="form-label">Specimen
                                                    Type</label>
                                                <select
                                                    class="form-select @error('split_specimen_requested') is-invalid @enderror"
                                                    name="split_specimen_requested" id="split_specimen_requested">
                                                    <option value="N" @selected(old('split_specimen_requested', 'N') == 'N')>
                                                        Single Specimen
                                                    </option>
                                                    <option value="Y" @selected(old('split_specimen_requested') == 'Y')>Split
                                                        Specimen
                                                    </option>
                                                </select>
                                                @error('split_specimen_requested')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- CSL --}}
                                            <div class="col-md-6">
                                                <label for="csl" class="form-label">Client Site Location (CSL) <span
                                                        class="optional-badge">Optional</span></label>
                                                <input type="text" class="form-control @error('csl') is-invalid @enderror"
                                                    name="csl" id="csl"
                                                    value="{{ old('csl', config('services.quest.default_csl')) }}"
                                                    placeholder="Enter CSL" maxlength="20">
                                                @error('csl')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- DER Contact --}}
                                            <div class="col-md-6">
                                                <label for="contact_name" class="form-label">DER Contact Name <span
                                                        class="optional-badge">Optional</span></label>
                                                <div class="input-icon-wrap">
                                                    <i class="fas fa-user-tie input-icon"></i>
                                                    <input type="text"
                                                        class="form-control @error('contact_name') is-invalid @enderror"
                                                        name="contact_name" id="contact_name"
                                                        value="{{ old('contact_name', config('services.quest.default_contact_name')) }}"
                                                        placeholder="DER full name" maxlength="45">
                                                </div>
                                                @error('contact_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- DER Phone --}}
                                            <div class="col-md-6">
                                                <label for="telephone_number" class="form-label">DER Phone Number <span
                                                        class="optional-badge">Optional</span></label>
                                                <div class="input-icon-wrap">
                                                    <i class="fas fa-phone input-icon"></i>
                                                    <input type="tel"
                                                        class="form-control @error('telephone_number') is-invalid @enderror"
                                                        name="telephone_number" id="telephone_number"
                                                        value="{{ old('telephone_number', config('services.quest.default_telephone')) }}"
                                                        placeholder="10-digit phone" maxlength="10">
                                                </div>
                                                @error('telephone_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Special Instructions --}}
                                            <div class="col-12">
                                                <label for="order_comments" class="form-label">Special Instructions <span
                                                        class="optional-badge">Optional</span></label>
                                                <textarea class="form-control @error('order_comments') is-invalid @enderror"
                                                    name="order_comments" id="order_comments"
                                                    placeholder="Any special instructions for the collection site..."
                                                    maxlength="250">{{ old('order_comments') }}</textarea>
                                                @error('order_comments')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ── SUBMIT ── --}}
                                <div class="text-center pt-2">
                                    <button type="submit" class="btn btn-submit">
                                        <i class="fas fa-paper-plane"></i>
                                        Submit to Quest Diagnostics
                                    </button>
                                    <p class="mt-3 mb-0" style="font-size:0.78rem;color:var(--text-muted);">
                                        <i class="fas fa-lock me-1"></i> Your data is transmitted securely
                                    </p>
                                </div>

                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            flatpickr("#dob", {
                dateFormat: "m/d/Y",   // US format: MM/DD/YYYY
                defaultDate: "today",
                maxDate: "today",
                allowInput: false
            });

            // DOT authority toggle
            const dotTestSelect = document.getElementById('dot_test');
            const testingAuthorityField = document.getElementById('testingAuthorityField');

            function toggleTestingAuthority() {
                if (!testingAuthorityField) return;
                const show = dotTestSelect.value === 'T';
                testingAuthorityField.style.display = show ? 'block' : 'none';
                const ta = document.getElementById('testing_authority');
                if (ta) ta.required = show;
            }

            if (dotTestSelect) {
                dotTestSelect.addEventListener('change', toggleTestingAuthority);
                toggleTestingAuthority();
            }

            // DOB datepicker
            if (typeof $ !== 'undefined' && $.fn.datepicker) {
                $('.datepicker').datepicker({
                    format: 'mm/dd/yyyy',
                    autoclose: true,
                    todayHighlight: true,
                    endDate: '0d'
                });
            }

            // Physical expiry validation
            const endDateTime = document.getElementById('end_datetime');
            const isPhysical = "{{ str_contains(strtolower($paymentData['portfolio']->title), 'physical') }}" ===
                "1";
            if (endDateTime && isPhysical) {
                endDateTime.addEventListener('change', function () {
                    const sel = new Date(this.value);
                    const max = new Date(Date.now() + 168 * 3600 * 1000);
                    if (sel > max) {
                        alert('For physical tests, the expiration date must be within 7 days.');
                        this.value = '';
                    }
                });
            }

            // Form validation UX
            const form = document.getElementById('questOrderForm');
            if (form) {
                // Restrict phone fields to digits only
                ['primary_phone', 'secondary_phone', 'telephone_number'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.addEventListener('input', function() {
                            this.value = this.value.replace(/\D/g, '');
                        });
                    }
                });

                form.addEventListener('submit', function (e) {
                    let valid = true;
                    form.querySelectorAll('[required]').forEach(f => {
                        if (!f.value.trim()) {
                            valid = false;
                            f.classList.add('is-invalid');
                        }
                    });
                    if (!valid) {
                        e.preventDefault();
                        const firstError = form.querySelector('.is-invalid');
                        if (firstError) firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                });
                form.querySelectorAll('input, select, textarea').forEach(el => {
                    el.addEventListener('input', () => el.classList.remove('is-invalid'));
                });
            }
        });

        // Select2
        $(document).ready(function () {
            $('.select2-collection-sites').select2({
                placeholder: 'Search by name, address, city, or zip…',
                allowClear: true,
                minimumInputLength: 2,
                width: '100%',
                ajax: {
                    url: '{{ route('collection-sites.search') }}',
                    type: 'GET',
                    dataType: 'json',
                    delay: 500,
                    data: function (p) {
                        return {
                            q: p.term,
                            page: p.page || 1
                        };
                    },
                    processResults: function (data, p) {
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
                templateResult: function (site) {
                    if (site.loading) return $('<span>Searching…</span>');
                    return $('<div><strong>' + site.text +
                        '</strong><br><small style="color:#64748b">Code: ' + (site
                            .collection_site_code || '') + '</small></div>');
                },
                escapeMarkup: m => m
            });
        });
    </script>
@endpush