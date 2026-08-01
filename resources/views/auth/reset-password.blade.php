@extends('auth.layout')

@section('title', __('Reset password'))
@section('heading', __('Reset password'))
@section('description', __('Choose a new password for your account.'))

@section('content')
    @include('auth.partials.validation-errors')

    <form method="POST" action="{{ route('password.update') }}" class="auth-form" data-auth-form>
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="auth-field">
            <label class="auth-label" for="email">{{ __('Email') }}</label>
            <input
                id="email"
                class="auth-input @error('email') is-invalid @enderror"
                type="email"
                name="email"
                value="{{ old('email', $request->email) }}"
                required
                autofocus
                autocomplete="username"
            >
        </div>

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

        <button type="submit" class="auth-btn" data-auth-submit data-loading-text="{{ __('Resetting…') }}">
            <span class="auth-spinner" aria-hidden="true"></span>
            <span class="auth-btn-label">{{ __('Reset Password') }}</span>
        </button>
    </form>
@endsection
