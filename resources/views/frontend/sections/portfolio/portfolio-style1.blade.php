@if (Auth::user())
    @can('portfolio view')
        <div class="easier-mode">
            <div class="easier-section-area">
            @endcan
@endif

@php
    $siteMainColor = (isset($color_option) && $color_option->color_option != 0)
        ? $color_option->main_color
        : '#ff4500';
@endphp

<!--// My Works Start //-->
<section class="section bg-primary-light" id="porfolio">
    <div class="container">
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
            @isset($portfolio_section_style1)
                <div class="col-md-5">
                    <div class="section-heading-left">
                        {{-- <span>@php echo html_entity_decode($portfolio_section_style1->section_title); @endphp</span> --}}
                        <h2>@php echo html_entity_decode($portfolio_section_style1->title); @endphp</h2>
                    </div>
                </div>
            @else
                @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                    <div class="col-md-5">
                        <div class="section-heading-left">
                            {{-- <span>Tests</span> --}}
                            <h2>Services</h2>
                        </div>
                    </div>
                @endif
            @endisset
            @if (is_countable($portfolios_style1) && count($portfolios_style1) > 0)
                <div class="col-md-7">
                    <div class="portfolio-filter">
                        @foreach ($portfolio_count_categories as $portfolio_category)
                            <a href="#"
                                data-portfolio-filter=".{{ $portfolio_category->portfolio_category->portfolio_category_slug }}">{{ $portfolio_category->portfolio_category->category_name }}</a>
                        @endforeach
                    </div>
                </div>
            @else
                @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                    <div class="col-md-7">
                        <div class="portfolio-filter">
                            <a href="#" data-portfolio-filter="*" class="current">All</a>
                            <a href="#" data-portfolio-filter=".mockup">Mockup</a>
                            <a href="#" data-portfolio-filter=".ui">UI/UX</a>
                        </div>
                    </div>
                @endif
            @endif
        </div>
        @if (is_countable($portfolios_style1) && count($portfolios_style1) > 0)
            <div class="row g-4 portfolio-grid" id="portfolio-masonry-wrap">
                @foreach ($portfolios_style1 as $item)
                    <div class="col-md-6 col-lg-3 portfolio-item {{ $item->portfolio_category->portfolio_category_slug }}">
                        @if (Auth::user())
                            @can('portfolio view')
                                @php
                                    $url = request()->path();
                                    $modified_url = str_replace('/', '-bracket-', $url);
                                @endphp
                                <form method="POST" action="{{ route('site-url.index') }}" class="svc-admin-edit">
                                    @csrf
                                    <input type="hidden" name="route" value="portfolio.edit">
                                    <input type="hidden" name="single_id" value="{{ $item->id }}">
                                    <input type="hidden" name="site_url" value="{{ $modified_url }}">
                                    <button type="submit" class="svc-edit-btn" title="Edit service">
                                        <i class="fa fa-edit" aria-hidden="true"></i>
                                    </button>
                                </form>
                            @endcan
                        @endif

                        <article class="svc-test-card h-100" style="--svc-accent: {{ $siteMainColor }};">
                            <a href="{{ !empty($item->url) ? $item->url : route('default-portfolio-detail-show', ['portfolio_slug' => $item->portfolio_slug]) }}"
                               class="svc-test-link text-decoration-none">
                                @if (!empty($item->section_image))
                                    <div class="svc-test-img">
                                        <img src="{{ asset('uploads/img/portfolio/' . $item->section_image) }}"
                                             alt="{{ $item->title }}" loading="lazy">
                                    </div>
                                @else
                                    <div class="svc-test-img svc-test-img--placeholder" aria-hidden="true">
                                        <i class="fas fa-vial"></i>
                                    </div>
                                @endif

                                <div class="svc-test-body">
                                    <h6 class="svc-test-title">{{ $item->title }}</h6>
                                    <div class="svc-test-meta">
                                        @if (!empty($item->code))
                                            <span class="svc-test-code">
                                                <i class="fas fa-barcode" aria-hidden="true"></i>
                                                {{ $item->code }}
                                            </span>
                                        @endif
                                        @if (!empty($item->price))
                                            <span class="svc-test-price">${{ $item->price }}</span>
                                        @endif
                                    </div>
                                    <span class="svc-test-cta">
                                        View Details <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                    </span>
                                </div>
                            </a>
                        </article>
                    </div>
                @endforeach
                @unset ($item)
            </div>
        @else
            @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                <div class="row g-4 portfolio-grid" id="portfolio-masonry-wrap">
                    @php
                        $draftCards = [
                            ['class' => 'mockup', 'label' => 'Mockup', 'title' => 'Card Mockup'],
                            ['class' => 'mockup', 'label' => 'Mockup', 'title' => 'Mockup Box'],
                            ['class' => 'mockup', 'label' => 'Mockup', 'title' => 'Coffee Mockup'],
                            ['class' => 'mockup', 'label' => 'Mockup', 'title' => 'Square Box'],
                            ['class' => 'ui', 'label' => 'Ui Design', 'title' => 'Paper Design'],
                            ['class' => 'mockup', 'label' => 'Mockup', 'title' => 'Business Card'],
                        ];
                    @endphp
                    @foreach ($draftCards as $draft)
                        <div class="col-md-6 col-lg-4 portfolio-item {{ $draft['class'] }}">
                            <article class="svc-test-card h-100" style="--svc-accent: {{ $siteMainColor }};">
                                <a href="{{ asset('uploads/img/dummy/600x600.jpg') }}"
                                   class="svc-zoom-btn"
                                   target="_blank"
                                   rel="noopener"
                                   title="Preview image"
                                   onclick="event.stopPropagation();">
                                    <i class="fas fa-search" aria-hidden="true"></i>
                                </a>
                                <a href="#" class="svc-test-link text-decoration-none">
                                    <div class="svc-test-img">
                                        <img src="{{ asset('uploads/img/dummy/600x600.jpg') }}" alt="{{ $draft['title'] }}" loading="lazy">
                                    </div>
                                    <div class="svc-test-body">
                                        <h6 class="svc-test-title">{{ $draft['title'] }}</h6>
                                        <div class="svc-test-meta">
                                            <span class="svc-test-code">
                                                <i class="fas fa-tag" aria-hidden="true"></i>
                                                {{ $draft['label'] }}
                                            </span>
                                        </div>
                                        <span class="svc-test-cta">
                                            View Details <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                </a>
                            </article>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
        <!--<div class="row">-->
        <!--    @isset($portfolio_section_style1)-->
            <!--        <div class="col-md-12 text-center">-->
            <!--            <a href="{{ $portfolio_section_style1->button_url }}" class="primary-btn">-->
            <!--                <span class="text">{{ $portfolio_section_style1->button_name }}</span>-->
            <!--                <span class="icon"><i class="fa fa-arrow-right"></i></span>-->
            <!--            </a>-->
            <!--        </div>-->
        <!--    @else-->
            <!--        @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
    -->
            <!--            <div class="col-md-12 text-center">-->
            <!--                <a href="javascript:void(0)" class="primary-btn">-->
            <!--                    <span class="text">Get Started</span>-->
            <!--                    <span class="icon"><i class="fa fa-arrow-right"></i></span>-->
            <!--                </a>-->
            <!--            </div>-->
            <!--
    @endif-->
        <!--    @endisset-->
        <!--</div>-->

    </div>
