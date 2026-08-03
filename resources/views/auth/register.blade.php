@extends('auth.layout')

@section('title', __('Create an account'))
@section('heading', __('Create an account'))
@section('description', __('Register your company to get started.'))
@section('card_class', 'auth-card--wide auth-card--compact')

@section('content')
    @php
        $isDot = (string) old('is_dot', '0') === '1';
    @endphp

    @include('auth.partials.validation-errors')

    <form method="POST" action="{{ route('register') }}" class="auth-form auth-form--compact" data-auth-form data-auth-dot-form>
        @csrf

        <input type="hidden" name="is_dot" id="is_dot" value="{{ old('is_dot', '0') }}" data-auth-dot-input>

        <div class="auth-dot-toggle" role="group" aria-label="{{ __('Registration type') }}" data-auth-dot-toggle>
            <button
                type="button"
                class="auth-dot-option {{ !$isDot ? 'is-active' : '' }}"
                data-auth-dot-value="0"
                aria-pressed="{{ !$isDot ? 'true' : 'false' }}"
            >
                {{ __('Not DOT user') }}
            </button>
            <button
                type="button"
                class="auth-dot-option {{ $isDot ? 'is-active' : '' }}"
                data-auth-dot-value="1"
                aria-pressed="{{ $isDot ? 'true' : 'false' }}"
            >
                {{ __('DOT user') }}
            </button>
        </div>

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

        <div class="auth-dot-fields {{ $isDot ? '' : 'auth-hidden' }}" data-auth-dot-fields>
            <div class="auth-row">
                <div class="auth-field">
                    <label class="auth-label" for="company_name">{{ __('Company Name') }}</label>
                    <input
                        id="company_name"
                        class="auth-input @error('company_name') is-invalid @enderror"
                        type="text"
                        name="company_name"
                        value="{{ old('company_name') }}"
                        @if ($isDot) required @endif
                        autocomplete="organization"
                        data-auth-dot-required
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
                    @if ($isDot) required @endif
                    autocomplete="street-address"
                    data-auth-dot-required
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
                        @if ($isDot) required @endif
                        autocomplete="address-level2"
                        data-auth-dot-required
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
                        @if ($isDot) required @endif
                        autocomplete="address-level1"
                        data-auth-dot-required
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
                        @if ($isDot) required @endif
                        autocomplete="postal-code"
                        data-auth-dot-required
                    >
                </div>
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
