@if (Auth::user())
    @can('service view')
        <div class="easier-mode">
            <div class="easier-section-area">
            @endcan
@endif

<!--// Blog Section Start //-->
<section class="section pb-minus-76" id="blog">
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
        @isset($service_section_style1)
            <div class="row">
                <div class="col-md-6">
                    <div class="section-heading-left">
                        <span>@php echo html_entity_decode($service_section_style1->section_title); @endphp</span>
                        <h2>@php echo html_entity_decode($service_section_style1->title); @endphp</h2>
                    </div>
                </div>
            </div>
        @else
            @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                <div class="row">
                    <div class="col-md-6">
                        <div class="section-heading-left">
                            <span>Services</span>
                            <h2>Our Services</h2>
                        </div>
                    </div>
                </div>
            @endif
        @endisset



        @if (is_countable($services_style1) && count($services_style1) > 0)
            <div class="owl-carousel owl-theme" id="serviceCarousel">
                @foreach ($services_style1 as $item)
                    <div class="item">
                        @if (Auth::user())
                            @can('blog view')
                                @php
                                    $url = request()->path();
                                    $modified_url = str_replace('/', '-bracket-', $url);
                                @endphp
                                <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                                    @csrf
                                    <input type="hidden" name="route" value="blog.edit">
                                    <input type="hidden" name="single_id" value="{{ $item->id }}">
                                    <input type="hidden" name="site_url" value="{{ $modified_url }}">
                                    <button type="submit" class="me-2 custom-pure-button ">
                                        <i class="fa fa-edit text-info easier-custom-font-size-24"></i>
                                    </button>
                                </form>
                            @endcan
                        @endif
                        <div class="blog-item">
                            @if (!empty($item->section_image_2))
                                <div class="blog-img">
                                    <a href="{{ route('default-blog-detail-show', ['slug' => $item->slug]) }}">
                                        <img src="{{ asset('uploads/img/service/' . $item->section_image_2) }}"
                                            alt="Service image" class="img-fluid w-100">
                                    </a>
                                </div>
                            @else
                                <div class="blog-img">
                                    <a href="{{ route('default-blog-detail-show', ['slug' => $item->slug]) }}">
                                        <img src="{{ asset('uploads/img/dummy/no-image.jpg') }}" alt="Blog image"
                                            class="img-fluid">
                                    </a>
                                </div>
                            @endif
                            <div class="blog-body">
                                
                                <h5>
                                    <a
                                        href="{{ route('default-blog-detail-show', ['slug' => $item->slug]) }}">{{ $item->title }}</a>
                                </h5>
                                @if (!empty($item->short_description))
                                    <p>{{ $item->short_description }}</p>
                                @endif
                                <a href="{{ route('default-blog-detail-show', ['slug' => $item->slug]) }}"
                                    class="blog-link">
                                    Schedule Now
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
                @unset ($item)
            </div>
        @else
            @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                <div class="owl-carousel owl-theme" id="serviceCarousel">
                    <div class="item">
                        <div class="blog-item">
                            <div class="blog-img">
                                <a href="#">
                                    <img src="{{ asset('uploads/img/dummy/600x400.jpg') }}" alt="Blog image"
                                        class="img-fluid">
                                </a>
                            </div>
                            <div class="blog-body">
                                {{-- <div class="blog-meta">
                                    <a href="#"><span><i class="far fa-user"></i>By Admin</span></a>
                                    <a href="#"><span><i class="far fa-bookmark"></i>Design</span></a>
                                </div> --}}
                                <h5>
                                    <a href="#">
                                        ALCOHOL TESTING
                                    </a>
                                </h5>
                                <p>
                                    BREATH & ETG
                                </p>
                                <a href="#" class="blog-link">
                                    Schedule Now
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="blog-item">
                            <div class="blog-img">
                                <a href="#">
                                    <img src="{{ asset('uploads/img/dummy/600x400.jpg') }}" alt="Blog image"
                                        class="img-fluid">
                                </a>
                            </div>
                            <div class="blog-body">
                                {{-- <div class="blog-meta">
                                    <a href="#"><span><i class="far fa-user"></i>By Admin</span></a>
                                    <a href="#"><span><i class="far fa-bookmark"></i>Design</span></a>
                                </div> --}}
                                <h5>
                                    <a href="#">
                                        HAIR DRUG TESTING
                                    </a>
                                </h5>
                                <p>
                                    90 DAY DETECTION
                                </p>
                                <a href="#" class="blog-link">
                                    Schedule Now
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="blog-item">
                            <div class="blog-img">
                                <a href="#">
                                    <img src="{{ asset('uploads/img/dummy/600x400.jpg') }}" alt="Blog image"
                                        class="img-fluid">
                                </a>
                            </div>
                            <div class="blog-body">
                                {{-- <div class="blog-meta">
                                    <a href="#"><span><i class="far fa-user"></i>By Admin</span></a>
                                    <a href="#"><span><i class="far fa-bookmark"></i>Design</span></a>
                                </div> --}}
                                <h5>
                                    <a href="#">
                                        DOT TESTING
                                    </a>
                                </h5>
                                <p>
                                    FMCSA, USCG, FAA, FTA
                                </p>
                                <a href="#" class="blog-link">
                                    Schedule Now
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="blog-item">
                            <div class="blog-img">
                                <a href="#">
                                    <img src="{{ asset('uploads/img/dummy/600x400.jpg') }}" alt="Blog image"
                                        class="img-fluid">
                                </a>
                            </div>
                            <div class="blog-body">
                                {{-- <div class="blog-meta">
                                    <a href="#"><span><i class="far fa-user"></i>By Admin</span></a>
                                    <a href="#"><span><i class="far fa-bookmark"></i>Wordpress</span></a>
                                </div> --}}
                                <h5>
                                    <a href="#">
                                        URINE DRUG TESTING
                                    </a>
                                </h5>
                                <p>
                                    5,7,9,10 PANELS
                                </p>
                                <a href="#" class="blog-link">
                                    Schedule Now
                                    <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
        
    </div>
</section>
<!--// Blog Section End //-->

@if (Auth::user())
    @can('blog view')
        </div>
        <div class="easier-middle">
            @php
                $url = request()->path();
                $modified_url = str_replace('/', '-bracket-', $url);
            @endphp
            <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                @csrf
                <input type="hidden" name="route" value="blog.index">
                <input type="hidden" name="style" value="">
                <input type="hidden" name="site_url" value="{{ $modified_url }}">
                <button type="submit" class="custom-btn text-white me-2 mb-2">
                    <i class="fa fa-edit text-white"></i> {{ __('content.edit_section_title_description') }}
                </button>
            </form>
            <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                @csrf
                <input type="hidden" name="route" value="blog.create">
                <input type="hidden" name="style" value="">
                <input type="hidden" name="site_url" value="{{ $modified_url }}">
                <button type="submit" class="custom-btn text-white me-2 mb-2">
                    <i class="fa fa-plus text-white"></i> {{ __('content.add_blog') }}
                </button>
            </form>
            <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                @csrf
                <input type="hidden" name="route" value="blog.index">
                <input type="hidden" name="style" value="">
                <input type="hidden" name="site_url" value="{{ $modified_url }}">
                <button type="submit" class="custom-btn text-white me-2 mb-2">
                    <i class="fab fa-blogger-b text-white"></i> {{ __('content.blogs') }}
                </button>
            </form>
        </div>
        </div>
    @endcan
@endif
