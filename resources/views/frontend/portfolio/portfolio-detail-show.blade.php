<!--// Portfolio Single Section Start //-->
<section class="section" id="portfolio-single-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">

                @if (Auth::user())
                    @can('portfolio view')
                        <div class="easier-mode">
                            @if (Auth::user())
                                @can('portfolio view')
                                    <!-- hover effect for mobile devices  -->
                                    <div class="click-icon d-md-none text-center">
                                        <button class="custom-btn text-white">
                                            <i class="fa fa-mobile-alt text-white"></i> {{ __('content.touch') }}
                                        </button>
                                    </div>
                                @endcan
                            @endif
                            <div class="easier-section-area">
                            @endcan
                @endif



                @if (Auth::user())
                    @can('portfolio view')
                </div>
                <div class="easier-middle">
                    @php
                        $url = request()->path();
                        $modified_url = str_replace('/', '-bracket-', $url);
                    @endphp
                    <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                        @csrf
                        <input type="hidden" name="route" value="portfolio-image.create">
                        <input type="hidden" name="style" value="{{ $portfolio->id }}">
                        <input type="hidden" name="site_url" value="{{ $modified_url }}">
                        <button type="submit" class="custom-btn text-white me-2">
                            <i class="fa fa-edit text-white"></i> {{ __('content.add_portfolio_image') }}
                        </button>
                    </form>
                </div>
            </div>
        @endcan
        @endif

        @if (Auth::user())
            @can('portfolio view')
                <div class="easier-mode">
                    @if (Auth::user())
                        @can('portfolio view')
                            <!-- hover effect for mobile devices  -->
                            <div class="click-icon d-md-none text-center">
                                <button class="custom-btn text-white">
                                    <i class="fa fa-mobile-alt text-white"></i> {{ __('content.touch') }}
                                </button>
                            </div>
                        @endcan
                    @endif
                    <div class="easier-section-area">
                    @endcan
        @endif

        @isset($portfolio_content)
            <div class="portfolio-single-inner custom-blog-img">
                <h4>{{ $portfolio->title }}</h4>
                <div class="author-meta">
                    {{-- <a href="#"><span
                            class="far fa-calendar-alt"></span>{{ Carbon\Carbon::parse($portfolio->created_at)->isoFormat('DD') }}
                        {{ Carbon\Carbon::parse($portfolio->created_at)->isoFormat('MMMM') }}
                        {{ Carbon\Carbon::parse($portfolio->created_at)->isoFormat('GGGG') }}</a> --}}
                    <a href="#"><span class="far fa-bookmark"></span>{{ $portfolio->category_name }}</a>
                </div>
                <p>@php echo html_entity_decode($portfolio_content->description); @endphp</p>
            </div>
        @else
            @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                <div class="portfolio-single-inner">
                    <h4>Business Card</h4>
                    <div class="author-meta">
                        <a href="#"><span class="far fa-user"></span>By Admin</a>
                        <a href="#"><span class="far fa-bookmark"></span>Mockup</a>
                    </div>
                    <p>It is a long established fact that a reader will be distracted by the readable
                        content of a page when looking at its layout. The point of using Lorem Ipsum is
                        that it has a more-or-less normal distribution of letters, as opposed to using
                        'Content here, content here', making it look like readable English. Many desktop
                        publishing packages and web page editors now use Lorem Ipsum as their default model
                        text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy.
                        Various versions have evolved over the years, sometimes by accident, sometimes on purpose
                        (injected humour and the like).
                    </p>
                    <div class="row">
                        <div class="col-md-6 col-sm-6 portfolio-grid-img">
                            <img src="{{ asset('uploads/img/dummy/600x600.jpg') }}" alt="Portfolio image" class="img-fluid">
                        </div>
                        <div class="col-md-6 col-sm-6 portfolio-grid-img">
                            <img src="{{ asset('uploads/img/dummy/600x600.jpg') }}" alt="Portfolio image" class="img-fluid">
                        </div>
                    </div>
                </div>
            @endif
        @endisset

        @if (Auth::user())
            @can('portfolio view')
        </div>
        <div class="easier-middle">
            @php
                $url = request()->path();
                $modified_url = str_replace('/', '-bracket-', $url);
            @endphp
            <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                @csrf
                <input type="hidden" name="route" value="portfolio-content.create">
                <input type="hidden" name="style" value="{{ $portfolio->id }}">
                <input type="hidden" name="site_url" value="{{ $modified_url }}">
                <button type="submit" class="custom-btn text-white me-2 mb-2">
                    <i class="fa fa-edit text-white"></i> {{ __('content.edit_portfolio_content') }}
                </button>
            </form>
        </div>
        </div>
    @endcan
    @endif

    </div>

    <div class="col-lg-12 col-md-12">
        <div class="widget-sidebar">
            @if (Auth::user())
                @can('portfolio view')
                    <div class="easier-mode">
                        @if (Auth::user())
                            @can('portfolio view')
                                <!-- hover effect for mobile devices  -->
                                <div class="click-icon d-md-none text-center">
                                    <button class="custom-btn text-white">
                                        <i class="fa fa-mobile-alt text-white"></i> {{ __('content.touch') }}
                                    </button>
                                </div>
                            @endcan
                        @endif
                        <div class="easier-section-area">
                        @endcan
            @endif

            @if (Auth::user())
                @can('portfolio view')
            </div>
            <div class="easier-middle">
                @php
                    $url = request()->path();
                    $modified_url = str_replace('/', '-bracket-', $url);
                @endphp
                <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                    @csrf
                    <input type="hidden" name="route" value="portfolio-detail.create">
                    <input type="hidden" name="style" value="{{ $portfolio->id }}">
                    <input type="hidden" name="site_url" value="{{ $modified_url }}">
                    <button type="submit" class="custom-btn text-white me-2 mb-2">
                        <i class="fa fa-edit text-white"></i> {{ __('content.edit_section_title_description') }}
                    </button>
                </form>
                <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                    @csrf
                    <input type="hidden" name="route" value="portfolio-detail.create">
                    <input type="hidden" name="style" value="{{ $portfolio->id }}">
                    <input type="hidden" name="site_url" value="{{ $modified_url }}">
                    <button type="submit" class="custom-btn text-white me-2">
                        <i class="fa fa-edit text-white"></i> {{ __('content.add_portfolio_detail') }}
                    </button>
                </form>
            </div>
        </div>
    @endcan
    @endif

    <hr>


    <!--// Application Form Section //-->
    <section class="section" id="application-form">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card shadow-sm">
                        <div class="card-body p-5">
                            <h2 class="text-center mb-4">Apply For {{ $portfolio->title }} Testing</h2>

                            <!-- Status Messages -->
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Please fix these errors:</strong>
                                    <ul class="mt-2 mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <form id="payment-form" action="{{ route('send.mail_dot') }}" method="POST">
                                @csrf
                                <input type="hidden" name="payment_intent_id" id="payment_intent_id">
                                <input type="hidden" name="test_name" id="test_name"
                                    value="{{ $portfolio->title }}">

                                <!-- ========== Personal Information Section ========== -->
                                <div class="mb-5">
                                    <h4 class="section-title mb-4">
                                        <i class="fas fa-user-circle me-2"></i> Personal Information
                                    </h4>

                                    <div class="row g-3">
                                        <!-- First Name -->
                                        <div class="col-md-6">
                                            <div class="form-floating">

                                                <input type="text"
                                                    class="form-control @error('first_name') is-invalid @enderror"
                                                    name="first_name" id="first_name"
                                                    value="{{ old('first_name') }}" placeholder="First name"
                                                    required>
                                                <label for="first_name">First Name <span
                                                        class="text-red">*</span></label>
                                                @error('first_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Last Name -->
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text"
                                                    class="form-control @error('last_name') is-invalid @enderror"
                                                    name="last_name" id="last_name" value="{{ old('last_name') }}"
                                                    placeholder="Last name" required>
                                                <label for="last_name">Last Name *</label>
                                                @error('last_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Email -->
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    name="email" id="email" value="{{ old('email') }}"
                                                    placeholder="Email address" required>
                                                <label for="email">Email Address *</label>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Phone -->
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="tel"
                                                    class="form-control @error('phone') is-invalid @enderror"
                                                    name="phone" id="phone" value="{{ old('phone') }}"
                                                    placeholder="Phone number" required>
                                                <label for="phone">Phone Number *</label>
                                                @error('phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Address -->
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <input type="text"
                                                    class="form-control @error('address') is-invalid @enderror"
                                                    name="address" id="address" value="{{ old('address') }}"
                                                    placeholder="Street address" required>
                                                <label for="address">Street Address </label>
                                                @error('address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Gender -->
                                        <div class="col-12">
                                            <label class="form-label">Gender *</label>
                                            <div class="d-flex gap-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="gender"
                                                        id="male" value="Male" @checked(old('gender') == 'Male')
                                                        required>
                                                    <label class="form-check-label" for="male">Male</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="gender"
                                                        id="female" value="Female" @checked(old('gender') == 'Female')>
                                                    <label class="form-check-label" for="female">Female</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="gender"
                                                        id="other" value="Other" @checked(old('gender') == 'Other')>
                                                    <label class="form-check-label" for="other">Other</label>
                                                </div>
                                            </div>
                                            @error('gender')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- ========== Testing Information Section ========== -->
                                <div class="mb-5">
                                    <h4 class="section-title mb-4">
                                        <i class="fas fa-flask me-2"></i> Testing Information
                                    </h4>

                                    <div class="row g-3">
                                        <!-- Test Date -->
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="date"
                                                    class="form-control @error('date') is-invalid @enderror"
                                                    name="date" id="date"
                                                    value="{{ old('date', date('Y-m-d')) }}" required>
                                                <label for="date">Preferred Test Date </label>
                                                @error('date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Preferred Location -->
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text"
                                                    class="form-control @error('preferred_location') is-invalid @enderror"
                                                    name="preferred_location" id="preferred_location"
                                                    value="{{ old('preferred_location') }}" placeholder="Location"
                                                    required>
                                                <label for="preferred_location">Preferred Location </label>
                                                @error('preferred_location')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Employer Information -->
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text"
                                                    class="form-control @error('employee_name') is-invalid @enderror"
                                                    name="employee_name" id="employee_name"
                                                    value="{{ old('employee_name') }}" placeholder="Employer name"
                                                    required>
                                                <label for="employee_name">Employer Name *</label>
                                                @error('employee_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Company Name -->
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text"
                                                    class="form-control @error('company_name') is-invalid @enderror"
                                                    name="company_name" id="company_name"
                                                    value="{{ old('company_name') }}" placeholder="Company name">
                                                <label for="company_name">Company Name</label>
                                                @error('company_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Accounting Email -->
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="email"
                                                    class="form-control @error('accounting_email') is-invalid @enderror"
                                                    name="accounting_email" id="accounting_email"
                                                    value="{{ old('accounting_email') }}"
                                                    placeholder="Accounting email">
                                                <label for="accounting_email">Accounting Email</label>
                                                @error('accounting_email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Reason for Testing -->
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select
                                                    class="form-select @error('reason_for_testing') is-invalid @enderror"
                                                    name="reason_for_testing" id="reason_for_testing" required>
                                                    <option value="" disabled selected>Select a reason</option>
                                                    <option value="Follow Up Test" @selected(old('reason_for_testing') == 'Follow Up Test')>Follow
                                                        Up Test</option>
                                                    <option value="Pre Employment" @selected(old('reason_for_testing') == 'Pre Employment')>Pre
                                                        Employment</option>
                                                    <option value="Random" @selected(old('reason_for_testing') == 'Random')>Random</option>
                                                    <option value="Return to Duty" @selected(old('reason_for_testing') == 'Return to Duty')>Return
                                                        to Duty</option>
                                                    <option value="Post Accident" @selected(old('reason_for_testing') == 'Post Accident')>Post
                                                        Accident</option>
                                                    <option value="Promotion" @selected(old('reason_for_testing') == 'Promotion')>Promotion
                                                    </option>
                                                    <option value="Reasonable Cause/Suspicion"
                                                        @selected(old('reason_for_testing') == 'Reasonable Cause/Suspicion')>Reasonable Cause/Suspicion</option>
                                                    <option value="Other" @selected(old('reason_for_testing') == 'Other')>Other</option>
                                                </select>
                                                <label for="reason_for_testing">Reason for Testing *</label>
                                                @error('reason_for_testing')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Price (readonly) -->
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <input type="text" class="form-control bg-light" name="price"
                                                    id="price" value="${{ $portfolio->price }}" readonly>
                                                <label for="price">Total Amount</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ========== Payment Section ========== -->
                                <div class="mb-5">
                                    <h4 class="section-title mb-4">
                                        <i class="fas fa-credit-card me-2"></i> Payment Details
                                    </h4>

                                    <div class="payment-card-container p-4 bg-light rounded-3">
                                        <!-- Cardholder Name -->
                                        <div class="form-floating mb-3">
                                            <input type="text"
                                                class="form-control @error('cardholder_name') is-invalid @enderror"
                                                id="cardholder-name" placeholder="Cardholder Name" required>
                                            <label for="cardholder-name">Cardholder Name *</label>
                                        </div>

                                        <!-- Card Elements -->
                                        <div class="mb-3">
                                            <label class="form-label">Card Number *</label>
                                            <div id="card-number" class="form-control p-3"></div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Expiration Date *</label>
                                                <div id="card-expiry" class="form-control p-3"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">CVC *</label>
                                                <div id="card-cvc" class="form-control p-3"></div>
                                            </div>
                                        </div>

                                        <div class="row g-3 mt-2">
                                            <div class="col-md-6">
                                                <label class="form-label">ZIP Code *</label>
                                                <div id="postal-code" class="form-control p-3"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Country *</label>
                                                <div class="form-floating">
                                                    {{-- <select class="form-select" id="country" name="country"
                                                        required>
                                                        <option value="" selected disabled>Select Country
                                                        </option>
                                                        <option value="US">United States</option>
                                                        <option value="CA">Canada</option>
                                                        <option value="GB">United Kingdom</option>
                                                        <option value="AU">Australia</option>
                                                    </select> --}}
                                                    <select class="form-select" id="country" name="country"
                                                        required>
                                                        <option value="" selected disabled>Select Country
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Card Errors -->
                                        <div id="card-errors" class="text-danger mt-3 small" role="alert"></div>

                                        <!-- Security Badge -->
                                        <div class="d-flex align-items-center mt-4 text-muted">
                                            <i class="fas fa-lock text-success me-2"></i>
                                            <small>Your payment is secured with 256-bit SSL encryption</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- ========== Terms and Conditions ========== -->
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input @error('terms') is-invalid @enderror"
                                            type="checkbox" id="terms-check" required>
                                        <label class="form-check-label" for="terms-check">
                                            I agree to the <a href="#" data-bs-toggle="modal"
                                                data-bs-target="#termsModal">Terms and Conditions</a>
                                            and <a href="#" data-bs-toggle="modal"
                                                data-bs-target="#privacyModal">Privacy Policy</a> *
                                        </label>
                                        @error('terms')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- ========== Submit Button ========== -->
                                <div class="text-center mt-4">
                                    <button type="button" id="pay-button" class="btn btn-primary btn-lg w-100 py-3">
                                        <span id="pay-button-text">Pay & Schedule Test -
                                            ${{ $portfolio->price }}</span>
                                        <span id="pay-button-loader"
                                            class="spinner-border spinner-border-sm ms-2 d-none"></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- @include('partials.terms-conditions')  --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">I Understand</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Privacy Policy Modal -->
    <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="privacyModalLabel">Privacy Policy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- @include('partials.privacy-policy')  --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">I Understand</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Success Modal -->
    <div class="modal fade" id="paymentSuccessModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center p-5">
                    <div class="mb-4">
                        <div class="checkmark-circle">
                            <div class="checkmark draw"></div>
                        </div>
                    </div>
                    <h4 class="mb-3">Payment Successful!</h4>
                    <p class="mb-4">Thank you for your payment. Your test has been scheduled successfully.</p>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Continue</button>
                </div>
            </div>
        </div>
    </div>

    </div>
    </div>
    </div>
    </div>
</section>
<!--// Portfolio Single Section End //-->


<!-- Stripe.js -->
<script src="https://js.stripe.com/v3/"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize Stripe
        const stripe = Stripe("{{ env('STRIPE_PUBLIC') }}");
        const elements = stripe.elements();
        const form = document.getElementById('payment-form');
        const payButton = document.getElementById('pay-button');
        const payButtonText = document.getElementById('pay-button-text');
        const payButtonLoader = document.getElementById('pay-button-loader');
        const errorContainer = document.getElementById('card-errors');
        const price = document.getElementById('price') ? document.getElementById('price').value.replace(
            /[^\d.]/g, '') : '0';

        // Element styles
        const elementStyles = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

        // Create Stripe elements
        const cardNumber = elements.create('cardNumber', {
            style: elementStyles,
            showIcon: true,
            placeholder: '4242 4242 4242 4242',
            classes: {
                base: 'StripeElement',
                complete: 'StripeElement--complete',
                focus: 'StripeElement--focus',
                invalid: 'StripeElement--invalid'
            }
        });
        const cardExpiry = elements.create('cardExpiry', {
            style: elementStyles
        });
        const cardCvc = elements.create('cardCvc', {
            style: elementStyles
        });
        const postalCode = elements.create('postalCode', {
            style: elementStyles
        });

        // Mount elements
        cardNumber.mount('#card-number');
        cardExpiry.mount('#card-expiry');
        cardCvc.mount('#card-cvc');
        postalCode.mount('#postal-code');

        // Real-time validation
        const stripeElements = [cardNumber, cardExpiry, cardCvc, postalCode];
        stripeElements.forEach(element => {
            element.addEventListener('change', (event) => {
                if (event.error) {
                    showError(event.error.message);
                } else {
                    clearError();
                }
            });
        });

        // Form validation
        function validateForm() {
            const requiredFields = [
                'first_name', 'last_name', 'email', 'phone',
                'date', 'reason_for_testing'
            ];

            let isValid = true;
            clearError();

            // Check required fields
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (!field) {
                    console.error(`Field with ID ${fieldId} not found`);
                    isValid = false;
                    return;
                }

                if (!field.value.trim()) {
                    markFieldInvalid(field);
                    isValid = false;
                } else {
                    markFieldValid(field);
                }
            });

            // Email validation
            const email = document.getElementById('email');
            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
                markFieldInvalid(email);
                isValid = false;
            }

            // Date validation

            const dateField = document.getElementById('date');
            if (dateField) {
                const selectedDateStr = dateField.value;
                const today = new Date();
                const todayStr = today.toISOString().split('T')[0]; // Format as YYYY-MM-DD

                if (!selectedDateStr || selectedDateStr < todayStr) {
                    markFieldInvalid(dateField);
                    isValid = false;
                }
            }

            // Gender validation
            if (!document.querySelector('input[name="gender"]:checked')) {
                showError('Please select your gender');
                isValid = false;
            }

            // Terms checkbox
            if (!document.getElementById('terms-check').checked) {
                showError('You must agree to the terms and conditions');
                isValid = false;
            }

            return isValid;
        }

        // Payment handler
        payButton.addEventListener('click', async (e) => {
            e.preventDefault();

            if (!validateForm()) return;

            setLoading(true);

            try {
                // Create payment intent
                const response = await fetch("/create-payment-intent", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                            .content
                    },
                    body: JSON.stringify({
                        price: price,
                        test_name: document.getElementById('test_name').value,
                        country: document.getElementById('country').value
                    })
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.error || 'Payment failed');

                // Confirm card payment
                const {
                    paymentIntent,
                    error
                } = await stripe.confirmCardPayment(data.client_secret, {
                    payment_method: {
                        card: cardNumber,
                        billing_details: {
                            name: document.getElementById('cardholder-name').value,
                            email: document.getElementById('email').value,
                            phone: document.getElementById('phone').value,
                            address: {
                                line1: document.getElementById('address').value,
                                postal_code: document.getElementById('postal-code').value,
                                country: document.getElementById('country').value
                            }
                        }
                    }
                });

                if (error) throw error;

                document.getElementById('payment_intent_id').value = paymentIntent.id;
                showSuccess();

                // Submit form after 1 second to show success state
                setTimeout(() => form.submit(), 1000);

            } catch (error) {
                console.error('Payment error:', error);
                showError(error.message || 'An error occurred during payment');
                setLoading(false);
            }
        });

        // Helper functions
        function setLoading(isLoading) {
            payButton.disabled = isLoading;
            payButtonText.textContent = isLoading ? 'Processing Payment...' : `Pay & Schedule Test - $${price}`;
            payButtonLoader.classList.toggle('d-none', !isLoading);
        }

        function showError(message) {
            errorContainer.innerHTML = `<div class="alert alert-danger">${message}</div>`;
            scrollToElement(errorContainer);
        }

        function clearError() {
            errorContainer.innerHTML = '';
        }

        function markFieldInvalid(field) {
            if (field) {
                field.classList.add('is-invalid');
                field.classList.remove('is-valid');
            }
        }

        function markFieldValid(field) {
            if (field) {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
            }
        }

        function showSuccess() {
            payButton.classList.remove('btn-primary');
            payButton.classList.add('btn-success');
            payButtonText.textContent = 'Payment Successful!';
        }

        function scrollToElement(element) {
            if (element) {
                element.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        }

        // Load countries
        fetch('https://restcountries.com/v3.1/all?fields=name,cca2')
            .then(res => res.json())
            .then(data => {
                const countrySelect = document.getElementById('country');
                if (!countrySelect) return;

                // Sort countries alphabetically
                const sortedCountries = data.sort((a, b) => a.name.common.localeCompare(b.name.common));

                sortedCountries.forEach(country => {
                    const option = document.createElement('option');
                    option.value = country.cca2;
                    option.textContent = country.name.common;
                    countrySelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error fetching countries:', error);
            });

    });
</script>

<style>
    /* Custom styles for the application form */
    .section-title {
        color: #2c3e50;
        font-weight: 600;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
    }

    .payment-card-container {
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        background-color: #f8f9fa;
    }

    .checkmark-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #28a745;
        margin: 0 auto 20px;
    }

    .checkmark {
        width: 40px;
        height: 40px;
    }

    .checkmark.draw:after {
        animation: checkmark 0.8s ease forwards;
        content: '';
        display: block;
        position: relative;
        left: 10px;
        top: 15px;
        width: 15px;
        height: 30px;
        border-right: 4px solid white;
        border-top: 4px solid white;
        transform: scaleX(-1) rotate(135deg);
    }

    @keyframes checkmark {
        0% {
            height: 0;
            width: 0;
            opacity: 1;
        }

        20% {
            height: 0;
            width: 15px;
            opacity: 1;
        }

        40% {
            height: 30px;
            width: 15px;
            opacity: 1;
        }

        100% {
            height: 30px;
            width: 15px;
            opacity: 1;
        }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .payment-card-container {
            padding: 1rem;
        }

        #pay-button {
            padding: 0.75rem;
            font-size: 1rem;
        }
    }

    /* Style for country select */
    #country {
        height: 50px;
        padding: 10px 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }

    /* Make sure the select matches other Stripe elements */
    .StripeElement+.form-select {
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 10px 12px;
        height: 50px;
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        #country {
            height: 45px;
            padding: 8px 10px;
        }
    }

    .StripeElement {
        box-sizing: border-box;
        padding: 10px 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        background-color: white;
    }

    .StripeElement--focus {
        box-shadow: 0 1px 3px 0 #cfd7df;
    }

    .StripeElement--invalid {
        border-color: #fa755a;
    }

    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }

    /* Responsive adjustments for card elements */
    @media (max-width: 576px) {
        .payment-card-container {
            padding: 15px;
        }

        #card-number,
        #card-expiry,
        #card-cvc,
        #postal-code {
            height: 45px !important;
            padding: 8px 10px !important;
        }
    }

    /* Custom styles for the payment section */
    .section-subtitle {
        color: #4a5568;
        font-weight: 600;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 8px;
        margin-bottom: 20px;
    }

    .payment-card-container {
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .payment-card-container:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    /* Checkmark animation for success modal */
    .checkmark-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #48bb78;
        margin: 0 auto 20px;
    }

    .checkmark {
        width: 40px;
        height: 40px;
    }

    .checkmark.draw:after {
        animation-duration: 1.2s;
        animation-timing-function: ease;
        animation-name: checkmark;
        transform: scaleX(-1) rotate(135deg);
        animation-fill-mode: forwards;
        content: '';
        display: block;
        position: relative;
        left: 10px;
        top: 15px;
        width: 15px;
        height: 30px;
        border-right: 4px solid white;
        border-top: 4px solid white;
    }

    @keyframes checkmark {
        0% {
            height: 0;
            width: 0;
            opacity: 1;
        }

        20% {
            height: 0;
            width: 15px;
            opacity: 1;
        }

        40% {
            height: 30px;
            width: 15px;
            opacity: 1;
        }

        100% {
            height: 30px;
            width: 15px;
            opacity: 1;
        }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .payment-card-container {
            padding: 20px 15px;
        }

        #pay-button {
            padding: 12px;
            font-size: 16px;
        }
    }
</style>
