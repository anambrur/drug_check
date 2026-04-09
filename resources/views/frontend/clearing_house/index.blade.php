@extends('layouts.frontend.master2')

@section('content')
    <!-- Content from Provided Info -->
    <section class="section pb-5">
        <div class="container">
            <!-- What is the FMCSA Clearinghouse? -->
            <div class="row mb-5 align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="about-inner wow fadeInLeft" data-wow-duration="0.5s">
                        <h6>Overview</h6>
                        <h2>What is the FMCSA Clearinghouse?</h2>
                        <p class="mb-3">
                            The FMCSA Drug and Alcohol Clearinghouse is a secure, online database that provides real-time
                            information about commercial drivers’ drug and alcohol violations. It is maintained by the
                            Federal Motor Carrier Safety Administration (FMCSA) and ensures that drivers who violate the
                            DOT’s drug and alcohol program rules complete the Return-to-Duty (RTD) process before operating
                            commercial vehicles again.
                        </p>
                        <p>
                            The Clearinghouse improves road safety and helps employers make informed hiring decisions by
                            accessing drivers' compliance history.
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 d-flex justify-content-center">
                    <div class="about-img wow fadeInRight w-100" data-wow-duration="0.5s">
                        <div class="card border-0 shadow p-4 w-100 bg-white" style="border-radius: 15px;">
                            <h4 class="mb-3 text-dark"><i class="fas fa-users-cog mr-2" style="color: #007bff;"></i> Who is
                                Required to Use it?</h4>
                            <p class="small text-muted">The Clearinghouse applies to all employers and drivers operating
                                commercial motor vehicles under FMCSA regulations, including:</p>
                            <ul class="list-unstyled mt-3 mb-4">
                                <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> CDL Drivers</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> Employers of CDL
                                    drivers (incl. owner-operators)</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> C/TPAs
                                    (Consortium/Third-Party Administrators)</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> Medical Review
                                    Officers (MROs)</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i> Substance Abuse
                                    Professionals (SAPs)</li>
                            </ul>
                            <div class="alert alert-warning mb-0 py-2 small border-left-warning">
                                <i class="fas fa-info-circle mr-1"></i> Registration is mandatory for all of the above under
                                FMCSA guidelines.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-5" style="border-color: #eee;">

            <!-- What is a C/TPA -->
            <div class="row mb-5 align-items-center flex-row-reverse">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="about-inner wow fadeInRight" data-wow-duration="0.5s">
                        <h6>Compliance Simplified</h6>
                        <h2>What is a C/TPA?</h2>
                        <p>
                            A C/TPA (Consortium/Third-Party Administrator) is a professional service agent that helps
                            employers manage all aspects of the DOT drug and alcohol testing program. This includes managing
                            testing, compliance, Clearinghouse queries, Return-to-Duty monitoring, and more.
                        </p>
                        <div class="alert alert-info mt-4" style="border-left: 4px solid #17a2b8; background: #f4fbfc;">
                            <strong>For small companies and owner-operators</strong>, selecting a C/TPA is required under
                            DOT rules.
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-inner wow fadeInLeft" data-wow-duration="0.5s">
                        <h2 class="mb-4">What Does a C/TPA Do?</h2>
                        <p>At <strong>Skyros Drug Checks Inc</strong>, we act as your designated C/TPA, managing your
                            compliance with all FMCSA Clearinghouse requirements.</p>

                        <ul class="list-unstyled mt-4">
                            <li class="mb-3 d-flex align-items-start">
                                <div class="icon bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-3 mt-1"
                                    style="width: 40px; height: 40px; min-width: 40px;"><i class="fas fa-search"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 text-dark" style="font-size: 1.1rem; font-weight: 600;">Managing
                                        Clearinghouse Queries</h5>
                                    <p class="text-muted small mb-0">Full and Limited queries for hiring and annual checks.
                                    </p>
                                </div>
                            </li>
                            <li class="mb-3 d-flex align-items-start">
                                <div class="icon bg-success text-white rounded-circle d-flex justify-content-center align-items-center me-3 mt-1"
                                    style="width: 40px; height: 40px; min-width: 40px;"><i class="fas fa-sync-alt"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 text-dark" style="font-size: 1.1rem; font-weight: 600;">Initiating
                                        Return-to-Duty (RTD)</h5>
                                    <p class="text-muted small mb-0">Managing notifications and process requirements.</p>
                                </div>
                            </li>
                            <li class="mb-3 d-flex align-items-start">
                                <div class="icon bg-info text-white rounded-circle d-flex justify-content-center align-items-center me-3 mt-1"
                                    style="width: 40px; height: 40px; min-width: 40px;"><i class="fas fa-file-contract"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 text-dark" style="font-size: 1.1rem; font-weight: 600;">Audit-Ready
                                        Records</h5>
                                    <p class="text-muted small mb-0">Maintaining compliance records, monitoring follow-up
                                        testing, and registration & onboarding assistance.</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Query Details Info Box -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card glass-card border-0 wow fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.4s"
                        style="border-radius: 20px;">
                        <div class="card-body p-4 p-md-5 border"
                            style="border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
                            <div class="about-inner text-center mb-5 wow fadeInUp">
                                <h6>Frequency</h6>
                                <h2 class="mb-3">Query Guidelines & Requirements</h2>
                                <p class="text-muted mx-auto" style="max-width: 600px;">The two primary types of FMCSA
                                    required queries and when you must utilize them.</p>
                            </div>
                            <div class="row">
                                <div class="col-md-6 border-right-md mb-4 mb-md-0 pb-4 pb-md-0 px-md-4"
                                    style="border-color: rgba(0,0,0,0.1) !important;">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="icon-shape bg-primary-soft text-primary me-3 rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 50px; height: 50px;">
                                            <i class="fas fa-user-check fa-lg"></i>
                                        </div>
                                        <h2 class="font-weight-bold mb-0">Full Query</h2>
                                    </div>
                                    <p class="text-muted mb-4">Provides complete details of any drug and alcohol program
                                        violations directly from the FMCSA clearinghouse.</p>

                                    <p class="font-weight-bold mb-3 text-dark text-uppercase small tracking-wide">When is
                                        it required?</p>
                                    <ul class="list-unstyled mb-4 text-muted small custom-check-list">
                                        <li class="mb-2"><i class="fas fa-check text-primary mr-2"></i> Before hiring a
                                            new CDL driver</li>
                                        <li class="mb-2"><i class="fas fa-check text-primary mr-2"></i> When a limited
                                            query returns “information found”</li>
                                        <li class="mb-2"><i class="fas fa-check text-primary mr-2"></i> During the
                                            Return-to-Duty (RTD) process</li>
                                        <li class="mb-2"><i class="fas fa-check text-primary mr-2"></i> During follow-up
                                            testing programs</li>
                                    </ul>
                                    <div
                                        class="bg-light p-3 rounded-lg border-0 shadow-sm d-flex align-items-center hover-up">
                                        <i class="fas fa-calendar-alt text-primary fa-2x me-3"></i>
                                        <div>
                                            <strong class="d-block text-dark">Frequency</strong>
                                            <span class="text-muted small">At hiring + as needed</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 px-md-4">
                                    <div class="d-flex align-items-center mb-4 mt-4 mt-md-0">
                                        <div class="icon-shape bg-info-soft text-info me-3 rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 50px; height: 50px;">
                                            <i class="fas fa-search-plus fa-lg"></i>
                                        </div>
                                        <h2 class="font-weight-bold mb-0">Limited Query</h2>
                                    </div>
                                    <p class="text-muted mb-4">Checks whether any records exist for a driver without
                                        showing full details. A faster way to monitor compliance.</p>

                                    <p class="font-weight-bold mb-3 text-dark text-uppercase small tracking-wide">When is
                                        it required?</p>
                                    <ul class="list-unstyled mb-4 text-muted small custom-check-list">
                                        <li class="mb-2"><i class="fas fa-check text-info mr-2"></i> Annually for all
                                            existing drivers</li>
                                        {{-- <li class="mb-2 opacity-0"><i class="fas fa-check mr-2"></i> Placeholder for
                                            spacing</li>
                                        <li class="mb-2 opacity-0"><i class="fas fa-check mr-2"></i> Placeholder for
                                            spacing</li>
                                        <li class="mb-2 opacity-0"><i class="fas fa-check mr-2"></i> Placeholder for
                                            spacing</li> --}}
                                    </ul>
                                    <div
                                        class="bg-light p-3 rounded-lg border-0 shadow-sm d-flex align-items-center hover-up">
                                        <i class="fas fa-calendar-check text-info fa-2x me-3"></i>
                                        <div>
                                            <strong class="d-block text-dark">Frequency</strong>
                                            <span class="text-muted small">1 Limited Query annually per driver</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Registration Instructions Sections -->
    <section class="section py-5" style="background: #fdfdfd; border-top: 1px solid #f1f1f1;">
        <div class="container">
            <div class="about-inner text-center mb-5 wow fadeInUp">
                <h6>Step by Step instructions</h6>
                <h2 class="mb-3">Clearinghouse Registration</h2>
                <p class="text-muted mx-auto" style="max-width: 600px;">Follow these simple steps to register in the
                    Clearinghouse, or let Skyros Drug Checks Inc assist you in achieving seamless compliance.</p>
            </div>

            <div class="row">
                <!-- Driver Registration -->
                <div class="col-lg-4 col-md-6 mb-4 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="card border-0 shadow-sm h-100"
                        style="border-radius: 15px; transition: transform 0.3s; border-top: 4px solid #007bff !important;">
                        <div class="card-body p-4 p-xl-5">
                            <div class="d-inline-block bg-primary text-white rounded-circle text-center mb-4"
                                style="width: 60px; height: 60px; line-height: 60px; font-size: 24px;">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <h4 class="mb-4" style="font-weight: 600;">For Drivers</h4>
                            <ol class="pl-3 text-muted small" style="line-height: 1.8;">
                                <li class="mb-2">Visit <a href="https://clearinghouse.fmcsa.dot.gov" target="_blank"
                                        class="font-weight-bold">clearinghouse.fmcsa.dot.gov</a></li>
                                <li class="mb-2">Click “Register” and select <strong>Driver</strong></li>
                                <li class="mb-2">Sign in or create an account using Login.gov</li>
                                <li class="mb-2">Enter CDL details and personal information</li>
                                <li class="mb-2">Complete registration</li>
                                <li class="mb-2">Use this account to provide consent for Full Queries by employers</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Employer Registration -->
                <div class="col-lg-4 col-md-6 mb-4 wow fadeInUp" data-wow-delay="0.2s">
                    <div class="card border-0 shadow-sm h-100"
                        style="border-radius: 15px; transition: transform 0.3s; border-top: 4px solid #28a745 !important;">
                        <div class="card-body p-4 p-xl-5">
                            <div class="d-inline-block bg-success text-white rounded-circle text-center mb-4"
                                style="width: 60px; height: 60px; line-height: 60px; font-size: 24px;">
                                <i class="fas fa-building"></i>
                            </div>
                            <h4 class="mb-4" style="font-weight: 600;">For Employers</h4>
                            <ol class="pl-3 text-muted small" style="line-height: 1.8;">
                                <li class="mb-2">Visit <a href="https://clearinghouse.fmcsa.dot.gov" target="_blank"
                                        class="font-weight-bold">clearinghouse.fmcsa.dot.gov</a></li>
                                <li class="mb-2">Click “Register” and select <strong>Employer</strong></li>
                                <li class="mb-2">Sign in or create an account using Login.gov</li>
                                <li class="mb-2">Enter company details, DOT number, and business information</li>
                                <li class="mb-2">If you're an owner-operator, you must designate a C/TPA</li>
                                <li class="mb-2">Purchase a Query Plan and complete setup</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Designate C/TPA -->
                <div class="col-lg-4 col-md-6 mb-4 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="card border-0 shadow-sm h-100 bg-primary text-white"
                        style="border-radius: 15px; transition: transform 0.3s;">
                        <div class="card-body p-4 p-xl-5" style="border-radius: 13px;">
                            <div class="d-inline-block bg-white text-primary rounded-circle text-center mb-4"
                                style="width: 60px; height: 60px; line-height: 60px; font-size: 24px;">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <h4 class="mb-4 text-white" style="font-weight: 600;">Designate a C/TPA</h4>
                            <ol class="pl-3 small" style="line-height: 1.8;">
                                <li class="mb-2">Log in to your Clearinghouse Employer Dashboard</li>
                                <li class="mb-2">Click on <strong>“Designate C/TPA”</strong></li>
                                <li class="mb-2">Search or enter our details:<br>
                                    <span
                                        class="d-inline-block bg-white text-primary px-2 py-1 rounded mt-1 font-weight-bold">Skyros
                                        Drug Checks Inc</span>
                                </li>
                                <li class="mb-2">Confirm selection</li>
                                <li class="mb-2">You're now connected and compliant!</li>
                            </ol>
                            <div class="mt-4 pt-3 border-top border-light text-center font-weight-bold">
                                <i class="fas fa-check-circle mr-1"></i> Fully Compliant FMCSA C/TPA
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Existing Downloadable Resources -->
    @if (
        ($clearing_house->driver_pdf && count(json_decode($clearing_house->driver_pdf, true) ?? []) > 0) ||
            ($clearing_house->employer_pdf && count(json_decode($clearing_house->employer_pdf, true) ?? []) > 0))
        <section class="section py-5" style="background: #f4f6f9;">
            <div class="container">
                <div class="about-inner text-center mb-5 wow fadeInUp">
                    <h6>Downloads</h6>
                    <h2>Resources & Guides</h2>
                </div>

                <div class="row">
                    @if ($clearing_house->driver_pdf)
                        @php $driver_pdfs = json_decode($clearing_house->driver_pdf, true) ?? []; @endphp
                        @if (count($driver_pdfs) > 0)
                            <div class="col-md-6 mb-4 wow fadeInLeft" data-wow-delay="0.1s">
                                <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                                    <div class="card-body p-4">
                                        <h2 class="mb-4 d-flex align-items-center">
                                            <div class="d-flex justify-content-center align-items-center me-3"
                                                style="width: 40px; height: 40px;"><i class="fas fa-id-card"></i>
                                            </div>
                                            Driver Resources
                                        </h2>
                                        <ul class="list-group list-group-flush">
                                            @foreach ($driver_pdfs as $pdf)
                                                <li class="list-group-item px-0 border-0 mb-2">
                                                    <a href="{{ asset('uploads/pdf/driver_pdf/' . $pdf) }}"
                                                        target="_blank"
                                                        class="d-flex align-items-center text-dark text-decoration-none p-3 rounded bg-light hover-shadow transition">
                                                        <div class="me-3 text-danger">
                                                            <i class="fas fa-file-pdf fa-2x"></i>
                                                        </div>
                                                        <div>
                                                            @php
                                                                $filenameWithoutTimestamp = substr(
                                                                    $pdf,
                                                                    strpos($pdf, '-') + 1,
                                                                );
                                                                $cleanName = pathinfo(
                                                                    $filenameWithoutTimestamp,
                                                                    PATHINFO_FILENAME,
                                                                );
                                                                $formattedName = ucwords(
                                                                    str_replace('-', ' ', $cleanName),
                                                                );
                                                            @endphp
                                                            <h6 class="mb-0"
                                                                style="font-weight: 600; font-size: 0.95rem;">
                                                                {{ $formattedName }}</h6>
                                                            <small class="text-primary">
                                                                <i class="fas fa-download me-1"></i> Download
                                                            </small>
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    @if ($clearing_house->employer_pdf)
                        @php $employer_pdfs = json_decode($clearing_house->employer_pdf, true) ?? []; @endphp
                        @if (count($employer_pdfs) > 0)
                            <div class="col-md-6 mb-4 wow fadeInRight" data-wow-delay="0.2s">
                                <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                                    <div class="card-body p-4">
                                        <h2 class="mb-4 d-flex align-items-center">
                                            <div class="d-flex justify-content-center align-items-center me-3"
                                                style="width: 40px; height: 40px;"><i class="fas fa-building"></i>
                                            </div>
                                            Employer Resources
                                        </h2>
                                        <ul class="list-group list-group-flush">
                                            @foreach ($employer_pdfs as $pdf)
                                                <li class="list-group-item px-0 border-0 mb-2">
                                                    <a href="{{ asset('uploads/pdf/employer_pdf/' . $pdf) }}"
                                                        target="_blank"
                                                        class="d-flex align-items-center text-dark text-decoration-none p-3 rounded bg-light hover-shadow transition">
                                                        <div class="me-3 text-danger">
                                                            <i class="fas fa-file-pdf fa-2x"></i>
                                                        </div>
                                                        <div>
                                                            @php
                                                                $filenameWithoutTimestamp = substr(
                                                                    $pdf,
                                                                    strpos($pdf, '-') + 1,
                                                                );
                                                                $cleanName = pathinfo(
                                                                    $filenameWithoutTimestamp,
                                                                    PATHINFO_FILENAME,
                                                                );
                                                                $formattedName = ucwords(
                                                                    str_replace('-', ' ', $cleanName),
                                                                );
                                                            @endphp
                                                            <h6 class="mb-0"
                                                                style="font-weight: 600; font-size: 0.95rem;">
                                                                {{ $formattedName }}</h6>
                                                            <small class="text-success"><i
                                                                    class="fas fa-download mr-1"></i> Download</small>
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </section>
    @endif

    <style>
        /* Custom Scoped Styles */
        .hover-shadow {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }

        .hover-shadow:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.12) !important;
            transform: translateY(-2px);
        }

        .transition {
            transition: all 0.3s ease-in-out;
        }

        .about-inner h6 {
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 10px;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        .about-inner h2 {
            font-weight: 700;
            margin-bottom: 20px;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        @media (max-width: 767.98px) {
            .border-right-md {
                border-right: none !important;
                border-bottom: 1px solid #dee2e6;
            }
        }
    </style>
@endsection
