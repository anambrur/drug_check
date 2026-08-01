@extends('auth.layout')

@section('title', __('Log in'))
@section('heading', __('Welcome back'))
@section('description', __('Sign in to your account to continue.'))

@section('content')
    @include('auth.partials.validation-errors')

    @if (session('status'))
        <div class="auth-alert auth-alert-success" role="status">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="auth-form" data-auth-form>
        @csrf

        <div class="auth-field">
            <label class="auth-label" for="email">{{ __('Email') }}</label>
            <input
                id="email"
                class="auth-input @error('email') is-invalid @enderror"
                type="email"
                name="email"
                value="{{ old('email') }}"
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
                    autocomplete="current-password"
                >
                <button type="button" class="auth-password-toggle" data-auth-password-toggle aria-label="{{ __('Show password') }}">
                    {{ __('Show') }}
                </button>
            </div>
        </div>

        <div class="auth-inline">
            <label class="auth-checkbox" for="remember_me">
                <input id="remember_me" type="checkbox" name="remember">
                <span>{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="auth-link-muted" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <button type="submit" class="auth-btn" data-auth-submit data-loading-text="{{ __('Signing in…') }}">
            <span class="auth-spinner" aria-hidden="true"></span>
            <span class="auth-btn-label">{{ __('Log in') }}</span>
        </button>
    </form>

    @if (Route::has('register'))
        <p class="auth-footer">
            {{ __('Don\'t have an account?') }}
            <a href="{{ route('register') }}"><strong>{{ __('Create an Account') }}</strong></a>
        </p>
    @endif
@endsection
