@php
    $order = $questOrder ?? null;
    $isEdit = $order !== null;
    $val = fn (string $key, mixed $default = '') => old($key, $order?->{$key} ?? $default);
    $dotValue = old('dot_test', $order?->dot_test ?? 'F');
    if (in_array($dotValue, ['Y', 'T'], true)) {
        $dotValue = 'T';
    } elseif (in_array($dotValue, ['N', 'F'], true)) {
        $dotValue = 'F';
    }
    $unitCodesValue = old(
        'unit_codes',
        $order
            ? (is_array($order->unit_codes) ? implode(',', $order->unit_codes) : $order->unit_codes)
            : ''
    );
    $selectedPortfolioId = old('portfolio_id', $order?->portfolio_id);
    $dobValue = $val('dob');
    if ($dobValue instanceof \Carbon\Carbon) {
        $dobValue = $dobValue->format('Y-m-d');
    } elseif (is_string($dobValue) && $dobValue !== '') {
        try {
            $dobValue = \Carbon\Carbon::parse($dobValue)->format('Y-m-d');
        } catch (\Throwable) {
            $dobValue = '';
        }
    }
    $endDatetimeValue = old('end_datetime');
    if ($endDatetimeValue === null && $order?->end_datetime) {
        $endDatetimeValue = $order->end_datetime->format('Y-m-d\TH:i');
    }
@endphp

<div class="row">
    <div class="col-md-12">
        <h5 class="mt-3 mb-3 text-primary">Test Selection</h5>
        <hr>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label for="portfolio_select">Select Test / Service <span class="text-red">*</span></label>
            <select id="portfolio_select" class="form-control" required>
                <option value="">Choose a test…</option>
                @php $currentCategory = null; @endphp
                @foreach ($portfolios as $portfolio)
                    @if ($currentCategory !== $portfolio->category_name)
                        @if ($currentCategory !== null)
                            </optgroup>
                        @endif
                        <optgroup label="{{ $portfolio->category_name }}">
                        @php $currentCategory = $portfolio->category_name; @endphp
                    @endif
                    <option value="{{ $portfolio->id }}" @selected((string) $selectedPortfolioId === (string) $portfolio->id)>
                        {{ $portfolio->title }}@if ($portfolio->code) ({{ $portfolio->code }})@endif
                    </option>
                @endforeach
                @if ($currentCategory !== null)
                    </optgroup>
                @endif
            </select>
            <small class="text-muted">Selecting a test fills unit codes, DOT settings, and related client fields automatically.</small>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="portfolio_id">Test ID</label>
            <input id="portfolio_id" name="portfolio_id" type="number" class="form-control" value="{{ $selectedPortfolioId }}" readonly>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="portfolio_name">Portfolio Name</label>
            <input id="portfolio_name" name="portfolio_name" type="text" class="form-control" value="{{ $val('portfolio_name') }}" readonly>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label for="unit_codes">Unit Codes</label>
            <input id="unit_codes" name="unit_codes" type="text" class="form-control" value="{{ $unitCodesValue }}" readonly>
            <small class="text-muted">Auto-filled from the selected test. Multiple codes = multiple panels.</small>
        </div>
    </div>
</div>

