@extends('layouts.frontend.master2')

@section('content')

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
