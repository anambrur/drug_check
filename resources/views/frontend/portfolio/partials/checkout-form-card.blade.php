<div class="pf-card rc-animate">
    <div class="pf-header">
        <div class="d-flex align-items-start justify-content-between gap-3">
            <div>
                <span class="pill">{{ $isNonDot ? 'Non-DOT Testing' : 'DOT Testing' }}</span>
                <h4>{{ $isNonDot ? 'Apply for' : 'Schedule' }} {{ $portfolio->title }}</h4>
                <p>Complete your order details below, then proceed to secure Stripe checkout. Your order will be submitted to Quest Diagnostics automatically after payment.</p>
            </div>
            <div class="pf-header-icon d-none d-sm-flex">
                <i class="fas {{ $isNonDot ? 'fa-clipboard-list' : 'fa-users' }}"></i>
            </div>
        </div>
    </div>

    <div class="pf-body pb-0">
        <div id="pf-form-errors" class="pf-alert pf-alert-danger d-none mb-3" role="alert">
            <i class="fas fa-exclamation-triangle mt-1"></i>
            <div id="pf-form-errors-body"></div>
        </div>
        @if (session('success'))
            <div class="pf-alert pf-alert-success mb-3" role="alert">
                <i class="fas fa-check-circle mt-1"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif
        @if (session('info'))
            <div class="pf-alert pf-alert-success mb-3" role="alert">
                <i class="fas fa-info-circle mt-1"></i>
                <div>{{ session('info') }}</div>
            </div>
        @endif
        @if (session('error'))
            <div class="pf-alert pf-alert-danger mb-3" role="alert">
                <i class="fas fa-exclamation-circle mt-1"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif
    </div>

    <div class="pf-body pt-2">
        <form id="portfolio-checkout-form">
            @csrf
            <input type="hidden" name="portfolio_id" value="{{ $portfolio->id }}">
            <input type="hidden" name="test_type" value="{{ $isNonDot ? 'non_dot' : 'dot' }}">

            @include('quest.partials.order-fields', [
                'questDefaults' => $questDefaults,
                'questIsPhysical' => $questIsPhysical,
                'questIsEbat' => $questIsEbat,
            ])

            <div class="pf-price-display mb-4">
                <div>
                    <div class="label">Total Amount Due</div>
                    <div style="font-size:.75rem;color:var(--pf-muted);margin-top:2px;">{{ $portfolio->title }}</div>
                </div>
                <div class="amount">${{ $priceFormatted }}</div>
            </div>

            @if (!$isNonDot && ($employees ?? collect())->isEmpty())
                {{-- checkout disabled when no employees --}}
            @else
                <div class="pf-terms mb-4">
                    <input type="checkbox" id="terms-check" required>
                    <label class="pf-terms-label" for="terms-check">
                        I agree to the <a href="{{ route('frontend.terms-and-conditions') }}" target="_blank" rel="noopener">Terms and Conditions</a>
                        and <a href="{{ route('frontend.privacy-policy') }}" target="_blank" rel="noopener">Privacy Policy</a>.
                    </label>
                </div>

                <button type="button" id="portfolio-checkout-btn" class="pf-btn-submit">
                    <i class="fas fa-lock"></i>
                    Continue to Checkout — ${{ $priceFormatted }}
                </button>
                <p class="pf-secure"><i class="fas fa-shield-alt"></i> Secure payment via Stripe Checkout</p>
            @endif
        </form>
    </div>
</div>
