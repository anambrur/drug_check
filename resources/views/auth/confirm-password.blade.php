@extends('auth.layout')

@section('title', __('Confirm password'))
@section('heading', __('Confirm password'))
@section('description', __('This is a secure area. Please confirm your password before continuing.'))

@section('content')
    @include('auth.partials.validation-errors')

    <form method="POST" action="{{ route('password.confirm') }}" class="auth-form" data-auth-form>
        @csrf

        <div class="auth-field">
            <label class="auth-label" for="password">{{ __('Password') }}</label>
            <div class="auth-password-wrap">
                <input
                    id="password"
                    class="auth-input @error('password') is-invalid @enderror"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    autofocus
                >
                <button type="button" class="auth-password-toggle" data-auth-password-toggle aria-label="{{ __('Show password') }}">
                    {{ __('Show') }}
                </button>
            </div>
        </div>

        <button type="submit" class="auth-btn" data-auth-submit data-loading-text="{{ __('Confirming…') }}">
            <span class="auth-spinner" aria-hidden="true"></span>
            <span class="auth-btn-label">{{ __('Confirm') }}</span>
        </button>
    </form>
@endsection
