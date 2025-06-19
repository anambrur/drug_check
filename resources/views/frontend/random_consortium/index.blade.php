@extends('layouts.frontend.master2')

@section('content')
    <section class="my-5">
        <div class="container pt-5">
            <div class="row">
                <div class="col-12">
                    <h2 class="text-center">Random Consortium</h2>
                    <p>Federal Motor Carrier Safety Administration (FMCSA) Random Testing
                        All employers regulated by 49 CFR Part 382.305 are required to implement a random drug and alcohol
                        testing program. All safety sensitive employees such as CDL drivers must be randomly tested
                        throughout the year and an employer who employs only himself/herself as a driver who is not leased
                        to a motor carrier, shall implement a random testing program of two or more covered employees in the
                        random testing selection pool as a member of a consortium/random testing pool. We specialize in DOT
                        random drug and alcohol testing programs for single owner operators and small, medium and large
                        trucking companies with multiple drivers.
                    </p>

                    <h5>The current rate for FMCSA random drug and alcohol testing is</h5>
                    <p>50% of the average number of driver positions for Controlled Substances (5 panel DOT urine) and 10%
                        of the average number of diver positions for Breath Alcohol Testing (BAT)
                    <p>
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

                            <form id="payment-form" action="{{ route('send.mail_form') }}" method="POST">
                                @csrf
                                <input type="hidden" name="payment_intent_id" id="payment_intent_id">

                                <div class="row g-3 mb-4"> <!-- g-3 adds consistent gaps between rows -->
                                    <div class="col-md-6">
                                        <div class="form-floating"> <!-- Bootstrap floating labels -->
                                            <input type="text" class="form-control" id="first_name" name="first_name"
                                                placeholder=" " required>
                                            <label for="first_name">First name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="last_name" name="last_name"
                                                placeholder=" " required>
                                            <label for="last_name">Last name</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="email" name="email"
                                                placeholder=" " required>
                                            <label for="email">{{ __('frontend.email') }}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="tel" class="form-control" id="phone" name="phone"
                                                placeholder=" " required>
                                            <label for="phone">{{ __('frontend.phone') }}</label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="address" name="address"
                                                placeholder=" " required>
                                            <label for="address">{{ __('frontend.address') }}</label>
                                        </div>
                                    </div>
                                </div>

                                <h5 class="mb-4 mt-5">Company Information</h5>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="company_name" name="Company_name"
                                                placeholder=" " required>
                                            <label for="company_name">Company name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="email" class="form-control" id="accounting_email"
                                                name="Accounting_Email" placeholder=" " required>
                                            <label for="accounting_email">Accounting Email</label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="company_address"
                                                name="company_address" placeholder=" " required>
                                            <label for="company_address">{{ __('frontend.address') }}</label>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-5 text-center">
                                        <button type="submit" class="primary-btn">
                                            <span class="text">Apply</span>
                                            <span class="icon"><i class="fa fa-arrow-right"></i></span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
