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
            --pf-primary:       #1a56db;
            --pf-primary-dark:  #1044b3;
            --pf-primary-light: #e8f0fe;
            --pf-primary-glow:  rgba(26,86,219,.15);
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
            --pf-font-head:     'Sora', sans-serif;
            --pf-font-body:     'DM Sans', sans-serif;

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


        /* ─── Plan Section Ranodom consortium section custom css ─── */
        .plan-section {
            background: var(--pf-surface-2);
            padding: 55px 0 60px;
            border-bottom: 1px solid var(--pf-border);
        }
        .section-eyebrow {
            font-family: var(--pf-font-head);
            font-size: .72rem; font-weight: 700;
            letter-spacing: .09em; text-transform: uppercase;
            color: var(--pf-primary); margin-bottom: .35rem;
        }
        .plan-section h2 {
            font-family: var(--pf-font-head);
            font-weight: 700; color: var(--pf-text); margin-bottom: .5rem;
        }
        .plan-section .sub {
            font-family: var(--pf-font-body);
            color: var(--pf-muted); max-width: 560px; margin: 0 auto 2.5rem;
        }

        /* Plan card */
        .plan-card {
            border: 2px solid var(--pf-border) !important;
            border-top: 4px solid var(--pf-border) !important;
            border-radius: 15px !important;
            box-shadow: 0 2px 12px rgba(15,23,42,.06) !important;
            transition: transform .3s, box-shadow .3s, border-color .25s;
            cursor: pointer;
            height: 100%;
            position: relative;
            overflow: hidden;
            background: #fff;
        }
        .plan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 14px 40px rgba(15,23,42,.11) !important;
        }
        .plan-card.active {
            border-color: var(--pf-primary) !important;
            box-shadow: 0 0 0 3px var(--pf-primary-glow), 0 10px 36px rgba(26,86,219,.14) !important;
        }
        .plan-card.active::after {
            content: '✓ Selected';
            position: absolute; top: 16px; right: -30px;
            background: var(--pf-primary); color: #fff;
            font-family: var(--pf-font-head); font-size: .62rem; font-weight: 700;
            letter-spacing: .06em; text-transform: uppercase;
            padding: 4px 38px; transform: rotate(45deg);
        }
        .plan-card .card-body { padding: 1.6rem 1.4rem; }
        .plan-icon-wrap {
            width: 52px; height: 52px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem; color: #fff; margin-bottom: 1rem;
        }
        .plan-card h4 {
            font-family: var(--pf-font-head);
            font-size: 1rem; font-weight: 700; color: var(--pf-text); margin-bottom: .2rem;
        }
        .plan-card .range {
            font-family: var(--pf-font-body);
            font-size: .78rem; color: var(--pf-muted); margin-bottom: .9rem;
        }
        .plan-card hr { border-color: var(--pf-border); margin: .75rem 0; }

        /* Fee list inside plan card */
        .plan-fee-list { list-style: none; padding: 0; margin: 0; }
        .plan-fee-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: var(--pf-font-body);
            font-size: .78rem;
            color: var(--pf-muted);
            padding: .32rem 0;
            border-bottom: 1px dashed var(--pf-border);
        }
        .plan-fee-list li:last-child { border-bottom: none; }
        .plan-fee-list li .fee-label { display: flex; align-items: center; gap: .4rem; }
        .plan-fee-list li .fee-label i { color: var(--pf-primary); font-size: .72rem; }
        .plan-fee-list li .fee-val { font-weight: 700; color: var(--pf-text); white-space: nowrap; }
        .plan-card.active .plan-fee-list li { color: var(--pf-primary-dark); }
        .plan-card.active .plan-fee-list li .fee-val { color: var(--pf-primary); }

        /* ─── Main form section ─── */
        #application-form {
            font-family: var(--pf-font-body);
            padding: 50px 0 70px;
            background: var(--pf-surface);
        }

        /* pf-card */
        .pf-card {
            background: var(--pf-surface);
            border-radius: 20px;
            box-shadow: var(--pf-shadow-lg);
            border: 1px solid var(--pf-border);
            overflow: hidden;
        }
        .pf-header {
            background: linear-gradient(135deg,#1a56db 0%,#0e3fa3 60%,#0c2f80 100%);
            padding: 1.9rem 2.25rem 1.65rem;
            position: relative; overflow: hidden;
        }
        .pf-header::before {
            content:''; position:absolute; top:-60px; right:-60px;
            width:220px; height:220px; border-radius:50%; background:rgba(255,255,255,.05);
        }
        .pf-header::after {
            content:''; position:absolute; bottom:-40px; left:30%;
            width:160px; height:160px; border-radius:50%; background:rgba(6,182,212,.1);
        }
        .pf-header .pill {
            background:rgba(255,255,255,.15); backdrop-filter:blur(6px);
            border:1px solid rgba(255,255,255,.2); color:#fff;
            font-family:var(--pf-font-head); font-size:.68rem; font-weight:600;
            letter-spacing:.07em; text-transform:uppercase;
            padding:.3rem .85rem; border-radius:100px;
            display:inline-block; margin-bottom:.75rem;
        }
        .pf-header h4 {
            font-family:var(--pf-font-head);
            font-size:1.3rem; font-weight:700; color:#fff; margin-bottom:.25rem; line-height:1.3;
        }
        .pf-header p { color:rgba(255,255,255,.7); font-size:.86rem; margin:0; }
        .pf-header-icon {
            width:46px; height:46px; background:rgba(255,255,255,.12); border-radius:12px;
            display:flex; align-items:center; justify-content:center; font-size:1.2rem; color:#fff; flex-shrink:0;
        }
        .pf-body { padding:1.75rem 2.25rem 2.25rem; }

        /* pf-section blocks */
        .pf-section {
            border:1px solid var(--pf-border); border-radius:var(--pf-radius);
            overflow:hidden; margin-bottom:1.5rem;
            background:var(--pf-surface); transition:box-shadow .2s;
        }
        .pf-section:focus-within {
            box-shadow:0 0 0 3px var(--pf-primary-glow); border-color:rgba(26,86,219,.3);
        }
        .pf-section-head {
            background:var(--pf-primary-light); padding:.8rem 1.4rem;
            display:flex; align-items:center; gap:.65rem;
            border-bottom:1px solid rgba(26,86,219,.1);
        }
        .pf-section-head .icon-wrap {
            width:28px; height:28px; background:var(--pf-primary); border-radius:7px;
            display:flex; align-items:center; justify-content:center;
            color:#fff; font-size:.72rem; flex-shrink:0;
        }
        .pf-section-head h6 {
            font-family:var(--pf-font-head); font-size:.87rem; font-weight:700;
            color:var(--pf-primary-dark); margin:0;
        }
        .pf-section-body { padding:1.35rem; }

        /* Controls */
        .pf-label {
            font-family:var(--pf-font-head); font-size:.78rem; font-weight:600;
            color:var(--pf-text); letter-spacing:.01em; margin-bottom:.35rem; display:block;
        }
        .pf-req { color:var(--pf-danger); margin-left:2px; }
        .pf-opt {
            font-size:.66rem; background:#f1f5f9; color:var(--pf-muted);
            border-radius:4px; padding:1px 5px; font-weight:500; margin-left:5px; vertical-align:middle;
        }
        .pf-icon-wrap { position:relative; }
        .pf-icon-wrap .pf-icon {
            position:absolute; left:.85rem; top:50%; transform:translateY(-50%);
            color:var(--pf-light); font-size:.8rem; pointer-events:none;
        }
        .pf-icon-wrap .pf-control { padding-left:2.3rem; }
        .pf-control {
            width:100%; border:1.5px solid var(--pf-border); border-radius:var(--pf-radius-sm);
            padding:.62rem 1rem; font-size:.87rem; font-family:var(--pf-font-body);
            color:var(--pf-text); background:var(--pf-surface);
            transition:border-color .2s,box-shadow .2s,background .2s;
            box-shadow:var(--pf-shadow-sm); outline:none; -webkit-appearance:none; appearance:none;
        }
        .pf-control::placeholder { color:var(--pf-light); }
        .pf-control:focus {
            border-color:var(--pf-primary); box-shadow:0 0 0 3.5px var(--pf-primary-glow); background:#fafcff;
        }
        textarea.pf-control { resize:vertical; min-height:80px; }
        .pf-hint { font-size:.74rem; color:var(--pf-muted); margin-top:.28rem; }
        .pf-hint.danger { color:var(--pf-danger) !important; }

        /* ─── Right-side sticky panel ─── */
        .summary-card {
            background:var(--pf-surface);
            border:1.5px solid var(--pf-border);
            border-radius:var(--pf-radius);
            box-shadow:var(--pf-shadow-md);
            overflow:hidden;
        }
        .summary-card-head {
            background:linear-gradient(135deg,#1a56db 0%,#0e3fa3 100%);
            padding:1.1rem 1.4rem;
        }
        .summary-card-head h5 {
            font-family:var(--pf-font-head); font-weight:700; color:#fff; margin:0; font-size:.95rem;
        }

        /* Driver count inside summary */
        .driver-block {
            padding:1.1rem 1.4rem;
            background:var(--pf-surface-2);
            border-bottom:1.5px solid var(--pf-border);
        }
        .driver-block .pf-label { margin-bottom:.4rem; }

        /* Price rows */
        .summary-body { padding:1.2rem 1.4rem 0; }
        .summary-row {
            display:flex; justify-content:space-between; align-items:center;
            padding:.48rem 0; border-bottom:1px solid var(--pf-border);
            font-family:var(--pf-font-body); font-size:.82rem; color:var(--pf-muted);
        }
        .summary-row:last-of-type { border-bottom:none; }
        .summary-row .val { font-weight:700; color:var(--pf-text); }
        .summary-total {
            background:var(--pf-primary-light);
            border-radius:var(--pf-radius-sm);
            padding:.95rem 1.1rem;
            display:flex; justify-content:space-between; align-items:center;
            margin:1rem 1.4rem;
        }
        .summary-total .label {
            font-family:var(--pf-font-head); font-weight:700; font-size:.88rem; color:var(--pf-primary-dark);
        }
        .summary-total .amount {
            font-family:var(--pf-font-head); font-size:1.75rem; font-weight:800; color:var(--pf-primary);
        }
        .summary-actions { padding:0 1.4rem 1.4rem; }

        /* Submit button */
        .pf-btn-submit {
            background:linear-gradient(135deg,#1a56db 0%,#0e3fa3 100%);
            border:none; border-radius:12px; width:100%;
            font-family:var(--pf-font-head); font-weight:700; font-size:.95rem;
            color:#fff; padding:.9rem 2rem;
            transition:all .25s; box-shadow:0 4px 20px rgba(26,86,219,.35);
            display:flex; align-items:center; justify-content:center; gap:.55rem; cursor:pointer;
        }
        .pf-btn-submit:hover {
            transform:translateY(-2px); box-shadow:0 8px 30px rgba(26,86,219,.45); color:#fff;
        }
        .pf-btn-submit:active { transform:translateY(0); }
        .pf-btn-submit:disabled { opacity:.7; cursor:not-allowed; transform:none; }
        .pf-secure {
            font-size:.74rem; color:var(--pf-muted);
            display:flex; align-items:center; gap:.35rem; justify-content:center; margin-top:.65rem;
        }
        .pf-secure i { color:#059669; }

        /* Alert */
        .pf-alert {
            border-radius:var(--pf-radius-sm); border:none; font-size:.865rem;
            padding:.85rem 1rem; margin-bottom:1.1rem;
            display:flex; align-items:flex-start; gap:.65rem;
        }
        .pf-alert-danger {
            background:rgba(225,29,72,.05); color:#9f1239; border:1px solid rgba(225,29,72,.2);
        }

        /* Notes toggle */
        .notes-toggle-btn {
            background:none; border:none; padding:0;
            font-family:var(--pf-font-head); font-size:.78rem; font-weight:600;
            color:var(--pf-primary); cursor:pointer; display:flex; align-items:center; gap:.4rem;
            margin-top:.4rem;
        }
        .notes-toggle-btn:hover { text-decoration:underline; }

        /* Loader */
        #loader-overlay {
            position:fixed; inset:0; background:rgba(15,23,42,.7);
            backdrop-filter:blur(5px); z-index:9999;
            display:flex; align-items:center; justify-content:center;
            color:#fff; flex-direction:column;
            opacity:0; pointer-events:none; transition:opacity .3s ease;
        }
        #loader-overlay.show { opacity:1; pointer-events:auto; }
        .spinner-custom {
            width:58px; height:58px;
            border:5px solid rgba(255,255,255,.2); border-top:5px solid #fff;
            border-radius:50%; animation:spin 1s linear infinite; margin-bottom:18px;
        }
        @keyframes spin { to { transform:rotate(360deg); } }

        @media (max-width:768px) {
            .pf-header { padding:1.6rem 1.25rem 1.4rem; }
            .pf-body    { padding:1.25rem 1rem 1.75rem; }
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
