@extends('layouts.frontend.master2')

@section('content')
    <div class="container py-5 mt-5">
        <div class="row justify-content-center mt-5">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">
                            <i class="fas fa-flask me-2"></i> Quest Diagnostics Order Form
                        </h3>
                        <p class="mb-0">Complete the form to schedule your {{ $paymentData['portfolio']->title }} test</p>
                    </div>

                    <div class="card-body p-4">
                        <!-- Progress Steps -->
                        <div class="steps mb-5">
                            <div class="step completed">
                                <div class="step-number">1</div>
                                <div class="step-title">Payment</div>
                            </div>
                            <div class="step active">
                                <div class="step-number">2</div>
                                <div class="step-title">Test Information</div>
                            </div>
                            <div class="step">
                                <div class="step-number">3</div>
                                <div class="step-title">Confirmation</div>
                            </div>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('quest.submit-order') }}">
                            @csrf
                            <input type="hidden" name="portfolio_id" value="{{ $paymentData['portfolio']->id }}">
                            <input type="hidden" name="payment_intent_id" value="{{ $paymentData['payment_intent_id'] }}">

                            <!-- ========== Personal Information Section ========== -->
                            <div class="mb-5">
                                <h4 class="section-title mb-4">
                                    <i class="fas fa-user-circle me-2"></i> Personal Information
                                </h4>

                                <div class="row g-3">
                                    <!-- First Name -->
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text"
                                                class="form-control @error('first_name') is-invalid @enderror"
                                                name="first_name" id="first_name"
                                                value="{{ old('first_name', $paymentData['first_name']) }}"
                                                placeholder="First name" required>
                                            <label for="first_name">First Name *</label>
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Last Name -->
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text"
                                                class="form-control @error('last_name') is-invalid @enderror"
                                                name="last_name" id="last_name"
                                                value="{{ old('last_name', $paymentData['last_name']) }}"
                                                placeholder="Last name" required>
                                            <label for="last_name">Last Name *</label>
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                name="email" id="email"
                                                value="{{ old('email', $paymentData['email']) }}"
                                                placeholder="Email address" required>
                                            <label for="email">Email Address *</label>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Phone -->
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                                name="phone" id="phone"
                                                value="{{ old('phone', $paymentData['phone']) }}"
                                                placeholder="Phone number" required>
                                            <label for="phone">Phone Number *</label>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Date of Birth -->
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text"
                                                class="form-control datepicker @error('dob') is-invalid @enderror"
                                                name="dob" id="dob" value="{{ old('dob') }}"
                                                placeholder="MM/DD/YYYY" autocomplete="off">
                                            <label for="dob">Date of Birth (MM/DD/YYYY)</label>
                                            @error('dob')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Primary ID -->
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text"
                                                class="form-control @error('primary_id') is-invalid @enderror"
                                                name="primary_id" id="primary_id" value="{{ old('primary_id') }}"
                                                placeholder="Driver's License or ID" required>
                                            <label for="primary_id">Primary ID (Driver's License/ID) *</label>
                                            @error('primary_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Primary ID Type -->
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select @error('primary_id_type') is-invalid @enderror"
                                                name="primary_id_type" id="primary_id_type">
                                                <option value="">Select ID Type</option>
                                                <option value="DL" @selected(old('primary_id_type') == 'DL')>Driver's License
                                                </option>
                                                <option value="OTHER" @selected(old('primary_id_type') == 'OTHER')>Other Government ID
                                                </option>
                                            </select>
                                            <label for="primary_id_type">Primary ID Type</label>
                                            @error('primary_id_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Zip Code -->
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text"
                                                class="form-control @error('zip_code') is-invalid @enderror"
                                                name="zip_code" id="zip_code" value="{{ old('zip_code') }}"
                                                placeholder="Zip Code">
                                            <label for="zip_code">Zip Code (for site search)</label>
                                            @error('zip_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ========== Test Information Section ========== -->
                            <div class="mb-5">
                                <h4 class="section-title mb-4">
                                    <i class="fas fa-flask me-2"></i> Test Information
                                </h4>

                                <div class="row g-3">
                                    <!-- DOT Test -->
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select @error('dot_test') is-invalid @enderror"
                                                name="dot_test" id="dot_test" required>
                                                <option value="F" @selected(old('dot_test', 'F') == 'F')>Non-DOT Test</option>
                                                <option value="T" @selected(old('dot_test') == 'T')>DOT Test</option>
                                            </select>
                                            <label for="dot_test">Test Type *</label>
                                            @error('dot_test')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Testing Authority (shown only for DOT) -->
                                    <div class="col-md-6" id="testingAuthorityField" style="display: none;">
                                        <div class="form-floating">
                                            <select class="form-select @error('testing_authority') is-invalid @enderror"
                                                name="testing_authority" id="testing_authority">
                                                <option value="">Select Authority</option>
                                                <option value="FMCSA" @selected(old('testing_authority') == 'FMCSA')>FMCSA</option>
                                                <option value="PHMSA" @selected(old('testing_authority') == 'PHMSA')>PHMSA</option>
                                                <option value="FAA" @selected(old('testing_authority') == 'FAA')>FAA</option>
                                                <option value="FTA" @selected(old('testing_authority') == 'FTA')>FTA</option>
                                                <option value="FRA" @selected(old('testing_authority') == 'FRA')>FRA</option>
                                                <option value="USCG" @selected(old('testing_authority') == 'USCG')>USCG</option>
                                            </select>
                                            <label for="testing_authority">DOT Testing Authority *</label>
                                            @error('testing_authority')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Reason for Test -->
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select @error('reason_for_test_id') is-invalid @enderror"
                                                name="reason_for_test_id" id="reason_for_test_id" required>
                                                <option value="1" @selected(old('reason_for_test_id') == '1')>Pre-Employment
                                                </option>
                                                <option value="2" @selected(old('reason_for_test_id') == '2')>Post Accident</option>
                                                <option value="3" @selected(old('reason_for_test_id') == '3')>Random</option>
                                                <option value="5" @selected(old('reason_for_test_id') == '5')>Reasonable
                                                    Suspicion/Cause</option>
                                                <option value="6" @selected(old('reason_for_test_id') == '6')>Return to Duty
                                                </option>
                                                <option value="23" @selected(old('reason_for_test_id') == '23')>Follow-Up</option>
                                                <option value="99" @selected(old('reason_for_test_id') == '99')>Other</option>
                                            </select>
                                            <label for="reason_for_test_id">Reason for Test *</label>
                                            @error('reason_for_test_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Physical Reason (if physical test) -->
                                    @if (str_contains(strtolower($paymentData['portfolio']->title), 'physical'))
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select
                                                    class="form-select @error('physical_reason_for_test_id') is-invalid @enderror"
                                                    name="physical_reason_for_test_id" id="physical_reason_for_test_id">
                                                    <option value="">Select Physical Reason</option>
                                                    <option value="NC" @selected(old('physical_reason_for_test_id') == 'NC')>New Certification
                                                    </option>
                                                    <option value="RE" @selected(old('physical_reason_for_test_id') == 'RE')>Recertification
                                                    </option>
                                                    <option value="FU" @selected(old('physical_reason_for_test_id') == 'FU')>Follow-Up</option>
                                                    <option value="OT" @selected(old('physical_reason_for_test_id') == 'OT')>Other</option>
                                                    <option value="SA" @selected(old('physical_reason_for_test_id') == 'SA')>Site Access
                                                    </option>
                                                    <option value="PE" @selected(old('physical_reason_for_test_id') == 'PE')>Pre-employment
                                                    </option>
                                                    <option value="RD" @selected(old('physical_reason_for_test_id') == 'RD')>Return to Duty
                                                    </option>
                                                    <option value="SU" @selected(old('physical_reason_for_test_id') == 'SU')>Surveillance
                                                    </option>
                                                </select>
                                                <label for="physical_reason_for_test_id">Physical Reason</label>
                                                @error('physical_reason_for_test_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Collection Site ID -->
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text"
                                                class="form-control @error('collection_site_id') is-invalid @enderror"
                                                name="collection_site_id" id="collection_site_id"
                                                value="{{ old('collection_site_id') }}" placeholder="Collection Site ID">
                                            <label for="collection_site_id">Collection Site ID (optional)</label>
                                            <small class="text-muted">Leave blank to let donor choose</small>
                                            @error('collection_site_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- End Date/Time -->
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="datetime-local"
                                                class="form-control @error('end_datetime') is-invalid @enderror"
                                                name="end_datetime" id="end_datetime" value="{{ old('end_datetime') }}"
                                                placeholder="Expiration Date/Time">
                                            <label for="end_datetime">Order Expiration Date/Time</label>
                                            @error('end_datetime')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Timezone -->
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select
                                                class="form-select @error('end_datetime_timezone_id') is-invalid @enderror"
                                                name="end_datetime_timezone_id" id="end_datetime_timezone_id">
                                                <option value="1" @selected(old('end_datetime_timezone_id') == '1')>Eastern Time</option>
                                                <option value="2" @selected(old('end_datetime_timezone_id') == '2')>Central Time</option>
                                                <option value="3" @selected(old('end_datetime_timezone_id') == '3')>Mountain Time</option>
                                                <option value="4" @selected(old('end_datetime_timezone_id') == '4')>Pacific Time</option>
                                                <option value="5" @selected(old('end_datetime_timezone_id') == '5')>Hawaii-Aleutian
                                                </option>
                                                <option value="6" @selected(old('end_datetime_timezone_id') == '6')>Alaskan Time</option>
                                                <option value="7" @selected(old('end_datetime_timezone_id') == '7')>Atlantic Time</option>
                                                <option value="8" @selected(old('end_datetime_timezone_id') == '8')>Guam Time</option>
                                            </select>
                                            <label for="end_datetime_timezone_id">Timezone</label>
                                            @error('end_datetime_timezone_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Observed Requested -->
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select @error('observed_requested') is-invalid @enderror"
                                                name="observed_requested" id="observed_requested">
                                                <option value="N" @selected(old('observed_requested', 'N') == 'N')>Not Observed</option>
                                                <option value="Y" @selected(old('observed_requested') == 'Y')>Observed</option>
                                            </select>
                                            <label for="observed_requested">Collection Type</label>
                                            @error('observed_requested')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Split Specimen -->
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select
                                                class="form-select @error('split_specimen_requested') is-invalid @enderror"
                                                name="split_specimen_requested" id="split_specimen_requested">
                                                <option value="N" @selected(old('split_specimen_requested', 'N') == 'N')>Single Specimen
                                                </option>
                                                <option value="Y" @selected(old('split_specimen_requested') == 'Y')>Split Specimen
                                                </option>
                                            </select>
                                            <label for="split_specimen_requested">Specimen Type</label>
                                            @error('split_specimen_requested')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Unit Codes (hidden, set based on portfolio) -->
                                    <input type="hidden" name="unit_codes[]"
                                        value="{{ $paymentData['portfolio']->quest_unit_code }}">

                                    <!-- Lab Account (from config) -->
                                    <input type="hidden" name="lab_account" value="{{ env('QUEST_LAB_ACCOUNT') }}">

                                    <!-- CSL (Client Site Location) -->
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('csl') is-invalid @enderror"
                                                name="csl" id="csl"
                                                value="{{ old('csl', config('services.quest.default_csl')) }}"
                                                placeholder="CSL">
                                            <label for="csl">Client Site Location (CSL)</label>
                                            @error('csl')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Contact Name (DER) -->
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text"
                                                class="form-control @error('contact_name') is-invalid @enderror"
                                                name="contact_name" id="contact_name"
                                                value="{{ old('contact_name', config('services.quest.default_contact_name')) }}"
                                                placeholder="Contact Name" required>
                                            <label for="contact_name">DER Contact Name *</label>
                                            @error('contact_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Telephone Number (DER) -->
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="tel"
                                                class="form-control @error('telephone_number') is-invalid @enderror"
                                                name="telephone_number" id="telephone_number"
                                                value="{{ old('telephone_number', config('services.quest.default_telephone')) }}"
                                                placeholder="Phone number" required>
                                            <label for="telephone_number">DER Phone Number *</label>
                                            @error('telephone_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Order Comments -->
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control @error('order_comments') is-invalid @enderror" name="order_comments"
                                                id="order_comments" placeholder="Special instructions" style="height: 100px">{{ old('order_comments') }}</textarea>
                                            <label for="order_comments">Special Instructions</label>
                                            @error('order_comments')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-lg px-5 py-3">
                                    <i class="fas fa-paper-plane me-2"></i> Submit to Quest Diagnostics
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show/hide DOT testing authority field based on DOT test selection
            const dotTestSelect = document.getElementById('dot_test');
            const testingAuthorityField = document.getElementById('testingAuthorityField');

            function toggleTestingAuthority() {
                testingAuthorityField.style.display = dotTestSelect.value === 'T' ? 'block' : 'none';
                document.getElementById('testing_authority').required = dotTestSelect.value === 'T';
            }

            dotTestSelect.addEventListener('change', toggleTestingAuthority);
            toggleTestingAuthority(); // Initialize

            // Initialize datepicker for DOB
            $('.datepicker').datepicker({
                format: 'mm/dd/yyyy',
                autoclose: true,
                todayHighlight: true,
                endDate: '0d'
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        /* Progress Steps */
        .steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }

        .steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #dee2e6;
            z-index: 1;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #dee2e6;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .step-title {
            font-size: 0.875rem;
            color: #6c757d;
            text-align: center;
        }

        .step.active .step-number {
            background-color: #0d6efd;
            color: white;
        }

        .step.active .step-title {
            color: #0d6efd;
            font-weight: 500;
        }

        .step.completed .step-number {
            background-color: #198754;
            color: white;
        }

        .step.completed .step-title {
            color: #198754;
        }

        /* Section titles */
        .section-title {
            color: #2c3e50;
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }

        /* Form floating labels adjustments */
        .form-floating label {
            color: #6c757d;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .steps {
                flex-direction: column;
                align-items: flex-start;
            }

            .steps::before {
                display: none;
            }

            .step {
                flex-direction: row;
                margin-bottom: 1rem;
            }

            .step-number {
                margin-right: 1rem;
                margin-bottom: 0;
            }
        }
    </style>
@endpush
