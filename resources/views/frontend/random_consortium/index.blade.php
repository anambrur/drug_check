@extends('layouts.frontend.master2')

@section('content')
    <section class="my-5">
        <div class="container pt-5">
            <div class="row mt-4">
                <div class="col-12">
                    <h2 class="text-center">{{ $random_consortium->title }}</h2>
                    <p class="text-center">
                        @php echo html_entity_decode($random_consortium->description); @endphp
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-5">
        <div class="container">
            <div class="row g-0 mx-0">
                <div class="col-12">
                    <div class="card border-0 shadow-none">
                        <div class="card-body p-4">
                            <h5 class="card-title text-center mb-4">Please fill out the information below</h5>

                            <form id="payment-form" action="{{ route('send.mail_form') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="payment_intent_id" id="payment_intent_id">

                                <h5 class="mb-4 mt-5">Company Information</h5>

                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="company_name" name="company_name"
                                                placeholder=" " required>
                                            <label for="company_name">Company Name</label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="company_address"
                                                name="company_address" placeholder=" " required>
                                            <label for="company_address">Company Address</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="company_city" name="company_city"
                                                placeholder=" " required>
                                            <label for="company_city">Company City</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="company_state"
                                                name="company_state" placeholder=" " required>
                                            <label for="company_state">Company State/Province</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="company_zip" name="company_zip"
                                                placeholder=" " required>
                                            <label for="company_zip">Company Zip/Postal</label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="tel" class="form-control" id="company_phone"
                                                name="company_phone" placeholder=" ">
                                            <label for="company_phone">Company Phone</label>
                                        </div>
                                    </div>
                                </div>

                                <h5 class="mb-4 mt-5">Designated Employer Representative (DER) Information</h5>

                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="der_name" name="der_name"
                                                placeholder=" " required>
                                            <label for="der_name">DER Contact Name</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="der_email" name="der_email"
                                                placeholder=" " required>
                                            <label for="der_email">DER Contact Email</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="tel" class="form-control" id="der_phone" name="der_phone"
                                                placeholder=" ">
                                            <label for="der_phone">DER Contact Phone</label>
                                        </div>
                                    </div>
                                </div>

                                <h5 class="mb-4 mt-5">Certificate Information (30 days drug test report)</h5>

                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="certificate_file" class="form-label">Upload Certificate
                                                File</label>
                                            <input class="form-control" type="file" id="certificate_file"
                                                name="certificate_file">
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="date" class="form-control" id="certificate_start_date"
                                                name="certificate_start_date" placeholder=" ">
                                            <label for="certificate_start_date">Certificate Start Date (if
                                                applicable)</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-5 text-center">
                                    <button type="submit" class="primary-btn">
                                        <span class="text">Apply</span>
                                        <span class="icon"><i class="fa fa-arrow-right"></i></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
