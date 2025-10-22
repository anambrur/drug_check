@extends('layouts.admin.master')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h4 class="mb-1 text-white">
                                    <i class="fas fa-flask me-2"></i>Quest Diagnostics Order Form
                                </h4>
                                <p class="mb-0 opacity-75">Complete the form to schedule your {{ $portfolio->title }} test
                                </p>
                            </div>
                            <div class="badge bg-white text-primary fs-6 px-3 py-2">
                                Step 2 of 3
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <!-- Progress Steps -->
                        <div class="row mb-5">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center position-relative">
                                    <div class="progress w-100 position-absolute"
                                        style="height: 4px; top: 20px; z-index: 1;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 50%"></div>
                                    </div>

                                    <div class="text-center z-2">
                                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                            style="width: 40px; height: 40px;">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div class="small fw-semibold text-success">Payment</div>
                                    </div>

                                    <div class="text-center z-2">
                                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                            style="width: 40px; height: 40px;">
                                            2
                                        </div>
                                        <div class="small fw-semibold text-primary">Test Information</div>
                                    </div>

                                    <div class="text-center z-2">
                                        <div class="bg-light text-muted rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                            style="width: 40px; height: 40px;">
                                            3
                                        </div>
                                        <div class="small text-muted">Confirmation</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif


                        <form method="POST" action="{{ route('admin.dot-test.submit-order') }}" id="questOrderForm"
                            novalidate>
                            @csrf
                            <input type="hidden" name="portfolio_id" value="{{ $portfolio->id }}">
                            <input type="hidden" name="payment_intent_id" value="{{ $paymentData['payment_intent_id'] }}">
                            <input type="hidden" name="lab_account" value="{{ $employee->clientProfile->account_no }}">
                            <input type="hidden" name="is_physical"
                                value="{{ $isPhysical = str_contains(strtolower($portfolio->title), 'physical') ? 'true' : 'false' }}">
                            <input type="hidden" name="is_ebat"
                                value="{{ str_contains(strtolower($portfolio->title), 'ebat') ? 'true' : 'false' }}">
                            <input type="hidden" name="unit_codes[]" value="{{ $portfolio->quest_unit_code }}">

                            <!-- Personal Information Section -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user-circle me-2 text-primary"></i>Personal Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="first_name" class="form-label">First Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('first_name') is-invalid @enderror"
                                                name="first_name" value="{{ old('first_name', $employee->first_name) }}"
                                                required maxlength="20">
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>



                                        <div class="col-md-6">
                                            <label for="last_name" class="form-label">Last Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('last_name') is-invalid @enderror"
                                                name="last_name" value="{{ old('last_name', $employee->last_name) }}"
                                                required maxlength="25">
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="middle_name" class="form-label">Middle Name</label>
                                            <input type="text"
                                                class="form-control @error('middle_name') is-invalid @enderror"
                                                name="middle_name" value="{{ old('middle_name', $employee->middle_name) }}"
                                                maxlength="20">
                                            @error('middle_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="primary_id" class="form-label">Primary ID <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('primary_id') is-invalid @enderror"
                                                name="primary_id" value="{{ old('primary_id', $employee->employee_id) }}"
                                                required maxlength="25" placeholder="Driver's license or ID">
                                            @error('primary_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>



                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Email Address <span
                                                    class="text-danger">*</span></label>
                                            <input type="email"
                                                class="form-control @error('email') is-invalid @enderror" name="email"
                                                value="{{ old('email', $employee->email) }}" required maxlength="254">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        @if ($isPhysical)
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default">
                                                    <label for="primary_id_type">Primary ID Type <span
                                                            class="text-red">*</span></label>
                                                    <select
                                                        class="form-control @error('primary_id_type') is-invalid @enderror"
                                                        name="primary_id_type">
                                                        <option value="">Select ID Type</option>
                                                        <option value="DL" @selected(old('primary_id_type') == 'DL')>Driver's
                                                            License
                                                        </option>
                                                        <option value="OTHER" @selected(old('primary_id_type') == 'OTHER')>Other
                                                            Government ID
                                                        </option>
                                                    </select>
                                                </div>
                                                @error('primary_id_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endif

                                        <div class="col-md-6">
                                            <label for="dob" class="form-label">Date of Birth</label>
                                            <input type="text"
                                                class="form-control datepicker @error('dob') is-invalid @enderror"
                                                name="dob" value="{{ old('dob', $employee->date_of_birth) }}"
                                                placeholder='' autocomplete="off">
                                            @error('dob')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="primary_phone" class="form-label">Primary Phone</label>
                                            <input type="tel"
                                                class="form-control @error('primary_phone') is-invalid @enderror"
                                                name="primary_phone"
                                                value="{{ old('primary_phone', $employee->phone) }}">
                                            @error('primary_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="secondary_phone" class="form-label">Secondary Phone</label>
                                            <input type="tel"
                                                class="form-control @error('secondary_phone') is-invalid @enderror"
                                                name="secondary_phone" value="{{ old('secondary_phone') }}">
                                            @error('secondary_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        @if ($isPhysical)
                                            <div class="col-md-6">
                                                <label for="zip_code" class="form-label">Zip Code</label>
                                                <input type="text"
                                                    class="form-control @error('zip_code') is-invalid @enderror"
                                                    name="zip_code" value="{{ old('zip_code') }}"
                                                    placeholder="For site search">
                                                @error('zip_code')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Test Information Section -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="fas fa-flask me-2 text-primary"></i>Test Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-group form-group-default">
                                                <label for="dot_test" class="form-label">Test Type <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-control @error('dot_test') is-invalid @enderror"
                                                    name="dot_test" required>
                                                    <option value="T" @selected(old('dot_test') == 'T')>DOT Test</option>
                                                    <option value="F" @selected(old('dot_test', 'F') == 'F')>Non-DOT Test
                                                    </option>
                                                </select>
                                                @error('dot_test')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6" id="testingAuthorityField" style="display: none;">
                                            <div class="form-group form-group-default">
                                                <label for="testing_authority" class="form-label">DOT Testing Authority
                                                    <span class="text-danger">*</span></label>
                                                <select
                                                    class="form-control @error('testing_authority') is-invalid @enderror"
                                                    name="testing_authority">
                                                    <option value="">Select Authority</option>
                                                    <option value="FMCSA">FMCSA</option>
                                                    <option value="PHMSA">PHMSA</option>
                                                    <option value="FAA">FAA</option>
                                                    <option value="FTA">FTA</option>
                                                    <option value="FRA">FRA</option>
                                                    <option value="USCG">USCG</option>
                                                </select>
                                                @error('testing_authority')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        @if (!$isPhysical)
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default">
                                                    <label for="reason_for_test_id" class="form-label">Reason for Test
                                                        <span class="text-danger">*</span></label>
                                                    <select
                                                        class="form-control @error('reason_for_test_id') is-invalid @enderror"
                                                        name="reason_for_test_id" required>
                                                        <option value="1" @selected(old('reason_for_test_id') == '1')>Pre-Employment
                                                        </option>
                                                        <option value="2" @selected(old('reason_for_test_id') == '2')>Post Accident
                                                        </option>
                                                        <option value="3" @selected(old('reason_for_test_id') == '3')>Random</option>
                                                        <option value="5" @selected(old('reason_for_test_id') == '5')>Reasonable
                                                            Suspicion/Cause</option>
                                                        <option value="6" @selected(old('reason_for_test_id') == '6')>Return to Duty
                                                        </option>
                                                        <option value="23" @selected(old('reason_for_test_id') == '23')>Follow-Up
                                                        </option>
                                                        <option value="99" @selected(old('reason_for_test_id') == '99')>Other</option>
                                                    </select>
                                                    @error('reason_for_test_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif

                                        @if ($isPhysical)
                                            <div class="col-md-6">
                                                <div class="form-group form-group-default">
                                                    <label for="physical_reason_for_test_id" class="form-label">Physical
                                                        Reason <span class="text-danger">*</span></label>
                                                    <select
                                                        class="form-control @error('physical_reason_for_test_id') is-invalid @enderror"
                                                        name="physical_reason_for_test_id" required>
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
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif

                                        @if (!$isPhysical)
                                            <div class="col-md-6">
                                                <label for="collection_site_id" class="form-label">Collection Site
                                                    ID</label>
                                                <input type="text"
                                                    class="form-control @error('collection_site_id') is-invalid @enderror"
                                                    name="collection_site_id" value="{{ old('collection_site_id') }}"
                                                    maxlength="6">
                                                <div class="form-text">Leave blank to let donor choose</div>
                                                @error('collection_site_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endif

                                        <div class="col-md-6">
                                            <label for="end_datetime" class="form-label">Order Expiration
                                                Date/Time</label>
                                            <input type="datetime-local"
                                                class="form-control @error('end_datetime') is-invalid @enderror"
                                                name="end_datetime" value="{{ old('end_datetime') }}">
                                            <div class="form-text">For ePhysical, must be within 7 days</div>
                                            @error('end_datetime')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group form-group-default">
                                                <label for="end_datetime_timezone_id" class="form-label">Timezone</label>
                                                <select
                                                    class="form-control @error('end_datetime_timezone_id') is-invalid @enderror"
                                                    name="end_datetime_timezone_id">
                                                    <option value="">Select Timezone</option>
                                                    <option value="1">Eastern Time</option>
                                                    <option value="2">Central Time</option>
                                                    <option value="3">Mountain Time</option>
                                                    <option value="4">Pacific Time</option>
                                                    <option value="5">Hawaii-Aleutian</option>
                                                    <option value="6">Alaskan Time</option>
                                                    <option value="7">Atlantic Time</option>
                                                    <option value="8">Guam Time</option>
                                                </select>
                                                <div class="form-text">Required if expiration date is set</div>
                                                @error('end_datetime_timezone_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group form-group-default">
                                                <label for="observed_requested" class="form-label">Collection Type</label>
                                                <select
                                                    class="form-control @error('observed_requested') is-invalid @enderror"
                                                    name="observed_requested">
                                                    <option value="N" @selected(old('observed_requested', 'N') == 'N')>Not Observed
                                                    </option>
                                                    <option value="Y" @selected(old('observed_requested') == 'Y')>Observed</option>
                                                </select>
                                                @error('observed_requested')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group form-group-default">
                                                <label for="split_specimen_requested" class="form-label">Specimen
                                                    Type</label>
                                                <select
                                                    class="form-control @error('split_specimen_requested') is-invalid @enderror"
                                                    name="split_specimen_requested">
                                                    <option value="N" @selected(old('split_specimen_requested', 'N') == 'N')>Single Specimen
                                                    </option>
                                                    <option value="Y" @selected(old('split_specimen_requested') == 'Y')>Split Specimen
                                                    </option>
                                                </select>
                                                @error('split_specimen_requested')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="csl" class="form-label">Client Site Location (CSL)</label>
                                            <input type="text" class="form-control @error('csl') is-invalid @enderror"
                                                name="csl"
                                                value="{{ old('csl', config('services.quest.default_csl')) }}"
                                                maxlength="20">
                                            @error('csl')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="contact_name" class="form-label">DER Contact Name</label>
                                            <input type="text"
                                                class="form-control @error('contact_name') is-invalid @enderror"
                                                name="contact_name"
                                                value="{{ old('contact_name', config('services.quest.default_contact_name')) }}"
                                                maxlength="45">
                                            @error('contact_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="telephone_number" class="form-label">DER Phone Number</label>
                                            <input type="tel"
                                                class="form-control @error('telephone_number') is-invalid @enderror"
                                                name="telephone_number"
                                                value="{{ old('telephone_number', config('services.quest.default_telephone')) }}"
                                                maxlength="10">
                                            @error('telephone_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label for="order_comments" class="form-label">Special Instructions</label>
                                            <textarea class="form-control @error('order_comments') is-invalid @enderror" name="order_comments" rows="3"
                                                maxlength="250" placeholder="Enter special instructions">{{ old('order_comments') }}</textarea>
                                            @error('order_comments')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12">
                                            <label for="response_url" class="form-label">Response URL</label>
                                            <input type="url"
                                                class="form-control @error('response_url') is-invalid @enderror"
                                                name="response_url" value="{{ old('response_url') }}" maxlength="255"
                                                placeholder="Enter response URL">
                                            @error('response_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-lg px-5 py-3">
                                    <i class="fas fa-paper-plane me-2"></i>Submit to Quest Diagnostics
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show/hide DOT testing authority field
            const dotTestSelect = document.getElementById('dot_test');
            const testingAuthorityField = document.getElementById('testingAuthorityField');

            function toggleTestingAuthority() {
                if (testingAuthorityField) {
                    testingAuthorityField.style.display = dotTestSelect.value === 'T' ? 'block' : 'none';
                    const testingAuthority = document.getElementById('testing_authority');
                    if (testingAuthority) {
                        testingAuthority.required = dotTestSelect.value === 'T';
                    }
                }
            }

            if (dotTestSelect) {
                dotTestSelect.addEventListener('change', toggleTestingAuthority);
                toggleTestingAuthority();
            }

            // Initialize datepicker
            if (typeof $ !== 'undefined' && $.fn.datepicker) {
                $('.datepicker').datepicker({
                    format: 'mm/dd/yyyy',
                    autoclose: true,
                    todayHighlight: true,
                    endDate: '0d'
                });
            }

            // Validate end datetime for physical tests
            const endDateTime = document.getElementById('end_datetime');
            const isPhysical = {{ $isPhysical ? 'true' : 'false' }};

            if (endDateTime && isPhysical) {
                endDateTime.addEventListener('change', function() {
                    const selectedDate = new Date(this.value);
                    const now = new Date();
                    const maxDate = new Date(now.getTime() + (168 * 60 * 60 * 1000)); // 7 days from now

                    if (selectedDate > maxDate) {
                        alert('For physical tests, the expiration date must be within 7 days.');
                        this.value = '';
                    }
                });
            }

            // Form validation
            const form = document.getElementById('questOrderForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    let valid = true;
                    const requiredFields = form.querySelectorAll('[required]');

                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            valid = false;
                            field.classList.add('is-invalid');
                        }
                    });

                    if (!valid) {
                        e.preventDefault();
                        const firstError = form.querySelector('.is-invalid');
                        if (firstError) {
                            firstError.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }
                    }
                });

                // Remove validation on input
                const inputs = form.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.addEventListener('input', function() {
                        this.classList.remove('is-invalid');
                    });
                });
            }
        });
    </script>
@endsection
