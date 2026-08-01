@php
    $authHeaderImage = \App\Models\Admin\HeaderImage::where('style', 'style1')->first();
    $authLogoLight = !empty($authHeaderImage?->section_image)
        ? asset('uploads/img/general/'.$authHeaderImage->section_image)
        : null;
    $authLogoDark = !empty($authHeaderImage?->section_image_2)
        ? asset('uploads/img/general/'.$authHeaderImage->section_image_2)
        : null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Authentication') — {{ config('app.name', 'MyDrugCheck') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600|fraunces:600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/auth/auth.css') }}">
</head>
<body class="auth-page">
    <div class="auth-shell">
        @include('auth.partials.brand-panel', [
            'authLogoLight' => $authLogoLight,
            'authLogoDark' => $authLogoDark,
        ])

        <main class="auth-main">
            <div class="auth-card @yield('card_class')">
                <div class="auth-mobile-brand">
                    <a href="{{ url('/') }}">
                        @if ($authLogoDark || $authLogoLight)
                            <img
                                src="{{ $authLogoDark ?? $authLogoLight }}"
                                alt="{{ config('app.name', 'MyDrugCheck') }}"
                                class="auth-brand-img"
                                width="160"
                                height="40"
                            >
                        @else
                            <span class="auth-brand-mark">{{ strtoupper(substr(config('app.name', 'M'), 0, 1)) }}</span>
                            <span>{{ config('app.name', 'MyDrugCheck') }}</span>
                        @endif
                    </a>
                </div>

                <div class="auth-card-header">
                    <h1 class="auth-card-title">@yield('heading')</h1>
                    @hasSection('description')
                        <p class="auth-card-desc">@yield('description')</p>
                    @endif
                </div>

                @yield('content')
            </div>
        </main>
    </div>

    <script src="{{ asset('assets/auth/auth.js') }}" defer></script>
</body>
</html>
