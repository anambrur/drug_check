@extends('auth.layout')

@section('title', __('Verify email'))
@section('heading', __('Verify your email'))
@section('description', __('Before continuing, please verify your email address using the link we sent you.'))

@section('content')
    @if (session('status') == 'verification-link-sent')
        <div class="auth-alert auth-alert-success" role="status">
            {{ __('A new verification link has been sent to the email address you provided in your profile settings.') }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="auth-form" data-auth-form>
        @csrf
        <button type="submit" class="auth-btn" data-auth-submit data-loading-text="{{ __('Sending…') }}">
            <span class="auth-spinner" aria-hidden="true"></span>
            <span class="auth-btn-label">{{ __('Resend Verification Email') }}</span>
        </button>
    </form>

    <div class="auth-actions-row" style="margin-top:1.25rem;">
        @if (Route::has('profile.show'))
            <a class="auth-link-muted" href="{{ route('profile.show') }}">{{ __('Edit Profile') }}</a>
        @endif

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="auth-toggle-link">{{ __('Log Out') }}</button>
        </form>
    </div>
@endsection