</section>
<!--// My Works End //-->

@if (Auth::user())
    @can('portfolio view')
        </div>
        <div class="easier-middle">
            @php
                $url = request()->path();
                $modified_url = str_replace('/', '-bracket-', $url);
            @endphp
            <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                @csrf
                <input type="hidden" name="route" value="portfolio.index">
                <input type="hidden" name="style" value="style1">
                <input type="hidden" name="site_url" value="{{ $modified_url }}">
                <button type="submit" class="custom-btn text-white me-2 mb-2">
                    <i class="fa fa-edit text-white"></i> {{ __('content.edit_section_title_description') }}
                </button>
            </form>
            <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                @csrf
                <input type="hidden" name="route" value="portfolio.create">
                <input type="hidden" name="style" value="style1">
                <input type="hidden" name="site_url" value="{{ $modified_url }}">
                <button type="submit" class="custom-btn text-white me-2 mb-2">
                    <i class="fa fa-plus text-white"></i> {{ __('content.add_portfolio') }}
                </button>
            </form>
            <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                @csrf
                <input type="hidden" name="route" value="portfolio.index">
                <input type="hidden" name="style" value="style1">
                <input type="hidden" name="site_url" value="{{ $modified_url }}">
                <button type="submit" class="custom-btn text-white me-2 mb-2">
                    <i class="fas fa-briefcase text-white"></i> {{ __('content.portfolio') }}
                </button>
            </form>
        </div>
        </div>
    @endcan
@endif

<script>
(function ($) {
    $(window).on('load', function () {
        var $wrap = $('#porfolio #portfolio-masonry-wrap');
        if (!$wrap.length || typeof $.fn.isotope !== 'function') return;

        $wrap.imagesLoaded(function () {
            if ($wrap.data('isotope')) {
                $wrap.isotope('destroy');
            }
            $wrap.isotope({
                itemSelector: '.portfolio-item',
                layoutMode: 'fitRows',
                percentPosition: true
            });
        });
    });
})(window.jQuery);
</script>
