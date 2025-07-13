@extends('layouts.frontend.master2')


@section('content')

    {{-- @include('frontend.sections.banner.banner-style3') --}}

    <!--// My Works Start //-->
    <section class="section bg-primary-light " id="porfolio">
        <div class="container pt-5">
            @if (Auth::user())
                @can('portfolio view')
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
                            <span>Tests</span>
                            <h2>Our {{ $portfolios_style1[0]->portfolio_category->category_name }} </h2>
                        </div>
                    </div>
                @endif
            </div>
            @if (is_countable($portfolios_style1) && count($portfolios_style1) > 0)
                <div class="row portfolio-grid" id="portfolio-masonry-wrap">
                    @foreach ($portfolios_style1 as $item)
                        <div
                            class="col-md-4 col-lg-3 portfolio-item {{ $item->portfolio_category->portfolio_category_slug }}">
                            @if (Auth::user())
                                @can('portfolio view')
                                    @php
                                        $url = request()->path();
                                        $modified_url = str_replace('/', '-bracket-', $url);
                                    @endphp
                                    <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                                        @csrf
                                        <input type="hidden" name="route" value="portfolio.edit">
                                        <input type="hidden" name="single_id" value="{{ $item->id }}">
                                        <input type="hidden" name="site_url" value="{{ $modified_url }}">
                                        <button type="submit" class="me-2 custom-pure-button ">
                                            <i class="fa fa-edit text-info easier-custom-font-size-24"></i>
                                        </button>
                                    </form>
                                @endcan
                            @endif
                            <div class="portfolio-item-inner">
                                @if (!empty($item->section_image))
                                    <div class="portfolio-item-img">
                                        <img src="{{ asset('uploads/img/portfolio/' . $item->section_image) }}"
                                            alt="Portfolio image" class="img-fluid">
                                        {{-- <a href="{{ asset('uploads/img/portfolio/' . $item->section_image) }}"
                                            class="portfolio-zoom-link">
                                            <i class="fas fa-search"></i>
                                        </a> --}}
                                    </div>
                                @else
                                    <div class="portfolio-item-img">
                                        <img src="{{ asset('uploads/img/dummy/600x600.jpg') }}" alt="Portfolio image"
                                            class="img-fluid">
                                        <a href="{{ asset('uploads/img/dummy/600x600.jpg') }}" class="portfolio-zoom-link">
                                            <i class="fas fa-search"></i>
                                        </a>
                                    </div>
                                @endif
                                <a href="{{ !empty($item->url) ? $item->url : route('default-portfolio-detail-show', ['portfolio_slug' => $item->portfolio_slug]) }}"
                                    class="portfolio-link">
                                    <div class="body d-block">
                                        <div class="portfolio-details">
                                            <h6>{{ $item->title }}</h6>
                                            <div class="d-flex justify-content-between mt-2">
                                                <span class="text-start">{{ $item->code }}</span>
                                                <span class="text-end">${{ $item->price }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                    @unset ($item)
                </div>
            @endif
            <div class="row">
                <div class="d-flex justify-content-center mt-4">
                    {{ $portfolios_style1->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>

        </div>
    </section>
    <!--// My Works End //-->

@endsection
