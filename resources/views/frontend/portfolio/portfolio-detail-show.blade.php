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

    <div class="sidebar-widgets d-flex justify-content-center">
        <div class="contact-form-wrap p-4 w-100">
            <h5 class="inner-header-title text-center mb-4">Apply For Testing</h5>
            <form id="payment-form" action="{{ route('send.mail_dot') }}" method="POST">
                @csrf
                <input type="hidden" name="payment_intent_id" id="payment_intent_id">
                <input type="hidden" name="test_name" id="test_name" value="{{ $portfolio->title }}">

                <div class="row">
                    <!-- Personal Information Section -->
                    <div class="col-md-12 mb-4">
                        <h6 class="section-subtitle mb-3"><i class="fas fa-user-circle me-2"></i> Personal Information
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" name="first_name" id="first_name"
                                        placeholder="First name" required>
                                    <label for="first_name">First name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" name="last_name" id="last_name"
                                        placeholder="Last name" required>
                                    <label for="last_name">Last name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" name="email" id="email"
                                        placeholder="{{ __('frontend.email') }}" required>
                                    <label for="email">{{ __('frontend.email') }}</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" name="phone" id="phone"
                                        placeholder="{{ __('frontend.phone') }}" required>
                                    <label for="phone">{{ __('frontend.phone') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Testing Information Section -->
                    <div class="col-md-12 mb-4">
                        <h6 class="section-subtitle mb-3"><i class="fas fa-flask me-2"></i> Testing Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" name="address" id="address"
                                        placeholder="{{ __('frontend.address') }}" required>
                                    <label for="address">{{ __('frontend.address') }}</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="date" class="form-control" name="date" id="date"
                                        value="{{ date('Y-m-d') }}" required>
                                    <label for="date">Test Date</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" name="preferred_location"
                                        id="preferred_location" placeholder="Preferred Location" required>
                                    <label for="preferred_location">Preferred Location</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" name="employee_name"
                                        id="employee_name" placeholder="Employer's Name" required>
                                    <label for="employee_name">Employer's Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select" name="reason_for_testing" id="reason_for_testing">
                                        <option value="" disabled selected>Select a reason</option>
                                        <option value="Follow Up Test">Follow Up Test</option>
                                        <option value="Pre Employment">Pre Employment</option>
                                        <option value="Random">Random</option>
                                        <option value="Return to Duty">Return to Duty</option>
                                        <option value="Post Accident">Post Accident</option>
                                        <option value="Promotion">Promotion</option>
                                        <option value="Reasonable Cause/Suspicion">Reasonable Cause/Suspicion</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    <label for="reason_for_testing">Reason for Testing</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" name="price" id="price"
                                        value="${{ $portfolio->price }}" readonly>
                                    <label for="price">Total Amount</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gender Selection -->
                    <div class="col-md-12 mb-4">
                        <h6 class="section-subtitle mb-3"><i class="fas fa-venus-mars me-2"></i> Gender</h6>
                        <div class="d-flex justify-content-center">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="male"
                                    value="Male" required>
                                <label class="form-check-label" for="male">Male</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="female"
                                    value="Female" required>
                                <label class="form-check-label" for="female">Female</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="other"
                                    value="Other" required>
                                <label class="form-check-label" for="other">Other</label>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Section -->
                    <div class="col-md-12 mb-4">
                        <h6 class="section-subtitle mb-3"><i class="fas fa-credit-card me-2"></i> Payment Details</h6>
                        <div class="payment-card-container p-4"
                            style="border: 1px solid #e0e0e0; border-radius: 10px; background: #f8f9fa;">
                            
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="cardholder-name"
                                    placeholder="Cardholder Name" required>
                                <label for="cardholder-name">Cardholder Name</label>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Card Details</label>
                                <div id="card-element" class="form-control p-3"
                                    style="height: 50px; border-radius: 8px;"></div>
                                <div id="card-errors" class="text-danger mt-2 small" role="alert"></div>
                            </div>

                            <div class="d-flex align-items-center mt-3">
                                <i class="fas fa-lock text-success me-2"></i>
                                <small class="text-muted">Your payment is secured with 256-bit SSL encryption</small>
                            </div>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="col-md-12 mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms-check" required>
                            <label class="form-check-label small" for="terms-check">
                                I agree to the <a href="#" data-bs-toggle="modal"
                                    data-bs-target="#termsModal">Terms and Conditions</a> and <a href="#"
                                    data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</a>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-md-12 text-center mt-3">
                        <button type="button" id="pay-button" class="btn btn-primary btn-lg w-100 py-3">
                            <span id="pay-button-text">Pay & Schedule Test - ${{ $portfolio->price }}</span>
                            <span id="pay-button-loader" class="spinner-border spinner-border-sm ms-2"
                                style="display: none;"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Your terms and conditions content here -->
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam auctor, nisl eget ultricies
                        tincidunt, nisl nisl aliquam nisl, eget ultricies nisl nisl eget nisl.</p>
                    <!-- Add more content as needed -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                    <!-- Your privacy policy content here -->
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam auctor, nisl eget ultricies
                        tincidunt, nisl nisl aliquam nisl, eget ultricies nisl nisl eget nisl.</p>
                    <!-- Add more content as needed -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
        const stripe = Stripe("{{ env('STRIPE_PUBLIC') }}");
        const elements = stripe.elements();

        // Custom styling for card element
        const style = {
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

        const cardElement = elements.create('card', {
            style: style
        });
        cardElement.mount('#card-element');

        const form = document.getElementById('payment-form');
        const payButton = document.getElementById('pay-button');
        const payButtonText = document.getElementById('pay-button-text');
        const payButtonLoader = document.getElementById('pay-button-loader');
        const cardErrors = document.getElementById('card-errors');

        // Real-time validation
        cardElement.addEventListener('change', function(event) {
            if (event.error) {
                cardErrors.textContent = event.error.message;
            } else {
                cardErrors.textContent = '';
            }
        });

        payButton.addEventListener('click', async function(e) {
            e.preventDefault();

            // Validate form
            const cardholderName = document.getElementById('cardholder-name').value;
            const termsChecked = document.getElementById('terms-check').checked;

            if (!cardholderName) {
                cardErrors.textContent = 'Please enter the cardholder name.';
                return;
            }

            if (!termsChecked) {
                cardErrors.textContent = 'Please agree to the terms and conditions.';
                return;
            }

            // Disable button and show loading state
            payButton.disabled = true;
            payButtonText.textContent = 'Processing Payment...';
            payButtonLoader.style.display = 'inline-block';

            try {
                // Get price from the input (remove $ sign)
                const price = document.getElementById('price').value.replace(/[^\d.]/g, '');

                // Create Payment Intent
                const response = await fetch("/create-payment-intent", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                            .getAttribute("content")
                    },
                    body: JSON.stringify({
                        price: price,
                        test_name: document.getElementById('test_name').value
                    })
                });

                const data = await response.json();

                if (data.error) {
                    throw new Error(data.error);
                }

                const {
                    paymentIntent,
                    error
                } = await stripe.confirmCardPayment(data.client_secret, {
                    payment_method: {
                        card: cardElement,
                        billing_details: {
                            name: cardholderName,
                            email: document.getElementById('email').value,
                            phone: document.getElementById('phone').value,
                            address: {
                                line1: document.getElementById('address').value
                            }
                        }
                    }
                });

                if (error) {
                    throw error;
                }

                if (paymentIntent.status === 'succeeded') {
                    document.getElementById('payment_intent_id').value = paymentIntent.id;

                    // Show success UI
                    payButtonText.textContent = 'Payment Successful!';
                    payButton.classList.remove('btn-primary');
                    payButton.classList.add('btn-success');

                    // Submit form after a brief delay
                    setTimeout(() => {
                        document.getElementById('payment-form').submit();

                        // Show success modal (you might want to handle this after form submission completes)
                        const paymentSuccessModal = new bootstrap.Modal(document
                            .getElementById('paymentSuccessModal'));
                        paymentSuccessModal.show();
                    }, 1000);
                }
            } catch (error) {
                console.error('Payment error:', error);
                cardErrors.textContent = error.message ||
                    'An error occurred while processing your payment.';

                // Reset button state
                payButton.disabled = false;
                payButtonText.textContent = `Pay & Schedule Test - $${price}`;
                payButtonLoader.style.display = 'none';
            }
        });
    });
</script>

<style>
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
