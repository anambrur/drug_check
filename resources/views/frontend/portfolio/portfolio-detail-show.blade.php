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
                    <a href="#"><span
                            class="far fa-calendar-alt"></span>{{ Carbon\Carbon::parse($portfolio->created_at)->isoFormat('DD') }}
                        {{ Carbon\Carbon::parse($portfolio->created_at)->isoFormat('MMMM') }}
                        {{ Carbon\Carbon::parse($portfolio->created_at)->isoFormat('GGGG') }}</a>
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
                            <img src="{{ asset('uploads/img/dummy/600x600.jpg') }}" alt="Portfolio image"
                                class="img-fluid">
                        </div>
                        <div class="col-md-6 col-sm-6 portfolio-grid-img">
                            <img src="{{ asset('uploads/img/dummy/600x600.jpg') }}" alt="Portfolio image"
                                class="img-fluid">
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
        <div class="contact-form-wrap p-3 w-100">
            <h5 class="inner-header-title text-center">Apply For Testing</h5>
            <form id="payment-form" action="{{ route('send.mail_dot') }}" method="POST">
                @csrf
                <input type="hidden" name="payment_intent_id" id="payment_intent_id">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" class="form-control mb-2" name="first_name" placeholder="First name"
                            required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control mb-2" name="last_name" placeholder="Last name"
                            required>
                    </div>
                    <div class="col-md-6">
                        <input type="email" class="form-control mb-2" name="email"
                            placeholder="{{ __('frontend.email') }}" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control mb-2" name="phone"
                            placeholder="{{ __('frontend.phone') }}" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control mb-2" name="address"
                            placeholder="{{ __('frontend.address') }}" required>
                    </div>
                    <div class="col-md-6">
                        <input type="date" class="form-control mb-2" name="date" value="{{ date('Y-m-d') }}"
                            required>
                    </div>
                    <div class="col-md-12 text-center py-2">
                        <label class="font-weight-bold">Gender:</label>
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
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control mb-2" name="preferred_location"
                            placeholder="Preferred Location" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control mb-2" name="employee_name"
                            placeholder="Employer's Name" required>
                    </div>
                    <div class="col-md-6">
                        <select class="form-select mb-2" name="reason_for_testing">
                            <option value="" disabled selected>Choose an option</option>
                            <option value="Follow Up Test">Follow Up Test</option>
                            <option value="Pre Employment">Pre Employment</option>
                            <option value="Random">Random</option>
                            <option value="Return to Duty">Return to Duty</option>
                            <option value="Post Accident">Post Accident</option>
                            <option value="Promotion">Promotion</option>
                            <option value="Reasonable Cause/Suspicion">Reasonable Cause/Suspicion</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control mb-2" name="price" id="price"
                            placeholder="Price" value="${{ $portfolio->price }}" readonly>
                    </div>
                    <div class="col-md-12">
                        <label class="font-weight-bold">Enter Payment Details</label>
                        <div class="payment-card-container p-3"
                            style="border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="font-weight-bold">Accepted Cards:</span>
                                <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Mastercard-logo.png"
                                    width="50" alt="Mastercard">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png"
                                    width="50" alt="Visa">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg"
                                    width="50" alt="PayPal">
                            </div>
                            <input type="text" class="form-control mb-2" id="cardholder-name"
                                placeholder="Cardholder Name" required>
                            <div id="card-element" class="form-control p-2 mt-2"
                                style="border-radius: 4px; background: white;"></div>
                            <div id="card-errors" class="text-danger mt-2" role="alert"></div>
                        </div>
                    </div>
                    <div class="col-md-12 text-center mt-3">
                        <button type="button" id="pay-button" class="primary-btn text-white px-4 py-2">
                            <span id="pay-button-text">Pay & Schedule Test</span>
                            <span id="pay-button-loader" style="display: none; position: absolute; right: 10px;">
                                <i class="fa fa-spinner fa-spin"></i>
                            </span>
                        </button>
                    </div>
                </div>
            </form>
            <!-- Stripe.js -->
            <script src="https://js.stripe.com/v3/"></script>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    let stripe = Stripe("{{ env('STRIPE_PUBLIC') }}");
                    let elements = stripe.elements();
                    let cardElement = elements.create("card");
                    cardElement.mount("#card-element");

                    let payButton = document.getElementById("pay-button");
                    let payButtonText = document.getElementById("pay-button-text");
                    let payButtonLoader = document.getElementById("pay-button-loader");

                    payButton.addEventListener("click", async function() {
                        let cardholderName = document.getElementById("cardholder-name").value;
                        if (!cardholderName) {
                            document.getElementById("card-errors").textContent =
                                "Please enter the cardholder name.";
                            return;
                        }

                        let price = document.getElementById("price").value.replace(/[^\d.]/g, '');

                        // Show loading state
                        payButton.disabled = true;
                        payButtonText.textContent = "Processing...";
                        payButtonLoader.style.display = "inline-block";

                        // Fetch Payment Intent
                        let response = await fetch("/create-payment-intent", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute("content")
                            },
                            body: JSON.stringify({
                                price: price
                            })
                        });

                        let data = await response.json();
                        let clientSecret = data.client_secret;

                        let {
                            paymentIntent,
                            error
                        } = await stripe.confirmCardPayment(clientSecret, {
                            payment_method: {
                                card: cardElement,
                                billing_details: {
                                    name: cardholderName
                                }
                            }
                        });

                        if (error) {
                            document.getElementById("card-errors").textContent = error.message;
                            payButton.disabled = false;
                            payButtonText.textContent = "Pay & Schedule Test";
                            payButtonLoader.style.display = "none";
                        } else {
                            document.getElementById("payment_intent_id").value = paymentIntent.id;
                            document.getElementById("payment-form").submit();
                        }
                    });
                });
            </script>
        </div>
    </div>

    </div>
    </div>
    </div>
    </div>
</section>
<!--// Portfolio Single Section End //-->
