@if (Auth::user())
    @can('section view')
        <div class="easier-mode">
            <div class="easier-section-area">
            @endcan
@endif

<!--// About Section Start //-->
<section class="section" id="about">
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

            <div class="col-lg-6">
                <div class="about-inner wow fadeInUp" data-wow-duration="0.5s" data-wow-delay="0.1s">
                    @isset($about_section_style1)
                        <h6>@php echo html_entity_decode($about_section_style1->section_title); @endphp</h6>
                        <h2>@php echo html_entity_decode($about_section_style1->title); @endphp</h2>

                        <div class="mb-4">
                            @if (request()->is('/'))
                                <p class="mb-2">@php echo html_entity_decode(Str::words($about_section_style1->description, 50, '...')); @endphp</p>
                            @else
                                <p class="mb-2">@php echo html_entity_decode($about_section_style1->description); @endphp</p>
                            @endif
                        </div>
                    @else
                        @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                            {{-- <h6>About Us</h6> --}}
                            <h2>We are here with 10 years of user experience</h2>
                            <p>
                                We are prevent your loss of time and indecision in the works I have
                                taken and the projects I have done and offer the best solution.
                                Many of my customers and brands express their satisfaction with
                                working with me.We can appeal to a huge audience and grow your business.
                            </p>
                        @endif
                    @endisset

                    @isset($about_section_style1)
                        @if (!empty($about_section_style1->button_name))
                            <a href="{{ $about_section_style1->button_url }}" class="primary-btn me-3 mb-3">
                                <span class="text">{{ $about_section_style1->button_name }}</span>
                                <span class="icon"><i class="fa fa-arrow-right"></i></span>
                            </a>
                        @endif
                        @if (!empty($about_section_style1->button_name_2))
                            <a href="@if (!empty($about_section_style1->cv_file)) {{ asset('uploads/img/about/' . $about_section_style1->cv_file) }} @else # @endif"
                                class="primary-btn" @if (!empty($about_section_style1->cv_file)) download @endif>
                                <span class="text">{{ $about_section_style1->button_name_2 }}</span>
                                <span class="icon"><i class="fa fa-download"></i></span>
                            </a>
                        @endif
                    @else
                        @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                            <a href="javascript:void(0)" class="primary-btn me-3 mb-3">
                                <span class="text">Get Started</span>
                                <span class="icon"><i class="fa fa-arrow-right"></i></span>
                            </a>
                            <a href="javascript:void(0)" class="primary-btn">
                                <span class="text">Download Cv</span>
                                <span class="icon"><i class="fa fa-download"></i></span>
                            </a>
                        @endif
                    @endisset
                </div>
            </div>


            @isset($about_section_style1)
                @if (!empty($about_section_style1->section_image))
                    <div class="col-lg-6 d-flex justify-content-center align-items-center">
                        <div class="about-img wow fadeInUp " data-wow-duration="0.5s" data-wow-delay="0.2s">
                            @if (!empty($about_section_style1->section_image))
                                <img src="{{ asset('uploads/img/about/' . $about_section_style1->section_image) }}"
                                    alt="About image" title="About image" class="img-fluid">
                            @endif
                            @if (!empty($about_section_style1->video_url))
                                @if ($about_section_style1->video_type == 'youtube')
                                    <a class="about-video-btn" href="{{ $about_section_style1->video_url }}"><i
                                            class="fa fa-play"></i></a>
                                @else
                                    <a class="about-video-btn-2" href="{{ $about_section_style1->video_url }}"><i
                                            class="fa fa-play"></i></a>
                                @endif
                                <div class="video-border-line"></div>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                    <div class="col-lg-6">
                        <div class="about-img wow fadeInUp" data-wow-duration="0.5s" data-wow-delay="0.2s">
                            <img src="{{ asset('uploads/img/dummy/480x600.jpg') }}" alt="About image" title="About image"
                                class="img-fluid">
                            <a class="about-video-btn" href="https://www.youtube.com/watch?v=YqQx75OPRa0"><i
                                    class="fa fa-play"></i></a>
                            <div class="video-border-line"></div>
                        </div>
                    </div>
                @endif
            @endisset

        </div>
    </div>
</section>
<!--// About Section End //-->

@if (Auth::user())
    @can('section view')
        </div>
        <div class="easier-middle">
            @php
                $url = request()->path();
                $modified_url = str_replace('/', '-bracket-', $url);
            @endphp
            <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                @csrf
                <input type="hidden" name="route" value="about.create">
                <input type="hidden" name="style" value="style1">
                <input type="hidden" name="site_url" value="{{ $modified_url }}">
                <button type="submit" class="custom-btn text-white me-2 mb-2">
                    <i class="fa fa-edit text-white"></i> {{ __('content.edit_about') }}
                </button>
            </form>
        </div>
        </div>
    @endcan
@endif
