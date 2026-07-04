<!DOCTYPE html>
<html
    dir="@if (session()->has('language_direction_from_dropdown')) @if (session()->get('language_direction_from_dropdown') == 1) {{ __('rtl') }} @else {{ __('ltr') }} @endif
@else
{{ __('ltr') }} @endif"
    lang="@if (session()->has('language_code_from_dropdown')) {{ str_replace('_', '-', session()->get('language_code_from_dropdown')) }}@else{{ str_replace('_', '-', $language->language_code) }} @endif">

<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="title"
        content="@if (!empty($page->title)) {{ $page->title }} @elseif (!empty($page_builder->meta_title)) {{ $page_builder->meta_title }} @elseif (isset($seo)){{ $seo->meta_title }} @endif">
    <meta name="description"
        content="@if (!empty($page->meta_description)) {{ $page->meta_description }} @elseif (!empty($page_builder->meta_description)) {{ $page_builder->meta_description }} @elseif (isset($seo)){{ $seo->meta_description }} @endif">
    <meta name="keywords"
        content="@if (!empty($page->meta_keyword)) {{ $page->meta_keyword }} @elseif (!empty($page_builder->meta_keyword)) {{ $page_builder->meta_keyword }} @elseif (isset($seo)){{ $seo->meta_keyword }} @endif">
    <meta name="author" content="elsecolor">
    <meta property="fb:app_id" content="@if (isset($seo)) {{ $seo->fb_app_id }} @endif">
    <meta property="og:title"
        content="@if (!empty($page->title)) {{ $page->title }} @elseif (!empty($page_builder->meta_title)) {{ $page_builder->meta_title }} @elseif (isset($seo)){{ $seo->meta_title }} @endif">
    <meta property="og:url" content="@if (isset($seo) || isset($page_builder)) {{ url()->current() }} @endif">
    <meta property="og:description"
        content="@if (!empty($page->meta_description)) {{ $page->meta_description }} @elseif (!empty($page_builder->meta_description)) {{ $page_builder->meta_description }} @elseif (isset($seo)){{ $seo->meta_description }} @endif">
    <meta property="og:image"
        content="@if (!empty($favicon->favicon_image)) {{ asset('uploads/img/general/' . $favicon->favicon_image) }} @endif">
    <meta itemprop="image"
        content="@if (!empty($favicon->favicon_image)) {{ asset('uploads/img/general/' . $favicon->favicon_image) }} @endif">
    <meta property="og:type" content="website">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image"
        content="@if (!empty($favicon->favicon_image)) {{ asset('uploads/img/general/' . $favicon->favicon_image) }} @endif">
    <meta property="twitter:title"
        content="@if (!empty($page->title)) {{ $page->title }} @elseif (!empty($page_builder->meta_title)) {{ $page_builder->meta_title }} @elseif (isset($seo)){{ $seo->meta_title }} @endif">
    <meta property="twitter:description"
        content="@if (!empty($page->meta_description)) {{ $page->meta_description }} @elseif (!empty($page_builder->meta_description)) {{ $page_builder->meta_description }} @elseif (isset($seo)){{ $seo->meta_description }} @endif">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title -->
    <title>
        @if (!empty($page->title)) {{ $page->title }}
        @elseif (!empty($page_builder->meta_title))
            {{ $page_builder->meta_title }}
        @elseif (isset($seo))
            {{ $seo->meta_title }} @endif
    </title>

    @if (!empty($favicon->favicon_image))
        <!-- Favicon -->
        <link href="{{ asset('uploads/img/general/' . $favicon->favicon_image) }}" sizes="128x128" rel="shortcut icon"
            type="image/x-icon" />
        <link href="{{ asset('uploads/img/general/' . $favicon->favicon_image) }}" sizes="128x128"
            rel="shortcut icon" />
    @else
        <!-- Favicon -->
        <link href="{{ asset('uploads/img/dummy/favicon.png') }}" sizes="128x128" rel="shortcut icon"
            type="image/x-icon" />
        <link href="{{ asset('uploads/img/dummy/favicon.png') }}" sizes="128x128" rel="shortcut icon" />
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @isset($font)
        <!-- Google Fonts -->
        <link href="{{ $font->text_font_link }}" rel="stylesheet">
        <link href="{{ $font->title_font_link }}" rel="stylesheet">
    @else
        <!-- Google Fonts -->
        <link
            href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,300;1,400;1,500;1,700;1,900&amp;display=swap"
            rel="stylesheet">
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800&amp;display=swap"
            rel="stylesheet">
        <link
            href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap"
            rel="stylesheet">
    @endisset


    <!--// Boostrap v5 //-->
    <link rel="stylesheet" href="{{ asset('assets/frontend/vendor/css/bootstrap.min.css') }}">
    <!--// Magnific Popup //-->
    <link rel="stylesheet" href="{{ asset('assets/frontend/vendor/css/magnific.popup.min.css') }}">
    <!--// Animate Css //-->
    <link rel="stylesheet" href="{{ asset('assets/frontend/vendor/css/animate.min.css') }}">
    <!--// Vegas Slider Css //-->
    <link rel="stylesheet" href="{{ asset('assets/frontend/vendor/css/vegas.slider.min.css') }}">
    <!--// Owl Carousel //-->
    <link rel="stylesheet" href="{{ asset('assets/frontend/vendor/css/owl.carousel.min.css') }}">
    <!--// Owl Carousel Default //-->
    <link rel="stylesheet" href="{{ asset('assets/frontend/vendor/css/owl.carousel.default.min.css') }}">
    <!--// Font Awesome //-->
    <link rel="stylesheet" href="{{ asset('assets/frontend/fonts/font_awesome/css/all.css') }}">
    <!--// Flat Icons //-->
    <link rel="stylesheet" href="{{ asset('assets/frontend/fonts/flat_icons/flaticon.css') }}">

    <style>
        :root {
            --pf-success:       #059669;
            --pf-danger:        #e11d48;
            --pf-surface:       #ffffff;
            --pf-surface-2:     #f8faff;
            --pf-border:        #e2e8f8;
            --pf-text:          #0f172a;
            --pf-muted:         #64748b;
            --pf-light:         #94a3b8;
            --pf-shadow-sm:     0 1px 3px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);
            --pf-shadow-md:     0 4px 16px rgba(15,23,42,.08),0 2px 6px rgba(15,23,42,.05);
            --pf-shadow-lg:     0 20px 60px rgba(15,23,42,.12),0 8px 24px rgba(15,23,42,.07);
            --pf-radius:        14px;
            --pf-radius-sm:     9px;

            @isset($color_option)

                @if ($color_option->color_option != 0)
                    --main-color: {{ $color_option->main_color }};
                    --secondary-color: {{ $color_option->secondary_color }};
                    --scroll-button-color: {{ $color_option->scroll_button_color }};
                    --bottom-button-color: {{ $color_option->bottom_button_color }};
                    --bottom-button-hover-color: {{ $color_option->bottom_button_hover_color }};
                    --side-button-color: {{ $color_option->side_button_color }};
                @else
                    --main-color: #ff4500;
                    --secondary-color: #171718;
                    --scroll-button-color: #00baa3;
                    --bottom-button-color: #212529;
                    --bottom-button-hover-color: #333;
                    --side-button-color: #25d366;
                @endif

            @else
                --main-color: #ff4500;
                --secondary-color: #171718;
                --scroll-button-color: #00baa3;
                --bottom-button-color: #212529;
                --bottom-button-hover-color: #333;
                --side-button-color: #25d366;
            @endisset

            @isset($font)

                --title-font: @php echo html_entity_decode($font->title_font_family);
            @endphp
            ;
            --text-font: @php echo html_entity_decode($font->text_font_family);
        @endphp
        ;

        @else
            --title-font: 'Poppins', sans-serif;
            --text-font: 'Roboto', sans-serif;
        @endisset

            /* Consortium UI tokens — inherit admin panel colors & fonts */
            --pf-primary:       var(--main-color);
            --pf-primary-dark:  color-mix(in srgb, var(--main-color) 78%, var(--secondary-color));
            --pf-primary-light: color-mix(in srgb, var(--main-color) 11%, #fff);
            --pf-primary-glow:  color-mix(in srgb, var(--main-color) 18%, transparent);
            --pf-font-head:     var(--title-font);
            --pf-font-body:     var(--text-font);


        }
    </style>

    <!--// Theme Main Css //-->
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/style.css') }}">
    <!--// Theme Color Css //-->

    <!--  helper style css file -->
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/helper-style.css') }}">

   

      <style>

        #counters {
            background-image: url({{ asset('uploads/img/dummy/bg/counter-bg.png') }});
        }

        /* Custom Scoped Styles */
        .hover-shadow {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }

        .hover-shadow:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.12) !important;
            transform: translateY(-2px);
        }

        .transition {
            transition: all 0.3s ease-in-out;
        }

        .about-inner h6 {
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 10px;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        .about-inner h2 {
            font-weight: 700;
            margin-bottom: 20px;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        @media (max-width: 767.98px) {
            .border-right-md {
                border-right: none !important;
                border-bottom: 1px solid #dee2e6;
            }
        }


        /* ═══ Random Consortium — Premium SaaS UI ═══ */

        /* Scroll reveal (Intersection Observer) */
        .rc-animate {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity .65s cubic-bezier(.22,1,.36,1),
                        transform .65s cubic-bezier(.22,1,.36,1);
            transition-delay: var(--rc-delay, 0s);
        }
        .rc-animate-delay-1 { --rc-delay: .12s; }
        .rc-animate-delay-2 { --rc-delay: .22s; }
        .rc-visible { opacity: 1; transform: translateY(0); }

        /* ─── Scroll progress bar ─── */
        .rc-scroll-progress {
            position: fixed; top: 0; left: 0; right: 0;
            height: 3px; z-index: 9998;
            background: rgba(15,23,42,.06);
            pointer-events: none;
        }
        .rc-scroll-progress span {
            display: block; height: 100%; width: 0%;
            background: linear-gradient(90deg, var(--main-color), color-mix(in srgb, var(--main-color) 60%, #fff));
            border-radius: 0 2px 2px 0;
            transition: width .12s linear;
            box-shadow: 0 0 8px color-mix(in srgb, var(--main-color) 50%, transparent);
        }

        /* ─── Hero load stagger ─── */
        .rc-hero-item {
            opacity: 0;
            transform: translateY(22px);
            animation: rc-hero-in .7s cubic-bezier(.22,1,.36,1) forwards;
        }
        .rc-hero-item--1 { animation-delay: .1s; }
        .rc-hero-item--2 { animation-delay: .22s; }
        .rc-hero-item--3 { animation-delay: .34s; }
        .rc-hero-item--4 { animation-delay: .48s; }
        @keyframes rc-hero-in {
            to { opacity: 1; transform: translateY(0); }
        }

        /* Floating particles */
        .rc-particles {
            position: absolute; inset: 0; overflow: hidden;
        }
        .rc-particles span {
            position: absolute;
            width: 4px; height: 4px;
            background: rgba(255,255,255,.35);
            border-radius: 50%;
            animation: rc-particle 12s linear infinite;
        }
        .rc-particles span:nth-child(1) { left: 10%; top: 20%; animation-delay: 0s; animation-duration: 14s; }
        .rc-particles span:nth-child(2) { left: 25%; top: 70%; animation-delay: -2s; animation-duration: 11s; width: 3px; height: 3px; }
        .rc-particles span:nth-child(3) { left: 45%; top: 35%; animation-delay: -4s; animation-duration: 16s; }
        .rc-particles span:nth-child(4) { left: 65%; top: 80%; animation-delay: -1s; animation-duration: 13s; width: 5px; height: 5px; }
        .rc-particles span:nth-child(5) { left: 80%; top: 25%; animation-delay: -6s; animation-duration: 15s; }
        .rc-particles span:nth-child(6) { left: 92%; top: 55%; animation-delay: -3s; animation-duration: 12s; width: 3px; height: 3px; }
        @keyframes rc-particle {
            0% { transform: translateY(0) scale(1); opacity: 0; }
            10% { opacity: .7; }
            90% { opacity: .4; }
            100% { transform: translateY(-120px) scale(.5); opacity: 0; }
        }

        /* ─── Hero ─── */
        .rc-hero {
            position: relative;
            padding: 3.5rem 0 2.5rem;
            overflow: hidden;
            background: linear-gradient(165deg, var(--secondary-color) 0%, color-mix(in srgb, var(--main-color) 55%, var(--secondary-color)) 50%, var(--main-color) 100%);
        }
        .rc-hero-bg { position: absolute; inset: 0; pointer-events: none; }
        .rc-hero-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: .45;
            animation: rc-float 8s ease-in-out infinite;
        }
        .rc-hero-orb--1 {
            width: 420px; height: 420px;
            background: var(--main-color);
            top: -120px; right: -80px;
        }
        .rc-hero-orb--2 {
            width: 300px; height: 300px;
            background: #06b6d4;
            bottom: -80px; left: -60px;
            animation-delay: -4s;
        }
        .rc-hero-grid {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: radial-gradient(ellipse 70% 60% at 50% 40%, #000 20%, transparent 100%);
        }
        @keyframes rc-float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(12px, -18px) scale(1.05); }
        }
        .rc-hero-content { position: relative; z-index: 1; max-width: 860px; margin: 0px auto; margin-top:80px}
        .rc-badge {
            display: inline-flex; align-items: center; gap: .45rem;
            background: rgba(255,255,255,.1);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,.18);
            color: rgba(255,255,255,.92);
            font-family: var(--pf-font-head);
            font-size: .72rem; font-weight: 600;
            letter-spacing: .06em; text-transform: uppercase;
            padding: .4rem 1rem; border-radius: 100px;
            margin-bottom: 1.1rem;
        }
        .rc-hero-title {
            font-family: var(--pf-font-head);
            font-size: clamp(1.75rem, 4vw, 2.65rem);
            font-weight: 800;
            color: #fff;
            line-height: 1.15;
            margin-bottom: 1rem;
            letter-spacing: -.02em;
        }
        .rc-hero-desc {
            font-family: var(--pf-font-body);
            font-size: clamp(.92rem, 2vw, 1.05rem);
            color: rgba(255,255,255,.72);
            line-height: 1.65;
            max-width: 840px;
            margin: 0 auto;
        }
        .rc-hero-desc p { margin-bottom: 0; }

        /* Step progress indicator */
        .rc-stepper {
            position: relative; z-index: 1;
            display: flex; align-items: center; justify-content: center;
            gap: 0; margin-top: 2.5rem;
            padding: 1rem 1.25rem;
            background: rgba(255,255,255,.08);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 16px;
            max-width: 520px;
            margin-left: auto; margin-right: auto;
        }
        .rc-step {
            display: flex; flex-direction: column; align-items: center; gap: .35rem;
            flex: 1; min-width: 0;
            transition: opacity .3s, transform .3s;
            opacity: .45;
            cursor: pointer;
        }
        .rc-step:hover { transform: translateY(-2px); }
        .rc-step--active { opacity: 1; }
        .rc-step--current .rc-step-num {
            background: #fff; color: var(--pf-primary);
            box-shadow: 0 0 0 4px rgba(255,255,255,.25);
            transform: scale(1.08);
            animation: rc-step-pulse 2s ease-in-out infinite;
        }
        @keyframes rc-step-pulse {
            0%, 100% { box-shadow: 0 0 0 4px rgba(255,255,255,.25); }
            50% { box-shadow: 0 0 0 7px rgba(255,255,255,.12); }
        }
        .rc-step-num {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: rgba(255,255,255,.15);
            color: #fff;
            font-family: var(--pf-font-head);
            font-size: .82rem; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            transition: all .35s cubic-bezier(.22,1,.36,1);
        }
        .rc-step-label {
            font-family: var(--pf-font-head);
            font-size: .68rem; font-weight: 600;
            color: rgba(255,255,255,.85);
            text-transform: uppercase;
            letter-spacing: .04em;
            white-space: nowrap;
        }
        .rc-step-line {
            flex: 0 0 32px; height: 3px;
            background: rgba(255,255,255,.15);
            border-radius: 3px;
            margin-bottom: 1.2rem;
            overflow: hidden;
            position: relative;
        }
        .rc-step-line-fill {
            display: block; height: 100%; width: 0%;
            background: linear-gradient(90deg, #fff, rgba(255,255,255,.7));
            border-radius: 3px;
            transition: width .55s cubic-bezier(.22,1,.36,1);
        }
        .rc-step-line-fill--done { width: 100%; }

        /* ─── Plan Section ─── */
        .plan-section {
            background: linear-gradient(180deg, #f1f5fb 0%, var(--pf-surface-2) 100%);
            padding: 3.5rem 0 4rem;
            border-bottom: 1px solid var(--pf-border);
        }
        .rc-section-head { margin-bottom: .5rem; }
        .section-eyebrow {
            font-family: var(--pf-font-head);
            font-size: .72rem; font-weight: 700;
            letter-spacing: .1em; text-transform: uppercase;
            color: var(--pf-primary); margin-bottom: .4rem;
            display: inline-block;
            background: var(--pf-primary-light);
            padding: .25rem .75rem;
            border-radius: 100px;
        }
        .plan-section h2 {
            font-family: var(--pf-font-head);
            font-weight: 800; font-size: clamp(1.45rem, 3vw, 1.85rem);
            color: var(--pf-text); margin-bottom: .6rem;
            letter-spacing: -.02em;
        }
        .plan-section .sub {
            font-family: var(--pf-font-body);
            color: var(--pf-muted); max-width: 560px; margin: 0 auto 2.75rem;
            font-size: .95rem; line-height: 1.6;
        }

        /* Plan cards */
        .plan-card {
            border: 1.5px solid var(--pf-border) !important;
            border-top: 3px solid var(--plan-accent, var(--pf-border)) !important;
            border-radius: 18px !important;
            box-shadow: var(--pf-shadow-sm) !important;
            transition: transform .35s cubic-bezier(.22,1,.36,1),
                        box-shadow .35s cubic-bezier(.22,1,.36,1),
                        border-color .25s;
            cursor: pointer;
            height: 100%;
            position: relative;
            overflow: hidden;
            background: #fff;
            outline: none;
        }
        .plan-card:focus-visible {
            box-shadow: 0 0 0 3px var(--pf-primary-glow), var(--pf-shadow-md) !important;
        }
        .plan-card-glow {
            position: absolute; inset: 0;
            background: radial-gradient(circle at 50% 0%, var(--plan-accent, var(--pf-primary)) 0%, transparent 65%);
            opacity: 0;
            transition: opacity .35s;
            pointer-events: none;
        }
        .plan-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--pf-shadow-lg) !important;
        }
        .plan-card:hover .plan-card-glow { opacity: .06; }
        .plan-card.rc-plan-pop {
            animation: rc-plan-pop .45s cubic-bezier(.22,1,.36,1);
        }
        @keyframes rc-plan-pop {
            0% { transform: scale(1); }
            35% { transform: scale(1.03); }
            100% { transform: scale(1); }
        }
        .rc-tilt-card { transform-style: preserve-3d; will-change: transform; }

        /* Click ripple */
        .rc-ripple {
            position: absolute;
            border-radius: 50%;
            background: color-mix(in srgb, var(--plan-accent, var(--main-color)) 28%, transparent);
            transform: scale(0);
            pointer-events: none;
            z-index: 3;
        }
        .rc-ripple.rc-ripple-active {
            animation: rc-ripple .6s ease-out forwards;
        }
        @keyframes rc-ripple {
            to { transform: scale(2.5); opacity: 0; }
        }

        .plan-card.active {
            border-color: var(--plan-accent, var(--pf-primary)) !important;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--plan-accent, var(--pf-primary)) 18%, transparent),
                        0 16px 48px color-mix(in srgb, var(--plan-accent, var(--pf-primary)) 14%, transparent) !important;
        }
        .plan-card.active .plan-card-glow { opacity: .1; }
        .plan-card.active::after {
            content: '✓ Selected';
            position: absolute; top: 14px; right: -32px;
            background: var(--plan-accent, var(--pf-primary)); color: #fff;
            font-family: var(--pf-font-head); font-size: .6rem; font-weight: 700;
            letter-spacing: .06em; text-transform: uppercase;
            padding: 4px 38px; transform: rotate(45deg);
            z-index: 2;
        }
        .plan-card .card-body { padding: 1.75rem 1.5rem; position: relative; z-index: 1; }
        .plan-icon-wrap {
            width: 56px; height: 56px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; color: #fff; margin-bottom: 1.1rem;
            box-shadow: 0 8px 20px color-mix(in srgb, var(--plan-accent, var(--main-color)) 35%, transparent);
            transition: transform .35s cubic-bezier(.22,1,.36,1);
        }
        .plan-card:hover .plan-icon-wrap,
        .plan-card.active .plan-icon-wrap { transform: scale(1.06); }
        .plan-card h4 {
            font-family: var(--pf-font-head);
            font-size: 1.05rem; font-weight: 700; color: var(--pf-text); margin-bottom: .25rem;
        }
        .plan-card .range {
            font-family: var(--pf-font-body);
            font-size: .8rem; color: var(--pf-muted); margin-bottom: 1rem;
        }
        .plan-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--pf-border), transparent);
            margin: .5rem 0 1rem;
        }

        .plan-fee-list { list-style: none; padding: 0; margin: 0; }
        .plan-fee-list li {
            display: flex; justify-content: space-between; align-items: center;
            font-family: var(--pf-font-body);
            font-size: .78rem; color: var(--pf-muted);
            padding: .42rem .5rem;
            border-radius: 8px;
            transition: background .2s, transform .2s;
        }
        .plan-card:hover .plan-fee-list li:hover {
            background: color-mix(in srgb, var(--plan-accent, var(--main-color)) 8%, #fff);
            transform: translateX(3px);
        }
        .plan-fee-list li:nth-child(odd) { background: rgba(248,250,255,.8); }
        .plan-fee-list li .fee-label { display: flex; align-items: center; gap: .45rem; }
        .plan-fee-list li .fee-label i {
            color: var(--plan-accent, var(--pf-primary));
            font-size: .72rem; width: 14px; text-align: center;
        }
        .plan-fee-list li .fee-val { font-weight: 700; color: var(--pf-text); white-space: nowrap; }
        .plan-card.active .plan-fee-list li .fee-val { color: var(--plan-accent, var(--pf-primary)); }

        /* ─── Form section ─── */
        #application-form {
            font-family: var(--pf-font-body);
            padding: 3.5rem 0 5rem;
            background: var(--pf-surface);
        }
        .rc-sticky-wrap { top: 90px; z-index: 10; }

        .pf-card {
            background: var(--pf-surface);
            border-radius: 22px;
            box-shadow: var(--pf-shadow-lg);
            border: 1px solid var(--pf-border);
            overflow: hidden;
            transition: box-shadow .3s;
        }
        .pf-card:hover { box-shadow: 0 24px 64px rgba(15,23,42,.1); }

        .pf-header {
            background: linear-gradient(135deg, var(--main-color) 0%, var(--pf-primary-dark) 55%, var(--secondary-color) 100%);
            padding: 2rem 2.25rem 1.75rem;
            position: relative; overflow: hidden;
        }
        .pf-header::before {
            content:''; position:absolute; top:-60px; right:-60px;
            width:240px; height:240px; border-radius:50%;
            background: rgba(255,255,255,.06);
            animation: rc-float 10s ease-in-out infinite;
        }
        .pf-header::after {
            content:''; position:absolute; bottom:-50px; left:25%;
            width:180px; height:180px; border-radius:50%;
            background: rgba(6,182,212,.12);
            animation: rc-float 12s ease-in-out infinite reverse;
        }
        .pf-header .pill {
            background: rgba(255,255,255,.14); backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,.22); color: #fff;
            font-family: var(--pf-font-head); font-size: .68rem; font-weight: 600;
            letter-spacing: .08em; text-transform: uppercase;
            padding: .32rem .9rem; border-radius: 100px;
            display: inline-block; margin-bottom: .8rem;
        }
        .pf-header h4 {
            font-family: var(--pf-font-head);
            font-size: 1.35rem; font-weight: 700; color: #fff;
            margin-bottom: .3rem; line-height: 1.3;
            position: relative; z-index: 1;
        }
        .pf-header p {
            color: rgba(255,255,255,.72); font-size: .88rem; margin: 0;
            position: relative; z-index: 1;
        }
        .pf-header-icon {
            width: 50px; height: 50px;
            background: rgba(255,255,255,.12);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem; color: #fff; flex-shrink: 0;
            position: relative; z-index: 1;
            animation: rc-icon-float 4s ease-in-out infinite;
        }
        @keyframes rc-icon-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }
        .pf-body { padding: 1.85rem 2.25rem 2.35rem; }

        .pf-section {
            border: 1px solid var(--pf-border);
            border-radius: var(--pf-radius);
            overflow: hidden; margin-bottom: 1.5rem;
            background: var(--pf-surface);
            transition: box-shadow .25s, border-color .25s, transform .25s;
        }
        .pf-section:hover { border-color: color-mix(in srgb, var(--main-color) 22%, transparent); }
        .pf-section:focus-within {
            box-shadow: 0 0 0 3px var(--pf-primary-glow);
            border-color: color-mix(in srgb, var(--main-color) 38%, transparent);
            transform: translateY(-1px);
        }
        .pf-section-head {
            background: linear-gradient(90deg, var(--pf-primary-light) 0%, rgba(232,240,254,.5) 100%);
            padding: .85rem 1.4rem;
            display: flex; align-items: center; gap: .7rem;
            border-bottom: 1px solid color-mix(in srgb, var(--main-color) 10%, transparent);
        }
        .pf-section-head .icon-wrap {
            width: 30px; height: 30px;
            background: linear-gradient(135deg, var(--pf-primary), var(--pf-primary-dark));
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: .75rem; flex-shrink: 0;
            box-shadow: 0 4px 12px color-mix(in srgb, var(--main-color) 28%, transparent);
        }
        .pf-section-head h6 {
            font-family: var(--pf-font-head); font-size: .88rem; font-weight: 700;
            color: var(--pf-primary-dark); margin: 0;
        }
        .pf-section-body { padding: 1.4rem 1.35rem; }

        .pf-label {
            font-family: var(--pf-font-head); font-size: .78rem; font-weight: 600;
            color: var(--pf-text); letter-spacing: .01em; margin-bottom: .38rem; display: block;
        }
        .pf-req { color: var(--pf-danger); margin-left: 2px; }
        .pf-opt {
            font-size: .65rem; background: #f1f5f9; color: var(--pf-muted);
            border-radius: 4px; padding: 2px 6px; font-weight: 500;
            margin-left: 5px; vertical-align: middle;
        }
        .pf-icon-wrap { position: relative; }
        .pf-icon-wrap .pf-icon {
            position: absolute; left: .9rem; top: 50%; transform: translateY(-50%);
            color: var(--pf-light); font-size: .82rem; pointer-events: none;
            transition: color .2s;
        }
        .pf-icon-wrap:focus-within .pf-icon { color: var(--pf-primary); }
        .pf-icon-wrap .pf-control { padding-left: 2.45rem; }
        .pf-control {
            width: 100%; border: 1.5px solid var(--pf-border);
            border-radius: var(--pf-radius-sm);
            padding: .68rem 1rem; font-size: .88rem;
            font-family: var(--pf-font-body);
            color: var(--pf-text); background: #fafbfe;
            transition: border-color .2s, box-shadow .2s, background .2s, transform .15s;
            box-shadow: inset 0 1px 2px rgba(15,23,42,.03);
            outline: none; -webkit-appearance: none; appearance: none;
        }
        .pf-control::placeholder { color: var(--pf-light); }
        .pf-control:hover { border-color: #cbd5e1; background: #fff; }
        .pf-control:focus {
            border-color: var(--pf-primary);
            box-shadow: 0 0 0 3.5px var(--pf-primary-glow), inset 0 1px 2px rgba(15,23,42,.02);
            background: #fff;
        }
        .pf-control.rc-input-valid {
            border-color: var(--pf-success);
            background-image: linear-gradient(transparent, transparent),
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23059669'%3E%3Cpath d='M13.485 1.929a1.5 1.5 0 0 1 0 2.122l-7.07 7.071-3.182-3.182a1.5 1.5 0 1 1 2.121-2.121l1.061 1.06 5.96-5.96a1.5 1.5 0 0 1 2.122 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right .75rem center;
            background-size: 14px;
            padding-right: 2.2rem;
            animation: rc-input-valid .35s ease;
        }
        @keyframes rc-input-valid {
            from { transform: scale(.98); }
            to { transform: scale(1); }
        }
        .rc-input-bump { animation: rc-input-bump .35s ease; }
        @keyframes rc-input-bump {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
        textarea.pf-control { resize: vertical; min-height: 80px; }
        .pf-hint { font-size: .74rem; color: var(--pf-muted); margin-top: .32rem; }
        .pf-hint.danger { color: var(--pf-danger) !important; }

        /* ─── Summary sidebar ─── */
        .summary-card {
            background: var(--pf-surface);
            border: 1.5px solid var(--pf-border);
            border-radius: 20px;
            box-shadow: var(--pf-shadow-md);
            overflow: hidden;
            transition: box-shadow .3s, transform .3s;
        }
        .summary-card.rc-summary-glow {
            animation: rc-summary-glow 4s ease-in-out infinite;
        }
        @keyframes rc-summary-glow {
            0%, 100% { box-shadow: var(--pf-shadow-md); }
            50% { box-shadow: 0 8px 32px color-mix(in srgb, var(--main-color) 12%, transparent), var(--pf-shadow-md); }
        }
        .summary-plan-name.rc-name-flip {
            animation: rc-name-flip .45s cubic-bezier(.22,1,.36,1);
        }
        @keyframes rc-name-flip {
            0% { opacity: 1; transform: translateY(0); }
            40% { opacity: 0; transform: translateY(-8px); }
            60% { opacity: 0; transform: translateY(8px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .summary-card:hover { box-shadow: var(--pf-shadow-lg); }
        .summary-card-head {
            background: linear-gradient(135deg, var(--main-color) 0%, var(--pf-primary-dark) 100%);
            padding: 1.15rem 1.5rem;
            position: relative; overflow: hidden;
        }
        .summary-card-head::before {
            content: ''; position: absolute; top: -30px; right: -30px;
            width: 100px; height: 100px; border-radius: 50%;
            background: rgba(255,255,255,.08);
        }
        .summary-card-head h5 {
            font-family: var(--pf-font-head); font-weight: 700; color: #fff;
            margin: 0; font-size: .98rem; position: relative; z-index: 1;
        }

        .driver-block {
            padding: 1.15rem 1.5rem;
            background: linear-gradient(180deg, var(--pf-surface-2) 0%, #fff 100%);
            border-bottom: 1.5px solid var(--pf-border);
        }
        .driver-block .pf-label { margin-bottom: .42rem; }

        .summary-body { padding: 1.25rem 1.5rem 0; }
        .summary-plan-name {
            font-family: var(--pf-font-head); font-weight: 700;
            font-size: .92rem; color: var(--pf-text);
            margin-bottom: .85rem;
            padding-bottom: .65rem;
            border-bottom: 2px solid var(--pf-primary-light);
        }
        .summary-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: .52rem 0;
            border-bottom: 1px dashed var(--pf-border);
            font-family: var(--pf-font-body); font-size: .82rem; color: var(--pf-muted);
            animation: rc-row-in .4s cubic-bezier(.22,1,.36,1) backwards;
            animation-delay: var(--row-delay, 0s);
        }
        @keyframes rc-row-in {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .summary-row:last-of-type { border-bottom: none; }
        .summary-row .val { font-weight: 700; color: var(--pf-text); font-variant-numeric: tabular-nums; }

        .summary-total {
            background: linear-gradient(135deg, var(--pf-primary-light) 0%, rgba(232,240,254,.6) 100%);
            border: 1px solid color-mix(in srgb, var(--main-color) 14%, transparent);
            border-radius: var(--pf-radius-sm);
            padding: 1rem 1.2rem;
            display: flex; justify-content: space-between; align-items: center;
            margin: 1rem 1.5rem;
        }
        .summary-total .label {
            font-family: var(--pf-font-head); font-weight: 700;
            font-size: .88rem; color: var(--pf-primary-dark);
        }
        .summary-total .amount {
            font-family: var(--pf-font-head);
            font-size: 1.85rem; font-weight: 800; color: var(--pf-primary);
            font-variant-numeric: tabular-nums;
            transition: transform .25s;
        }
        .summary-total .amount.rc-total-pulse {
            animation: rc-total-pop .4s cubic-bezier(.22,1,.36,1);
        }
        @keyframes rc-total-pop {
            0% { transform: scale(1); }
            40% { transform: scale(1.08); }
            100% { transform: scale(1); }
        }
        .summary-actions { padding: 0 1.5rem 1.5rem; }

        .pf-btn-submit {
            position: relative; overflow: hidden;
            background: linear-gradient(135deg, var(--main-color) 0%, var(--pf-primary-dark) 100%);
            border: none; border-radius: 13px; width: 100%;
            font-family: var(--pf-font-head); font-weight: 700; font-size: .95rem;
            color: #fff; padding: .95rem 2rem;
            transition: transform .25s, box-shadow .25s;
            box-shadow: 0 4px 24px color-mix(in srgb, var(--main-color) 38%, transparent);
            display: flex; align-items: center; justify-content: center; gap: .55rem;
            cursor: pointer;
        }
        .pf-btn-shimmer {
            position: absolute; inset: 0;
            background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.18) 50%, transparent 60%);
            transform: translateX(-100%);
            animation: rc-shimmer 3s ease-in-out infinite;
        }
        @keyframes rc-shimmer {
            0%, 100% { transform: translateX(-100%); }
            50% { transform: translateX(100%); }
        }
        .pf-btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 36px color-mix(in srgb, var(--main-color) 48%, transparent);
            color: #fff;
        }
        .pf-btn-submit:active { transform: translateY(0); }
        .pf-btn-submit:disabled { opacity: .7; cursor: not-allowed; transform: none; }
        .pf-btn-submit:focus-visible {
            outline: 3px solid var(--pf-primary-glow);
            outline-offset: 2px;
        }

        .rc-trust-badges {
            display: flex; justify-content: center; gap: 1rem;
            margin-top: .85rem;
        }
        .rc-trust-badges span {
            font-family: var(--pf-font-head);
            font-size: .68rem; font-weight: 600;
            color: var(--pf-muted);
            display: flex; align-items: center; gap: .3rem;
            text-transform: uppercase; letter-spacing: .04em;
        }
        .rc-trust-badges i { color: #059669; font-size: .75rem; }

        .pf-secure {
            font-size: .72rem; color: var(--pf-muted);
            display: flex; align-items: center; gap: .35rem;
            justify-content: center; margin-top: .55rem;
        }
        .pf-secure i { color: #059669; }

        .rc-enterprise-block h6 {
            font-family: var(--pf-font-head); font-weight: 700; color: var(--pf-text);
        }
        .rc-enterprise-icon {
            width: 58px; height: 58px;
            border-radius: 16px;
            background: linear-gradient(135deg, #64748b, #475569);
            color: #fff; font-size: 1.35rem;
            display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: 1rem;
            box-shadow: 0 8px 24px rgba(100,116,139,.3);
        }

        .pf-alert {
            border-radius: var(--pf-radius-sm); border: none;
            font-size: .865rem; padding: .9rem 1.1rem; margin-bottom: 1.1rem;
            display: flex; align-items: flex-start; gap: .65rem;
            animation: rc-fade-in .35s ease;
        }
        .pf-alert-danger {
            background: rgba(225,29,72,.06); color: #9f1239;
            border: 1px solid rgba(225,29,72,.18);
        }

        .notes-toggle-btn {
            background: none; border: none; padding: .25rem 0;
            font-family: var(--pf-font-head); font-size: .78rem; font-weight: 600;
            color: var(--pf-primary); cursor: pointer;
            display: flex; align-items: center; gap: .4rem;
            margin-top: .4rem;
            transition: color .2s, gap .2s;
        }
        .notes-toggle-btn:hover { color: var(--pf-primary-dark); gap: .55rem; }
        .notes-toggle-btn:focus-visible {
            outline: 2px solid var(--pf-primary);
            outline-offset: 3px; border-radius: 4px;
        }

        /* Notes smooth collapse */
        .rc-notes-collapse {
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            transition: max-height .4s cubic-bezier(.22,1,.36,1),
                        opacity .3s ease,
                        margin .3s ease;
            margin-top: 0 !important;
        }
        .rc-notes-collapse.rc-notes-open {
            max-height: 200px;
            opacity: 1;
            margin-top: .5rem !important;
        }

        /* Calculator panel crossfade */
        .rc-calc-panel.rc-calc-enter {
            animation: rc-calc-in .35s cubic-bezier(.22,1,.36,1);
        }
        .rc-calc-panel.rc-calc-exit {
            animation: rc-calc-out .28s ease forwards;
        }
        @keyframes rc-calc-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes rc-calc-out {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-8px); }
        }

        @keyframes rc-fade-in {
            from { opacity: 0; transform: translateX(-6px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* Loader overlay */
        #loader-overlay {
            position: fixed; inset: 0;
            background: rgba(15,23,42,.75);
            backdrop-filter: blur(8px);
            z-index: 9999;
            display: flex; align-items: center; justify-content: center;
            opacity: 0; pointer-events: none;
            transition: opacity .35s ease;
        }
        #loader-overlay.show { opacity: 1; pointer-events: auto; }
        .rc-loader-card {
            text-align: center; color: #fff;
            padding: 2.5rem 3rem;
            background: rgba(255,255,255,.06);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 24px;
            max-width: 380px;
            animation: rc-fade-in .4s ease;
        }
        .rc-loader-spinner {
            position: relative; width: 64px; height: 64px;
            margin: 0 auto 1.25rem;
        }
        .rc-loader-ring {
            position: absolute; inset: 0;
            border: 3px solid rgba(255,255,255,.15);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .9s linear infinite;
        }
        .rc-loader-ring--inner {
            inset: 10px;
            border-width: 2px;
            border-top-color: rgba(255,255,255,.6);
            animation-direction: reverse;
            animation-duration: 1.2s;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .rc-loader-sub { color: rgba(255,255,255,.65); font-size: .9rem; margin: 0; }
        .rc-loader-dots {
            display: flex; justify-content: center; gap: 6px; margin-top: 1rem;
        }
        .rc-loader-dots span {
            width: 6px; height: 6px; border-radius: 50%;
            background: rgba(255,255,255,.5);
            animation: rc-dot 1.4s ease-in-out infinite;
        }
        .rc-loader-dots span:nth-child(2) { animation-delay: .2s; }
        .rc-loader-dots span:nth-child(3) { animation-delay: .4s; }
        @keyframes rc-dot {
            0%, 80%, 100% { opacity: .3; transform: scale(.8); }
            40% { opacity: 1; transform: scale(1.2); }
        }

        /* Reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .rc-animate, .rc-hero-orb, .rc-hero-item, .rc-particles span,
            .pf-btn-shimmer, .rc-loader-ring, .rc-loader-dots span,
            .summary-row, .summary-card.rc-summary-glow,
            .pf-header-icon, .rc-step--current .rc-step-num {
                animation: none !important;
                transition: none !important;
            }
            .rc-animate, .rc-hero-item { opacity: 1; transform: none; }
            .plan-card:hover, .pf-btn-submit:hover, .rc-step:hover { transform: none; }
            .rc-tilt-card { transform: none !important; }
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .rc-sticky-wrap { position: relative !important; top: 0 !important; }
            .rc-stepper { max-width: 100%; }
        }
        @media (max-width: 575.98px) {
            .rc-hero { padding: 2.5rem 0 2rem; }
            .rc-step-label { font-size: .58rem; }
            .rc-step-line { flex: 0 0 16px; }
            .rc-step-num { width: 28px; height: 28px; font-size: .72rem; }
            .plan-section { padding: 2.5rem 0 3rem; }
            .rc-plans-row { --bs-gutter-x: 1rem; }
            .plan-card .card-body { padding: 1.35rem 1.15rem; }
            .pf-header { padding: 1.5rem 1.25rem 1.35rem; }
            .pf-body { padding: 1.25rem 1rem 1.75rem; }
            .summary-total .amount { font-size: 1.55rem; }
            .rc-loader-card { padding: 2rem 1.5rem; margin: 0 1rem; }
        }
    </style>

    @if (isset($google_analytic))
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $google_analytic->google_analytic }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());

            gtag('config', '{{ $google_analytic->google_analytic }}');
        </script>
    @endif
    @livewireStyles
    
    
    
    <script
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initMap" >
    </script>

  
<!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

{{-- <body data-bs-spy="scroll" data-bs-target="#fixedNavbar" @if (session()->has('language_direction_from_dropdown')) @if (session()->get('language_direction_from_dropdown') == 1)  class="rtl-mode" @endif @elseif (isset($language)) @if ($language->direction == 1) class="rtl-mode" @endif  @endif > --}}

<body data-bs-spy="scroll" data-bs-target="#fixedNavbar" @if (session()->has('language_direction_from_dropdown')) @if (session()->get('language_direction_from_dropdown') == 1)  class="rtl-mode" @endif @elseif (isset($language)) @if ($language->direction == 1) class="rtl-mode" @endif  @endif >
    

    <!--// Page Wrapper Start //-->
    <div class="page-wrapper" id="wrapper">
        @include('frontend.sections.header.header-style1')

        <!--// Main Area Start //-->
        <main class="main-area" >
            
             <!--// dynamic content //-->
             
            @yield('content')


            @include('frontend.sections.footer.footer-style1')

        </main>
        <!--// Main Area End //-->

        <a href="#" class="scroll-top-btn" data-scroll-goto="1">
            <i class="fa fa-arrow-up"></i>
        </a>
        <!--// .scroll-top-btn // -->

        @include('frontend.sections.preloader.preloader')

    </div>
    <!--// Page Wrapper End //-->

    @include('frontend.sections.widget.bottom-style1')
    @include('frontend.sections.widget.side-style1')



    <!--// JQuery //-->
    <script src="{{ asset('assets/frontend/vendor/js/jquery.min.js') }}"></script>
    <!--// Bootstrap //-->
    <script src="{{ asset('assets/frontend/vendor/js/bootstrap.min.js') }}"></script>
    <!--// Images Loaded Js //-->
    <script src="{{ asset('assets/frontend/vendor/js/images.loaded.min.js') }}"></script>
    <!--// Wow Js //-->
    <script src="{{ asset('assets/frontend/vendor/js/wow.min.js') }}"></script>
    <!--// Magnific Popup //-->
    <script src="{{ asset('assets/frontend/vendor/js/magnific.popup.min.js') }}"></script>
    <!--// Waypoint Js //-->
    <script src="{{ asset('assets/frontend/vendor/js/waypoint.min.js') }}"></script>
    <!--// Counter Up Js //-->
    <script src="{{ asset('assets/frontend/vendor/js/counter.up.min.js') }}"></script>
    <!--// JQuery Easing Functions //-->
    <script src="{{ asset('assets/frontend/vendor/js/jquery.easing.min.js') }}"></script>
    <!--// Owl Carousel //-->
    <script src="{{ asset('assets/frontend/vendor/js/owl.carousel.min.js') }}"></script>
    <!--// Form Validate //-->
    <script src="{{ asset('assets/frontend/vendor/js/validate.min.js') }}"></script>
    <!--// Form Validate //-->
    <script src="{{ asset('assets/frontend/vendor/js/custom.select.plugin.js') }}"></script>
    <!--// Scroll It //-->
    <script src="{{ asset('assets/frontend/vendor/js/scrollit.min.js') }}"></script>
    <!--// Isotope Js //-->
    <script src="{{ asset('assets/frontend/vendor/js/isotope.min.js') }}"></script>
    <!--// Vegas Slider //-->
    <script src="{{ asset('assets/frontend/vendor/js/vegas.slider.min.js') }}"></script>
    <!--// Main Js //-->
    <script src="{{ asset('assets/frontend/js/main.js') }}"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @stack('scripts')

    @isset ($banner_style3)

        <script>
            jQuery(document).ready(function() {
                jQuery("#heroSliderContainer").vegas({
                    slides: [
                            @if (!empty($banner_style3->section_image))
                        {
                            src: "{{ asset('uploads/img/banner/'.$banner_style3->section_image) }}"
                        },
                            @endif
                            @if (!empty($banner_style3->section_image_2))
                        {
                            src: "{{ asset('uploads/img/banner/'.$banner_style3->section_image_2) }}"
                        },
                            @endif
                            @if (!empty($banner_style3->section_image_3))
                        {
                            src: "{{ asset('uploads/img/banner/'.$banner_style3->section_image_3) }}"
                        },
                        @endif
                    ],
                    overlay: true,
                    transition: 'fade2',
                    animation: 'kenburnsUpLeft'
                });
            });
        </script>

    @else

        <script>
            jQuery(document).ready(function() {
                jQuery("#heroSliderContainer").vegas({
                    slides: [

                        {
                            src: "{{ asset('uploads/img/dummy/1920x1080.jpg') }}"
                        },

                        {
                            src: "{{ asset('uploads/img/dummy/1920x1080.jpg') }}"
                        },

                    ],
                    overlay: true,
                    transition: 'fade2',
                    animation: 'kenburnsUpLeft'
                });
            });
        </script>

    @endif

    @isset($tawk_to)
        <script>
            @php echo html_entity_decode($tawk_to->tawk_to); @endphp
        </script>
    @endisset

    @livewireScripts



</body>

</html>
