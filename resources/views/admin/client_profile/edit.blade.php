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
                        <div id="imageModal" class="border border-success iyzi-modal">
                            <img class="img-fluid " src="{{ asset('uploads/img/dummy/style/clientProfile-style1.jpg') }}"
                                alt="draft image">
                        </div>
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
                                    <input id="company_name" name="company_name" value="{{ $clientProfile->company_name }}"
                                        type="text" class="form-control" required>
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="short_description">{{ __('content.short_description') }}</label>
                                    <textarea id="short_description" name="short_description" class="form-control" rows="3">{{ $clientProfile->short_description }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">Address <span class="text-red">*</span></label>
                                    <input id="address" name="address" value="{{ $clientProfile->address }}"
                                        type="text" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="city">City <span class="text-red">*</span></label>
                                    <input id="city" name="city" value="{{ $clientProfile->city }}" type="text"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="state">State <span class="text-red">*</span></label>
                                    <input id="state" name="state" value="{{ $clientProfile->state }}" type="text"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="zip">Zip <span class="text-red">*</span></label>
                                    <input id="zip" name="zip" value="{{ $clientProfile->zip }}" type="text"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input id="phone" name="phone" value="{{ $clientProfile->phone }}" type="text"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="fax">Fax</label>
                                    <input id="fax" name="fax" value="{{ $clientProfile->fax }}" type="text"
                                        class="form-control" required>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="shipping_address">Shipping Address</label>
                                    <input id="shipping_address" name="shipping_address"
                                        value="{{ $clientProfile->shipping_address }}" type="text" class="form-control"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="billing_contact_name">Billing Contact Name</label>
                                    <input id="billing_contact_name" name="billing_contact_name"
                                        value="{{ $clientProfile->billing_contact_name }}" type="text"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="billing_contact_email">Billing Contact Email</label>
                                    <input id="billing_contact_email" name="billing_contact_email"
                                        value="{{ $clientProfile->billing_contact_email }}" type="text"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="billing_contact_phone">Billing Contact Phone</label>
                                    <input id="billing_contact_phone" name="billing_contact_phone"
                                        value="{{ $clientProfile->billing_contact_phone }}" type="text"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="der_contact_name">DER Contact Name<span class="text-red">*</span></label>
                                    <input id="der_contact_name" name="der_contact_name" type="text"
                                        value="{{ $clientProfile->der_contact_name }}" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="der_contact_email">DER Contact Email<span
                                            class="text-red">*</span></label>
                                    <input id="der_contact_email" name="der_contact_email" type="text"
                                        value="{{ $clientProfile->der_contact_email }}" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="der_contact_phone">DER Contact Phone</label>
                                    <input id="der_contact_phone" name="der_contact_phone" type="text"
                                        value="{{ $clientProfile->der_contact_phone }}" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="status" class="col-form-label">{{ __('content.status') }}</label>
                                    <select class="form-control" name="status" id="status">
                                        <option value="active" selected>{{ __('content.select_your_option') }}</option>
                                        <option value="active"
                                            {{ $clientProfile->status == 'active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="inactive" {{ $clientProfile->status == 'inactive"' ? 'selected' : '' }}>
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
