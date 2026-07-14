@extends('layouts.frontend.master2')

@section('content')
    @if (Auth::user())
        @can('blog view')
            <div class="easier-mode">
                <div class="easier-section-area">
        @endcan
    @endif

    @php
        $siteMainColor = (isset($color_option) && $color_option->color_option != 0)
            ? $color_option->main_color
            : '#ff4500';
        $hasItems = is_countable($blogs_paginate_style) && count($blogs_paginate_style) > 0;
        $totalPosts = $hasItems ? $blogs_paginate_style->total() : 0;
        $categoryName = $category->category_name ?? __('frontend.all_blogs');
    @endphp

    <div class="svc-page ch-page" id="blog">
        <div class="rc-scroll-progress" id="rc-scroll-progress" aria-hidden="true"><span></span></div>

        <section class="rc-hero rc-hero--blog">
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
                        <i class="fas fa-bookmark" aria-hidden="true"></i>
                        Category
                        <span class="rc-badge-sep" aria-hidden="true"></span>
                        {{ $totalPosts }} {{ \Illuminate\Support\Str::plural('Article', $totalPosts) }}
                    </span>
                    <h1 class="rc-hero-title rc-hero-item rc-hero-item--2" id="bl-cat-heading">{{ $categoryName }}</h1>

                    @if (is_countable($blog_count_categories) && count($blog_count_categories) > 0)
                        <nav class="rc-cat-bar rc-hero-item rc-hero-item--3" aria-label="Blog categories">
                            @if (!empty($blog_index?->page_uri))
                                <a href="{{ url($blog_index->page_uri) }}" class="rc-cat-chip">{{ __('frontend.all') }}</a>
                            @endif
                            @foreach ($blog_count_categories as $blog_count_category)
                                @if (isset($blog_count_category->category->category_slug))
                                    <a class="rc-cat-chip @if ($categoryName == $blog_count_category->category->category_name) is-active @endif"
                                       href="{{ route('default-blog-category-index', $blog_count_category->category->category_slug) }}">
                                        {{ $blog_count_category->category->category_name }}
                                        <span class="rc-cat-count">{{ $blog_count_category->category_count }}</span>
                                    </a>
                                @endif
                            @endforeach
                            @unset($blog_count_category)
                        </nav>
                    @endif
                </div>
            </div>
        </section>

        <section class="plan-section svc-section" id="svc-catalog">
            <div class="container">
                @if (Auth::user())
                    @can('blog view')
                        <div class="svc-admin-touch d-md-none text-center rc-animate">
                            <button type="button" class="svc-touch-btn">
                                <i class="fa fa-mobile-alt" aria-hidden="true"></i> {{ __('content.touch') }}
                            </button>
                        </div>
                    @endcan
                @endif

                @if ($hasItems)
                    <div class="row g-4">
                        @foreach ($blogs_paginate_style as $item)
                            <div class="col-md-6 col-lg-4 portfolio-item rc-animate" style="--rc-delay: {{ ($loop->index % 9) * 0.06 }}s;">
                                @if (Auth::user())
                                    @can('blog view')
                                        @php
                                            $url = request()->path();
                                            $modified_url = str_replace('/', '-bracket-', $url);
                                        @endphp
                                        <form method="POST" action="{{ route('site-url.index') }}" class="svc-admin-edit">
                                            @csrf
                                            <input type="hidden" name="route" value="blog.edit">
                                            <input type="hidden" name="single_id" value="{{ $item->id }}">
                                            <input type="hidden" name="site_url" value="{{ $modified_url }}">
                                            <button type="submit" class="svc-edit-btn" title="Edit blog">
                                                <i class="fa fa-edit" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    @endcan
                                @endif

                                <article class="svc-test-card h-100" style="--svc-accent: {{ $siteMainColor }};">
                                    <a href="{{ route('default-blog-detail-show', ['slug' => $item->slug]) }}"
                                       class="svc-test-link text-decoration-none">
                                        @if (!empty($item->section_image))
                                            <div class="svc-test-img">
                                                <img src="{{ asset('uploads/img/blog/thumbnail/' . $item->section_image) }}"
                                                     alt="{{ $item->title }}"
                                                     loading="lazy">
                                            </div>
                                        @else
                                            <div class="svc-test-img svc-test-img--placeholder" aria-hidden="true">
                                                <i class="fas fa-newspaper"></i>
                                            </div>
                                        @endif

                                        <div class="svc-test-body">
                                            <h6 class="svc-test-title">{{ $item->title }}</h6>
                                            <div class="svc-test-meta">
                                                <span class="svc-test-code">
                                                    <i class="far fa-bookmark" aria-hidden="true"></i>
                                                    {{ $item->category_name }}
                                                </span>
                                                <span class="svc-test-code">
                                                    <i class="far fa-user" aria-hidden="true"></i>
                                                    @if ($item->type == 'with_this_account')
                                                        {{ $item->author_name }}
                                                    @else
                                                        {{ __('frontend.anonymous') }}
                                                    @endif
                                                </span>
                                            </div>
                                            @if (!empty($item->short_description))
                                                <p class="bl-svc-excerpt">{{ \Illuminate\Support\Str::limit($item->short_description, 90) }}</p>
                                            @endif
                                            <span class="svc-test-cta">
                                                {{ __('frontend.read_more') }} <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                            </span>
                                        </div>
                                    </a>
                                </article>
                            </div>
                        @endforeach
                        @unset($item)
                    </div>

                    <div class="row">
                        <div class="d-flex justify-content-center mt-4 mt-lg-5 svc-pagination rc-animate">
                            {{ $blogs_paginate_style->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                @else
                    <div class="pf-card svc-empty-card text-center rc-animate">
                        <div class="pf-body py-5">
                            <div class="dst-empty-icon mx-auto mb-3">
                                <i class="fas fa-bookmark" aria-hidden="true"></i>
                            </div>
                            <h3 class="dst-empty-title">{{ __('frontend.nothing_found') }}</h3>
                            <p class="ch-text-muted mb-3">No articles found in this category.</p>
                            @if (!empty($blog_index?->page_uri))
                                <a href="{{ url($blog_index->page_uri) }}" class="svc-test-cta d-inline-flex">
                                    {{ __('frontend.all_blogs') }} <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </div>

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
                        <input type="hidden" name="route" value="blog.create">
                        <input type="hidden" name="style" value="">
                        <input type="hidden" name="site_url" value="{{ $modified_url }}">
                        <button type="submit" class="custom-btn text-white me-2 mb-2">
                            <i class="fa fa-plus text-white"></i> {{ __('content.add_blog') }}
                        </button>
                    </form>
                </div>
            </div>
        @endcan
    @endif

    <script>
    (function () {
        document.querySelectorAll('.breadcrumb-section').forEach(function (el) {
            el.style.display = 'none';
        });

        function initScrollAnimations() {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) {
                document.querySelectorAll('#blog.svc-page .rc-animate').forEach(function (el) {
                    el.classList.add('rc-visible');
                });
                return;
            }
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('rc-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
            document.querySelectorAll('#blog.svc-page .rc-animate').forEach(function (el) {
                observer.observe(el);
            });
        }

        function initScrollProgress() {
            var bar = document.querySelector('#rc-scroll-progress span');
            if (!bar) return;
            window.addEventListener('scroll', function () {
                var doc = document.documentElement;
                var pct = (doc.scrollTop / (doc.scrollHeight - doc.clientHeight)) * 100;
                bar.style.width = Math.min(100, Math.max(0, pct)) + '%';
            }, { passive: true });
        }

        document.addEventListener('DOMContentLoaded', function () {
            initScrollAnimations();
            initScrollProgress();
        });
    })();
    </script>
@endsection
