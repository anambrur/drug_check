@extends('layouts.frontend.master2')

@section('content')

    {{-- Scroll progress bar --}}
    <div class="rc-scroll-progress" id="rc-scroll-progress" aria-hidden="true"><span></span></div>

    {{-- ═══════════════════════════════════════════
         HERO + PROGRESS STEPPER
    ══════════════════════════════════════════════ --}}
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
                    <i class="fas fa-shield-alt" aria-hidden="true"></i>
                    Consortium Enrollment
                </span>
                <h1 class="rc-hero-title rc-hero-item rc-hero-item--2">{{ $random_consortium->title }}</h1>
                <div class="rc-hero-desc rc-hero-item rc-hero-item--3">@php echo html_entity_decode($random_consortium->description); @endphp</div>
            </div>

            {{-- Visual step indicator (no logic change) --}}
            <nav class="rc-stepper rc-hero-item rc-hero-item--4" aria-label="Enrollment steps" id="rc-stepper">
                <div class="rc-step rc-step--active rc-step--current" data-step="1">
                    <span class="rc-step-num">1</span>
                    <span class="rc-step-label">Choose Plan</span>
                </div>
                <div class="rc-step-line" aria-hidden="true"><span class="rc-step-line-fill" data-line="1"></span></div>
                <div class="rc-step" data-step="2">
                    <span class="rc-step-num">2</span>
                    <span class="rc-step-label">Your Details</span>
                </div>
                <div class="rc-step-line" aria-hidden="true"><span class="rc-step-line-fill" data-line="2"></span></div>
                <div class="rc-step" data-step="3">
                    <span class="rc-step-num">3</span>
                    <span class="rc-step-label">Checkout</span>
                </div>
            </nav>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         STEP 1 — PLAN SELECTION CARDS
    ══════════════════════════════════════════════ --}}
    <section class="plan-section" id="rc-step-plans">
        <div class="container">
            <div class="rc-section-head text-center rc-animate">
                <p class="section-eyebrow">Step 1</p>
                <h2>Choose Your Consortium Plan</h2>
                <p class="sub">Select the plan that matches your fleet size. All fees are live from our pricing system and fully itemized.</p>
            </div>

            <div class="row g-4 justify-content-center rc-plans-row">
                @php
                    $siteMainColor = (isset($color_option) && $color_option->color_option != 0)
                        ? $color_option->main_color
                        : '#ff4500';
                    $colors = [
                        'owner-operator' => $siteMainColor,
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
                    <div class="col-md-6 col-lg rc-animate" style="--rc-delay: {{ $loop->index * 0.08 }}s;">
                        <div class="plan-card card rc-tilt-card {{ $loop->first ? 'active' : '' }}"
                             style="--plan-accent: {{ $color }}; border-top-color: {{ $color }} !important;"
                             role="button"
                             tabindex="0"
                             aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                             onclick="selectPlan(this, '{{ $plan->name }}', {{ $plan->min_drivers ?? 1 }}, {{ $plan->max_drivers ?? 9999 }}, event)"
                             onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();selectPlan(this,'{{ $plan->name }}',{{ $plan->min_drivers ?? 1 }},{{ $plan->max_drivers ?? 9999 }},event);}">
                            <span class="rc-ripple" aria-hidden="true"></span>
                            <div class="plan-card-glow" aria-hidden="true"></div>
                            <div class="card-body text-center">
                                <div class="plan-icon-wrap mx-auto" style="background: {{ $color }};">
                                    <i class="fas {{ $icon }}" aria-hidden="true"></i>
                                </div>
                                <h4>{{ $plan->name }}</h4>
                                <p class="range">
                                    <i class="fas {{ $plan->slug == 'owner-operator' ? 'fa-user-check' : 'fa-users' }} me-1" aria-hidden="true"></i>
                                    @if ($plan->min_drivers === $plan->max_drivers)
                                        {{ $plan->min_drivers }} driver (fixed)
                                    @elseif ($plan->max_drivers === null)
                                        {{ $plan->min_drivers }}+ drivers
                                    @else
                                        {{ $plan->min_drivers }} – {{ $plan->max_drivers }} drivers
                                    @endif
                                </p>
                                <div class="plan-divider" aria-hidden="true"></div>
                                <ul class="plan-fee-list text-start">
                                    @foreach ($plan->fees as $fee)
                                        <li>
                                            <span class="fee-label">
                                                <i class="fas {{ $fee->fee_key == 'annual_enrollment_fee' ? 'fa-calendar-alt' : ($fee->fee_key == 'clearinghouse_maintenance_fee' ? 'fa-database' : ($fee->fee_key == 'fmcsa_queries_fee' ? 'fa-search' : 'fa-id-card')) }}" aria-hidden="true"></i>
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
    ══════════════════════════════════════════════ --}}
    <section id="application-form">
        <div class="container">
            <div class="row g-4 align-items-start">

                {{-- ── LEFT: Enrollment Form ── --}}
                <div class="col-lg-8 rc-animate">
                    <div class="pf-card">
                        <div class="pf-header">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <span class="pill">Step 2</span>
                                    <h4>Enrollment Details</h4>
                                    <p>Complete your company &amp; Designated Employer Representative (DER) information.</p>
                                </div>
                                <div class="pf-header-icon d-none d-sm-flex" aria-hidden="true">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                            </div>
                        </div>

                        <div class="pf-body pb-1">
                            <div id="form-errors" class="pf-alert pf-alert-danger d-none" role="alert">
                                <i class="fas fa-exclamation-circle mt-1" aria-hidden="true"></i>
                                <div id="form-errors-body"></div>
                            </div>
                        </div>

                        <div class="pf-body pt-1">
                            <form id="enrollment-form" method="POST">
                                @csrf
                                <input type="hidden" name="selected_plan" id="selected_plan" value="Owner Operator">

                                {{-- Company Details --}}
                                <div class="pf-section rc-animate" style="--rc-delay: .05s;">
                                    <div class="pf-section-head">
                                        <div class="icon-wrap"><i class="fas fa-briefcase" aria-hidden="true"></i></div>
                                        <h6>Company Details</h6>
                                    </div>
                                    <div class="pf-section-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="pf-label" for="company_name">Company Name <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-building pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="company_name" name="company_name" class="pf-control" placeholder="e.g. Acme Trucking LLC" required autocomplete="organization">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="pf-label" for="dba_name">DBA Name <span class="pf-opt">Optional</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-tag pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="dba_name" name="dba_name" class="pf-control" placeholder="Doing Business As">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="pf-label" for="dot_number">USDOT Number <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-id-badge pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="dot_number" name="dot_number" class="pf-control" placeholder="e.g. 1234567" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="pf-label" for="mc_number">MC Number <span class="pf-opt">Optional</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-hashtag pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="mc_number" name="mc_number" class="pf-control" placeholder="MC-XXXXXXX">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="pf-label" for="ein_number">EIN / Tax ID <span class="pf-opt">Optional</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-file-invoice pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="ein_number" name="ein_number" class="pf-control" placeholder="XX-XXXXXXX">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- DER Contact --}}
                                <div class="pf-section rc-animate" style="--rc-delay: .12s;">
                                    <div class="pf-section-head">
                                        <div class="icon-wrap"><i class="fas fa-user-tie" aria-hidden="true"></i></div>
                                        <h6>Designated Employer Representative (DER)</h6>
                                    </div>
                                    <div class="pf-section-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="pf-label" for="first_name">First Name <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-user pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="first_name" name="first_name" class="pf-control" placeholder="e.g. John" required autocomplete="given-name">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="pf-label" for="last_name">Last Name <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-user pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="last_name" name="last_name" class="pf-control" placeholder="e.g. Smith" required autocomplete="family-name">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="pf-label" for="email">DER Email <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-envelope pf-icon" aria-hidden="true"></i>
                                                    <input type="email" id="email" name="email" class="pf-control" placeholder="you@company.com" required autocomplete="email">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="pf-label" for="phone">DER Phone <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-phone pf-icon" aria-hidden="true"></i>
                                                    <input type="tel" id="phone" name="phone" class="pf-control" placeholder="(555) 000-0000" required autocomplete="tel">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Address --}}
                                <div class="pf-section mb-0 rc-animate" style="--rc-delay: .19s;">
                                    <div class="pf-section-head">
                                        <div class="icon-wrap"><i class="fas fa-map-marker-alt" aria-hidden="true"></i></div>
                                        <h6>Company Address</h6>
                                    </div>
                                    <div class="pf-section-body">
                                        <div class="row g-3">
                                            <div class="col-md-8">
                                                <label class="pf-label" for="address_line_1">Address Line 1 <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-map-marker-alt pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="address_line_1" name="address_line_1" class="pf-control" placeholder="123 Main Street" required autocomplete="address-line1">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="pf-label" for="address_line_2">Suite / Unit <span class="pf-opt">Optional</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-door-open pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="address_line_2" name="address_line_2" class="pf-control" placeholder="Suite 100" autocomplete="address-line2">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="pf-label" for="city">City <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-city pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="city" name="city" class="pf-control" placeholder="e.g. Dallas" required autocomplete="address-level2">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="pf-label" for="state">State <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-flag pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="state" name="state" class="pf-control" placeholder="e.g. TX" required autocomplete="address-level1">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="pf-label" for="zip_code">Zip Code <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-mail-bulk pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="zip_code" name="zip_code" class="pf-control" placeholder="75001" required autocomplete="postal-code">
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
                <div class="col-lg-4 rc-animate rc-animate-delay-2">
                    <div class="rc-sticky-wrap sticky-top">
                        <p class="section-eyebrow mb-2">Step 3</p>

                        <div class="summary-card rc-summary-glow">
                            <div class="summary-card-head">
                                <h5><i class="fas fa-receipt me-2" aria-hidden="true"></i>Order Summary</h5>
                            </div>

                            <div class="driver-block" id="driver-block">
                                <label class="pf-label" for="driver_count">
                                    <i class="fas fa-users me-1 text-primary" aria-hidden="true"></i>Number of Drivers <span class="pf-req">*</span>
                                </label>
                                <div class="pf-icon-wrap rc-driver-input-wrap">
                                    <i class="fas fa-users pf-icon" aria-hidden="true"></i>
                                    <input type="number" id="driver_count" name="driver_count_display"
                                           class="pf-control" value="1" min="1" max="1" required
                                           oninput="enforceDriverCount()" onblur="enforceDriverCount(true)">
                                </div>
                                <input type="hidden" id="driver_count_hidden" form="enrollment-form" name="driver_count" value="1">
                                <p class="pf-hint danger" id="driver_count_help">Owner Operator plan is fixed to exactly 1 driver.</p>

                                <button type="button" class="notes-toggle-btn mt-2" onclick="toggleNotes()" aria-expanded="false" aria-controls="notes-area">
                                    <i class="fas fa-plus-circle" id="notes-toggle-icon" aria-hidden="true"></i> Add Notes / Comments
                                </button>
                                <div id="notes-area" class="rc-notes-collapse mt-2">
                                    <textarea id="notes" name="notes" form="enrollment-form"
                                              class="pf-control" style="min-height:70px;"
                                              placeholder="Any additional enrollment information…"></textarea>
                                </div>
                            </div>

                            <div id="calculator-online" class="rc-calc-panel">
                                <div class="summary-body">
                                    <p class="summary-plan-name" id="summary_plan_name">Owner Operator</p>
                                    <div id="itemized-fees-container">
                                        <!-- Generated dynamically by JS -->
                                    </div>
                                </div>

                                <div class="summary-total">
                                    <span class="label">Total Due</span>
                                    <span class="amount" id="summary_total">$0.00</span>
                                </div>

                                <div class="summary-actions">
                                    <button type="button" class="pf-btn-submit rc-btn-magnetic" onclick="submitForm()">
                                        <span class="pf-btn-shimmer" aria-hidden="true"></span>
                                        <i class="fas fa-lock" aria-hidden="true"></i> Proceed to Checkout
                                    </button>
                                    <div class="rc-trust-badges">
                                        <span><i class="fas fa-shield-alt" aria-hidden="true"></i> SSL Secured</span>
                                        <span><i class="fab fa-stripe" aria-hidden="true"></i> Stripe</span>
                                    </div>
                                    <p class="pf-secure">
                                        <i class="fas fa-shield-alt" aria-hidden="true"></i> Secure Stripe Payment — 256-bit SSL
                                    </p>
                                </div>
                            </div>

                            <div id="calculator-enterprise" class="rc-calc-panel d-none">
                                <div class="rc-enterprise-block text-center p-4">
                                    <div class="rc-enterprise-icon">
                                        <i class="fas fa-building" aria-hidden="true"></i>
                                    </div>
                                    <h6>Enterprise Pricing</h6>
                                    <p class="text-muted small mb-4">For 100+ driver fleets we offer optimized bulk rates with dedicated support.</p>
                                    <a href="mailto:{{ App\Models\Admin\ContactInfoWidget::pluck('email')->first() ?? 'support@mydrugcheck.com' }}?subject=Enterprise Consortium Pricing"
                                       class="pf-btn-submit text-decoration-none">
                                        <i class="fas fa-envelope" aria-hidden="true"></i> Contact Our Team
                                    </a>
                                    <p class="pf-secure mt-3"><i class="fas fa-headset" aria-hidden="true"></i> Get a custom volume proposal</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- Loader --}}
    <div id="loader-overlay" role="dialog" aria-modal="true" aria-labelledby="loader-title" aria-hidden="true">
        <div class="rc-loader-card">
            <div class="rc-loader-spinner" aria-hidden="true">
                <div class="rc-loader-ring"></div>
                <div class="rc-loader-ring rc-loader-ring--inner"></div>
            </div>
            <h4 class="fw-bold" id="loader-title" style="font-family:var(--pf-font-head);">Generating Secure Stripe Checkout…</h4>
            <p class="rc-loader-sub">Please wait, redirecting shortly.</p>
            <div class="rc-loader-dots" aria-hidden="true">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>

    <script>
        const PLANS = {!! $pricingJson !!};
        let activeMinDrivers = 1;
        let activeMaxDrivers = 1;

        function selectPlan(cardEl, planName, minDrivers, maxDrivers, evt) {
            document.querySelectorAll('.plan-card').forEach(c => {
                c.classList.remove('active', 'rc-plan-pop');
                c.setAttribute('aria-pressed', 'false');
            });
            if (cardEl) {
                cardEl.classList.add('active', 'rc-plan-pop');
                cardEl.setAttribute('aria-pressed', 'true');
                spawnRipple(cardEl, evt || window.event);
            }

            const summaryName = document.getElementById('summary_plan_name');
            summaryName.classList.remove('rc-name-flip');
            void summaryName.offsetWidth;
            summaryName.classList.add('rc-name-flip');

            document.getElementById('selected_plan').value = planName;
            summaryName.innerText = planName;

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
            updateStepper(1);
        }

        function showOnlineCalc() {
            crossfadeCalc('calculator-online', 'calculator-enterprise');
        }
        function showEnterpriseCalc() {
            crossfadeCalc('calculator-enterprise', 'calculator-online');
        }

        function crossfadeCalc(showId, hideId) {
            const showEl = document.getElementById(showId);
            const hideEl = document.getElementById(hideId);
            if (!showEl || !hideEl) return;

            const hideVisible = !hideEl.classList.contains('d-none');
            const showHidden  = showEl.classList.contains('d-none');
            if (!hideVisible && !showHidden) return;

            if (hideVisible) {
                hideEl.classList.add('rc-calc-exit');
                setTimeout(() => {
                    hideEl.classList.add('d-none');
                    hideEl.classList.remove('rc-calc-exit');
                }, 280);
            }
            if (showHidden) {
                showEl.classList.remove('d-none');
                showEl.classList.remove('rc-calc-enter');
                void showEl.offsetWidth;
                showEl.classList.add('rc-calc-enter');
            }
        }

        // HTML min/max only constrain spinner arrows — typed values must be clamped in JS.
        function enforceDriverCount(onBlur) {
            const countInput = document.getElementById('driver_count');
            if (!countInput || countInput.readOnly) {
                calculateTotal();
                return;
            }

            const raw = countInput.value;
            if (raw === '' || raw === '-') {
                if (onBlur) {
                    countInput.value = activeMinDrivers;
                }
                calculateTotal();
                return;
            }

            let drivers = parseInt(raw, 10);
            if (isNaN(drivers)) {
                if (onBlur) {
                    countInput.value = activeMinDrivers;
                }
                calculateTotal();
                return;
            }

            // Cap over-max immediately while typing; under-min only on blur so users can clear/retype.
            if (drivers > activeMaxDrivers) {
                drivers = activeMaxDrivers;
                countInput.value = drivers;
            } else if (onBlur && drivers < activeMinDrivers) {
                drivers = activeMinDrivers;
                countInput.value = drivers;
            }

            calculateTotal();
        }

        function calculateTotal() {
            const planName = document.getElementById('selected_plan').value;
            const plan = PLANS.find(p => p.name === planName);
            if (!plan) return;

            const countInput = document.getElementById('driver_count');
            let drivers = parseInt(countInput.value, 10) || 0;
            if (drivers < activeMinDrivers) drivers = activeMinDrivers;
            if (drivers > activeMaxDrivers) drivers = activeMaxDrivers;

            document.getElementById('driver_count_hidden').value = drivers;

            let grandTotal = 0;
            let html = '';

            plan.fees.forEach((fee, idx) => {
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
                    <div class="summary-row" style="--row-delay:${idx * 0.05}s">
                        <span>${fee.fee_label}${multiplierText}</span>
                        <span class="val">$${lineTotal.toFixed(2)}</span>
                    </div>
                `;
            });

            document.getElementById('itemized-fees-container').innerHTML = html;
            const totalEl = document.getElementById('summary_total');
            totalEl.innerText = `$${grandTotal.toFixed(2)}`;
            totalEl.classList.remove('rc-total-pulse');
            void totalEl.offsetWidth;
            totalEl.classList.add('rc-total-pulse');

            const driverWrap = document.querySelector('.rc-driver-input-wrap');
            if (driverWrap) {
                driverWrap.classList.remove('rc-input-bump');
                void driverWrap.offsetWidth;
                driverWrap.classList.add('rc-input-bump');
            }
        }

        function toggleNotes() {
            const area = document.getElementById('notes-area');
            const icon = document.getElementById('notes-toggle-icon');
            const btn  = document.querySelector('.notes-toggle-btn');
            const isOpen = area.classList.toggle('rc-notes-open');
            icon.className = isOpen ? 'fas fa-minus-circle' : 'fas fa-plus-circle';
            if (btn) btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        }

        function spawnRipple(cardEl, evt) {
            if (!cardEl || !evt || typeof evt.clientX === 'undefined') return;
            const ripple = cardEl.querySelector('.rc-ripple');
            if (!ripple) return;
            const rect = cardEl.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = (evt.clientX - rect.left - size / 2) + 'px';
            ripple.style.top  = (evt.clientY - rect.top - size / 2) + 'px';
            ripple.classList.remove('rc-ripple-active');
            void ripple.offsetWidth;
            ripple.classList.add('rc-ripple-active');
        }

        function submitForm() {
            const errBox  = document.getElementById('form-errors');
            const errBody = document.getElementById('form-errors-body');
            errBox.classList.add('d-none');
            errBody.innerHTML = '';

            const form = document.getElementById('enrollment-form');
            if (!form.reportValidity()) return;

            const formData = new FormData(form);
            formData.set('driver_count', document.getElementById('driver_count_hidden').value);

            const loader = document.getElementById('loader-overlay');
            loader.classList.add('show');
            loader.setAttribute('aria-hidden', 'false');

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
                    loader.classList.remove('show');
                    loader.setAttribute('aria-hidden', 'true');
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
                loader.classList.remove('show');
                loader.setAttribute('aria-hidden', 'true');
                errBox.classList.remove('d-none');
                errBody.innerHTML = 'Unable to connect to the server. Please check your internet and try again.';
                errBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        }

        function updateStepper(activeStep) {
            document.querySelectorAll('.rc-step').forEach(step => {
                const n = parseInt(step.dataset.step, 10);
                step.classList.toggle('rc-step--active', n <= activeStep);
                step.classList.toggle('rc-step--current', n === activeStep);
            });
            document.querySelectorAll('.rc-step-line-fill').forEach(line => {
                const n = parseInt(line.dataset.line, 10);
                line.classList.toggle('rc-step-line-fill--done', n < activeStep);
            });
        }

        function initScrollProgress() {
            const bar = document.querySelector('#rc-scroll-progress span');
            if (!bar) return;
            window.addEventListener('scroll', () => {
                const doc = document.documentElement;
                const pct = (doc.scrollTop / (doc.scrollHeight - doc.clientHeight)) * 100;
                bar.style.width = Math.min(100, Math.max(0, pct)) + '%';
            }, { passive: true });
        }

        function initPlanTilt() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            if (window.matchMedia('(max-width: 991px)').matches) return;

            document.querySelectorAll('.rc-tilt-card').forEach(card => {
                card.addEventListener('mousemove', (e) => {
                    const rect = card.getBoundingClientRect();
                    const x = (e.clientX - rect.left) / rect.width - 0.5;
                    const y = (e.clientY - rect.top) / rect.height - 0.5;
                    card.style.transform = `perspective(800px) rotateY(${x * 8}deg) rotateX(${-y * 8}deg) translateY(-6px)`;
                });
                card.addEventListener('mouseleave', () => {
                    card.style.transform = '';
                });
            });
        }

        function initMagneticButton() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            const btn = document.querySelector('.rc-btn-magnetic');
            if (!btn) return;
            btn.addEventListener('mousemove', (e) => {
                const rect = btn.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                btn.style.transform = `translate(${x * 0.12}px, ${y * 0.18}px)`;
            });
            btn.addEventListener('mouseleave', () => { btn.style.transform = ''; });
        }

        function initInputAnimations() {
            document.querySelectorAll('#enrollment-form .pf-control').forEach(input => {
                input.addEventListener('blur', () => {
                    if (input.checkValidity() && input.value.trim() !== '') {
                        input.classList.add('rc-input-valid');
                    } else {
                        input.classList.remove('rc-input-valid');
                    }
                });
            });
        }

        function initStepperClicks() {
            document.querySelectorAll('.rc-step').forEach(step => {
                step.addEventListener('click', () => {
                    const n = parseInt(step.dataset.step, 10);
                    const targets = {
                        1: '#rc-step-plans',
                        2: '#application-form',
                        3: '.summary-card'
                    };
                    const el = document.querySelector(targets[n]);
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    updateStepper(n);
                });
            });
        }

        function initSummaryObserver() {
            const summary = document.querySelector('.summary-card');
            if (!summary) return;
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) updateStepper(3);
                });
            }, { threshold: 0.3 });
            observer.observe(summary);
        }

        function initScrollAnimations() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                document.querySelectorAll('.rc-animate').forEach(el => el.classList.add('rc-visible'));
                return;
            }
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('rc-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
            document.querySelectorAll('.rc-animate').forEach(el => observer.observe(el));
        }

        function initStepperScroll() {
            const formSection = document.getElementById('application-form');
            if (!formSection) return;
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) updateStepper(2);
                });
            }, { threshold: 0.15 });
            observer.observe(formSection);
        }

        document.addEventListener('DOMContentLoaded', () => {
            initScrollAnimations();
            initStepperScroll();
            initSummaryObserver();
            initScrollProgress();
            initPlanTilt();
            initMagneticButton();
            initInputAnimations();
            initStepperClicks();
            updateStepper(1);

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
