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
                                    <input id="company_name" name="company_name" type="text" class="form-control"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="short_description">{{ __('content.short_description') }}</label>
                                    <textarea id="short_description" name="short_description" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">Address <span class="text-red">*</span></label>
                                    <input id="address" name="address" type="text" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="city">City <span class="text-red">*</span></label>
                                    <input id="city" name="city" type="text" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="state">State <span class="text-red">*</span></label>
                                    <input id="state" name="state" type="text" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="zip">Zip <span class="text-red">*</span></label>
                                    <input id="zip" name="zip" type="text" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input id="phone" name="phone" type="text" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="fax">Fax</label>
                                    <input id="fax" name="fax" type="text" class="form-control" required>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="shipping_address">Shipping Address</label>
                                    <input id="shipping_address" name="shipping_address" type="text" class="form-control"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="billing_contact_name">Billing Contact Name</label>
                                    <input id="billing_contact_name" name="billing_contact_name" type="text"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="billing_contact_email">Billing Contact Email</label>
                                    <input id="billing_contact_email" name="billing_contact_email" type="text"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="billing_contact_phone">Billing Contact Phone</label>
                                    <input id="billing_contact_phone" name="billing_contact_phone" type="text"
                                        class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group" id="dot-agency-group">
                                    <label for="dot_agency_id">DOT Agency</label>
                                    <select class="form-control" id="dot_agency_id" name="dot_agency_id">
                                        @foreach ($dotAgencies as $agency)
                                            <option value="{{ $agency->id }}">{{ $agency->dot_agency_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="der_contact_name">DER Contact Name<span class="text-red">*</span></label>
                                    <input id="der_contact_name" name="der_contact_name" type="text"
                                        class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="der_contact_email">DER Contact Email<span
                                            class="text-red">*</span></label>
                                    <input id="der_contact_email" name="der_contact_email" type="text"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="der_contact_phone">DER Contact Phone</label>
                                    <input id="der_contact_phone" name="der_contact_phone" type="text"
                                        class="form-control" required>
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
