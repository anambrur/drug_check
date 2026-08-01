<aside class="auth-brand" aria-label="Brand">
    <div class="auth-brand-inner">
        <a href="{{ url('/') }}" class="auth-brand-logo">
            @if (!empty($authLogoLight) || !empty($authLogoDark))
                <img
                    src="{{ $authLogoLight ?? $authLogoDark }}"
                    alt="{{ config('app.name', 'MyDrugCheck') }}"
                    class="auth-brand-img auth-brand-img--light"
                    width="200"
                    height="48"
                >
            @else
                <span class="auth-brand-mark">{{ strtoupper(substr(config('app.name', 'M'), 0, 1)) }}</span>
                <span class="auth-brand-name">{{ config('app.name', 'MyDrugCheck') }}</span>
            @endif
        </a>

        <h2 class="auth-brand-title">Workplace drug &amp; alcohol compliance, simplified.</h2>
        <p class="auth-brand-text">
            Secure access for companies managing testing programs, results, and consortium enrollment in one place.
        </p>
    </div>

    <p class="auth-brand-footer">&copy; {{ date('Y') }} {{ config('app.name', 'MyDrugCheck') }}. All rights reserved.</p>
</aside>
