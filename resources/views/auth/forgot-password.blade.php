@extends('auth.layout')

@section('title', __('Forgot password'))
@section('heading', __('Forgot password'))
@section('description', __('Enter your email and we will send you a reset link.'))

@section('content')
    @include('auth.partials.validation-errors')

    @if (session('status'))
        <div class="auth-alert auth-alert-success" role="status">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="auth-form" data-auth-form>
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

        <button type="submit" class="auth-btn" data-auth-submit data-loading-text="{{ __('Sending…') }}">
            <span class="auth-spinner" aria-hidden="true"></span>
            <span class="auth-btn-label">{{ __('Email Password Reset Link') }}</span>
        </button>
    </form>

    <p class="auth-footer">
        <a href="{{ route('login') }}">{{ __('Back to log in') }}</a>
    </p>
@endsection
