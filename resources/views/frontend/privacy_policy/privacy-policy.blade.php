@extends('layouts.frontend.master2')


@section('content')
    <!--// Breadcrumb Section Start //-->
    <section class="breadcrumb-section section" data-scroll-index="1"
        style="background-image: url('{{ !empty($PrivacyPolicy->custom_breadcrumb_image) && $PrivacyPolicy->breadcrumb_status == 'yes'
            ? asset('uploads/img/background/breadcrumb/' . $PrivacyPolicy->custom_breadcrumb_image)
            : (!empty($breadcrumb_image->section_image)
                ? asset('uploads/img/background/breadcrumb/' . $breadcrumb_image->section_image)
                : asset('uploads/img/default-breadcrumb.jpg')) }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">

        <div class="container">
            @if (Auth::user())
                @can('background view')
                    <!-- hover effect for mobile devices  -->
                    <div class="click-icon d-md-none text-center">
                        <button class="custom-btn text-white">
                            <i class="fa fa-mobile-alt text-white"></i> {{ __('content.touch') }}
                        </button>
                    </div>
                @endcan
            @endif
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="breadcrumb-inner">
                        <h1>Privacy Policy</h1>
                        <ul class="breadcrumb-links">
                            <li><a href="{{ url('/') }}">{{ __('frontend.home') }}</a></li>
                            <li class="active">Privacy Policy</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--// Breadcrumb Section end //-->



    <!--// Services Section Start //-->
    <section class="section pb-minus-70">
        <div class="container">
            @if (Auth::user())
                @can('service view')
                    <!-- hover effect for mobile devices  -->
                    <div class="click-icon d-md-none text-center">
                        <button class="custom-btn text-white">
                            <i class="fa fa-mobile-alt text-white"></i> {{ __('content.touch') }}
                        </button>
                    </div>
                @endcan
            @endif

            @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="section-heading">
                            {{-- <h2>Customizable Screening Solutions</h2> --}}
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-12"></div>
                    <div class="section-heading">
                        <h2>Privacy Policy</h2>
                    </div>

                    <p></p>
                        @php echo html_entity_decode($PrivacyPolicy->content); @endphp
                    </p>
                </div>
            </div>
        </div>
    </section>
    <!--// Services Section End //-->

    
@endsection
