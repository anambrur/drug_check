@extends('layouts.frontend.master2')


@section('content')
    <!--// Breadcrumb Section Start //-->
    <section class="breadcrumb-section section" data-scroll-index="1"
        style="background-image: url('{{ !empty($backgrounds->custom_breadcrumb_image2) && $backgrounds->breadcrumb_status == 'yes'
            ? asset('uploads/img/background/breadcrumb/' . $backgrounds->custom_breadcrumb_image2)
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
                        <h1>Packages</h1>
                        <ul class="breadcrumb-links">
                            <li><a href="{{ url('/') }}">{{ __('frontend.home') }}</a></li>
                            <li class="active">Packages</li>
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
                @foreach ($categories as $item)
                    <div class="col-lg-12 col-md-12 mb-4 wow fadeInLeft" data-wow-duration="0.5s"
                        data-wow-delay="0.{{ $loop->iteration }}s">
                        <h2 class="text-center py-4">{{ $item->category_name }}</h2>
                        <div class="row d-flex">
                            @foreach ($item->packages as $package)
                                <div class="col-lg-4 col-md-6 mb-4 wow fadeInLeft" data-wow-duration="0.5s"
                                    data-wow-delay="0.{{ $loop->iteration }}s">
                                    <a class="text-decoration-none text-dark" href="{{ route('frontend.background-check-forms') }}">
                                        <div class="services-item h-100">
                                            <div class="body">
                                                <h5>{{ $package->title }}</h5>
                                                <p>{!! html_entity_decode($package->description) !!}</p>
                                                <h5 class="text-primary text-faded">{{ $package->result }}</h5>
                                                <h5 class="text-success fst-italic text-faded">{{ $package->price }}</h5>
    
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                @unset ($item)
            </div>
        </div>
    </section>
    <!--// Services Section End //-->

    <style>
        .services-item {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 100%;
        }

        .text-faded {
            opacity: 0.6;
        }
    </style>
@endsection
