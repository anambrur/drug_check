@extends('auth.layout')

@section('title', __('Create an account'))
@section('heading', __('Create an account'))
@section('description', __('Register your company to get started.'))
@section('card_class', 'auth-card--wide auth-card--compact')

@section('content')
    @include('auth.partials.validation-errors')

    <form method="POST" action="{{ route('register') }}" class="auth-form auth-form--compact" data-auth-form>
        @csrf

        <div class="auth-row">
            <div class="auth-field">
                <label class="auth-label" for="name">{{ __('Full Name') }}</label>
                <input
                    id="name"
                    class="auth-input @error('name') is-invalid @enderror"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    autocomplete="name"
                >
            </div>
            <div class="auth-field">
                <label class="auth-label" for="company_name">{{ __('Company Name') }}</label>
                <input
                    id="company_name"
                    class="auth-input @error('company_name') is-invalid @enderror"
                    type="text"
                    name="company_name"
                    value="{{ old('company_name') }}"
                    required
                    autocomplete="organization"
                >
            </div>
        </div>

        <div class="auth-row">
            <div class="auth-field">
                <label class="auth-label" for="email">{{ __('Email') }}</label>
                <input
                    id="email"
                    class="auth-input @error('email') is-invalid @enderror"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="username"
                >
            </div>
            <div class="auth-field">
                <label class="auth-label" for="phone">{{ __('Phone') }} <span class="auth-optional">({{ __('optional') }})</span></label>
                <input
                    id="phone"
                    class="auth-input @error('phone') is-invalid @enderror"
                    type="tel"
                    name="phone"
                    value="{{ old('phone') }}"
                    autocomplete="tel"
                >
            </div>
        </div>

        <div class="auth-field">
            <label class="auth-label" for="address">{{ __('Address') }}</label>
            <input
                id="address"
                class="auth-input @error('address') is-invalid @enderror"
                type="text"
                name="address"
                value="{{ old('address') }}"
                required
                autocomplete="street-address"
            >
        </div>

        <div class="auth-row auth-row--3">
            <div class="auth-field">
                <label class="auth-label" for="city">{{ __('City') }}</label>
                <input
                    id="city"
                    class="auth-input @error('city') is-invalid @enderror"
                    type="text"
                    name="city"
                    value="{{ old('city') }}"
                    required
                    autocomplete="address-level2"
                >
            </div>
            <div class="auth-field">
                <label class="auth-label" for="state">{{ __('State') }}</label>
                <input
                    id="state"
                    class="auth-input @error('state') is-invalid @enderror"
                    type="text"
                    name="state"
                    value="{{ old('state') }}"
                    required
                    autocomplete="address-level1"
                >
            </div>
            <div class="auth-field">
                <label class="auth-label" for="zip">{{ __('ZIP') }}</label>
                <input
                    id="zip"
                    class="auth-input @error('zip') is-invalid @enderror"
                    type="text"
                    name="zip"
                    value="{{ old('zip') }}"
                    required
                    autocomplete="postal-code"
                >
            </div>
        </div>

        <div class="auth-row">
            <div class="auth-field">
                <label class="auth-label" for="password">{{ __('Password') }}</label>
                <div class="auth-password-wrap">
                    <input
                        id="password"
                        class="auth-input @error('password') is-invalid @enderror"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                    >
                    <button type="button" class="auth-password-toggle" data-auth-password-toggle aria-label="{{ __('Show password') }}">
                        {{ __('Show') }}
                    </button>
                </div>
            </div>
            <div class="auth-field">
                <label class="auth-label" for="password_confirmation">{{ __('Confirm Password') }}</label>
                <div class="auth-password-wrap">
                    <input
                        id="password_confirmation"
                        class="auth-input"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                    >
                    <button type="button" class="auth-password-toggle" data-auth-password-toggle aria-label="{{ __('Show password') }}">
                        {{ __('Show') }}
                    </button>
                </div>
            </div>
        </div>

        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            <label class="auth-checkbox" for="terms">
                <input id="terms" type="checkbox" name="terms" required>
                <span>
                    {!! __('I agree to the :terms_of_service and :privacy_policy', [
                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'">'.__('Terms of Service').'</a>',
                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'">'.__('Privacy Policy').'</a>',
                    ]) !!}
                </span>
            </label>
        @endif

        <button type="submit" class="auth-btn" data-auth-submit data-loading-text="{{ __('Creating account…') }}">
            <span class="auth-spinner" aria-hidden="true"></span>
            <span class="auth-btn-label">{{ __('Create Account') }}</span>
        </button>
    </form>

    <p class="auth-footer">
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}"><strong>{{ __('Log in') }}</strong></a>
    </p>
@endsection
