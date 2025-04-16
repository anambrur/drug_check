@extends('layouts.frontend.master2')


@section('content')

    <!--// Breadcrumb Section Start //-->
    <section class="breadcrumb-section section" data-scroll-index="1"
        style="background-image: url('{{ !empty($backgrounds->custom_breadcrumb_image) && $backgrounds->breadcrumb_status == 'yes'
            ? asset('uploads/img/background/breadcrumb/' . $backgrounds->custom_breadcrumb_image)
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
                        <h1>Top Rated Background Screening Solutions</h1>
                        <ul class="breadcrumb-links">
                            <li><a href="{{ url('/') }}">{{ __('frontend.home') }}</a></li>
                            <li class="active">Top Rated Background Screening Solutions</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--// Breadcrumb Section end //-->
    @php
        // dd($backgrounds);
    @endphp


    <!--// background Section Start //-->
    <section class="section" id="background">
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
            <div class="row">
                @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                    <div class="col-md-6">
                        <div class="section-heading-left">
                            <span>{{ $backgrounds->section_title }}</span>

                        </div>
                    </div>
                    <div>
                        <p>
                            @php echo html_entity_decode($backgrounds->description2); @endphp
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </section>


    <!--// Skills Section Start //-->
    <section class="section bg-primary-light">
        <div class="container">
            @if (Auth::user())
                @can('section view')
                    <!-- hover effect for mobile devices  -->
                    <div class="click-icon d-md-none text-center">
                        <button class="custom-btn text-white">
                            <i class="fa fa-mobile-alt text-white"></i> {{ __('content.touch') }}
                        </button>
                    </div>
                @endcan
            @endif
            <div class="row">

                <div class="col-lg-12 wow fadeInUp" data-wow-duration="0.7s" data-wow-delay="0.3s">
                    <div class="skills-inner">
                        @isset($why_choose_section_style1)
                            {{-- <h6>@php echo html_entity_decode($why_choose_section_style1->section_title); @endphp</h6> --}}
                            <h2>@php echo html_entity_decode($why_choose_section_style1->title); @endphp</h2>
                            <p>@php echo html_entity_decode($why_choose_section_style1->description); @endphp</p>
                            @if (!empty($why_choose_section_style1->item))
                                @php
                                    $str = $why_choose_section_style1->item;
                                    $array_items = explode(',', $str);
                                @endphp
                                <div class="row">
                                    <div class="col-md-6 col-sm-6">
                                        <ul class="mb-resp-15">
                                            @foreach ($array_items as $index => $item)
                                                @if ($index < count($array_items) / 2)
                                                    <li>{{ $item }}</li>
                                                @endif
                                            @endforeach
                                            @unset ($item)
                                        </ul>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <ul>
                                            @foreach ($array_items as $index => $item)
                                                @if ($index >= count($array_items) / 2)
                                                    <li>{{ $item }}</li>
                                                @endif
                                            @endforeach
                                            @unset ($item)
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        @else
                            @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                                <h6>Why Choose Us</h6>
                                <h2>We are specialize in frameworks UI for years</h2>
                                <p>
                                    A front end library is being released every day and it is requested
                                    to master these technologies.I also follow the market every day and
                                    follow the updates of new frontend frameworks and programming frameworks.
                                    It is easier for me to keep up with new technologies in projects
                                </p>
                                <div class="row">
                                    <div class="col-md-6 col-sm-6">
                                        <ul class="mb-resp-15">
                                            <li>Full Responsive Design</li>
                                            <li>Modern Browser Compatible</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <ul>
                                            <li>Clean & Quality Code</li>
                                            <li>7/24 Customer Support</li>
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        @endisset
                        @if (is_countable($why_chooses_style1) && count($why_chooses_style1) > 0)
                            <div class="row">
                                @foreach ($why_chooses_style1 as $item)
                                    <div class="col-md-6 col-sm-6 mb-3 skills-item-resp">
                                        @if (Auth::user())
                                            @can('section view')
                                                @php
                                                    $url = request()->path();
                                                    $modified_url = str_replace('/', '-bracket-', $url);
                                                @endphp
                                                <form method="POST" action="{{ route('site-url.index') }}"
                                                    class="d-inline-block">
                                                    @csrf
                                                    <input type="hidden" name="route" value="why-choose.edit">
                                                    <input type="hidden" name="single_id" value="{{ $item->id }}">
                                                    <input type="hidden" name="site_url" value="{{ $modified_url }}">
                                                    <button type="submit" class="me-2 custom-pure-button ">
                                                        <i class="fa fa-edit text-info easier-custom-font-size-24"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        @endif
                                        <div class="skills-item">
                                            <div class="skills-item-text">
                                                <h5>@php echo html_entity_decode($item->title); @endphp</h5>
                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                                @unset ($item)
                            </div>
                        @else
                            @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 skills-item-resp">
                                        <div class="skills-item">
                                            <div class="skills-item-text">
                                                <h5>Design</h5>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6 skills-item-resp">
                                        <div class="skills-item">
                                            <div class="skills-item-text">
                                                <h5>Coding</h5>
                                            </div>
                                            <div class="body">
                                                <h2 class="counter">90</h2>
                                                <div class="skills-progress-bar">
                                                    <div class="skills-progress-value slideInLeft wow" data-percent="90">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--// Skills Section End //-->



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
                            <h2>Customizable Screening Solutions</h2>
                        </div>
                    </div>
                </div>
            @endif

            @if (is_countable($backgroundCategory) && count($backgroundCategory) > 0)
                <div class="row">
                    @foreach ($backgroundCategory as $item)
                        <div class="col-lg-4 col-md-6 wow fadeInLeft" data-wow-duration="0.5s"
                            data-wow-delay="0.{{ $loop->iteration }}s">
                            @if (Auth::user())
                                @can('background view')
                                    @php
                                        $url = request()->path();
                                        $modified_url = str_replace('/', '-bracket-', $url);
                                    @endphp
                                    <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                                        @csrf
                                        <input type="hidden" name="route" value="service.edit">
                                        <input type="hidden" name="single_id" value="{{ $item->id }}">
                                        <input type="hidden" name="site_url" value="{{ $modified_url }}">
                                        <button type="submit" class="me-2 custom-pure-button ">
                                            <i class="fa fa-edit text-info easier-custom-font-size-24"></i>
                                        </button>
                                    </form>
                                @endcan
                            @endif
                            <div class="services-item" style="min-height: 350px;">
                                <div class="body">
                                    <h4>0{{ $loop->index + 1 }}</h4>
                                    <h5>{{ $item->category_name }}</h5>
                                    <p>{{ $item->short_description }}</p>
                                </div>
                            </div>

                        </div>
                    @endforeach
                    @unset ($item)
                </div>
            @endif

        </div>
    </section>
    <!--// Services Section End //-->



    <!--// Services 2 Section start //-->
    <section class="section pb-minus-70">
        <div class="container">
            <hr>
            @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="section-heading">
                            <h2>Services</h2>
                        </div>
                    </div>
                </div>
            @endif


            <div class="row">
                <div class="col-lg-12">
                    <p>
                        @php echo html_entity_decode($backgrounds->description); @endphp
                    </p>
                </div>
            </div>

        </div>
    </section>
    <!--// Services 2 Section End //-->








@endsection
