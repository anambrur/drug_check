@extends('layouts.admin.master')

@section('content')
    <!-- Form row -->
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <div class="d-md-flex justify-content-between align-items-center mb-20">
                    <h4 class="card-title">Edit Client Profile
                        <!-- Button -->
                        <a id="hoverButton" class="iyzi-btn"><i class="fas fa-camera"></i> {{ __('content.view_draft') }}</a>
                        <!-- Modal -->
                        {{-- <div id="imageModal" class="border border-success iyzi-modal">
                            <img class="img-fluid " src="{{ asset('uploads/img/dummy/style/clientProfile-style1.jpg') }}"
                                alt="draft image">
                        </div> --}}
                    </h4>
                    <div>
                        <a href="{{ url()->previous() }}" class="btn btn-primary"><i class="fas fa-angle-left"></i>
                            {{ __('content.back') }}</a>
                    </div>
                </div>
                @if ($demo_mode == 'on')
                    <!-- Include Alert Blade -->
                    @include('admin.demo_mode.demo-mode')
                @else
                    <form action="{{ route('client-profile.update', $clientProfile->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @method('PUT')
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
                                        placeholder="Enter company name"
                                        value="{{ old('company_name', $clientProfile->company_name) }}" required>
                                    @error('company_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="account_no">Lab Account Number <span class="text-red">*</span></label>
                                    <input id="account_no" name="account_no" type="text"
                                        class="form-control @error('account_no') is-invalid @enderror"
                                        placeholder="Enter Lab account number"
                                        value="{{ old('account_no', $clientProfile->account_no) }}" required>
                                    @error('account_no')
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
                                        class="form-control @error('short_description') is-invalid @enderror" rows="3"
                                        placeholder="Enter short description">{{ old('short_description', $clientProfile->short_description) }}</textarea>
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
                                        placeholder="Enter address" value="{{ old('address', $clientProfile->address) }}"
                                        required>
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
                                        value="{{ old('city', $clientProfile->city) }}" required>
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
                                        value="{{ old('state', $clientProfile->state) }}" required>
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
                                        value="{{ old('zip', $clientProfile->zip) }}" required>
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
                                        placeholder="Enter phone number"
                                        value="{{ old('phone', $clientProfile->phone) }}">
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
                                        placeholder="Enter fax number" value="{{ old('fax', $clientProfile->fax) }}">
                                    @error('fax')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
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
                                        placeholder="Enter shipping address"
                                        value="{{ old('shipping_address', $clientProfile->shipping_address) }}">
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
                                        value="{{ old('billing_contact_name', $clientProfile->billing_contact_name) }}">
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
                                        value="{{ old('billing_contact_email', $clientProfile->billing_contact_email) }}">
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
                                        value="{{ old('billing_contact_phone', $clientProfile->billing_contact_phone) }}">
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
                                                {{ old('dot_agency_id', $clientProfile->dot_agency_id) == $agency->id ? 'selected' : '' }}>
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
                                        placeholder="Enter DER contact name"
                                        value="{{ old('der_contact_name', $clientProfile->der_contact_name) }}" required>
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
                                        placeholder="Enter DER contact email"
                                        value="{{ old('der_contact_email', $clientProfile->der_contact_email) }}"
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
                                        placeholder="Enter DER contact phone"
                                        value="{{ old('der_contact_phone', $clientProfile->der_contact_phone) }}">
                                    @error('der_contact_phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="client_start_date">Client Start Date</label>
                                    <input id="client_start_date" name="client_start_date" type="date"
                                        class="form-control @error('client_start_date') is-invalid @enderror"
                                        placeholder="Enter client start date"
                                        value="{{ old('client_start_date', $clientProfile->client_start_date) }}">
                                    @error('client_start_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="certificate_start_date">Certificate Start Date</label>
                                    <input id="certificate_start_date" name="certificate_start_date" type="date"
                                        class="form-control @error('certificate_start_date') is-invalid @enderror"
                                        placeholder="Enter certificate start date"
                                        value="{{ old('certificate_start_date', $clientProfile->certificate_start_date) }}">
                                    @error('certificate_start_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="status" class="col-form-label">{{ __('content.status') }}</label>
                                    <select class="form-control" name="status" id="status">
                                        <option value="active" selected>{{ __('content.select_your_option') }}</option>
                                        <option value="active" {{ $clientProfile->status == 'active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="inactive"
                                            {{ $clientProfile->status == 'inactive"' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <small class="form-text text-muted">{{ __('content.required_fields') }}</small>
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
    <!-- end row -->
@endsection
