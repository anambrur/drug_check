@if(Auth::user())
    @can('blog view')
        <div class="easier-mode">
            <div class="easier-section-area">
    @endcan
@endif

<!--// Blog Section Start //-->
<section class="section bl-modern bl-modern--carousel pb-minus-76" id="blog" aria-labelledby="bl-home-heading">
    <div class="bl-modern-bg" aria-hidden="true"></div>
    <div class="container position-relative">
        @if(Auth::user())
            @can('blog view')
                <div class="click-icon d-md-none text-center mb-3">
                    <button class="custom-btn text-white" type="button">
                        <i class="fa fa-mobile-alt text-white" aria-hidden="true"></i> {{ __('content.touch') }}
                    </button>
                </div>
            @endcan
        @endif

        @isset ($blog_section_style1)
            <div class="bl-section-head bl-animate">
                <p class="bl-eyebrow">@php echo html_entity_decode($blog_section_style1->section_title); @endphp</p>
                <h2 class="bl-title" id="bl-home-heading">@php echo html_entity_decode($blog_section_style1->title); @endphp</h2>
            </div>
        @else
            @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                <div class="bl-section-head bl-animate">
                    <p class="bl-eyebrow">Blog</p>
                    <h2 class="bl-title" id="bl-home-heading">Our Blog</h2>
                </div>
            @endif
        @endisset

        @if (is_countable($blogs_style1) && count($blogs_style1) > 0)
            <div class="owl-carousel owl-theme bl-carousel" id="blogCarousel">
                @foreach ($blogs_style1 as $item)
                    <div class="item">
                        @if(Auth::user())
                            @can('blog view')
                                @php
                                    $url = request()->path();
                                    $modified_url = str_replace('/', '-bracket-', $url);
                                @endphp
                                <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block bl-admin-edit">
                                    @csrf
                                    <input type="hidden" name="route" value="blog.edit">
                                    <input type="hidden" name="single_id" value="{{ $item->id }}">
                                    <input type="hidden" name="site_url" value="{{ $modified_url }}">
                                    <button type="submit" class="me-2 custom-pure-button">
                                        <i class="fa fa-edit text-info easier-custom-font-size-24"></i>
                                    </button>
                                </form>
                            @endcan
                        @endif
                        <article class="bl-card blog-item h-100">
                            <div class="bl-card-media blog-img">
                                <a href="{{ route('default-blog-detail-show', ['slug' => $item->slug]) }}">
                                    @if (!empty($item->section_image))
                                        <img src="{{ asset('uploads/img/blog/thumbnail/'.$item->section_image) }}" alt="{{ $item->title }}" class="img-fluid">
                                    @else
                                        <img src="{{ asset('uploads/img/dummy/no-image.jpg') }}" alt="{{ $item->title }}" class="img-fluid">
                                    @endif
                                </a>
                                <span class="bl-card-badge">{{ $item->category_name }}</span>
                            </div>
                            <div class="bl-card-body blog-body">
                                <div class="bl-card-meta blog-meta">
                                    <span><i class="far fa-user" aria-hidden="true"></i>@if ($item->type == "with_this_account") {{ $item->author_name }} @else {{ __('frontend.anonymous') }} @endif</span>
                                </div>
                                <h5 class="bl-card-title">
                                    <a href="{{ route('default-blog-detail-show', ['slug' => $item->slug]) }}">{{ $item->title }}</a>
                                </h5>
                                @if (!empty($item->short_description))
                                    <p class="bl-card-excerpt">{{ $item->short_description }}</p>
                                @endif
                                <a href="{{ route('default-blog-detail-show', ['slug' => $item->slug]) }}" class="bl-card-link blog-link">
                                    {{ __('frontend.read_more') }}
                                    <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                </a>
                            </div>
                        </article>
                    </div>
                @endforeach
                @unset ($item)
            </div>
        @else
            @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                <div class="owl-carousel owl-theme bl-carousel" id="blogCarousel">
                    @foreach (['How To Create A Design Brief', 'Work On The Latest UI Design Models', 'The Golden Rule Between Unique Design', 'How to set up a Wordpress website ?'] as $dummyTitle)
                        <div class="item">
                            <article class="bl-card blog-item h-100">
                                <div class="bl-card-media blog-img">
                                    <a href="#"><img src="{{ asset('uploads/img/dummy/600x400.jpg') }}" alt="Blog image" class="img-fluid"></a>
                                    <span class="bl-card-badge">Design</span>
                                </div>
                                <div class="bl-card-body blog-body">
                                    <div class="bl-card-meta blog-meta">
                                        <span><i class="far fa-user"></i>By Admin</span>
                                    </div>
                                    <h5 class="bl-card-title"><a href="#">{{ $dummyTitle }}</a></h5>
                                    <p class="bl-card-excerpt">It is a long established fact that a reader will be distracted [..]</p>
                                    <a href="#" class="bl-card-link blog-link">Read More <i class="fa fa-arrow-right"></i></a>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif

        <div class="row mt-5">
            @isset ($blog_section_style1)
                <div class="col-md-12 text-center">
                    <a href="{{ $blog_section_style1->button_url }}" class="bl-cta-btn primary-btn">
                        <span class="text">{{ $blog_section_style1->button_name }}</span>
                        <span class="icon"><i class="fa fa-arrow-right"></i></span>
                    </a>
                </div>
            @else
                @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                    <div class="col-md-12 text-center">
                        <a href="javascript:void(0)" class="bl-cta-btn primary-btn">
                            <span class="text">Get Started</span>
                            <span class="icon"><i class="fa fa-arrow-right"></i></span>
                        </a>
                    </div>
                @endif
            @endisset
        </div>
    </div>
</section>
<!--// Blog Section End //-->

@if(Auth::user())
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

<script>
(function () {
    var items = document.querySelectorAll('#blog.bl-modern--carousel .bl-animate');
    if (!items.length) return;
    function reveal(el) { el.classList.add('bl-visible'); }
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) {
        items.forEach(reveal);
        return;
    }
    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                reveal(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08 });
    items.forEach(function (el) { observer.observe(el); });
})();
</script>
