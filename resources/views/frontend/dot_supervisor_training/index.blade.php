@extends('layouts.frontend.master2')

@section('content')
    <section class="my-5">
        <div class="container pt-5">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="text-center">DOT Supervisor Training</h2>
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

    <style>
        .contact-form-wrap2 {
            background: #fff;
            margin: 250px !important;

        }

        @media (max-width: 576px) {
            .contact-form-wrap2 {
                background: #fff;
                margin: 20px !important;

            }
        }

        @media (max-width: 768px) {
            .contact-form-wrap2 {
                background: #fff;
                margin: 100px !important;

            }
        }

        @media (max-width: 992px) {
            .contact-form-wrap2 {
                background: #fff;
                margin: 100px !important;

            }
        }
    </style>

    <section class="">
        <div class="container m-auto ">
            <div class="sidebar-widgets contact-form-wrap2 ">
                <h5 class="inner-header-title">Please fill out the information below
                    Information</h5>
                <div class="contact-form-wrap p-3">
                    <form id="payment-form" action="{{ route('send.mail_form') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_intent_id" id="payment_intent_id">

                        <div class="row align-self-center ">
                            <div class="col-md-12 d-flex">
                                <div class="contact-form-group">
                                    <input type="text" class="form-control" name="first_name" placeholder="First name"
                                        required>
                                    <div class="form-validate-icons">
                                        <span></span>
                                    </div>
                                </div>
                                <div class="contact-form-group">
                                    <input type="text" class="form-control" name="last_name" placeholder="Last name"
                                        required>
                                    <div class="form-validate-icons">
                                        <span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 d-flex">
                                <div class="contact-form-group">
                                    <input type="email" class="form-control" name="email"
                                        placeholder="{{ __('frontend.email') }}" required>
                                    <div class="form-validate-icons">
                                        <span></span>
                                    </div>
                                </div>
                                <div class="contact-form-group">
                                    <input type="text" class="form-control" name="phone"
                                        placeholder="{{ __('frontend.phone') }}" required>
                                    <div class="form-validate-icons">
                                        <span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 d-flex">
                                <div class="contact-form-group">
                                    <input type="text" class="form-control" name="address"
                                        placeholder="{{ __('frontend.address') }}" required>
                                    <div class="form-validate-icons">
                                        <span></span>
                                    </div>
                                </div>

                            </div>
                            <h5 class="inner-header-title">Company Information</h5>
                            <div class="col-md-12 d-flex">
                                <div class="contact-form-group">
                                    <input type="text" class="form-control" name="Company_name"
                                        placeholder="Company name" required>
                                    <div class="form-validate-icons">
                                        <span></span>
                                    </div>
                                </div>
                                <div class="contact-form-group">
                                    <input type="text" class="form-control" name="Accounting_Email"
                                        placeholder="Accounting Email" required>
                                    <div class="form-validate-icons">
                                        <span></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 d-flex">
                                <div class="contact-form-group">
                                    <input type="text" class="form-control" name="company_address"
                                        placeholder="{{ __('frontend.address') }}" required>
                                    <div class="form-validate-icons">
                                        <span></span>
                                    </div>
                                </div>

                            </div>
                            <!-- check list -->
                            <h5 class="inner-header-title">Select Service</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]" value="Choice Package"
                                    id="choicePackage">
                                <label class="form-check-label" for="choicePackage">
                                    CHOICE PACKAGE: InstaCrim National Criminal
                                    Database Search; National Sex Offender Search; OFAC/Terrorist Watchlist; SSN +
                                    Alias Name Check + Address History Search
                                    ----------$19.95 (1-3 Business Day Turn Around) ----------Recomended
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]" value="Premier Package"
                                    id="premierPackage">
                                <label class="form-check-label" for="premierPackage">
                                    PREMIER PACKAGE: InstaCrim National Criminal Database Search;
                                    National Sex Offender Search; OFAC/Terrorist Watchlist; Current County Criminal Records
                                    Search*
                                    applicable Court access fees may apply; SSN +
                                    Alias Name Check + Address History Search ----------$19.95 (1-3 Business Day Turn
                                    Around) ----------Recomended
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    value="County Criminal Records" id="countyCriminal">
                                <label class="form-check-label" for="countyCriminal">
                                    COUNTY CRIMINAL COURT RECORDS SEARCH:
                                    A name and date of birth search of county criminal records for a given jurisdiction.
                                    Includes all available, reportable criminal records, including felonies, misdemeanors,
                                    and serious traffic violations. Jurisdictions that fall within territories covered
                                    under the All County Criminal Record Search will be substituted for the
                                    All County Criminal Record Search a
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    value="Federal Court Records" id="federalCourt">
                                <label class="form-check-label" for="federalCourt">
                                    FEDERAL COURT RECORDS SEARCH: A name and date of birth search of federal
                                    court records for an applicant. Includes all available, reportable Federal records,
                                    including criminal records, bankruptcies,
                                    tax liens and other records from the district and appellate courts.
                                    ----------$4.75 (1 Business Day Turn Around)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    value="Education Verification" id="educationVerification">
                                <label class="form-check-label" for="educationVerification">
                                    EDUCATION VERIFICATION: A name and date of
                                    birth verification of an applicant's education history.
                                    Can be applied to both secondary and postsecondary schools and includes
                                    the applicant's date of graduation, degree obtained, and re-entry status.
                                    ---------- $7.00 + Access Fees (Instant - 3 Business Day Turn Around)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    value="Employment Verification" id="employmentVerification">
                                <label class="form-check-label" for="employmentVerification">
                                    EMPLOYMENT VERIFICATION: A name and date of birth verification of an applicant's
                                    previous employment verification. Includes a verification of the applicant's dates of
                                    hire, re-hire status,
                                    position held, and reason for departure. ---------- $7.00 + Access Fees (Instant - 3
                                    Business Day Turn Around)
                                </label>
                            </div>


                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    value="Personal Reference Verification" id="personalReference">
                                <label class="form-check-label" for="personalReference">
                                    PERSONAL REFERENCE VERIFICATION: A personal reference verification for
                                    an applicant based on their personal history. Can be tailored to address and identify
                                    key characteristics that your organization supports, such as trustworthiness,
                                    timeliness, personal temperament and overall competencies.
                                    ----------$7.00 (Instant - 3 Business Day Turn Around)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                                <label class="form-check-label" for="flexCheckChecked">
                                    PREVIOUS VOLUNTEER VERIFICATION: A previous volunteer verification
                                    for an applicant based on their past volunteer experiences.
                                    Includes a confirmation of the applicant's actual participation with that organization,
                                    including duties or responsibilities, time with the organization,
                                    known incidents, and ability to volunteer in the future.
                                    ----------$7.00 (Instant - 3 Business Day Turn Around)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    value="Professional License Verification" id="licenseVerification">
                                <label class="form-check-label" for="licenseVerification">
                                    PROFESSIONAL LICENSE VERIFICATION: Includes A Professional
                                    License Verification search that validates information provided by your candidate or
                                    prospective connection against national, state, or municipal databases. This includes
                                    their
                                    first name, last name, date of birth,
                                    sex, and any other necessary identifying characteristics.
                                    ----------$7.00 (Instant - 3 Business Day Turn Around)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                                <label class="form-check-label" for="flexCheckChecked">
                                    PROFESSIONAL REFERENCE VERIFICATION: A professional
                                    reference verification for an applicant based on their previous
                                    professional history. Can be tailored to address and identify key
                                    characteristics that your organization supports, such as timeliness, creativity,
                                    completion of projects, and overall competencies.
                                    ----------$7.00 (Instant - 3 Business Day Turn Around)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]" value="SSN Trace"
                                    id="ssnTrace">
                                <label class="form-check-label" for="ssnTrace">
                                    SSN TRACE: A name, date of birth and social security number check for an applicant to
                                    confirm that the
                                    social provided is valid and matches to the applicant's information.
                                    ----------$4.00 (Instant Turn Around)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    value="Pre-employment Credit Report" id="creditReport">
                                <label class="form-check-label" for="creditReport">
                                    PRE-EMPLOYMENT CREDIT REPORT: A name, date of birth and social
                                    security number based retrieval of all known credit history for an applicant. Includes
                                    all known tax liens, collections, open accounts, accounts under bad debt collection,
                                    inquiries and date of birth confirmation. Restricted in
                                    many jurisdictions to applicants with specific fiduciary responsibilities.
                                    ----------$7.25 (Instant Turn Arou
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    value="international_credit" id="internationalCredit">
                                <label class="form-check-label" for="internationalCredit">
                                    INTERNATIONAL CREDIT REPORT: An identity based credit report for
                                    an applicant from a country outside of the United States. Includes all
                                    available records for that country's credit system
                                    ----------$Price upon request (Varies by Country)
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    value="international_criminal" id="internationalCriminal">
                                <label class="form-check-label" for="internationalCriminal">
                                    INTERNATIONAL CRIMINAL RECORDS SEARCH: A name, date of birth and,
                                    where available, identity based criminal record search for countries outside of the
                                    United States. Includes all
                                    available records for that country's criminal justice system.
                                    ----------$Price upon request (Varies by Country)
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    value="international_education" id="internationalEducation">
                                <label class="form-check-label" for="internationalEducation">
                                    INTERNATIONAL EDUCATION VERIFICATION: A name, date of birth and,
                                    where available, identity based education verification for institutions located outside
                                    of the United States.
                                    Includes all available records for that institution's system.
                                    ----------$Price upon request (Varies by Country)
                                </label>
                            </div>

                            <div class="form-check pb-3">
                                <input class="form-check-input" type="checkbox" name="services[]"
                                    value="Motor Vehicle Report" id="motorVehicle">
                                <label class="form-check-label" for="motorVehicle">
                                    MOTOR VEHICLE REPORT: A name, date of birth and driver's license number
                                    search for all available traffic citations and serious violations for an applicant in a
                                    given state.
                                    Includes tickets, DUI, DWI, revocations, suspensions,
                                    date of expiration, state of issuance. ----------$4.00 + State and Search Fee (Varies by
                                    State)
                                </label>
                            </div>




                            <div class="col-md-12 text-center">
                                <div class="form-alerts">
                                    @error('name')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                    @error('email')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                    @error('phone')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                    @error('message')
                                        <span class="error">{{ $message }}</span>
                                    @enderror
                                    @if (session('success'))
                                        <span class="error">{{ __(session('success')) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <h5 class="inner-header-title p-3">Terms and Conditions</h5>
                        <p>By checking the box below, you have agreed to the <a href="#">Terms and Conditions</a></p>
                        <div class="form-check p-3">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                            <label class="form-check-label" for="flexCheckChecked">
                                I agree to the service agreement
                            </label>
                        </div>
                        <div class="contact-btn-left m-5">
                            <button type="submit" id="contactBtn" class="primary-btn">
                                <span class="text">Apply</span>
                                <span class="icon"><i class="fa fa-arrow-right"></i></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
