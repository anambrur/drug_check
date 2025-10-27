@extends('layouts.admin.master')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <div class="card border-0 shadow">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h3 class="mb-2 fw-bold">Complete Your Payment</h3>
                        <p class="mb-0 opacity-75">Secure payment for {{ $portfolio->title }}</p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <!-- Progress Steps -->
                        <div class="d-flex justify-content-between align-items-center position-relative mb-5">
                            <div class="position-absolute top-50 start-0 end-0 bg-light"
                                style="height: 2px; z-index: 1; margin-top: -1px;"></div>

                            <div class="d-flex flex-column align-items-center position-relative z-2 bg-white px-3">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mb-2"
                                    style="width: 40px; height: 40px; font-weight: 600;">1</div>
                                <span class="small fw-semibold text-primary">Payment</span>
                            </div>

                            <div class="d-flex flex-column align-items-center position-relative z-2 bg-white px-3">
                                <div class="rounded-circle bg-light text-muted d-flex align-items-center justify-content-center mb-2"
                                    style="width: 40px; height: 40px; font-weight: 600;">2</div>
                                <span class="small text-muted">Test Details</span>
                            </div>

                            <div class="d-flex flex-column align-items-center position-relative z-2 bg-white px-3">
                                <div class="rounded-circle bg-light text-muted d-flex align-items-center justify-content-center mb-2"
                                    style="width: 40px; height: 40px; font-weight: 600;">3</div>
                                <span class="small text-muted">Confirmation</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="employee_id" class="col-form-label"><i
                                    class="fas fa-user mr-2 text-primary"></i>Select Employee <span
                                    class="text-red">*</span></label>
                            <select class="form-control" name="employee_id" id="employee_id" required>
                                <option value="" selected disabled>Choose an employee...</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->first_name }} {{ $employee->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Select the employee who will take this test
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="bg-light rounded-3 p-4 mb-4 border">
                            <h5 class="text-center mb-3 fw-semibold">Order Summary</h5>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Test Type:</span>
                                <span class="fw-semibold">{{ $portfolio->title }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Unit Code:</span>
                                <span class="fw-semibold">{{ $portfolio->code }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-0">
                                <span class="text-muted">Amount:</span>
                                <span class="fw-bold text-success fs-5">${{ number_format($portfolio->price, 2) }}</span>
                            </div>
                        </div>

                        <form id="payment-form" method="POST">
                            @csrf
                            <input type="hidden" name="portfolio_id" value="{{ $portfolio->id }}">
                            <input type="hidden" name="payment_intent_id" id="payment_intent_id">
                            <input type="hidden" name="test_name" id="test_name" value="{{ $portfolio->title }}">
                            <input type="hidden" id="price" value="{{ $portfolio->price * 100 }}">
                            <!-- Add hidden input for employee_id -->
                            <input type="hidden" name="employee_id" id="selected_employee_id">

                            <!-- Rest of your payment form remains the same -->
                            <!-- Payment Card Section -->
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-3 text-dark">
                                    <i class="fas fa-credit-card mr-2 text-primary"></i>Payment Information
                                </h6>

                                <div class="border rounded-3 p-4 bg-white">
                                    <!-- Card Type Icons -->
                                    <div class="text-center mb-4">
                                        <div class="d-flex justify-content-center">
                                            <i class="fab fa-cc-visa fa-2x mx-2 text-primary"></i>
                                            <i class="fab fa-cc-mastercard fa-2x mx-2 text-danger"></i>
                                            <i class="fab fa-cc-amex fa-2x mx-2 text-info"></i>
                                            <i class="fab fa-cc-discover fa-2x mx-2 text-warning"></i>
                                        </div>
                                    </div>

                                    <!-- Cardholder Name -->
                                    <div class="form-floating mb-3">
                                        <label for="cardholder-name" class="col-form-label text-muted">Cardholder Name
                                            *</label>
                                        <input type="text" class="form-control" id="cardholder-name"
                                            placeholder="Cardholder Name" required>
                                    </div>

                                    <!-- Card Elements -->
                                    <div class="mb-3">
                                        <label class="col-form-label">Card Number *</label>
                                        <div id="card-number" class="border rounded p-2 bg-white"></div>
                                    </div>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="col-form-label">Expiration Date *</label>
                                            <div id="card-expiry" class="border rounded p-2 bg-white"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-form-label">CVC *</label>
                                            <div id="card-cvc" class="border rounded p-2 bg-white"></div>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="col-form-label">ZIP Code *</label>
                                            <div id="postal-code" class="border rounded p-2 bg-white"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="status" class="col-form-label">Country <span
                                                        class="text-red">*</span>
                                                </label>
                                                <select class="form-control" name="country" id="country" required>
                                                    <option value="" selected disabled>Select Country</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Card Errors -->
                                    <div id="card-errors" class="mt-3" role="alert"></div>

                                    <!-- Security Badge -->
                                    <div class="text-center mt-4 pt-3 border-top">
                                        <div class="d-flex align-items-center justify-content-center text-muted small">
                                            <i class="fas fa-lock text-success mr-2"></i>
                                            <span>Secure SSL Encryption • Your payment information is safe</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms and Submit -->
                            <div class="mt-4">
                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" id="terms-check" required>
                                    <label class="form-check-label small text-muted ml-3" for="terms-check">
                                        I agree to the <a href="#" class="text-decoration-none"
                                            data-bs-toggle="modal" data-bs-target="#termsModal">Terms of Service</a>
                                        and authorize the charge of ${{ number_format($portfolio->price, 2) }}
                                    </label>
                                </div>

                                <!-- Submit Button -->
                                <div class="text-center">
                                    <button type="button" id="pay-button" class="btn btn-primary btn-lg w-100 py-3">
                                        <span id="pay-button-text">
                                            <i class="fas fa-lock me-2"></i>
                                            Pay ${{ number_format($portfolio->price, 2) }} & Continue
                                        </span>
                                        <span id="pay-button-loader"
                                            class="spinner-border spinner-border-sm d-none ms-2"></span>
                                    </button>
                                    <p class="text-muted small mt-2 mb-0">
                                        You'll provide test details after payment confirmation
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Terms of Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6 class="fw-semibold">Payment Terms</h6>
                    <p class="text-muted">By completing this payment, you authorize us to charge your card for the test
                        service.
                        All payments are final and non-refundable once the test process has been initiated.</p>

                    <h6 class="fw-semibold mt-4">Service Terms</h6>
                    <p class="text-muted">After payment, you'll be redirected to complete the test information form.
                        The test must be scheduled within 30 days of payment.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center py-5">
                    <div class="rounded-circle bg-success d-flex align-items-center justify-content-center mx-auto mb-3"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-check text-white fa-lg"></i>
                    </div>
                    <h5 class="text-success mb-3 fw-semibold">Payment Successful!</h5>
                    <p class="text-muted mb-0">Redirecting to test details...</p>
                </div>
            </div>
        </div>
    </div>

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
            const price = {{ $portfolio->price * 100 }}; // Convert to cents
            const employeeSelect = document.getElementById('employee_id');
            const selectedEmployeeIdInput = document.getElementById('selected_employee_id');

            // Element styles using Bootstrap theme colors
            const elementStyles = {
                base: {
                    color: '#212529',
                    fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#6c757d'
                    }
                },
                invalid: {
                    color: '#dc3545',
                    iconColor: '#dc3545'
                }
            };

            // Create Stripe elements
            const cardNumber = elements.create('cardNumber', {
                style: elementStyles,
                showIcon: true,
                placeholder: '4242 4242 4242 4242'
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
                let isValid = true;
                clearError();

                // Employee validation
                const employeeId = employeeSelect.value;
                if (!employeeId) {
                    employeeSelect.classList.add('is-invalid');
                    showError('Please select an employee');
                    isValid = false;
                } else {
                    employeeSelect.classList.remove('is-invalid');
                    employeeSelect.classList.add('is-valid');
                    // Set the selected employee ID in the hidden input
                    selectedEmployeeIdInput.value = employeeId;
                }

                // Cardholder name validation
                const cardholderName = document.getElementById('cardholder-name');
                if (!cardholderName.value.trim()) {
                    cardholderName.classList.add('is-invalid');
                    showError('Please enter cardholder name');
                    isValid = false;
                } else {
                    cardholderName.classList.remove('is-invalid');
                    cardholderName.classList.add('is-valid');
                }

                // Country validation
                const country = document.getElementById('country');
                if (!country.value) {
                    country.classList.add('is-invalid');
                    showError('Please select your country');
                    isValid = false;
                } else {
                    country.classList.remove('is-invalid');
                    country.classList.add('is-valid');
                }

                // Terms checkbox
                if (!document.getElementById('terms-check').checked) {
                    showError('Please agree to the terms and conditions');
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
                    // Get the selected employee ID
                    const employeeId = employeeSelect.value;

                    // Create payment intent with employee_id
                    const response = await fetch("{{ route('admin.dot-test.process-payment') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            portfolio_id: {{ $portfolio->id }},
                            price: price,
                            employee_id: employeeId // Add employee_id to the request
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Payment processing failed');
                    }

                    // Confirm card payment
                    const {
                        paymentIntent,
                        error
                    } = await stripe.confirmCardPayment(data.client_secret, {
                        payment_method: {
                            card: cardNumber,
                            billing_details: {
                                name: document.getElementById('cardholder-name').value,
                                address: {
                                    country: document.getElementById('country').value,
                                    postal_code: document.getElementById('postal-code').value
                                }
                            }
                        }
                    });

                    if (error) {
                        throw error;
                    }

                    // Payment successful
                    document.getElementById('payment_intent_id').value = paymentIntent.id;

                    // Show success modal
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();

                    // Redirect to order form after 2 seconds
                    setTimeout(() => {
                        window.location.href =
                            "{{ route('admin.dot-test.order-form', '') }}/" + paymentIntent.id;
                    }, 2000);

                } catch (error) {
                    console.error('Payment error:', error);
                    showError(error.message || 'An error occurred during payment');
                    setLoading(false);
                }
            });

            // Helper functions
            function setLoading(isLoading) {
                payButton.disabled = isLoading;
                payButtonText.innerHTML = isLoading ?
                    'Processing Payment...' :
                    '<i class="fas fa-lock me-2"></i>Pay ${{ number_format($portfolio->price, 2) }} & Continue';
                payButtonLoader.classList.toggle('d-none', !isLoading);

                if (isLoading) {
                    payButton.classList.remove('btn-primary');
                    payButton.classList.add('btn-secondary');
                } else {
                    payButton.classList.remove('btn-secondary');
                    payButton.classList.add('btn-primary');
                }
            }

            function showError(message) {
                errorContainer.innerHTML = `<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div>${message}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>`;

                // Scroll to error
                errorContainer.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }

            function clearError() {
                errorContainer.innerHTML = '';
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

                    // Set default to US
                    countrySelect.value = 'US';
                })
                .catch(error => {
                    console.error('Error fetching countries:', error);
                });

            // Real-time form validation
            document.querySelectorAll('#employee_id, #cardholder-name, #country, #terms-check').forEach(element => {
                element.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                    clearError();
                });
            });

            // Employee select change
            employeeSelect.addEventListener('change', function() {
                this.classList.remove('is-invalid');
                clearError();
            });

            // Terms checkbox validation
            document.getElementById('terms-check').addEventListener('change', function() {
                clearError();
            });
        });
    </script>
@endsection
