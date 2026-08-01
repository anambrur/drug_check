@extends('auth.layout')

@section('title', __('Two-factor challenge'))
@section('heading', __('Two-factor authentication'))
@section('description', __('Confirm access to your account.'))

@section('content')
    <div data-auth-2fa>
        <p class="auth-card-desc" data-auth-2fa-hint-code style="margin-bottom:1rem;">
            {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
        </p>
        <p class="auth-card-desc auth-hidden" data-auth-2fa-hint-recovery style="margin-bottom:1rem;">
            {{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}
        </p>

        @include('auth.partials.validation-errors')

        <form method="POST" action="{{ route('two-factor.login') }}" class="auth-form" data-auth-form>
            @csrf

            <div class="auth-field" data-auth-2fa-code>
                <label class="auth-label" for="code">{{ __('Code') }}</label>
                <input
                    id="code"
                    class="auth-input @error('code') is-invalid @enderror"
                    type="text"
                    inputmode="numeric"
                    name="code"
                    autofocus
                    autocomplete="one-time-code"
                >
            </div>

            <div class="auth-field auth-hidden" data-auth-2fa-recovery>
                <label class="auth-label" for="recovery_code">{{ __('Recovery Code') }}</label>
                <input
                    id="recovery_code"
                    class="auth-input @error('recovery_code') is-invalid @enderror"
                    type="text"
                    name="recovery_code"
                    autocomplete="one-time-code"
                >
            </div>

            <div class="auth-inline">
                <button type="button" class="auth-toggle-link" data-auth-2fa-use-recovery>
                    {{ __('Use a recovery code') }}
                </button>
                <button type="button" class="auth-toggle-link auth-hidden" data-auth-2fa-use-code>
                    {{ __('Use an authentication code') }}
                </button>
            </div>

            <button type="submit" class="auth-btn" data-auth-submit data-loading-text="{{ __('Verifying…') }}">
                <span class="auth-spinner" aria-hidden="true"></span>
                <span class="auth-btn-label">{{ __('Log in') }}</span>
            </button>
        </form>
    </div>
@endsection
