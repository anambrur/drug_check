@php
    $defaults = $questDefaults ?? [];
    $isPhysical = $questIsPhysical ?? str_contains(strtolower($portfolio->title ?? ''), 'physical');
    $isEbat = $questIsEbat ?? str_contains(strtolower($portfolio->title ?? ''), 'ebat');
    $defaultDotTest = $isNonDot ? 'F' : 'T';
@endphp

<input type="hidden" name="is_physical" value="{{ $isPhysical ? 'true' : 'false' }}">
<input type="hidden" name="is_ebat" value="{{ $isEbat ? 'true' : 'false' }}">

@if (!$isNonDot)
    <div class="pf-section">
        <div class="pf-section-head">
            <div class="icon-wrap"><i class="fas fa-users"></i></div>
            <h6>Select Employee</h6>
        </div>
        <div class="pf-section-body">
            @if (($employees ?? collect())->isEmpty())
                <div class="pf-alert pf-alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle mt-1"></i>
                    <div>No active employees are available for your account. Please contact your administrator.</div>
                </div>
            @else
                <label class="pf-label" for="employee_id">Employee <span class="pf-req">*</span></label>
                <div class="pf-icon-wrap">
                    <i class="fas fa-user pf-icon"></i>
                    <select id="employee_id" name="employee_id" class="pf-control" required>
                        <option value="" disabled selected>Choose an employee…</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>
                                {{ $employee->first_name }} {{ $employee->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <p class="pf-hint mt-2"><i class="fas fa-info-circle"></i> Select the employee who will take this DOT test.</p>
            @endif
        </div>
    </div>
@endif

<div class="pf-section">
    <div class="pf-section-head">
        <div class="icon-wrap"><i class="fas fa-user"></i></div>
        <h6>Personal Information</h6>
    </div>
    <div class="pf-section-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="pf-label" for="first_name">First Name <span class="pf-req">*</span></label>
                <div class="pf-icon-wrap">
                    <i class="fas fa-user pf-icon"></i>
                    <input type="text" id="first_name" name="first_name" class="pf-control" value="{{ old('first_name', $defaults['first_name'] ?? '') }}" maxlength="20" required>
                </div>
            </div>
            <div class="col-md-6">
                <label class="pf-label" for="last_name">Last Name <span class="pf-req">*</span></label>
                <div class="pf-icon-wrap">
                    <i class="fas fa-user pf-icon"></i>
                    <input type="text" id="last_name" name="last_name" class="pf-control" value="{{ old('last_name', $defaults['last_name'] ?? '') }}" maxlength="25" required>
                </div>
            </div>
            <div class="col-md-6">
                <label class="pf-label" for="middle_name">Middle Name <span class="pf-opt">Optional</span></label>
                <input type="text" id="middle_name" name="middle_name" class="pf-control" value="{{ old('middle_name', $defaults['middle_name'] ?? '') }}" maxlength="20">
            </div>
            <div class="col-md-6">
                <label class="pf-label" for="primary_id">Driver's License / ID <span class="pf-req">*</span></label>
                <div class="pf-icon-wrap">
                    <i class="fas fa-id-card pf-icon"></i>
                    <input type="text" id="primary_id" name="primary_id" class="pf-control" value="{{ old('primary_id', $defaults['primary_id'] ?? '') }}" maxlength="25" required>
                </div>
            </div>
            <div class="col-md-6">
                <label class="pf-label" for="email">Email Address <span class="pf-req">*</span></label>
                <div class="pf-icon-wrap">
                    <i class="fas fa-envelope pf-icon"></i>
                    <input type="email" id="email" name="email" class="pf-control" value="{{ old('email', $defaults['email'] ?? auth()->user()->email) }}" maxlength="254" required>
                </div>
            </div>
            @if ($isPhysical)
                <div class="col-md-6">
                    <label class="pf-label" for="primary_id_type">Primary ID Type <span class="pf-opt">Optional</span></label>
                    <select id="primary_id_type" name="primary_id_type" class="pf-control">
                        <option value="">Select ID Type</option>
                        <option value="DL" @selected(old('primary_id_type', $defaults['primary_id_type'] ?? '') == 'DL')>Driver's License</option>
                        <option value="OTHER" @selected(old('primary_id_type', $defaults['primary_id_type'] ?? '') == 'OTHER')>Other Government ID</option>
                    </select>
                </div>
            @endif
            <div class="col-md-6">
                <label class="pf-label" for="dob">Date of Birth <span class="pf-opt">Optional</span></label>
                <div class="pf-icon-wrap">
                    <i class="fas fa-calendar pf-icon"></i>
                    <input type="text" id="dob" name="dob" class="pf-control quest-dob-picker" value="{{ old('dob', $defaults['dob'] ?? '') }}" placeholder="MM/DD/YYYY" autocomplete="off">
                </div>
            </div>
            <div class="col-md-6">
                <label class="pf-label" for="primary_phone">Primary Phone <span class="pf-opt">Optional</span></label>
                <div class="pf-icon-wrap">
                    <i class="fas fa-phone pf-icon"></i>
                    <input type="tel" id="primary_phone" name="primary_phone" class="pf-control quest-phone-digits" value="{{ old('primary_phone', $defaults['primary_phone'] ?? '') }}" placeholder="5550000000" inputmode="numeric">
                </div>
            </div>
            <div class="col-md-6">
                <label class="pf-label" for="secondary_phone">Secondary Phone <span class="pf-opt">Optional</span></label>
                <div class="pf-icon-wrap">
                    <i class="fas fa-phone-alt pf-icon"></i>
                    <input type="tel" id="secondary_phone" name="secondary_phone" class="pf-control quest-phone-digits" value="{{ old('secondary_phone', $defaults['secondary_phone'] ?? '') }}" placeholder="5550000000" inputmode="numeric">
                </div>
            </div>
            @if ($isPhysical)
                <div class="col-md-6">
                    <label class="pf-label" for="zip_code">Zip Code <span class="pf-opt">Optional</span></label>
                    <div class="pf-icon-wrap">
                        <i class="fas fa-map-pin pf-icon"></i>
                        <input type="text" id="zip_code" name="zip_code" class="pf-control" value="{{ old('zip_code', $defaults['zip_code'] ?? '') }}" placeholder="e.g. 90210">
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="pf-section">
    <div class="pf-section-head">
        <div class="icon-wrap"><i class="fas fa-vial"></i></div>
        <h6>Test Information</h6>
    </div>
    <div class="pf-section-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="pf-label" for="dot_test">Test Type <span class="pf-req">*</span></label>
                <select id="dot_test" name="dot_test" class="pf-control" required>
                    <option value="F" @selected(old('dot_test', $defaultDotTest) == 'F')>Non-DOT Test</option>
                    <option value="T" @selected(old('dot_test', $defaultDotTest) == 'T')>DOT Test</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="pf-label" for="collection_site_id">Collection Site <span class="pf-opt">Optional</span></label>
                <select name="collection_site_id" id="collection_site_id" class="pf-control select2-collection-sites">
                    <option value="">Select a collection site…</option>
                </select>
            </div>
            <div class="col-md-6" id="testingAuthorityField" style="{{ old('dot_test', $defaultDotTest) == 'T' ? '' : 'display:none;' }}">
                <label class="pf-label" for="testing_authority">DOT Testing Authority <span class="pf-req">*</span></label>
                <select id="testing_authority" name="testing_authority" class="pf-control">
                    <option value="">Select Authority</option>
                    @foreach (['FMCSA', 'PHMSA', 'FAA', 'FTA', 'FRA', 'USCG'] as $authority)
                        <option value="{{ $authority }}" @selected(old('testing_authority', $defaults['testing_authority'] ?? '') == $authority)>{{ $authority }}</option>
                    @endforeach
                </select>
            </div>
            @if (!$isPhysical)
                <div class="col-md-6">
                    <label class="pf-label" for="reason_for_test_id">Reason for Test <span class="pf-req">*</span></label>
                    <select id="reason_for_test_id" name="reason_for_test_id" class="pf-control" required>
                        <option value="1" @selected(old('reason_for_test_id', '1') == '1')>Pre-Employment</option>
                        <option value="2" @selected(old('reason_for_test_id') == '2')>Post Accident</option>
                        <option value="3" @selected(old('reason_for_test_id') == '3')>Random</option>
                        <option value="5" @selected(old('reason_for_test_id') == '5')>Reasonable Suspicion / Cause</option>
                        <option value="6" @selected(old('reason_for_test_id') == '6')>Return to Duty</option>
                        <option value="23" @selected(old('reason_for_test_id') == '23')>Follow-Up</option>
                        <option value="99" @selected(old('reason_for_test_id') == '99')>Other</option>
                    </select>
                </div>
            @else
                <div class="col-md-6">
                    <label class="pf-label" for="physical_reason_for_test_id">Physical Reason <span class="pf-req">*</span></label>
                    <select id="physical_reason_for_test_id" name="physical_reason_for_test_id" class="pf-control" required>
                        <option value="">Select Physical Reason</option>
                        @foreach (['NC' => 'New Certification', 'RE' => 'Recertification', 'FU' => 'Follow-Up', 'OT' => 'Other', 'SA' => 'Site Access', 'PE' => 'Pre-employment', 'RD' => 'Return to Duty', 'SU' => 'Surveillance'] as $code => $label)
                            <option value="{{ $code }}" @selected(old('physical_reason_for_test_id', $defaults['physical_reason_for_test_id'] ?? '') == $code)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-6">
                <label class="pf-label" for="end_datetime">Order Expiration <span class="pf-opt">Optional</span></label>
                <div class="pf-icon-wrap">
                    <i class="fas fa-clock pf-icon"></i>
                    <input type="datetime-local" id="end_datetime" name="end_datetime" class="pf-control" value="{{ old('end_datetime') }}">
                </div>
                <p class="pf-hint mt-1"><i class="fas fa-info-circle"></i> For ePhysical, must be within 7 days.</p>
            </div>
            <div class="col-md-6">
                <label class="pf-label" for="end_datetime_timezone_id">Timezone <span class="pf-opt">Optional</span></label>
                <select id="end_datetime_timezone_id" name="end_datetime_timezone_id" class="pf-control">
                    <option value="">Select Timezone</option>
                    @foreach ([1 => 'Eastern Time', 2 => 'Central Time', 3 => 'Mountain Time', 4 => 'Pacific Time', 5 => 'Hawaii-Aleutian', 6 => 'Alaskan Time', 7 => 'Atlantic Time', 8 => 'Guam Time'] as $id => $label)
                        <option value="{{ $id }}" @selected(old('end_datetime_timezone_id') == $id)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="pf-label" for="observed_requested">Collection Type</label>
                <select id="observed_requested" name="observed_requested" class="pf-control">
                    <option value="N" @selected(old('observed_requested', 'N') == 'N')>Not Observed</option>
                    <option value="Y" @selected(old('observed_requested') == 'Y')>Observed</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="pf-label" for="split_specimen_requested">Specimen Type</label>
                <select id="split_specimen_requested" name="split_specimen_requested" class="pf-control">
                    <option value="N" @selected(old('split_specimen_requested', 'N') == 'N')>Single Specimen</option>
                    <option value="Y" @selected(old('split_specimen_requested') == 'Y')>Split Specimen</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="pf-label" for="csl">Client Site Location (CSL) <span class="pf-opt">Optional</span></label>
                <input type="text" id="csl" name="csl" class="pf-control" value="{{ old('csl', config('services.quest.default_csl')) }}" maxlength="20">
            </div>
            <div class="col-md-6 {{ $isEbat ? '' : 'd-none' }}" id="ebatContactField">
                <label class="pf-label" for="contact_name">DER Contact Name @if($isEbat)<span class="pf-req">*</span>@else<span class="pf-opt">Optional</span>@endif</label>
                <input type="text" id="contact_name" name="contact_name" class="pf-control" value="{{ old('contact_name', config('services.quest.default_contact_name')) }}" maxlength="45" @if($isEbat) required @endif>
            </div>
            <div class="col-md-6 {{ $isEbat ? '' : 'd-none' }}" id="ebatPhoneField">
                <label class="pf-label" for="telephone_number">DER Phone Number @if($isEbat)<span class="pf-req">*</span>@else<span class="pf-opt">Optional</span>@endif</label>
                <input type="tel" id="telephone_number" name="telephone_number" class="pf-control quest-phone-digits" value="{{ old('telephone_number', config('services.quest.default_telephone')) }}" maxlength="10" @if($isEbat) required @endif>
            </div>
            <div class="col-12">
                <label class="pf-label" for="order_comments">Special Instructions <span class="pf-opt">Optional</span></label>
                <textarea id="order_comments" name="order_comments" class="pf-control" maxlength="250" rows="3" placeholder="Any special instructions for the collection site…">{{ old('order_comments') }}</textarea>
            </div>
        </div>
    </div>
</div>
