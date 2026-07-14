@php
    $blTitle = $blog->title ?? 'Blog Post';
    $blCategory = $blog->category_name ?? null;
    $blAuthor = isset($blog) && $blog->type == 'with_this_account'
        ? ($blog->author_name ?? null)
        : (__('frontend.anonymous') ?? 'Anonymous');
    $blDate = isset($blog) ? \Carbon\Carbon::parse($blog->created_at)->isoFormat('DD MMMM YYYY') : null;
    $blHasImage = isset($blog) && $blog->image_status == 'show' && !empty($blog->section_image_2);
@endphp

<div class="bl-modern bl-modern--detail" id="blog-sidebar-page">

    {{-- Hero --}}
    <section class="bl-hero" aria-labelledby="bl-detail-title">
        <div class="bl-hero-bg" aria-hidden="true">
            <div class="bl-hero-orb bl-hero-orb--1"></div>
            <div class="bl-hero-orb bl-hero-orb--2"></div>
            <div class="bl-hero-grid"></div>
        </div>
        <div class="container position-relative">
            <div class="bl-hero-content text-center">
                <span class="bl-badge">
                    <i class="fas fa-newspaper" aria-hidden="true"></i>
                    {{ $blCategory ?: 'Blog' }}
                </span>
                <h1 class="bl-hero-title" id="bl-detail-title">{{ $blTitle }}</h1>
                <div class="bl-hero-meta">
                    @if ($blDate)
                        <span>
                            <i class="far fa-calendar-alt" aria-hidden="true"></i>
                            {{ $blDate }}
                        </span>
                    @endif
                    @if ($blAuthor)
                        <span>
                            <i class="far fa-user" aria-hidden="true"></i>
                            {{ $blAuthor }}
                        </span>
                    @endif
                    <a href="{{ url('/') }}" class="text-decoration-none">
                        <i class="fas fa-home" aria-hidden="true"></i>
                        {{ __('frontend.home') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Content + Sidebar --}}
    <section class="bl-detail-body section">
        <div class="container">
            <div class="row g-4 g-xl-5">

                {{-- Main article --}}
                <div class="col-lg-8">
                    @if (Auth::user())
                        @can('blog view')
                            <div class="easier-mode">
                                <div class="click-icon d-md-none text-center mb-3">
                                    <button class="custom-btn text-white" type="button">
                                        <i class="fa fa-mobile-alt text-white" aria-hidden="true"></i>
                                        {{ __('content.touch') }}
                                    </button>
                                </div>
                                <div class="easier-section-area">
                        @endcan
                    @endif

                    @isset($blog)
                        <article class="bl-article blog-post-single">
                            @if ($blHasImage)
                                <div class="bl-article-media blog-post-img">
                                    <img src="{{ asset('uploads/img/blog/' . $blog->section_image_2) }}"
                                         alt="{{ $blog->title }}"
                                         class="img-fluid">
                                </div>
                            @endif
                            <div class="bl-article-body blog-text custom-blog-img">
                                <div class="author-meta bl-article-meta d-flex flex-wrap gap-3 mb-3">
                                    <span>
                                        <i class="far fa-calendar-alt" aria-hidden="true"></i>
                                        {{ $blDate }}
                                    </span>
                                    <span>
                                        <i class="far fa-bookmark" aria-hidden="true"></i>
                                        {{ $blog->category_name }}
                                    </span>
                                </div>
                                <div class="bl-prose dst-prose">
                                    {!! html_entity_decode($blog->description) !!}
                                </div>
                            </div>
                        </article>
                    @else
                        @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                            <article class="bl-article blog-post-single">
                                <div class="bl-article-media blog-post-img">
                                    <img src="{{ asset('uploads/img/dummy/800x600.jpg') }}"
                                         alt="Blog Post Image"
                                         class="img-fluid">
                                </div>
                                <div class="bl-article-body blog-text">
                                    <h4>Creating projects in Laravel 11</h4>
                                    <div class="author-meta bl-article-meta mb-3">
                                        <a href="#"><span class="far fa-user"></span>By Admin</a>
                                        <a href="#"><span class="far fa-calendar-alt"></span>17 August 2024</a>
                                    </div>
                                    <div class="bl-prose">
                                        <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.</p>
                                    </div>
                                </div>
                            </article>
                        @endif
                    @endisset

                    @if (Auth::user())
                        @can('blog view')
                                </div>{{-- .easier-section-area --}}
                                <div class="easier-middle">
                                    @php
                                        $url = request()->path();
                                        $modified_url = str_replace('/', '-bracket-', $url);
                                    @endphp
                                    <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                                        @csrf
                                        <input type="hidden" name="route" value="blog.edit">
                                        <input type="hidden" name="style" value="{{ $blog->id ?? '' }}">
                                        <input type="hidden" name="site_url" value="{{ $modified_url }}">
                                        <button type="submit" class="custom-btn text-white me-2">
                                            <i class="fa fa-edit text-white"></i>
                                            {{ __('content.edit_blog') }}
                                        </button>
                                    </form>
                                </div>
                            </div>{{-- .easier-mode --}}
                        @endcan
                    @endif
                </div>{{-- .col-lg-8 --}}

                {{-- Sidebar --}}
                <aside class="col-lg-4">
                    <div class="bl-sidebar">

                        {{-- Search --}}
                        <div class="bl-widget">
                            <h5 class="bl-widget-title">{{ __('frontend.search') }}</h5>
                            <form action="{{ route('default-blog-search-index') }}" method="POST">
                                @csrf
                                <div class="bl-search">
                                    <input type="search"
                                           name="search"
                                           placeholder="{{ __('frontend.type_to_search') }}"
                                           class="bl-search-input"
                                           required>
                                    <button type="submit" class="bl-search-btn" aria-label="Search">
                                        <i class="fas fa-search" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        @if (Auth::user())
                            @can('blog view')
                                <div class="easier-mode">
                                    <div class="click-icon d-md-none text-center mb-2">
                                        <button class="custom-btn text-white" type="button">
                                            <i class="fa fa-mobile-alt text-white" aria-hidden="true"></i>
                                            {{ __('content.touch') }}
                                        </button>
                                    </div>
                                    <div class="easier-section-area">
                            @endcan
                        @endif

                        {{-- Categories --}}
                        @if (is_countable($blog_count_categories) && count($blog_count_categories) > 0)
                            <div class="bl-widget">
                                <h5 class="bl-widget-title">{{ __('content.categories') }}</h5>
                                <ul class="bl-cat-list">
                                    @foreach ($blog_count_categories as $blog_count_category)
                                        @if (isset($blog_count_category->category->category_slug))
                                            <li @class([
                                                'active' => isset($blog) && $blog_count_category->category->category_name == $blog->category_name,
                                            ])>
                                                <a href="{{ route('default-blog-category-index', $blog_count_category->category->category_slug) }}">
                                                    <span class="bl-cat-name">{{ $blog_count_category->category->category_name }}</span>
                                                    <span class="bl-cat-count">{{ $blog_count_category->category_count }}</span>
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (Auth::user())
                            @can('blog view')
                                    </div>{{-- .easier-section-area --}}
                                    <div class="easier-middle">
                                        @php
                                            $url = request()->path();
                                            $modified_url = str_replace('/', '-bracket-', $url);
                                        @endphp
                                        <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                                            @csrf
                                            <input type="hidden" name="route" value="category.create">
                                            <input type="hidden" name="style" value="">
                                            <input type="hidden" name="site_url" value="{{ $modified_url }}">
                                            <button type="submit" class="custom-btn text-white me-2">
                                                <i class="fa fa-edit text-white"></i>
                                                {{ __('content.add_category') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>{{-- .easier-mode --}}
                            @endcan
                        @endif

                        {{-- Recent posts --}}
                        @if (is_countable($recent_posts) && count($recent_posts) > 0)
                            <div class="bl-widget">
                                <h5 class="bl-widget-title">{{ __('frontend.recent_blogs') }}</h5>
                                @foreach ($recent_posts as $item)
                                    <div class="bl-recent">
                                        <div class="bl-recent-img">
                                            <a href="{{ route('default-blog-detail-show', ['slug' => $item->slug]) }}">
                                                @if (!empty($item->section_image))
                                                    <img src="{{ asset('uploads/img/blog/thumbnail/' . $item->section_image) }}"
                                                         alt="{{ $item->title }}">
                                                @else
                                                    <img src="{{ asset('uploads/img/dummy/no-image.jpg') }}"
                                                         alt="{{ $item->title }}">
                                                @endif
                                            </a>
                                        </div>
                                        <div class="bl-recent-body">
                                            <a href="{{ route('default-blog-detail-show', ['slug' => $item->slug]) }}">
                                                <h6 class="bl-recent-title">{{ $item->title }}</h6>
                                            </a>
                                            <p class="bl-recent-date">
                                                <i class="far fa-calendar-alt" aria-hidden="true"></i>
                                                {{ \Carbon\Carbon::parse($item->created_at)->isoFormat('DD MMMM YYYY') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                                @unset($item)
                            </div>
                        @endif

                        {{-- Share --}}
                        <div class="bl-widget">
                            <h5 class="bl-widget-title">{{ __('frontend.copy_link_and_share') }}</h5>
                            <div class="bl-share">
                                <div id="hiddenURLDiv" style="display: none;"></div>
                                <a href="#"
                                   class="bl-share-btn"
                                   onclick="copyPageURL(); return false;"
                                   aria-label="Copy link"
                                   title="Copy link">
                                    <i class="fas fa-link" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>

                        {{-- Tags --}}
                        @if (!empty($blog->tag))
                            @php
                                $array_tags = explode(',', $blog->tag);
                            @endphp
                            <div class="bl-widget">
                                <h5 class="bl-widget-title">{{ __('frontend.tags') }}</h5>
                                <ul class="bl-tags">
                                    @foreach ($array_tags as $tag)
                                        @if (!empty($blog_tag_index->page_uri))
                                            <li>
                                                <a href="{{ url($blog_tag_index->page_uri . '/' . $tag) }}">{{ $tag }}</a>
                                            </li>
                                        @else
                                            <li>
                                                <a href="{{ route('default-blog-tag-index', $tag) }}">{{ $tag }}</a>
                                            </li>
                                        @endif
                                    @endforeach
                                    @unset($tag)
                                </ul>
                            </div>
                        @endif

                    </div>{{-- .bl-sidebar --}}
                </aside>

            </div>{{-- .row --}}
        </div>{{-- .container --}}
    </section>

</div>{{-- #blog-sidebar-page --}}
