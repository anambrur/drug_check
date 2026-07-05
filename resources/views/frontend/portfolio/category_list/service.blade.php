@extends('layouts.frontend.master2')

@section('content')

    @php
        $siteMainColor =
            isset($color_option) && $color_option->color_option != 0 ? $color_option->main_color : '#ff4500';
        $hasItems = is_countable($portfolios_style1) && count($portfolios_style1) > 0;
        $categoryName = $hasItems ? $portfolios_style1[0]->portfolio_category->category_name : 'Testing Services';
        $isDotTesting = request()->routeIs('frontend.dot-testing');
        $svcIntro = $isDotTesting
            ? 'Explore our complete lineup of DOT-compliant drug and alcohol tests. Each service includes test codes and transparent pricing — select a test to view details and order.'
            : 'Browse workplace drug testing services outside DOT regulations. Clear test codes and upfront pricing on every option.';
        $totalTests = $hasItems ? $portfolios_style1->total() : 0;
    @endphp

    <div class="svc-page ch-page" id="porfolio">
        {{-- Scroll progress --}}
        <div class="rc-scroll-progress" id="rc-scroll-progress" aria-hidden="true"><span></span></div>

        {{-- Hero --}}
        <section class="rc-hero">
            <div class="rc-hero-bg" aria-hidden="true">
                <div class="rc-hero-orb rc-hero-orb--1"></div>
                <div class="rc-hero-orb rc-hero-orb--2"></div>
                <div class="rc-hero-grid"></div>
                <div class="rc-particles">
                    <span></span><span></span><span></span><span></span><span></span><span></span>
                </div>
            </div>
            <div class="container position-relative">
                <div class="rc-hero-content text-center">
                    <span class="rc-badge rc-hero-item rc-hero-item--1">
                        <i class="fas {{ $isDotTesting ? 'fa-truck' : 'fa-flask' }}" aria-hidden="true"></i>
                        {{ $isDotTesting ? 'DOT Compliance' : 'Workplace Testing' }}
                    </span>
                    @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                        <h1 class="rc-hero-title rc-hero-item rc-hero-item--2">{{ $categoryName }}</h1>
                    @endif
                    <div class="rc-hero-desc rc-hero-item rc-hero-item--3">{{ $svcIntro }}</div>

                    @if ($hasItems)
                        <div class="svc-hero-stats rc-hero-item rc-hero-item--4">
                            <span class="svc-stat-pill">
                                <i class="fas fa-vial" aria-hidden="true"></i>
                                {{ $totalTests }} {{ Str::plural('Test', $totalTests) }} Available
                            </span>
                            <span class="svc-stat-pill">
                                <i class="fas fa-tag" aria-hidden="true"></i>
                                Transparent Pricing
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- Test catalog --}}
        <section class="plan-section svc-section" id="svc-catalog">
            <div class="container">
                @if (Auth::user())
                    @can('portfolio view')
                        <div class="svc-admin-touch d-md-none text-center rc-animate">
                            <button type="button" class="svc-touch-btn">
                                <i class="fa fa-mobile-alt" aria-hidden="true"></i> {{ __('content.touch') }}
                            </button>
                        </div>
                    @endcan
                @endif

                @if ($hasItems)
                    <div class="rc-section-head text-center rc-animate mb-4 mb-lg-5">
                        <p class="section-eyebrow">Test Catalog</p>
                        <h2>{{ $categoryName }}</h2>
                        <p class="sub">Select a test below to view full details, specifications, and ordering options.
                        </p>
                    </div>

                    <div class="row g-4 portfolio-grid" id="portfolio-masonry-wrap">
                        @foreach ($portfolios_style1 as $item)
                            <div class="col-md-6 col-lg-4 col-xl-3 portfolio-item {{ $item->portfolio_category->portfolio_category_slug }} rc-animate"
                                style="--rc-delay: {{ ($loop->index % 12) * 0.06 }}s;">
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
                                            <button type="submit" class="svc-edit-btn" title="Edit test">
                                                <i class="fa fa-edit" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    @endcan
                                @endif

                                <article class="svc-test-card h-100" style="--svc-accent: {{ $siteMainColor }};">
                                    @if (empty($item->section_image))
                                        <a href="{{ asset('uploads/img/dummy/600x600.jpg') }}" class="svc-zoom-btn"
                                            target="_blank" rel="noopener" title="Preview image"
                                            onclick="event.stopPropagation();">
                                            <i class="fas fa-search" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                    <a href="{{ !empty($item->url) ? $item->url : route('default-portfolio-detail-show', ['portfolio_slug' => $item->portfolio_slug]) }}"
                                        class="svc-test-link text-decoration-none">
                                        <div class="svc-test-img">
                                            @if (!empty($item->section_image))
                                                <img src="{{ asset('uploads/img/portfolio/' . $item->section_image) }}"
                                                    alt="{{ $item->title }}" loading="lazy">
                                            @else
                                                <img src="{{ asset('uploads/img/dummy/600x600.jpg') }}"
                                                    alt="{{ $item->title }}" loading="lazy">
                                            @endif
                                        </div>

                                        <div class="svc-test-body">
                                            <h6 class="svc-test-title">{{ $item->title }}</h6>
                                            <div class="svc-test-meta">
                                                @if (!empty($item->test_code))
                                                    <span class="svc-test-code">
                                                        <i class="fas fa-barcode" aria-hidden="true"></i>
                                                        {{ $item->test_code }}
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

                    <div class="row">
                        <div class="d-flex justify-content-center mt-4 mt-lg-5 svc-pagination rc-animate">
                            {{ $portfolios_style1->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                @else
                    <div class="pf-card svc-empty-card text-center rc-animate">
                        <div class="pf-body py-5">
                            <div class="dst-empty-icon mx-auto mb-3">
                                <i class="fas fa-vial" aria-hidden="true"></i>
                            </div>
                            <h3 class="dst-empty-title">No Tests Available</h3>
                            <p class="ch-text-muted mb-0">Tests for this category will appear here once published.</p>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        {{-- Trust strip --}}
        <div class="dst-trust-strip svc-trust-strip">
            <div class="container">
                <div class="dst-trust-inner">
                    <span><i class="fas fa-shield-alt" aria-hidden="true"></i> Certified Labs</span>
                    <span><i class="fas fa-clock" aria-hidden="true"></i> Fast Turnaround</span>
                    <span><i class="fas fa-lock" aria-hidden="true"></i> Confidential Results</span>
                    <span><i class="fas fa-headset" aria-hidden="true"></i> Expert Support</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            function initScrollAnimations() {
                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    document.querySelectorAll('.svc-page .rc-animate').forEach(function(el) {
                        el.classList.add('rc-visible');
                    });
                    return;
                }
                if (!('IntersectionObserver' in window)) {
                    document.querySelectorAll('.svc-page .rc-animate').forEach(function(el) {
                        el.classList.add('rc-visible');
                    });
                    return;
                }
                var observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('rc-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -40px 0px'
                });
                document.querySelectorAll('.svc-page .rc-animate').forEach(function(el) {
                    observer.observe(el);
                });
            }

            function initScrollProgress() {
                var bar = document.querySelector('#rc-scroll-progress span');
                if (!bar) return;
                window.addEventListener('scroll', function() {
                    var doc = document.documentElement;
                    var pct = (doc.scrollTop / (doc.scrollHeight - doc.clientHeight)) * 100;
                    bar.style.width = Math.min(100, Math.max(0, pct)) + '%';
                }, {
                    passive: true
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                initScrollAnimations();
                initScrollProgress();
            });
        })();
    </script>

@endsection
