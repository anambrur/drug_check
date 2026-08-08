{{-- Clearing House C/TPA Enrollment (plans + form + sticky summary) --}}
<div id="ch-enroll">

    {{-- ═══════════════════════════════════════════
         PLAN SELECTION CARDS
    ══════════════════════════════════════════════ --}}
    <section class="plan-section ch-section" id="ch-step-plans">
        <div class="container">
            <div class="rc-section-head text-center rc-animate">
                <p class="section-eyebrow">C/TPA Clearinghouse Enrollment</p>
                <h2>Choose Your Clearing House Plan</h2>
                <p class="sub">Select the plan that matches your fleet size. All fees are live from our pricing system and fully itemized.</p>
            </div>

            <div class="pf-alert ch-alert-note rc-animate mb-4" role="note">
                <i class="fas fa-info-circle mt-1" aria-hidden="true"></i>
                <span>
                    Official FMCSA query plans ($1.25/query) must be purchased by the employer on
                    <a href="https://clearinghouse.fmcsa.dot.gov" target="_blank" rel="noopener noreferrer">clearinghouse.fmcsa.dot.gov</a>.
                    The fees below are Skyros C/TPA service fees only.
                </span>
            </div>

            @if ($plans->isEmpty())
                <div class="pf-alert ch-alert-note text-center rc-animate" role="status">
                    <i class="fas fa-exclamation-circle mt-1" aria-hidden="true"></i>
                    <span>Enrollment plans are not available at this time. Please check back soon or contact our team for assistance.</span>
                </div>
            @else
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
                            <div class="plan-card card rc-tilt-card ch-enroll-plan-card {{ $loop->first ? 'active' : '' }}"
                                 style="--plan-accent: {{ $color }}; border-top-color: {{ $color }} !important;"
                                 role="button"
                                 tabindex="0"
                                 aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                                 data-plan-name="{{ $plan->name }}"
                                 data-min-drivers="{{ $plan->min_drivers ?? 1 }}"
                                 data-max-drivers="{{ $plan->max_drivers ?? 9999 }}">
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
                                                    <i class="fas {{ $fee->fee_key == 'annual_enrollment_fee' ? 'fa-calendar-alt' : ($fee->fee_key == 'clearinghouse_maintenance_fee' ? 'fa-database' : ($fee->fee_key == 'query_admin_fee' ? 'fa-search' : 'fa-id-card')) }}" aria-hidden="true"></i>
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
            @endif
        </div>
    </section>

    @unless ($plans->isEmpty())
    {{-- ═══════════════════════════════════════════
         FORM (left) + STICKY PANEL (right)
    ══════════════════════════════════════════════ --}}
    <section class="ch-section ch-section--alt" id="ch-application-form">
        <div class="container">
            <div class="row g-4 align-items-start">

                {{-- ── LEFT: Enrollment Form ── --}}
                <div class="col-lg-8 rc-animate">
                    <div class="pf-card">
                        <div class="pf-header">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <span class="pill">Enrollment</span>
                                    <h4>C/TPA Enrollment Details</h4>
                                    <p>Complete your company &amp; Designated Employer Representative (DER) information.</p>
                                </div>
                                <div class="pf-header-icon d-none d-sm-flex" aria-hidden="true">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                            </div>
                        </div>

                        <div class="pf-body pb-1">
                            <div id="ch-form-errors" class="pf-alert pf-alert-danger d-none" role="alert">
                                <i class="fas fa-exclamation-circle mt-1" aria-hidden="true"></i>
                                <div id="ch-form-errors-body"></div>
                            </div>
                        </div>

                        <div class="pf-body pt-1">
                            <form id="ch-enrollment-form" method="POST">
                                @csrf
                                <input type="hidden" name="selected_plan" id="ch-selected_plan" value="{{ $plans->first()->name ?? '' }}">

                                {{-- Company Details --}}
                                <div class="pf-section rc-animate" style="--rc-delay: .05s;">
                                    <div class="pf-section-head">
                                        <div class="icon-wrap"><i class="fas fa-briefcase" aria-hidden="true"></i></div>
                                        <h6>Company Details</h6>
                                    </div>
                                    <div class="pf-section-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="pf-label" for="ch_company_name">Company Name <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-building pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="ch_company_name" name="company_name" class="pf-control" placeholder="e.g. Acme Trucking LLC" required autocomplete="organization">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="pf-label" for="ch_dba_name">DBA Name <span class="pf-opt">Optional</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-tag pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="ch_dba_name" name="dba_name" class="pf-control" placeholder="Doing Business As">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="pf-label" for="ch_dot_number">USDOT Number <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-id-badge pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="ch_dot_number" name="dot_number" class="pf-control" placeholder="e.g. 1234567" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="pf-label" for="ch_mc_number">MC Number <span class="pf-opt">Optional</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-hashtag pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="ch_mc_number" name="mc_number" class="pf-control" placeholder="MC-XXXXXXX">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="pf-label" for="ch_ein_number">EIN / Tax ID <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-file-invoice pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="ch_ein_number" name="ein_number" class="pf-control" placeholder="XX-XXXXXXX" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="pf-label" for="ch_company_phone">Company Phone <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-phone-alt pf-icon" aria-hidden="true"></i>
                                                    <input type="tel" id="ch_company_phone" name="company_phone" class="pf-control" placeholder="(555) 000-0000" required autocomplete="tel">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="pf-label" for="ch_is_owner_operator">Owner-Operator? <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-user-check pf-icon" aria-hidden="true"></i>
                                                    <select id="ch_is_owner_operator" name="is_owner_operator" class="pf-control" required>
                                                        <option value="" disabled selected>Select…</option>
                                                        <option value="1">Yes</option>
                                                        <option value="0">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="pf-label" for="ch_clearinghouse_registered">Already registered in FMCSA Clearinghouse? <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-database pf-icon" aria-hidden="true"></i>
                                                    <select id="ch_clearinghouse_registered" name="clearinghouse_registered" class="pf-control" required>
                                                        <option value="" disabled selected>Select…</option>
                                                        <option value="yes">Yes</option>
                                                        <option value="no">No</option>
                                                        <option value="unsure">Unsure</option>
                                                    </select>
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
                                                <label class="pf-label" for="ch_first_name">First Name <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-user pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="ch_first_name" name="first_name" class="pf-control" placeholder="e.g. John" required autocomplete="given-name">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="pf-label" for="ch_last_name">Last Name <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-user pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="ch_last_name" name="last_name" class="pf-control" placeholder="e.g. Smith" required autocomplete="family-name">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="pf-label" for="ch_job_title">Job Title <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-briefcase pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="ch_job_title" name="job_title" class="pf-control" placeholder="e.g. Safety Manager" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="pf-label" for="ch_email">DER Email <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-envelope pf-icon" aria-hidden="true"></i>
                                                    <input type="email" id="ch_email" name="email" class="pf-control" placeholder="you@company.com" required autocomplete="email">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="pf-label" for="ch_phone">DER Phone <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-phone pf-icon" aria-hidden="true"></i>
                                                    <input type="tel" id="ch_phone" name="phone" class="pf-control" placeholder="(555) 000-0000" required autocomplete="tel">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Address --}}
                                <div class="pf-section rc-animate" style="--rc-delay: .19s;">
                                    <div class="pf-section-head">
                                        <div class="icon-wrap"><i class="fas fa-map-marker-alt" aria-hidden="true"></i></div>
                                        <h6>Company Address</h6>
                                    </div>
                                    <div class="pf-section-body">
                                        <div class="row g-3">
                                            <div class="col-md-8">
                                                <label class="pf-label" for="ch_address_line_1">Address Line 1 <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-map-marker-alt pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="ch_address_line_1" name="address_line_1" class="pf-control" placeholder="123 Main Street" required autocomplete="address-line1">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="pf-label" for="ch_address_line_2">Suite / Unit <span class="pf-opt">Optional</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-door-open pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="ch_address_line_2" name="address_line_2" class="pf-control" placeholder="Suite 100" autocomplete="address-line2">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="pf-label" for="ch_city">City <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-city pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="ch_city" name="city" class="pf-control" placeholder="e.g. Dallas" required autocomplete="address-level2">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="pf-label" for="ch_state">State <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-flag pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="ch_state" name="state" class="pf-control" placeholder="e.g. TX" required autocomplete="address-level1">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="pf-label" for="ch_zip_code">Zip Code <span class="pf-req">*</span></label>
                                                <div class="pf-icon-wrap">
                                                    <i class="fas fa-mail-bulk pf-icon" aria-hidden="true"></i>
                                                    <input type="text" id="ch_zip_code" name="zip_code" class="pf-control" placeholder="75001" required autocomplete="postal-code">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Authorizations & Acknowledgements --}}
                                <div class="pf-section mb-0 rc-animate" style="--rc-delay: .26s;">
                                    <div class="pf-section-head">
                                        <div class="icon-wrap"><i class="fas fa-file-signature" aria-hidden="true"></i></div>
                                        <h6>C/TPA Authorizations &amp; Acknowledgements</h6>
                                    </div>
                                    <div class="pf-section-body">
                                        <p class="pf-hint mb-3">Select the authorities you will grant to Drugcheckr in the FMCSA Clearinghouse (at least one required).</p>

                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="authorize_conduct_queries" id="ch_authorize_conduct_queries" value="1" checked>
                                            <label class="form-check-label" for="ch_authorize_conduct_queries">
                                                Authorize <strong>Conduct Queries</strong>
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="authorize_report_violations" id="ch_authorize_report_violations" value="1" checked>
                                            <label class="form-check-label" for="ch_authorize_report_violations">
                                                Authorize <strong>Report Violations</strong>
                                            </label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="authorize_report_rtd" id="ch_authorize_report_rtd" value="1" checked>
                                            <label class="form-check-label" for="ch_authorize_report_rtd">
                                                Authorize <strong>Report Return-to-Duty (RTD)</strong>
                                            </label>
                                        </div>

                                        <hr class="my-3">

                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="acknowledge_designate_ctpa" id="ch_acknowledge_designate_ctpa" value="1" required>
                                            <label class="form-check-label" for="ch_acknowledge_designate_ctpa">
                                                I acknowledge I must designate <strong>Drugcheckr</strong> as my C/TPA in the FMCSA Clearinghouse. <span class="pf-req">*</span>
                                            </label>
                                        </div>
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" name="acknowledge_query_plan" id="ch_acknowledge_query_plan" value="1" required>
                                            <label class="form-check-label" for="ch_acknowledge_query_plan">
                                                I acknowledge I must purchase the official FMCSA query plan ($1.25/query) on clearinghouse.fmcsa.dot.gov. <span class="pf-req">*</span>
                                            </label>
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
                        <p class="section-eyebrow mb-2">Checkout</p>

                        <div class="summary-card rc-summary-glow" id="ch-summary-card">
                            <div class="summary-card-head">
                                <h5><i class="fas fa-receipt me-2" aria-hidden="true"></i>Order Summary</h5>
                            </div>

                            <div class="driver-block" id="ch-driver-block">
                                <label class="pf-label" for="ch_driver_count">
                                    <i class="fas fa-users me-1 text-primary" aria-hidden="true"></i>Number of Drivers <span class="pf-req">*</span>
                                </label>
                                <div class="pf-icon-wrap rc-driver-input-wrap" id="ch-driver-input-wrap">
                                    <i class="fas fa-users pf-icon" aria-hidden="true"></i>
                                    <input type="number" id="ch_driver_count" name="driver_count_display"
                                           class="pf-control" value="1" min="1" max="1" required>
                                </div>
                                <input type="hidden" id="ch_driver_count_hidden" form="ch-enrollment-form" name="driver_count" value="1">
                                <p class="pf-hint danger" id="ch_driver_count_help">Owner Operator plan is fixed to exactly 1 driver.</p>

                                <button type="button" class="notes-toggle-btn mt-2" id="ch-notes-toggle" aria-expanded="false" aria-controls="ch-notes-area">
                                    <i class="fas fa-plus-circle" id="ch-notes-toggle-icon" aria-hidden="true"></i> Add Notes / Comments
                                </button>
                                <div id="ch-notes-area" class="rc-notes-collapse mt-2">
                                    <textarea id="ch_notes" name="notes" form="ch-enrollment-form"
                                              class="pf-control" style="min-height:70px;"
                                              placeholder="Any additional enrollment information…"></textarea>
                                </div>
                            </div>

                            <div id="ch-calculator-online" class="rc-calc-panel">
                                <div class="summary-body">
                                    <p class="summary-plan-name" id="ch_summary_plan_name">{{ $plans->first()->name ?? '' }}</p>
                                    <div id="ch-itemized-fees-container">
                                        <!-- Generated dynamically by JS -->
                                    </div>
                                </div>

                                <div class="summary-total">
                                    <span class="label">Total Due</span>
                                    <span class="amount" id="ch_summary_total">$0.00</span>
                                </div>

                                <div class="summary-actions">
                                    <button type="button" class="pf-btn-submit rc-btn-magnetic" id="ch-submit-btn">
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

                            <div id="ch-calculator-enterprise" class="rc-calc-panel d-none">
                                <div class="rc-enterprise-block text-center p-4">
                                    <div class="rc-enterprise-icon">
                                        <i class="fas fa-building" aria-hidden="true"></i>
                                    </div>
                                    <h6>Enterprise Pricing</h6>
                                    <p class="text-muted small mb-4">For 100+ driver fleets we offer optimized bulk rates with dedicated support.</p>
                                    <a href="mailto:{{ App\Models\Admin\ContactInfoWidget::pluck('email')->first() ?? 'support@mydrugcheck.com' }}?subject=Enterprise Clearing House Pricing"
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
    <div id="ch-loader-overlay" role="dialog" aria-modal="true" aria-labelledby="ch-loader-title" aria-hidden="true">
        <div class="rc-loader-card">
            <div class="rc-loader-spinner" aria-hidden="true">
                <div class="rc-loader-ring"></div>
                <div class="rc-loader-ring rc-loader-ring--inner"></div>
            </div>
            <h4 class="fw-bold" id="ch-loader-title" style="font-family:var(--pf-font-head);">Generating Secure Stripe Checkout…</h4>
            <p class="rc-loader-sub">Please wait, redirecting shortly.</p>
            <div class="rc-loader-dots" aria-hidden="true">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>

    <style>
        #ch-loader-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .75);
            backdrop-filter: blur(8px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity .35s ease;
        }
        #ch-loader-overlay.show {
            opacity: 1;
            pointer-events: auto;
        }
        #ch-enroll select.pf-control {
            appearance: auto;
            padding-left: 2.75rem;
            cursor: pointer;
        }
    </style>

    <script>
    (function () {
        const PLANS = {!! $pricingJson !!};
        let activeMinDrivers = 1;
        let activeMaxDrivers = 1;
        const root = document.getElementById('ch-enroll');
        if (!root) return;

        function $(id) { return document.getElementById(id); }

        function selectPlan(cardEl, planName, minDrivers, maxDrivers, evt) {
            root.querySelectorAll('.ch-enroll-plan-card').forEach(c => {
                c.classList.remove('active', 'rc-plan-pop');
                c.setAttribute('aria-pressed', 'false');
            });
            if (cardEl) {
                cardEl.classList.add('active', 'rc-plan-pop');
                cardEl.setAttribute('aria-pressed', 'true');
                spawnRipple(cardEl, evt || window.event);
            }

            const summaryName = $('ch_summary_plan_name');
            if (summaryName) {
                summaryName.classList.remove('rc-name-flip');
                void summaryName.offsetWidth;
                summaryName.classList.add('rc-name-flip');
                summaryName.innerText = planName;
            }

            $('ch-selected_plan').value = planName;

            activeMinDrivers = minDrivers;
            activeMaxDrivers = maxDrivers === null || maxDrivers === undefined ? 9999 : maxDrivers;

            const countInput  = $('ch_driver_count');
            const hiddenInput = $('ch_driver_count_hidden');
            const helpText    = $('ch_driver_count_help');
            const driverBlock = $('ch-driver-block');

            const plan = PLANS.find(p => p.name === planName);

            if (planName === 'Enterprise Fleet' || (plan && plan.max_drivers === null && plan.min_drivers >= 101)) {
                countInput.value    = 100;
                countInput.readOnly = true;
                hiddenInput.value   = 100;
                helpText.innerText  = 'Contact our team for Enterprise accounts.';
                helpText.className  = 'pf-hint';
                driverBlock.classList.add('d-none');
                showEnterpriseCalc();
            } else {
                // Owner Operator and other flexible tiers: editable driver count
                const isUnlimited = !plan || plan.max_drivers === null;
                const max = isUnlimited ? 9999 : maxDrivers;
                activeMaxDrivers = max;
                countInput.readOnly = false;
                countInput.min      = minDrivers;
                countInput.max      = max;
                if (parseInt(countInput.value) < minDrivers || parseInt(countInput.value) > max || isNaN(parseInt(countInput.value))) {
                    countInput.value = minDrivers;
                }
                hiddenInput.value  = countInput.value;
                helpText.innerText = isUnlimited
                    ? `Enter ${minDrivers} or more drivers.`
                    : `Enter between ${minDrivers} and ${max} drivers.`;
                helpText.className = 'pf-hint';
                driverBlock.classList.remove('d-none');
                showOnlineCalc();
            }

            calculateTotal();
        }

        function showOnlineCalc() {
            crossfadeCalc('ch-calculator-online', 'ch-calculator-enterprise');
        }
        function showEnterpriseCalc() {
            crossfadeCalc('ch-calculator-enterprise', 'ch-calculator-online');
        }

        function crossfadeCalc(showId, hideId) {
            const showEl = $(showId);
            const hideEl = $(hideId);
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

        function enforceDriverCount(onBlur) {
            const countInput = $('ch_driver_count');
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
            const planName = $('ch-selected_plan').value;
            const plan = PLANS.find(p => p.name === planName);
            if (!plan) return;

            const countInput = $('ch_driver_count');
            let drivers = parseInt(countInput.value, 10) || 0;
            if (drivers < activeMinDrivers) drivers = activeMinDrivers;
            if (drivers > activeMaxDrivers) drivers = activeMaxDrivers;

            $('ch_driver_count_hidden').value = drivers;

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

            $('ch-itemized-fees-container').innerHTML = html;
            const totalEl = $('ch_summary_total');
            totalEl.innerText = `$${grandTotal.toFixed(2)}`;
            totalEl.classList.remove('rc-total-pulse');
            void totalEl.offsetWidth;
            totalEl.classList.add('rc-total-pulse');

            const driverWrap = $('ch-driver-input-wrap');
            if (driverWrap) {
                driverWrap.classList.remove('rc-input-bump');
                void driverWrap.offsetWidth;
                driverWrap.classList.add('rc-input-bump');
            }
        }

        function toggleNotes() {
            const area = $('ch-notes-area');
            const icon = $('ch-notes-toggle-icon');
            const btn  = $('ch-notes-toggle');
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
            const errBox  = $('ch-form-errors');
            const errBody = $('ch-form-errors-body');
            errBox.classList.add('d-none');
            errBody.innerHTML = '';

            const form = $('ch-enrollment-form');
            if (!form.reportValidity()) return;

            const formData = new FormData(form);
            formData.set('driver_count', $('ch_driver_count_hidden').value);

            const loader = $('ch-loader-overlay');
            loader.classList.add('show');
            loader.setAttribute('aria-hidden', 'false');

            fetch('{{ route("frontend.clearing-house.enroll") }}', {
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

        function initPlanTilt() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            if (window.matchMedia('(max-width: 991px)').matches) return;

            root.querySelectorAll('.rc-tilt-card').forEach(card => {
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
            const btn = root.querySelector('.rc-btn-magnetic');
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
            root.querySelectorAll('#ch-enrollment-form .pf-control').forEach(input => {
                input.addEventListener('blur', () => {
                    if (input.checkValidity && input.checkValidity() && input.value.trim() !== '') {
                        input.classList.add('rc-input-valid');
                    } else {
                        input.classList.remove('rc-input-valid');
                    }
                });
            });
        }

        function bindPlanCards() {
            root.querySelectorAll('.ch-enroll-plan-card').forEach(card => {
                const activate = (evt) => {
                    const name = card.dataset.planName;
                    const min = parseInt(card.dataset.minDrivers, 10) || 1;
                    const maxRaw = card.dataset.maxDrivers;
                    const max = maxRaw === '' || maxRaw === 'null' ? 9999 : (parseInt(maxRaw, 10) || 9999);
                    selectPlan(card, name, min, max, evt);
                };
                card.addEventListener('click', activate);
                card.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        activate(event);
                    }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initPlanTilt();
            initMagneticButton();
            initInputAnimations();
            bindPlanCards();

            const countInput = $('ch_driver_count');
            if (countInput) {
                countInput.addEventListener('input', () => enforceDriverCount(false));
                countInput.addEventListener('blur', () => enforceDriverCount(true));
            }

            const notesToggle = $('ch-notes-toggle');
            if (notesToggle) notesToggle.addEventListener('click', toggleNotes);

            const submitBtn = $('ch-submit-btn');
            if (submitBtn) submitBtn.addEventListener('click', submitForm);

            if (PLANS.length > 0) {
                const firstPlan = PLANS[0];
                const firstCard = root.querySelector('.ch-enroll-plan-card');
                const max = firstPlan.max_drivers === null ? 9999 : (firstPlan.max_drivers || 9999);
                selectPlan(firstCard, firstPlan.name, firstPlan.min_drivers || 1, max);
            }
        });
    })();
    </script>
    @endunless

</div>
