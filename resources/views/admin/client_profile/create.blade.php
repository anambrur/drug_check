@extends('layouts.admin.master')

@section('content')
    <!-- Form row -->
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">Add Client Profile
                    <!-- Button -->
                    <a id="hoverButton" class="iyzi-btn"><i class="fas fa-camera"></i> {{ __('content.view_draft') }}</a>
                </h4>
                @if ($demo_mode == 'on')
                    <!-- Include Alert Blade -->
                    @include('admin.demo_mode.demo-mode')
                @else
                    <form action="{{ route('client-profile.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="company_name">Company Name <span class="text-red">*</span></label>
                                    <input id="company_name" name="company_name" type="text"
                                        class="form-control @error('company_name') is-invalid @enderror"
                                        placeholder="Enter company name" value="{{ old('company_name') }}" required>
                                    @error('company_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="short_description">{{ __('content.short_description') }}</label>
                                    <textarea id="short_description" name="short_description"
                                        class="form-control @error('short_description') is-invalid @enderror" placeholder="Enter short description"
                                        rows="3">{{ old('short_description') }}</textarea>
                                    @error('short_description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">Address <span class="text-red">*</span></label>
                                    <input id="address" name="address" type="text"
                                        class="form-control @error('address') is-invalid @enderror"
                                        placeholder="Enter address" value="{{ old('address') }}" required>
                                    @error('address')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="city">City <span class="text-red">*</span></label>
                                    <input id="city" name="city" type="text"
                                        class="form-control @error('city') is-invalid @enderror" placeholder="Enter city"
                                        value="{{ old('city') }}" required>
                                    @error('city')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="state">State <span class="text-red">*</span></label>
                                    <input id="state" name="state" type="text"
                                        class="form-control @error('state') is-invalid @enderror" placeholder="Enter state"
                                        value="{{ old('state') }}" required>
                                    @error('state')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="zip">Zip <span class="text-red">*</span></label>
                                    <input id="zip" name="zip" type="text"
                                        class="form-control @error('zip') is-invalid @enderror" placeholder="Enter zip code"
                                        value="{{ old('zip') }}" required>
                                    @error('zip')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input id="phone" name="phone" type="text"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        placeholder="Enter phone number" value="{{ old('phone') }}">
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="fax">Fax</label>
                                    <input id="fax" name="fax" type="text"
                                        class="form-control @error('fax') is-invalid @enderror"
                                        placeholder="Enter fax number" value="{{ old('fax') }}">
                                    @error('fax')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label for="send_email">Send Welcome Email</label>
                                <div class="form-group d-flex align-items-center">
                                    <label class="switch me-3">
                                        <input type="checkbox" id="send_email" name="send_email" value="1">
                                        <span class="slider round"></span>
                                    </label>
                                    <label for="send_email" class="mb-0 ml-2">Enable to send welcome email
                                        to the client</label>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="shipping_address">Shipping Address</label>
                                    <input id="shipping_address" name="shipping_address" type="text"
                                        class="form-control @error('shipping_address') is-invalid @enderror"
                                        placeholder="Enter shipping address" value="{{ old('shipping_address') }}">
                                    @error('shipping_address')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="billing_contact_name">Billing Contact Name</label>
                                    <input id="billing_contact_name" name="billing_contact_name" type="text"
                                        class="form-control @error('billing_contact_name') is-invalid @enderror"
                                        placeholder="Enter billing contact name"
                                        value="{{ old('billing_contact_name') }}">
                                    @error('billing_contact_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="billing_contact_email">Billing Contact Email</label>
                                    <input id="billing_contact_email" name="billing_contact_email" type="text"
                                        class="form-control @error('billing_contact_email') is-invalid @enderror"
                                        placeholder="Enter billing contact email"
                                        value="{{ old('billing_contact_email') }}">
                                    @error('billing_contact_email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="billing_contact_phone">Billing Contact Phone</label>
                                    <input id="billing_contact_phone" name="billing_contact_phone" type="text"
                                        class="form-control @error('billing_contact_phone') is-invalid @enderror"
                                        placeholder="Enter billing contact phone"
                                        value="{{ old('billing_contact_phone') }}">
                                    @error('billing_contact_phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group" id="dot-agency-group">
                                    <label for="dot_agency_id">DOT Agency</label>
                                    <select class="form-control @error('dot_agency_id') is-invalid @enderror"
                                        id="dot_agency_id" name="dot_agency_id">
                                        @foreach ($dotAgencies as $agency)
                                            <option value="{{ $agency->id }}"
                                                {{ old('dot_agency_id') == $agency->id ? 'selected' : '' }}>
                                                {{ $agency->dot_agency_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('dot_agency_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="der_contact_name">DER Contact Name<span class="text-red">*</span></label>
                                    <input id="der_contact_name" name="der_contact_name" type="text"
                                        class="form-control @error('der_contact_name') is-invalid @enderror"
                                        placeholder="Enter DER contact name" value="{{ old('der_contact_name') }}"
                                        required>
                                    @error('der_contact_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="der_contact_email">DER Contact Email<span
                                            class="text-red">*</span></label>
                                    <input id="der_contact_email" name="der_contact_email" type="text"
                                        class="form-control @error('der_contact_email') is-invalid @enderror"
                                        placeholder="Enter DER contact email" value="{{ old('der_contact_email') }}"
                                        required>
                                    @error('der_contact_email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="der_contact_phone">DER Contact Phone</label>
                                    <input id="der_contact_phone" name="der_contact_phone" type="text"
                                        class="form-control @error('der_contact_phone') is-invalid @enderror"
                                        placeholder="Enter DER contact phone" value="{{ old('der_contact_phone') }}">
                                    @error('der_contact_phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-xl-12">
                                <div class="form-group">
                                    <label for="status" class="col-form-label">{{ __('content.status') }} </label>
                                    <select class="form-control" name="status" id="status">
                                        <option value="active" selected>{{ __('content.select_your_option') }}</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary mr-2">{{ __('content.submit') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Custom Switch Styles */
        .switch {
            position: relative;
            display: inline-block;
            width: 36px;
            height: 19px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 11px;
            width: 11px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #2196F3;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.slider:before {
            transform: translateX(17px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
    <!-- end row -->
@endsection
