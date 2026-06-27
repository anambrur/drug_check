@extends('layouts.frontend.master2')

@section('content')

    {{-- ── Google Fonts ── --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        /* ─── Design Tokens ─── */
        :root {
            --pf-primary:       #1a56db;
            --pf-primary-dark:  #1044b3;
            --pf-primary-light: #e8f0fe;
            --pf-primary-glow:  rgba(26,86,219,.15);
            --pf-success:       #059669;
            --pf-danger:        #e11d48;
            --pf-surface:       #ffffff;
            --pf-surface-2:     #f8faff;
            --pf-border:        #e2e8f8;
            --pf-text:          #0f172a;
            --pf-muted:         #64748b;
            --pf-light:         #94a3b8;
            --pf-shadow-sm:     0 1px 3px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);
            --pf-shadow-md:     0 4px 16px rgba(15,23,42,.08),0 2px 6px rgba(15,23,42,.05);
            --pf-shadow-lg:     0 20px 60px rgba(15,23,42,.12),0 8px 24px rgba(15,23,42,.07);
            --pf-radius:        14px;
            --pf-radius-sm:     9px;
            --pf-font-head:     'Sora', sans-serif;
            --pf-font-body:     'DM Sans', sans-serif;
        }

        /* ─── Plan Section ─── */
        .plan-section {
            background: var(--pf-surface-2);
            padding: 55px 0 60px;
            border-bottom: 1px solid var(--pf-border);
        }
        .section-eyebrow {
            font-family: var(--pf-font-head);
            font-size: .72rem; font-weight: 700;
            letter-spacing: .09em; text-transform: uppercase;
            color: var(--pf-primary); margin-bottom: .35rem;
        }
        .plan-section h2 {
            font-family: var(--pf-font-head);
            font-weight: 700; color: var(--pf-text); margin-bottom: .5rem;
        }
        .plan-section .sub {
            font-family: var(--pf-font-body);
            color: var(--pf-muted); max-width: 560px; margin: 0 auto 2.5rem;
        }

        /* Plan card */
        .plan-card {
            border: 2px solid var(--pf-border) !important;
            border-top: 4px solid var(--pf-border) !important;
            border-radius: 15px !important;
            box-shadow: 0 2px 12px rgba(15,23,42,.06) !important;
            transition: transform .3s, box-shadow .3s, border-color .25s;
            cursor: pointer;
            height: 100%;
            position: relative;
            overflow: hidden;
            background: #fff;
        }
        .plan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 14px 40px rgba(15,23,42,.11) !important;
        }
        .plan-card.active {
            border-color: var(--pf-primary) !important;
            box-shadow: 0 0 0 3px var(--pf-primary-glow), 0 10px 36px rgba(26,86,219,.14) !important;
        }
        .plan-card.active::after {
            content: '✓ Selected';
            position: absolute; top: 16px; right: -30px;
            background: var(--pf-primary); color: #fff;
            font-family: var(--pf-font-head); font-size: .62rem; font-weight: 700;
            letter-spacing: .06em; text-transform: uppercase;
            padding: 4px 38px; transform: rotate(45deg);
        }
        .plan-card .card-body { padding: 1.6rem 1.4rem; }
        .plan-icon-wrap {
            width: 52px; height: 52px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem; color: #fff; margin-bottom: 1rem;
        }
        .plan-card h4 {
            font-family: var(--pf-font-head);
            font-size: 1rem; font-weight: 700; color: var(--pf-text); margin-bottom: .2rem;
        }
        .plan-card .range {
            font-family: var(--pf-font-body);
            font-size: .78rem; color: var(--pf-muted); margin-bottom: .9rem;
        }
        .plan-card hr { border-color: var(--pf-border); margin: .75rem 0; }

        /* Fee list inside plan card */
        .plan-fee-list { list-style: none; padding: 0; margin: 0; }
        .plan-fee-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: var(--pf-font-body);
            font-size: .78rem;
            color: var(--pf-muted);
            padding: .32rem 0;
            border-bottom: 1px dashed var(--pf-border);
        }
        .plan-fee-list li:last-child { border-bottom: none; }
        .plan-fee-list li .fee-label { display: flex; align-items: center; gap: .4rem; }
        .plan-fee-list li .fee-label i { color: var(--pf-primary); font-size: .72rem; }
        .plan-fee-list li .fee-val { font-weight: 700; color: var(--pf-text); white-space: nowrap; }
        .plan-card.active .plan-fee-list li { color: var(--pf-primary-dark); }
        .plan-card.active .plan-fee-list li .fee-val { color: var(--pf-primary); }

        /* ─── Main form section ─── */
        #application-form {
            font-family: var(--pf-font-body);
            padding: 50px 0 70px;
            background: var(--pf-surface);
        }

        /* pf-card */
        .pf-card {
            background: var(--pf-surface);
            border-radius: 20px;
            box-shadow: var(--pf-shadow-lg);
            border: 1px solid var(--pf-border);
            overflow: hidden;
        }
        .pf-header {
            background: linear-gradient(135deg,#1a56db 0%,#0e3fa3 60%,#0c2f80 100%);
            padding: 1.9rem 2.25rem 1.65rem;
            position: relative; overflow: hidden;
        }
        .pf-header::before {
            content:''; position:absolute; top:-60px; right:-60px;
            width:220px; height:220px; border-radius:50%; background:rgba(255,255,255,.05);
        }
        .pf-header::after {
            content:''; position:absolute; bottom:-40px; left:30%;
            width:160px; height:160px; border-radius:50%; background:rgba(6,182,212,.1);
        }
        .pf-header .pill {
            background:rgba(255,255,255,.15); backdrop-filter:blur(6px);
            border:1px solid rgba(255,255,255,.2); color:#fff;
            font-family:var(--pf-font-head); font-size:.68rem; font-weight:600;
            letter-spacing:.07em; text-transform:uppercase;
            padding:.3rem .85rem; border-radius:100px;
            display:inline-block; margin-bottom:.75rem;
        }
        .pf-header h4 {
            font-family:var(--pf-font-head);
            font-size:1.3rem; font-weight:700; color:#fff; margin-bottom:.25rem; line-height:1.3;
        }
        .pf-header p { color:rgba(255,255,255,.7); font-size:.86rem; margin:0; }
        .pf-header-icon {
            width:46px; height:46px; background:rgba(255,255,255,.12); border-radius:12px;
            display:flex; align-items:center; justify-content:center; font-size:1.2rem; color:#fff; flex-shrink:0;
        }
        .pf-body { padding:1.75rem 2.25rem 2.25rem; }

        /* pf-section blocks */
        .pf-section {
            border:1px solid var(--pf-border); border-radius:var(--pf-radius);
            overflow:hidden; margin-bottom:1.5rem;
            background:var(--pf-surface); transition:box-shadow .2s;
        }
        .pf-section:focus-within {
            box-shadow:0 0 0 3px var(--pf-primary-glow); border-color:rgba(26,86,219,.3);
        }
        .pf-section-head {
            background:var(--pf-primary-light); padding:.8rem 1.4rem;
            display:flex; align-items:center; gap:.65rem;
            border-bottom:1px solid rgba(26,86,219,.1);
        }
        .pf-section-head .icon-wrap {
            width:28px; height:28px; background:var(--pf-primary); border-radius:7px;
            display:flex; align-items:center; justify-content:center;
            color:#fff; font-size:.72rem; flex-shrink:0;
        }
        .pf-section-head h6 {
            font-family:var(--pf-font-head); font-size:.87rem; font-weight:700;
            color:var(--pf-primary-dark); margin:0;
        }
        .pf-section-body { padding:1.35rem; }

        /* Controls */
        .pf-label {
            font-family:var(--pf-font-head); font-size:.78rem; font-weight:600;
            color:var(--pf-text); letter-spacing:.01em; margin-bottom:.35rem; display:block;
        }
        .pf-req { color:var(--pf-danger); margin-left:2px; }
        .pf-opt {
            font-size:.66rem; background:#f1f5f9; color:var(--pf-muted);
            border-radius:4px; padding:1px 5px; font-weight:500; margin-left:5px; vertical-align:middle;
        }
        .pf-icon-wrap { position:relative; }
        .pf-icon-wrap .pf-icon {
            position:absolute; left:.85rem; top:50%; transform:translateY(-50%);
            color:var(--pf-light); font-size:.8rem; pointer-events:none;
        }
        .pf-icon-wrap .pf-control { padding-left:2.3rem; }
        .pf-control {
            width:100%; border:1.5px solid var(--pf-border); border-radius:var(--pf-radius-sm);
            padding:.62rem 1rem; font-size:.87rem; font-family:var(--pf-font-body);
            color:var(--pf-text); background:var(--pf-surface);
            transition:border-color .2s,box-shadow .2s,background .2s;
            box-shadow:var(--pf-shadow-sm); outline:none; -webkit-appearance:none; appearance:none;
        }
        .pf-control::placeholder { color:var(--pf-light); }
        .pf-control:focus {
            border-color:var(--pf-primary); box-shadow:0 0 0 3.5px var(--pf-primary-glow); background:#fafcff;
        }
        textarea.pf-control { resize:vertical; min-height:80px; }
        .pf-hint { font-size:.74rem; color:var(--pf-muted); margin-top:.28rem; }
        .pf-hint.danger { color:var(--pf-danger) !important; }

        /* ─── Right-side sticky panel ─── */
        .summary-card {
            background:var(--pf-surface);
            border:1.5px solid var(--pf-border);
            border-radius:var(--pf-radius);
            box-shadow:var(--pf-shadow-md);
            overflow:hidden;
        }
        .summary-card-head {
            background:linear-gradient(135deg,#1a56db 0%,#0e3fa3 100%);
            padding:1.1rem 1.4rem;
        }
        .summary-card-head h5 {
            font-family:var(--pf-font-head); font-weight:700; color:#fff; margin:0; font-size:.95rem;
        }

        /* Driver count inside summary */
        .driver-block {
            padding:1.1rem 1.4rem;
            background:var(--pf-surface-2);
            border-bottom:1.5px solid var(--pf-border);
        }
        .driver-block .pf-label { margin-bottom:.4rem; }

        /* Price rows */
        .summary-body { padding:1.2rem 1.4rem 0; }
        .summary-row {
            display:flex; justify-content:space-between; align-items:center;
            padding:.48rem 0; border-bottom:1px solid var(--pf-border);
            font-family:var(--pf-font-body); font-size:.82rem; color:var(--pf-muted);
        }
        .summary-row:last-of-type { border-bottom:none; }
        .summary-row .val { font-weight:700; color:var(--pf-text); }
        .summary-total {
            background:var(--pf-primary-light);
            border-radius:var(--pf-radius-sm);
            padding:.95rem 1.1rem;
            display:flex; justify-content:space-between; align-items:center;
            margin:1rem 1.4rem;
        }
        .summary-total .label {
            font-family:var(--pf-font-head); font-weight:700; font-size:.88rem; color:var(--pf-primary-dark);
        }
        .summary-total .amount {
            font-family:var(--pf-font-head); font-size:1.75rem; font-weight:800; color:var(--pf-primary);
        }
        .summary-actions { padding:0 1.4rem 1.4rem; }

        /* Submit button */
        .pf-btn-submit {
            background:linear-gradient(135deg,#1a56db 0%,#0e3fa3 100%);
            border:none; border-radius:12px; width:100%;
            font-family:var(--pf-font-head); font-weight:700; font-size:.95rem;
            color:#fff; padding:.9rem 2rem;
            transition:all .25s; box-shadow:0 4px 20px rgba(26,86,219,.35);
            display:flex; align-items:center; justify-content:center; gap:.55rem; cursor:pointer;
        }
        .pf-btn-submit:hover {
            transform:translateY(-2px); box-shadow:0 8px 30px rgba(26,86,219,.45); color:#fff;
        }
        .pf-btn-submit:active { transform:translateY(0); }
        .pf-btn-submit:disabled { opacity:.7; cursor:not-allowed; transform:none; }
        .pf-secure {
            font-size:.74rem; color:var(--pf-muted);
            display:flex; align-items:center; gap:.35rem; justify-content:center; margin-top:.65rem;
        }
        .pf-secure i { color:#059669; }

        /* Alert */
        .pf-alert {
            border-radius:var(--pf-radius-sm); border:none; font-size:.865rem;
            padding:.85rem 1rem; margin-bottom:1.1rem;
            display:flex; align-items:flex-start; gap:.65rem;
        }
        .pf-alert-danger {
            background:rgba(225,29,72,.05); color:#9f1239; border:1px solid rgba(225,29,72,.2);
        }

        /* Notes toggle */
        .notes-toggle-btn {
            background:none; border:none; padding:0;
            font-family:var(--pf-font-head); font-size:.78rem; font-weight:600;
            color:var(--pf-primary); cursor:pointer; display:flex; align-items:center; gap:.4rem;
            margin-top:.4rem;
        }
        .notes-toggle-btn:hover { text-decoration:underline; }

        /* Loader */
        #loader-overlay {
            position:fixed; inset:0; background:rgba(15,23,42,.7);
            backdrop-filter:blur(5px); z-index:9999;
            display:flex; align-items:center; justify-content:center;
            color:#fff; flex-direction:column;
            opacity:0; pointer-events:none; transition:opacity .3s ease;
        }
        #loader-overlay.show { opacity:1; pointer-events:auto; }
        .spinner-custom {
            width:58px; height:58px;
            border:5px solid rgba(255,255,255,.2); border-top:5px solid #fff;
            border-radius:50%; animation:spin 1s linear infinite; margin-bottom:18px;
        }
        @keyframes spin { to { transform:rotate(360deg); } }

        @media (max-width:768px) {
            .pf-header { padding:1.6rem 1.25rem 1.4rem; }
            .pf-body    { padding:1.25rem 1rem 1.75rem; }
        }
    </style>

    {{-- ═══════════════════════════════════════════
         PAGE TITLE
    ══════════════════════════════════════════════ --}}
    <section class="my-5">
        <div class="container pt-5">
            <div class="row mt-4">
                <div class="col-12">
                    <h2 class="text-center">{{ $random_consortium->title }}</h2>
                    <p class="text-center">@php echo html_entity_decode($random_consortium->description); @endphp</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         STEP 1 — PLAN SELECTION CARDS
    ══════════════════════════════════════════════ --}}
    <section class="plan-section">
        <div class="container">
            <div class="text-center">
                <p class="section-eyebrow">Step 1</p>
                <h2>Choose Your Consortium Plan</h2>
                <p class="sub">Select the plan that matches your fleet size. All fees are live from our pricing system and fully itemized.</p>
            </div>

            <div class="row g-4 justify-content-center">
                @php
                    $colors = [
                        'owner-operator' => '#1a56db',
                        'small-fleet' => '#059669',
                        'medium-fleet' => '#f59e0b',
                        'large-fleet' => '#8b5cf6',
                        'enterprise-fleet' => '#64748b'
                    ];
                    $icons = [
                        'owner-operator' => 'fa-user',
                        'small-fleet' => 'fa-users',
                        'medium-fleet' => 'fa-truck',
                        'large-fleet' => 'fa-shield-alt',
                        'enterprise-fleet' => 'fa-building'
                    ];
                @endphp

                @foreach ($plans as $plan)
                    @php
                        $color = $colors[$plan->slug] ?? '#1a56db';
                        $icon = $icons[$plan->slug] ?? 'fa-money-bill-wave';
                    @endphp
                    <div class="col-md-6 col-lg">
                        <div class="plan-card card {{ $loop->first ? 'active' : '' }}" style="border-top-color: {{ $color }} !important;"
                             onclick="selectPlan(this, '{{ $plan->name }}', {{ $plan->min_drivers ?? 1 }}, {{ $plan->max_drivers ?? 9999 }})">
                            <div class="card-body text-center">
                                <div class="plan-icon-wrap mx-auto" style="background: {{ $color }};">
                                    <i class="fas {{ $icon }}"></i>
                                </div>
                                <h4>{{ $plan->name }}</h4>
                                <p class="range">
                                    <i class="fas {{ $plan->slug == 'owner-operator' ? 'fa-user-check' : 'fa-users' }} me-1"></i>
                                    @if ($plan->min_drivers === $plan->max_drivers)
                                        {{ $plan->min_drivers }} driver (fixed)
                                    @elseif ($plan->max_drivers === null)
                                        {{ $plan->min_drivers }}+ drivers
                                    @else
                                        {{ $plan->min_drivers }} – {{ $plan->max_drivers }} drivers
                                    @endif
                                </p>
                                <hr>
                                <ul class="plan-fee-list text-start">
                                    @foreach ($plan->fees as $fee)
                                        <li>
                                            <span class="fee-label">
                                                <i class="fas {{ $fee->fee_key == 'annual_enrollment_fee' ? 'fa-calendar-alt' : ($fee->fee_key == 'clearinghouse_maintenance_fee' ? 'fa-database' : ($fee->fee_key == 'fmcsa_queries_fee' ? 'fa-search' : 'fa-id-card')) }}"></i>
                                                {{ $fee->fee_label }}
                                            </span>
                                            <span class="fee-val">
                                                ${{ number_format($fee->fee_amount_in_dollars, 2) }}
                                                @if ($fee->fee_type == 'per_driver')
                                                    <small class="text-muted fw-normal">/driver</small>
                                                @endif
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         STEP 2 + 3 — FORM (left) + STICKY PANEL (right)
         KEY UX FIX: Driver count is in the RIGHT panel
         so user sees live total update without scrolling
    ══════════════════════════════════════════════ --}}
    <section id="application-form">
        <div class="container">
            <div class="row g-4 align-items-start">

                {{-- ── LEFT: Enrollment Form ── --}}
                <div class="col-lg-8">
                    <div class="pf-card">

                        <div class="pf-header">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <span class="pill">Step 2</span>
                                    <h4>Enrollment Details</h4>
                                    <p>Complete your company &amp; Designated Employer Representative (DER) information.</p>
                                </div>
                                <div class="pf-header-icon d-none d-sm-flex">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                            </div>
                        </div>

                        <div class="pf-body pb-1">
                            <div id="form-errors" class="pf-alert pf-alert-danger d-none">
                                <i class="fas fa-exclamation-circle mt-1"></i>
                                <div id="form-errors-body"></div>
                            </div>
                        </div>

                        <div class="pf-body pt-1">
                            <form id="enrollment-form" method="POST">
                                @csrf
                                <input type="hidden" name="selected_plan" id="selected_plan" value="Owner Operator">

                                {{-- Company Details --}}
                                <div class="pf-section">
                                    <div class="pf-section-head">
                                        <div class="icon-wrap"><i class="fas fa-briefcase"></i></div>
                                        <h6>Company Details</h6>
                                    </div>
                                    <div class="pf-section-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="pf-label" for="company_name">Company Name <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-building pf-icon"></i>
                                                    <input type="text" id="company_name" name="company_name" class="pf-control" placeholder="e.g. Acme Trucking LLC" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="pf-label" for="dba_name">DBA Name <span class="pf-opt">Optional</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-tag pf-icon"></i>
                                                    <input type="text" id="dba_name" name="dba_name" class="pf-control" placeholder="Doing Business As">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="pf-label" for="dot_number">USDOT Number <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-id-badge pf-icon"></i>
                                                    <input type="text" id="dot_number" name="dot_number" class="pf-control" placeholder="e.g. 1234567" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="pf-label" for="mc_number">MC Number <span class="pf-opt">Optional</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-hashtag pf-icon"></i>
                                                    <input type="text" id="mc_number" name="mc_number" class="pf-control" placeholder="MC-XXXXXXX">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="pf-label" for="ein_number">EIN / Tax ID <span class="pf-opt">Optional</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-file-invoice pf-icon"></i>
                                                    <input type="text" id="ein_number" name="ein_number" class="pf-control" placeholder="XX-XXXXXXX">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- DER Contact --}}
                                <div class="pf-section">
                                    <div class="pf-section-head">
                                        <div class="icon-wrap"><i class="fas fa-user-tie"></i></div>
                                        <h6>Designated Employer Representative (DER)</h6>
                                    </div>
                                    <div class="pf-section-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="pf-label" for="first_name">First Name <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-user pf-icon"></i>
                                                    <input type="text" id="first_name" name="first_name" class="pf-control" placeholder="e.g. John" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="pf-label" for="last_name">Last Name <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-user pf-icon"></i>
                                                    <input type="text" id="last_name" name="last_name" class="pf-control" placeholder="e.g. Smith" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="pf-label" for="email">DER Email <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-envelope pf-icon"></i>
                                                    <input type="email" id="email" name="email" class="pf-control" placeholder="you@company.com" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="pf-label" for="phone">DER Phone <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-phone pf-icon"></i>
                                                    <input type="tel" id="phone" name="phone" class="pf-control" placeholder="(555) 000-0000" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Address --}}
                                <div class="pf-section mb-0">
                                    <div class="pf-section-head">
                                        <div class="icon-wrap"><i class="fas fa-map-marker-alt"></i></div>
                                        <h6>Company Address</h6>
                                    </div>
                                    <div class="pf-section-body">
                                        <div class="row g-3">
                                            <div class="col-md-8">
                                                <label class="pf-label" for="address_line_1">Address Line 1 <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-map-marker-alt pf-icon"></i>
                                                    <input type="text" id="address_line_1" name="address_line_1" class="pf-control" placeholder="123 Main Street" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="pf-label" for="address_line_2">Suite / Unit <span class="pf-opt">Optional</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-door-open pf-icon"></i>
                                                    <input type="text" id="address_line_2" name="address_line_2" class="pf-control" placeholder="Suite 100">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="pf-label" for="city">City <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-city pf-icon"></i>
                                                    <input type="text" id="city" name="city" class="pf-control" placeholder="e.g. Dallas" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="pf-label" for="state">State <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-flag pf-icon"></i>
                                                    <input type="text" id="state" name="state" class="pf-control" placeholder="e.g. TX" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="pf-label" for="zip_code">Zip Code <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-mail-bulk pf-icon"></i>
                                                    <input type="text" id="zip_code" name="zip_code" class="pf-control" placeholder="75001" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                {{-- ── RIGHT: Sticky Summary + Driver Count + Notes ── --}}
                <div class="col-lg-4">
                    <div class="sticky-top" style="top:90px; z-index:10;">

                        <p class="section-eyebrow mb-2">Step 3</p>

                        <div class="summary-card">
                            {{-- Header --}}
                            <div class="summary-card-head">
                                <h5><i class="fas fa-receipt me-2"></i>Order Summary</h5>
                            </div>

                            {{-- ✅ UX FIX: Driver count is HERE, right above the price breakdown --}}
                            <div class="driver-block" id="driver-block">
                                <label class="pf-label" for="driver_count">
                                    <i class="fas fa-users me-1 text-primary"></i>Number of Drivers <span class="pf-req">*</span>
                                </label>
                                <div class="pf-icon-wrap">
                                    <i class="fas fa-users pf-icon"></i>
                                    <input type="number" id="driver_count" name="driver_count_display"
                                           class="pf-control" value="1" min="1" max="1" required oninput="calculateTotal()">
                                </div>
                                {{-- hidden field synced to form --}}
                                <input type="hidden" id="driver_count_hidden" form="enrollment-form" name="driver_count" value="1">
                                <p class="pf-hint danger" id="driver_count_help">Owner Operator plan is fixed to exactly 1 driver.</p>

                                {{-- Optional notes toggle --}}
                                <button type="button" class="notes-toggle-btn mt-2" onclick="toggleNotes()">
                                    <i class="fas fa-plus-circle" id="notes-toggle-icon"></i> Add Notes / Comments
                                </button>
                                <div id="notes-area" class="mt-2 d-none">
                                    <textarea id="notes" name="notes" form="enrollment-form"
                                              class="pf-control" style="min-height:70px;"
                                              placeholder="Any additional enrollment information…"></textarea>
                                </div>
                            </div>

                            {{-- Online price breakdown --}}
                            <div id="calculator-online">
                                <div class="summary-body">
                                    <p style="font-family:var(--pf-font-head);font-weight:700;font-size:.9rem;color:var(--pf-text);margin-bottom:.75rem;" id="summary_plan_name">Owner Operator</p>
                                    <div id="itemized-fees-container">
                                        <!-- Generated dynamically by JS -->
                                    </div>
                                </div>

                                <div class="summary-total">
                                    <span class="label">Total Due</span>
                                    <span class="amount" id="summary_total">$0.00</span>
                                </div>

                                <div class="summary-actions">
                                    <button type="button" class="pf-btn-submit" onclick="submitForm()">
                                        <i class="fas fa-lock"></i> Proceed to Checkout
                                    </button>
                                    <p class="pf-secure">
                                        <i class="fas fa-shield-alt"></i> Secure Stripe Payment — 256-bit SSL
                                    </p>
                                </div>
                            </div>

                            {{-- Enterprise placeholder --}}
                            <div id="calculator-enterprise" class="d-none">
                                <div class="text-center p-4">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary text-white mb-3"
                                         style="width:52px;height:52px;font-size:1.3rem;">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <h6 style="font-family:var(--pf-font-head);font-weight:700;color:var(--pf-text);">Enterprise Pricing</h6>
                                    <p class="text-muted small mb-4">For 100+ driver fleets we offer optimized bulk rates with dedicated support.</p>
                                    <a href="mailto:{{ App\Models\Admin\ContactInfoWidget::pluck('email')->first() ?? 'support@mydrugcheck.com' }}?subject=Enterprise Consortium Pricing"
                                       class="pf-btn-submit text-decoration-none">
                                        <i class="fas fa-envelope"></i> Contact Our Team
                                    </a>
                                    <p class="pf-secure mt-3"><i class="fas fa-headset"></i> Get a custom volume proposal</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- Loader --}}
    <div id="loader-overlay">
        <div class="spinner-custom"></div>
        <h4 class="fw-bold" style="font-family:var(--pf-font-head);">Generating Secure Stripe Checkout…</h4>
        <p style="color:rgba(255,255,255,.7);">Please wait, redirecting shortly.</p>
    </div>

    <script>
        const PLANS = {!! $pricingJson !!};
        let activeMinDrivers = 1;
        let activeMaxDrivers = 1;

        function selectPlan(cardEl, planName, minDrivers, maxDrivers) {
            document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('active'));
            if (cardEl) {
                cardEl.classList.add('active');
            }

            document.getElementById('selected_plan').value = planName;
            document.getElementById('summary_plan_name').innerText = planName;

            activeMinDrivers = minDrivers;
            activeMaxDrivers = maxDrivers;

            const countInput  = document.getElementById('driver_count');
            const hiddenInput = document.getElementById('driver_count_hidden');
            const helpText    = document.getElementById('driver_count_help');
            const driverBlock = document.getElementById('driver-block');

            const plan = PLANS.find(p => p.name === planName);

            if (planName === 'Enterprise Fleet' || (plan && plan.max_drivers === null && plan.min_drivers >= 101)) {
                countInput.value    = 100;
                countInput.readOnly = true;
                hiddenInput.value   = 100;
                helpText.innerText  = 'Contact our team for Enterprise accounts.';
                helpText.className  = 'pf-hint';
                driverBlock.classList.add('d-none');
                showEnterpriseCalc();
            } else if (plan && plan.min_drivers === 1 && plan.max_drivers === 1) {
                countInput.value    = 1;
                countInput.readOnly = true;
                countInput.min      = 1;
                countInput.max      = 1;
                hiddenInput.value   = 1;
                helpText.innerText  = 'Plan is fixed to exactly 1 driver.';
                helpText.className  = 'pf-hint danger';
                driverBlock.classList.remove('d-none');
                showOnlineCalc();
            } else {
                countInput.readOnly = false;
                countInput.min      = minDrivers;
                countInput.max      = maxDrivers;
                if (parseInt(countInput.value) < minDrivers || parseInt(countInput.value) > maxDrivers || isNaN(parseInt(countInput.value))) {
                    countInput.value = minDrivers;
                }
                hiddenInput.value  = countInput.value;
                helpText.innerText = `Enter between ${minDrivers} and ${maxDrivers} drivers.`;
                helpText.className = 'pf-hint';
                driverBlock.classList.remove('d-none');
                showOnlineCalc();
            }

            calculateTotal();
        }

        function showOnlineCalc() {
            document.getElementById('calculator-online').classList.remove('d-none');
            document.getElementById('calculator-enterprise').classList.add('d-none');
        }
        function showEnterpriseCalc() {
            document.getElementById('calculator-online').classList.add('d-none');
            document.getElementById('calculator-enterprise').classList.remove('d-none');
        }

        function calculateTotal() {
            const planName = document.getElementById('selected_plan').value;
            const plan = PLANS.find(p => p.name === planName);
            if (!plan) return;

            let drivers = parseInt(document.getElementById('driver_count').value) || 0;
            if (drivers < activeMinDrivers) drivers = activeMinDrivers;
            if (drivers > activeMaxDrivers) drivers = activeMaxDrivers;

            // Sync hidden form field
            document.getElementById('driver_count_hidden').value = drivers;

            let grandTotal = 0;
            let html = '';

            plan.fees.forEach(fee => {
                let amount = fee.fee_amount;
                let lineTotal = 0;
                let multiplierText = '';

                if (fee.fee_type === 'per_driver') {
                    lineTotal = amount * drivers;
                    multiplierText = ` (${drivers}×)`;
                } else {
                    lineTotal = amount;
                }

                grandTotal += lineTotal;

                html += `
                    <div class="summary-row">
                        <span>${fee.fee_label}${multiplierText}</span>
                        <span class="val">$${lineTotal.toFixed(2)}</span>
                    </div>
                `;
            });

            document.getElementById('itemized-fees-container').innerHTML = html;
            document.getElementById('summary_total').innerText = `$${grandTotal.toFixed(2)}`;
        }

        function toggleNotes() {
            const area = document.getElementById('notes-area');
            const icon = document.getElementById('notes-toggle-icon');
            const isHidden = area.classList.toggle('d-none');
            icon.className = isHidden ? 'fas fa-plus-circle' : 'fas fa-minus-circle';
        }

        function submitForm() {
            const errBox  = document.getElementById('form-errors');
            const errBody = document.getElementById('form-errors-body');
            errBox.classList.add('d-none');
            errBody.innerHTML = '';

            const form = document.getElementById('enrollment-form');
            if (!form.reportValidity()) return;

            const formData = new FormData(form);
            // Ensure driver_count from the visible input is in the payload
            formData.set('driver_count', document.getElementById('driver_count_hidden').value);

            document.getElementById('loader-overlay').classList.add('show');

            fetch('{{ route("frontend.random-consortium.enroll") }}', {
                method:  'POST',
                body:    formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success && data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    document.getElementById('loader-overlay').classList.remove('show');
                    errBox.classList.remove('d-none');
                    if (data.errors && data.errors.length) {
                        data.errors.forEach(e => {
                            errBody.innerHTML += `<div><i class="fas fa-exclamation-circle me-2"></i>${e}</div>`;
                        });
                    } else {
                        errBody.innerHTML = 'An unexpected error occurred. Please try again.';
                    }
                    errBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            })
            .catch(() => {
                document.getElementById('loader-overlay').classList.remove('show');
                errBox.classList.remove('d-none');
                errBody.innerHTML = 'Unable to connect to the server. Please check your internet and try again.';
                errBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        }

        // Init on load
        document.addEventListener('DOMContentLoaded', () => {
            if (PLANS.length > 0) {
                const firstPlan = PLANS[0];
                const firstCard = document.querySelector('.plan-card');
                selectPlan(firstCard, firstPlan.name, firstPlan.min_drivers || 1, firstPlan.max_drivers || 9999);
            } else {
                calculateTotal();
            }
        });
    </script>

@endsection