<div class="row mt-4" id="dot_selection_section" style="display:none;">
    <div class="col-md-12">
        <h5 class="mt-3 mb-3 text-primary">DOT Client &amp; Employee</h5>
        <hr>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="client_profile_id">Client Profile</label>
            <select id="client_profile_id" class="form-control">
                <option value="">All clients — show all employees</option>
                @foreach ($clientProfiles as $profile)
                    <option value="{{ $profile->id }}" @selected(old('client_profile_id') == $profile->id)>
                        {{ $profile->company_name }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted">Filter employees by company, or leave blank to see every active employee.</small>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="employee_id">Employee @if (!$isEdit)<span class="text-red">*</span>@endif</label>
            <select id="employee_id" class="form-control" @if (!$isEdit) data-required-dot="true" @endif>
                <option value="">Choose an employee…</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}"
                        data-client-profile-id="{{ $employee->client_profile_id }}"
                        @selected(old('employee_id') == $employee->id)>
                        {{ $employee->first_name }} {{ $employee->last_name }}
                        @if ($employee->clientProfile?->company_name)
                            — {{ $employee->clientProfile->company_name }}
                        @endif
                    </option>
                @endforeach
            </select>
            <small class="text-muted">Selecting an employee fills donor and client information below.</small>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <h5 class="mt-3 mb-3 text-primary">Donor Information</h5>
        <hr>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="first_name">First Name <span class="text-red">*</span></label>
            <input id="first_name" name="first_name" type="text" class="form-control" value="{{ $val('first_name') }}" maxlength="20" required>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="last_name">Last Name <span class="text-red">*</span></label>
            <input id="last_name" name="last_name" type="text" class="form-control" value="{{ $val('last_name') }}" maxlength="25" required>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="middle_name">Middle Name</label>
            <input id="middle_name" name="middle_name" type="text" class="form-control" value="{{ $val('middle_name') }}" maxlength="20">
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="primary_id">Driver's License / Employee ID <span class="text-red">*</span></label>
            <input id="primary_id" name="primary_id" type="text" class="form-control" value="{{ $val('primary_id') }}" maxlength="25" required>
        </div>
    </div>

    <div class="col-md-4" id="primary_id_type_wrap">
        <div class="form-group">
            <label for="primary_id_type">Primary ID Type</label>
            <select id="primary_id_type" name="primary_id_type" class="form-control">
                <option value="">Select ID Type</option>
                <option value="DL" @selected($val('primary_id_type') == 'DL')>Driver's License</option>
                <option value="OTHER" @selected($val('primary_id_type') == 'OTHER')>Other Government ID</option>
            </select>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="dob">Date of Birth</label>
            <input id="dob" name="dob" type="date" class="form-control" value="{{ $dobValue }}">
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="primary_phone">Primary Phone </label>
            <input id="primary_phone" name="primary_phone" type="text" class="form-control quest-phone-digits" value="{{ $val('primary_phone') }}" maxlength="20">
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="secondary_phone">Secondary Phone</label>
            <input id="secondary_phone" name="secondary_phone" type="text" class="form-control quest-phone-digits" value="{{ $val('secondary_phone') }}" maxlength="20">
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" class="form-control" value="{{ $val('email') }}" maxlength="254">
        </div>
    </div>

    <div class="col-md-4" id="zip_code_wrap">
        <div class="form-group">
            <label for="zip_code">Zip Code</label>
            <input id="zip_code" name="zip_code" type="text" class="form-control" value="{{ $val('zip_code') }}" maxlength="10">
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <h5 class="mt-3 mb-3 text-primary">Test Information</h5>
        <hr>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="dot_test">DOT Test <span class="text-red">*</span></label>
            <select class="form-control" name="dot_test" id="dot_test" required>
                <option value="F" @selected($dotValue === 'F')>No (Non-DOT)</option>
                <option value="T" @selected($dotValue === 'T')>Yes (DOT)</option>
            </select>
        </div>
    </div>

    <div class="col-md-6" id="testing_authority_wrap" style="{{ $dotValue === 'T' ? '' : 'display:none;' }}">
        <div class="form-group">
            <label for="testing_authority">Testing Authority <span class="text-red">*</span></label>
            <select class="form-control" name="testing_authority" id="testing_authority">
                <option value="">Select authority</option>
                @foreach (['FMCSA', 'PHMSA', 'FAA', 'FTA', 'FRA', 'USCG'] as $authority)
                    <option value="{{ $authority }}" @selected($val('testing_authority') == $authority)>{{ $authority }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="collection_site_id">Collection Site</label>
            <select name="collection_site_id" id="collection_site_id" class="form-control select2-collection-sites">
                <option value="">Select a collection site…</option>
            </select>
        </div>
    </div>

    <div class="col-md-6" id="reason_for_test_wrap">
        <div class="form-group">
            <label for="reason_for_test_id">Reason for Test</label>
            <select id="reason_for_test_id" name="reason_for_test_id" class="form-control">
                @php $reasonDefault = old('reason_for_test_id', $order?->reason_for_test_id ?? '1'); @endphp
                <option value="1" @selected((string) $reasonDefault === '1')>Pre-Employment</option>
                <option value="2" @selected((string) $reasonDefault === '2')>Post Accident</option>
                <option value="3" @selected((string) $reasonDefault === '3')>Random</option>
                <option value="5" @selected((string) $reasonDefault === '5')>Reasonable Suspicion / Cause</option>
                <option value="6" @selected((string) $reasonDefault === '6')>Return to Duty</option>
                <option value="23" @selected((string) $reasonDefault === '23')>Follow-Up</option>
                <option value="99" @selected((string) $reasonDefault === '99')>Other</option>
            </select>
        </div>
    </div>

    <div class="col-md-6" id="physical_reason_wrap" style="display:none;">
        <div class="form-group">
            <label for="physical_reason_for_test_id">Physical Reason</label>
            <select id="physical_reason_for_test_id" name="physical_reason_for_test_id" class="form-control">
                <option value="">Select Physical Reason</option>
                @foreach (['NC' => 'New Certification', 'RE' => 'Recertification', 'FU' => 'Follow-Up', 'OT' => 'Other', 'SA' => 'Site Access', 'PE' => 'Pre-employment', 'RD' => 'Return to Duty', 'SU' => 'Surveillance'] as $code => $label)
                    <option value="{{ $code }}" @selected($val('physical_reason_for_test_id') == $code)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="observed_requested">Observed Requested <span class="text-red">*</span></label>
            <select class="form-control" name="observed_requested" id="observed_requested" required>
                <option value="N" @selected($val('observed_requested', 'N') == 'N')>No</option>
                <option value="Y" @selected($val('observed_requested') == 'Y')>Yes</option>
            </select>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="split_specimen_requested">Split Specimen Requested <span class="text-red">*</span></label>
            <select class="form-control" name="split_specimen_requested" id="split_specimen_requested" required>
                <option value="N" @selected($val('split_specimen_requested', 'N') == 'N')>No</option>
                <option value="Y" @selected($val('split_specimen_requested') == 'Y')>Yes</option>
            </select>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label for="order_comments">Order Comments</label>
            <textarea id="order_comments" name="order_comments" class="form-control" rows="3" maxlength="250">{{ $val('order_comments') }}</textarea>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <h5 class="mt-3 mb-3 text-primary">Client Information</h5>
        <hr>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="lab_account">Lab Account <span class="text-red">*</span></label>
            <input id="lab_account" name="lab_account" type="text" class="form-control"
                value="{{ $val('lab_account') }}" required>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="csl">CSL</label>
            <input id="csl" name="csl" type="text" class="form-control"
                value="{{ $val('csl', config('services.quest.default_csl')) }}" maxlength="20">
        </div>
    </div>

    <div class="col-md-6" id="ebat_contact_wrap" style="display:none;">
        <div class="form-group">
            <label for="contact_name">Contact Name <span class="text-red ebat-required-marker" style="display:none;">*</span></label>
            <input id="contact_name" name="contact_name" type="text" class="form-control"
                value="{{ $val('contact_name', config('services.quest.default_contact_name')) }}" maxlength="45">
        </div>
    </div>

    <div class="col-md-6" id="ebat_phone_wrap" style="display:none;">
        <div class="form-group">
            <label for="telephone_number">Telephone Number <span class="text-red ebat-required-marker" style="display:none;">*</span></label>
            <input id="telephone_number" name="telephone_number" type="text" class="form-control quest-phone-digits"
                value="{{ $val('telephone_number', config('services.quest.default_telephone')) }}" maxlength="20">
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <h5 class="mt-3 mb-3 text-primary">Additional Information</h5>
        <hr>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="end_datetime">End Date &amp; Time</label>
            <input id="end_datetime" name="end_datetime" type="datetime-local" class="form-control" value="{{ $endDatetimeValue }}">
            <small class="text-muted">For ePhysical tests, must be within 7 days.</small>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label for="end_datetime_timezone_id">Timezone</label>
            <select id="end_datetime_timezone_id" name="end_datetime_timezone_id" class="form-control">
                <option value="">Select timezone</option>
                @foreach ([1 => 'Eastern Time', 2 => 'Central Time', 3 => 'Mountain Time', 4 => 'Pacific Time', 5 => 'Hawaii-Aleutian', 6 => 'Alaskan Time', 7 => 'Atlantic Time', 8 => 'Guam Time'] as $id => $label)
                    <option value="{{ $id }}" @selected((string) $val('end_datetime_timezone_id') === (string) $id)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
