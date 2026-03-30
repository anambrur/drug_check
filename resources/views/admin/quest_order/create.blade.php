@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <div class="d-md-flex justify-content-between align-items-center mb-20">
                    <h4 class="card-title">Create Quest Order</h4>
                    <div>
                        <a href="{{ route('quest-order.index') }}" class="btn btn-primary">
                            <i class="fas fa-angle-left"></i> Back
                        </a>
                    </div>
                </div>

                <form action="{{ route('quest-order.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Donor Information Section -->
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="mt-3 mb-3 text-primary">Donor Information</h5>
                            <hr>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="first_name">First Name <span class="text-red">*</span></label>
                                <input id="first_name" name="first_name" type="text" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="last_name">Last Name <span class="text-red">*</span></label>
                                <input id="last_name" name="last_name" type="text" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="middle_name">Middle Name</label>
                                <input id="middle_name" name="middle_name" type="text" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="primary_id">Primary ID <span class="text-red">*</span></label>
                                <input id="primary_id" name="primary_id" type="text" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="primary_id_type">Primary ID Type</label>
                                <input id="primary_id_type" name="primary_id_type" type="text" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="dob">Date of Birth</label>
                                <input id="dob" name="dob" type="date" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="primary_phone">Primary Phone <span class="text-red">*</span></label>
                                <input id="primary_phone" name="primary_phone" type="text" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="secondary_phone">Secondary Phone</label>
                                <input id="secondary_phone" name="secondary_phone" type="text" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input id="email" name="email" type="email" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="zip_code">Zip Code</label>
                                <input id="zip_code" name="zip_code" type="text" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Test Information Section -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="mt-3 mb-3 text-primary">Test Information</h5>
                            <hr>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="portfolio_id">Portfolio ID</label>
                                <input id="portfolio_id" name="portfolio_id" type="number" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="portfolio_name">Portfolio Name</label>
                                <input id="portfolio_name" name="portfolio_name" type="text" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="unit_codes">Unit Codes (comma separated)</label>
                                <input id="unit_codes" name="unit_codes" type="text" class="form-control"
                                    placeholder="e.g., 1234,5678,9012">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dot_test">DOT Test <span class="text-red">*</span></label>
                                <select class="form-control" name="dot_test" id="dot_test" required>
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="testing_authority">Testing Authority</label>
                                <input id="testing_authority" name="testing_authority" type="text"
                                    class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="reason_for_test_id">Reason for Test ID</label>
                                <input id="reason_for_test_id" name="reason_for_test_id" type="text"
                                    class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="physical_reason_for_test_id">Physical Reason for Test ID</label>
                                <input id="physical_reason_for_test_id" name="physical_reason_for_test_id" type="text"
                                    class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="collection_site_id">Collection Site ID</label>
                                <input id="collection_site_id" name="collection_site_id" type="text"
                                    class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="observed_requested">Observed Requested <span class="text-red">*</span></label>
                                <select class="form-control" name="observed_requested" id="observed_requested" required>
                                    <option value="N">No</option>
                                    <option value="Y">Yes</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="split_specimen_requested">Split Specimen Requested <span
                                        class="text-red">*</span></label>
                                <select class="form-control" name="split_specimen_requested"
                                    id="split_specimen_requested" required>
                                    <option value="N">No</option>
                                    <option value="Y">Yes</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="order_comments">Order Comments</label>
                                <textarea id="order_comments" name="order_comments" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Client Information Section -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="mt-3 mb-3 text-primary">Client Information</h5>
                            <hr>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lab_account">Lab Account <span class="text-red">*</span></label>
                                <input id="lab_account" name="lab_account" type="text" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="csl">CSL</label>
                                <input id="csl" name="csl" type="text" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact_name">Contact Name</label>
                                <input id="contact_name" name="contact_name" type="text" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telephone_number">Telephone Number</label>
                                <input id="telephone_number" name="telephone_number" type="text"
                                    class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information Section -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="mt-3 mb-3 text-primary">Additional Information</h5>
                            <hr>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="client_reference_id">Client Reference ID <span
                                        class="text-red">*</span></label>
                                <input id="client_reference_id" name="client_reference_id" type="text"
                                    class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_datetime">End Date & Time</label>
                                <input id="end_datetime" name="end_datetime" type="datetime-local" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_datetime_timezone_id">Timezone ID</label>
                                <input id="end_datetime_timezone_id" name="end_datetime_timezone_id" type="number"
                                    class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <small class="form-text text-muted">Fields marked with <span class="text-red">*</span> are
                                    required.</small>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary mr-2">Create Quest Order</button>
                            <a href="{{ route('quest-order.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
