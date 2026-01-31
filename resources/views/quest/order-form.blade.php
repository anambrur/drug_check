@extends('layouts.frontend.master2')

@section('content')
    <style>
        .modern-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 2rem 0 3rem;
            padding: 0 3rem;
        }

        .modern-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 60px;
            right: 60px;
            height: 4px;
            background-color: #e9ecef;
            z-index: 1;
        }

        .modern-steps .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .modern-steps .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .modern-steps .step-title {
            font-size: 0.85rem;
            font-weight: 500;
            color: #6c757d;
            text-align: center;
            transition: all 0.3s ease;
        }

        .modern-steps .step.completed .step-number,
        .modern-steps .step.active .step-number {
            background-color: #0066cc;
            color: white;
        }

        .modern-steps .step.completed .step-title,
        .modern-steps .step.active .step-title {
            color: #0066cc;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .modern-steps {
                padding: 0 1rem;
            }

            .modern-steps::before {
                left: 30px;
                right: 30px;
            }
        }

        /* Additional styles for the rest of your form */
        .modern-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .modern-card-header {
            background: linear-gradient(135deg, #0066cc 0%, #004d99 100%);
            border-bottom: none;
            padding: 1.5rem 2rem;
            color: white;
        }

        .section-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .section-header {
            background-color: #e6f0ff;
            padding: 1rem 1.5rem;
            font-weight: 600;
            color: #0066cc;
            border-bottom: 1px solid rgba(0, 102, 204, 0.1);
        }

        .section-content {
            padding: 1.5rem;
        }

        .modern-form-group {
            /* margin-bottom: 1.5rem; */
        }

        .modern-form-group .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #495057;
        }

        .required-asterisk {
            color: #dc3545;
        }

        .modern-input,
        .modern-select,
        .modern-textarea {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .modern-input:focus,
        .modern-select:focus,
        .modern-textarea:focus {
            border-color: #0066cc;
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
        }

        .modern-btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.75rem 2rem;
            transition: all 0.3s ease;
        }

        .modern-btn-primary {
            background: linear-gradient(135deg, #0066cc 0%, #004d99 100%);
            border: none;
        }

        .modern-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 102, 204, 0.3);
        }

        .modern-alert {
            border-radius: 8px;
            border: none;
            box-shadow: 0 2px 10px rgba(220, 53, 69, 0.1);
        }

        .form-text {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
    </style>
    <div class="container py-5 mt-5">
        <div class="row justify-content-center mt-3">
            <div class="col-lg-10">
                <div class="card modern-card">
                    <div class="card-header modern-card-header">
                        <div class="d-flex align-items-center">
                            <div class="header-content">
                                <h4 class="mb-1">Quest Diagnostics Order Form</h4>
                                <p class="mb-0">Complete the form to schedule your {{ $paymentData['portfolio']->title }}
                                    test</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <!-- Progress Steps -->
                        <div class="modern-steps mb-5">
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
                            <div class="alert alert-danger alert-dismissible fade show modern-alert" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('quest.submit-order') }}" id="questOrderForm">
                            @csrf
                            <input type="hidden" name="portfolio_id" value="{{ $paymentData['portfolio']->id }}">
                            <input type="hidden" name="payment_intent_id" value="{{ $paymentData['payment_intent_id'] }}">
                            <input type="hidden" name="lab_account" value="{{ $paymentData['portfolio']->quest_lab_account }}">
                            <input type="hidden" name="is_physical"
                                value="{{ str_contains(strtolower($paymentData['portfolio']->title), 'physical') ? 'true' : 'false' }}">
                            <input type="hidden" name="is_ebat"
                                value="{{ str_contains(strtolower($paymentData['portfolio']->title), 'ebat') ? 'true' : 'false' }}">

                           
                            <!-- ========== Personal Information Section ========== -->
                            <div class="section-container mb-5">
                                <div class="section-header">
                                    <i class="fas fa-user-circle me-2"></i> Personal Information
                                </div>
                                <div class="section-content">
                                    <div class="row g-4">
                                        <!-- First Name (Required) -->
                                        <div class="col-md-6">
                                            <div class="modern-form-group">
                                                <label for="first_name" class="form-label">First Name <span
                                                        class="required-asterisk">*</span></label>
                                                <input type="text"
                                                    class="form-control modern-input @error('first_name') is-invalid @enderror"
                                                    name="first_name" id="first_name"
                                                    value="{{ old('first_name', $paymentData['first_name']) }}"
                                                    placeholder="Enter first name" required maxlength="20">
                                                @error('first_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Last Name (Required) -->
                                        <div class="col-md-6">
                                            <div class="modern-form-group">
                                                <label for="last_name" class="form-label">Last Name <span
                                                        class="required-asterisk">*</span></label>
                                                <input type="text"
                                                    class="form-control modern-input @error('last_name') is-invalid @enderror"
                                                    name="last_name" id="last_name"
                                                    value="{{ old('last_name', $paymentData['last_name']) }}"
                                                    placeholder="Enter last name" required maxlength="25">
                                                @error('last_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Middle Name (Optional) -->
                                        <div class="col-md-6">
                                            <div class="modern-form-group">
                                                <label for="middle_name" class="form-label">Middle Name</label>
                                                <input type="text"
                                                    class="form-control modern-input @error('middle_name') is-invalid @enderror"
                                                    name="middle_name" id="middle_name" value="{{ old('middle_name') }}"
                                                    placeholder="Enter middle name" maxlength="20">
                                                @error('middle_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Primary ID (Required) -->
                                        <div class="col-md-6">
                                            <div class="modern-form-group">
                                                <label for="primary_id" class="form-label">Primary ID (Driver's License/ID)
                                                    <span class="required-asterisk">*</span></label>
                                                <input type="text"
                                                    class="form-control modern-input @error('primary_id') is-invalid @enderror"
                                                    name="primary_id" id="primary_id" value="{{ old('primary_id') }}"
                                                    placeholder="Enter driver's license or ID" required maxlength="25">
                                                @error('primary_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="col-md-6">
                                            <div class="modern-form-group">
                                                <label for="email" class="form-label">Email Address <span
                                                        class="required-asterisk">*</span></label>
                                                <input type="email"
                                                    class="form-control modern-input @error('email') is-invalid @enderror"
                                                    name="email" id="email"
                                                    value="{{ old('email', $paymentData['email']) }}"
                                                    placeholder="Enter email address" required maxlength="254">
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Primary ID Type (Optional - Only for Physical) -->
                                        @if (str_contains(strtolower($paymentData['portfolio']->title), 'physical'))
                                            <div class="col-md-6">
                                                <div class="modern-form-group">
                                                    <label for="primary_id_type" class="form-label">Primary ID
                                                        Type</label>
                                                    <select
                                                        class="form-select modern-select @error('primary_id_type') is-invalid @enderror"
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
                                            </div>
                                        @endif

                                        <!-- Date of Birth (Optional) -->
                                        <div class="col-md-6">
                                            <div class="modern-form-group">
                                                <label for="dob" class="form-label">Date of Birth</label>
                                                <input type="text"
                                                    class="form-control modern-input datepicker @error('dob') is-invalid @enderror"
                                                    name="dob" id="dob" value="{{ old('dob') }}"
                                                    placeholder="MM/DD/YYYY" autocomplete="off">
                                                @error('dob')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Primary Phone (Optional) -->
                                        <div class="col-md-6">
                                            <div class="modern-form-group">
                                                <label for="primary_phone" class="form-label">Primary Phone</label>
                                                <input type="tel"
                                                    class="form-control modern-input @error('primary_phone') is-invalid @enderror"
                                                    name="primary_phone" id="primary_phone"
                                                    value="{{ old('primary_phone', $paymentData['phone']) }}"
                                                    placeholder="Enter primary phone number">
                                                @error('primary_phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Secondary Phone (Optional) -->
                                        <div class="col-md-6">
                                            <div class="modern-form-group">
                                                <label for="secondary_phone" class="form-label">Secondary Phone</label>
                                                <input type="tel"
                                                    class="form-control modern-input @error('secondary_phone') is-invalid @enderror"
                                                    name="secondary_phone" id="secondary_phone"
                                                    value="{{ old('secondary_phone') }}"
                                                    placeholder="Enter secondary phone number">
                                                @error('secondary_phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Zip Code (Optional - Only for Physical) -->
                                        @if (str_contains(strtolower($paymentData['portfolio']->title), 'physical'))
                                            <div class="col-md-6">
                                                <div class="modern-form-group">
                                                    <label for="zip_code" class="form-label">Zip Code</label>
                                                    <input type="text"
                                                        class="form-control modern-input @error('zip_code') is-invalid @enderror"
                                                        name="zip_code" id="zip_code" value="{{ old('zip_code') }}"
                                                        placeholder="Enter zip code for site search">
                                                    @error('zip_code')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Email Address (Required for Physical) -->
                                        {{-- @if (str_contains(strtolower($paymentData['portfolio']->title), 'physical'))
                                            <div class="col-md-6">
                                                <div class="modern-form-group">
                                                    <label for="email" class="form-label">Email Address <span
                                                            class="required-asterisk">*</span></label>
                                                    <input type="email"
                                                        class="form-control modern-input @error('email') is-invalid @enderror"
                                                        name="email" id="email"
                                                        value="{{ old('email', $paymentData['email']) }}"
                                                        placeholder="Enter email address" required maxlength="254">
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif --}}
                                    </div>
                                </div>
                            </div>

                            <!-- ========== Test Information Section ========== -->
                            <div class="section-container mb-5">
                                <div class="section-header">
                                    <i class="fas fa-flask me-2"></i> Test Information
                                </div>
                                <div class="section-content">
                                    <div class="row g-4">
                                        <!-- DOT Test (Required) -->
                                        <div class="col-md-6">
                                            <div class="modern-form-group">
                                                <label for="dot_test" class="form-label">Test Type <span
                                                        class="required-asterisk">*</span></label>
                                                <select
                                                    class="form-select modern-select @error('dot_test') is-invalid @enderror"
                                                    name="dot_test" id="dot_test" required>
                                                    <option value="F" @selected(old('dot_test', 'F') == 'F')>Non-DOT Test
                                                    </option>
                                                    <option value="T" @selected(old('dot_test') == 'T')>DOT Test</option>
                                                </select>
                                                @error('dot_test')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="modern-form-group">
                                                <label for="collection_site_id" class="form-label">Collection site</label>
                                                <select name="collection_site_id" id="collection_site_id"
                                                    class="form-control select2-collection-sites">
                                                    <option value="">Select a collection site...</option>
                                                </select>
                                                @error('collection_site_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Testing Authority (Required if DOT) -->
                                        <div class="col-md-6" id="testingAuthorityField" style="display: none;">
                                            <div class="modern-form-group">
                                                <label for="testing_authority" class="form-label">DOT Testing Authority
                                                    <span class="required-asterisk">*</span></label>
                                                <select
                                                    class="form-select modern-select @error('testing_authority') is-invalid @enderror"
                                                    name="testing_authority" id="testing_authority">
                                                    <option value="">Select Authority</option>
                                                    <option value="FMCSA" @selected(old('testing_authority') == 'FMCSA')>FMCSA</option>
                                                    <option value="PHMSA" @selected(old('testing_authority') == 'PHMSA')>PHMSA</option>
                                                    <option value="FAA" @selected(old('testing_authority') == 'FAA')>FAA</option>
                                                    <option value="FTA" @selected(old('testing_authority') == 'FTA')>FTA</option>
                                                    <option value="FRA" @selected(old('testing_authority') == 'FRA')>FRA</option>
                                                    <option value="USCG" @selected(old('testing_authority') == 'USCG')>USCG</option>
                                                </select>
                                                @error('testing_authority')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Reason for Test ID (Required for Drug/eBAT) -->
                                        @if (!str_contains(strtolower($paymentData['portfolio']->title), 'physical'))
                                            <div class="col-md-6">
                                                <div class="modern-form-group">
                                                    <label for="reason_for_test_id" class="form-label">Reason for Test
                                                        <span class="required-asterisk">*</span></label>
                                                    <select
                                                        class="form-select modern-select @error('reason_for_test_id') is-invalid @enderror"
                                                        name="reason_for_test_id" id="reason_for_test_id" required>
                                                        <option value="1" @selected(old('reason_for_test_id') == '1')>Pre-Employment
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
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Physical Reason for Test ID (Required for Physical) -->
                                        @if (str_contains(strtolower($paymentData['portfolio']->title), 'physical'))
                                            <div class="col-md-6">
                                                <div class="modern-form-group">
                                                    <label for="physical_reason_for_test_id" class="form-label">Physical
                                                        Reason <span class="required-asterisk">*</span></label>
                                                    <select
                                                        class="form-select modern-select @error('physical_reason_for_test_id') is-invalid @enderror"
                                                        name="physical_reason_for_test_id"
                                                        id="physical_reason_for_test_id" required>
                                                        <option value="">Select Physical Reason</option>
                                                        <option value="NC" @selected(old('physical_reason_for_test_id') == 'NC')>New
                                                            Certification</option>
                                                        <option value="RE" @selected(old('physical_reason_for_test_id') == 'RE')>
                                                            Recertification</option>
                                                        <option value="FU" @selected(old('physical_reason_for_test_id') == 'FU')>Follow-Up
                                                        </option>
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
                                                    @error('physical_reason_for_test_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Collection Site ID (Optional - Not allowed for Physical) -->
                                        {{-- @if (!str_contains(strtolower($paymentData['portfolio']->title), 'physical'))
                                            <div class="col-md-6">
                                                <div class="modern-form-group">
                                                    <label for="collection_site_id" class="form-label">Collection Site
                                                        ID</label>
                                                    <input type="text"
                                                        class="form-control modern-input @error('collection_site_id') is-invalid @enderror"
                                                        name="collection_site_id" id="collection_site_id"
                                                        value="{{ old('collection_site_id') }}"
                                                        placeholder="Enter collection site ID" maxlength="6">
                                                    <div class="form-text">Leave blank to let donor choose</div>
                                                    @error('collection_site_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif --}}

                                        <!-- End Date/Time (Optional) -->
                                        <div class="col-md-6">
                                            <div class="modern-form-group">
                                                <label for="end_datetime" class="form-label">Order Expiration
                                                    Date/Time</label>
                                                <input type="datetime-local"
                                                    class="form-control modern-input @error('end_datetime') is-invalid @enderror"
                                                    name="end_datetime" id="end_datetime"
                                                    value="{{ old('end_datetime') }}" placeholder="Expiration Date/Time">
                                                <div class="form-text">For ePhysical, must be within 7 days</div>
                                                @error('end_datetime')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Timezone (Required if EndDateTime specified) -->
                                        <div class="col-md-6">
                                            <div class="modern-form-group">
                                                <label for="end_datetime_timezone_id" class="form-label">Timezone</label>
                                                <select
                                                    class="form-select modern-select @error('end_datetime_timezone_id') is-invalid @enderror"
                                                    name="end_datetime_timezone_id" id="end_datetime_timezone_id">
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
                                                <div class="form-text">Required if expiration date is set</div>
                                                @error('end_datetime_timezone_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Observed Requested (Optional) -->
                                        <div class="col-md-6">
                                            <div class="modern-form-group">
                                                <label for="observed_requested" class="form-label">Collection Type</label>
                                                <select
                                                    class="form-select modern-select @error('observed_requested') is-invalid @enderror"
                                                    name="observed_requested" id="observed_requested">
                                                    <option value="N" @selected(old('observed_requested', 'N') == 'N')>Not Observed
                                                    </option>
                                                    <option value="Y" @selected(old('observed_requested') == 'Y')>Observed</option>
                                                </select>
                                                @error('observed_requested')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Split Specimen Requested (Optional) -->
                                        <div class="col-md-6">
                                            <div class="modern-form-group">
                                                <label for="split_specimen_requested" class="form-label">Specimen
                                                    Type</label>
                                                <select
                                                    class="form-select modern-select @error('split_specimen_requested') is-invalid @enderror"
                                                    name="split_specimen_requested" id="split_specimen_requested">
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

                                        <!-- Unit Codes (Required - Hidden, set from portfolio) -->
                                        <input type="hidden" name="unit_codes[]"
                                            value="{{ $paymentData['portfolio']->quest_unit_code }}">

                                        <!-- CSL (Client Site Location - Optional) -->
                                        <div class="col-md-6">
                                            <div class="modern-form-group">
                                                <label for="csl" class="form-label">Client Site Location
                                                    (CSL)</label>
                                                <input type="text"
                                                    class="form-control modern-input @error('csl') is-invalid @enderror"
                                                    name="csl" id="csl"
                                                    value="{{ old('csl', config('services.quest.default_csl')) }}"
                                                    placeholder="Enter CSL" maxlength="20">
                                                @error('csl')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Contact Name (Required for eBAT) -->
                                        <div class="col-md-6">
                                            <div class="modern-form-group">
                                                <label for="contact_name" class="form-label">DER Contact Name</label>
                                                <input type="text"
                                                    class="form-control modern-input @error('contact_name') is-invalid @enderror"
                                                    name="contact_name" id="contact_name"
                                                    value="{{ old('contact_name', config('services.quest.default_contact_name')) }}"
                                                    placeholder="Enter contact name" maxlength="45">
                                                @error('contact_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Telephone Number (Required for eBAT) -->
                                        <div class="col-md-6">
                                            <div class="modern-form-group">
                                                <label for="telephone_number" class="form-label">DER Phone Number</label>
                                                <input type="tel"
                                                    class="form-control modern-input @error('telephone_number') is-invalid @enderror"
                                                    name="telephone_number" id="telephone_number"
                                                    value="{{ old('telephone_number', config('services.quest.default_telephone')) }}"
                                                    placeholder="Enter phone number" maxlength="10">
                                                @error('telephone_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Order Comments (Optional) -->
                                        <div class="col-12">
                                            <div class="modern-form-group">
                                                <label for="order_comments" class="form-label">Special
                                                    Instructions</label>
                                                <textarea class="form-control modern-textarea @error('order_comments') is-invalid @enderror" name="order_comments"
                                                    id="order_comments" placeholder="Enter special instructions" style="height: 100px" maxlength="250">{{ old('order_comments') }}</textarea>
                                                @error('order_comments')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Response URL (Optional) -->
                                        <div class="col-12">
                                            <div class="modern-form-group">
                                                <label for="response_url" class="form-label">Response URL</label>
                                                <input type="url"
                                                    class="form-control modern-input @error('response_url') is-invalid @enderror"
                                                    name="response_url" id="response_url"
                                                    value="{{ old('response_url') }}" placeholder="Enter response URL"
                                                    maxlength="255">
                                                @error('response_url')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center mt-4">
                                <button type="submit" class="btn modern-btn btn-primary btn-lg px-5 py-3">
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
                if (testingAuthorityField) {
                    testingAuthorityField.style.display = dotTestSelect.value === 'T' ? 'block' : 'none';
                    if (document.getElementById('testing_authority')) {
                        document.getElementById('testing_authority').required = dotTestSelect.value === 'T';
                    }
                }
            }

            if (dotTestSelect) {
                dotTestSelect.addEventListener('change', toggleTestingAuthority);
                toggleTestingAuthority(); // Initialize
            }

            // Initialize datepicker for DOB
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
            const isPhysical = "{{ str_contains(strtolower($paymentData['portfolio']->title), 'physical') }}" ===
                "1";

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

            // Add form validation and enhance UX
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
                        // Scroll to first error
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
        // Select2 initialization - SIMPLE SOLUTION
        $(document).ready(function() {
            // Initialize Select2 with AJAX
            $('.select2-collection-sites').select2({
                placeholder: 'Search for collection site by name, address, city, zip...',
                allowClear: true,
                minimumInputLength: 2,
                width: '100%',
                ajax: {
                    url: '{{ route('collection-sites.search') }}',
                    type: 'GET',
                    dataType: 'json',
                    delay: 500,
                    data: function(params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        // Use collection_site_code as the ID/value
                        const mappedData = data.map(function(site) {
                            return {
                                id: site.collection_site_code, // This is what will be submitted
                                text: site.text + ' (' + site.collection_site_code +
                                    ')', // Show code in display
                                collection_site_code: site.collection_site_code,
                                original_data: site // Keep all original data
                            };
                        });

                        return {
                            results: mappedData,
                            pagination: {
                                more: (params.page * 50) < 1000
                            }
                        };
                    },
                    cache: true
                },
                templateResult: function(site) {
                    if (site.loading) {
                        return $('<div class="loading-results">Searching...</div>');
                    }

                    return $(
                        '<div class="select2-site-result">' +
                        '<div class="site-name"><strong>' + site.text + '</strong></div>' +
                        '<div class="site-code"><small>Code: ' + site.collection_site_code +
                        '</small></div>' +
                        '</div>'
                    );
                },
                escapeMarkup: function(markup) {
                    return markup;
                }
            });
        });
    </script>
@endpush
