@if (Auth::user())
    @can('section view')
        <div class="easier-mode">
            <div class="easier-section-area">
    @endcan
@endif

@if (is_countable($sponsors_style1) && count($sponsors_style1) > 0)
    <!--// Partners Section Start //-->
    <section class="partners-section section sp-modern" aria-label="Partners and sponsors">
        <div class="sp-modern-bg" aria-hidden="true"></div>
        <div class="container position-relative">
            @if (Auth::user())
                @can('section view')
                    <div class="click-icon d-md-none text-center">
                        <button class="custom-btn text-white" type="button">
                            <i class="fa fa-mobile-alt text-white" aria-hidden="true"></i> {{ __('content.touch') }}
                        </button>
                    </div>
                @endcan
            @endif

            <div class="sp-section-head text-center wp-animate">
                <p class="sp-eyebrow">Trusted By</p>
                <h2 class="sp-title">Our Partners &amp; Sponsors</h2>
            </div>

            <div class="sp-carousel-wrap wp-animate" style="--wp-delay: .1s;">
                <div class="owl-carousel owl-theme sp-carousel" id="partners-carousel">
                    @foreach ($sponsors_style1 as $item)
                        <div class="item">
                            @if (Auth::user())
                                @can('section view')
                                    @php
                                        $url = request()->path();
                                        $modified_url = str_replace('/', '-bracket-', $url);
                                    @endphp
                                    <form method="POST" action="{{ route('site-url.index') }}" class="sp-admin-edit d-inline-block">
                                        @csrf
                                        <input type="hidden" name="route" value="sponsor.edit">
                                        <input type="hidden" name="single_id" value="{{ $item->id }}">
                                        <input type="hidden" name="site_url" value="{{ $modified_url }}">
                                        <button type="submit" class="custom-pure-button" aria-label="Edit sponsor">
                                            <i class="fa fa-edit text-info easier-custom-font-size-24"></i>
                                        </button>
                                    </form>
                                @endcan
                            @endif
                            <div class="partner-item sp-partner-card">
                                <a href="{{ $item->url }}" class="sp-partner-link">
                                    @if (!empty($item->section_image))
                                        <img src="{{ asset('uploads/img/sponsor/' . $item->section_image) }}"
                                            alt="sponsor image" class="img-fluid sp-partner-logo">
                                    @endif
                                </a>
                            </div>
                        </div>
                    @endforeach
                    @unset ($item)
                </div>
            </div>
        </div>
    </section>
    <!--// Partners Section End  //-->
@else
    @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
        <!--// Partners Section Start //-->
        <section class="partners-section section sp-modern" aria-label="Partners and sponsors">
            <div class="sp-modern-bg" aria-hidden="true"></div>
            <div class="container position-relative">
                @if (Auth::user())
                    @can('section view')
                        <div class="click-icon d-md-none text-center">
                            <button class="custom-btn text-white" type="button">
                                <i class="fa fa-mobile-alt text-white" aria-hidden="true"></i> {{ __('content.touch') }}
                            </button>
                        </div>
                    @endcan
                @endif

                <div class="sp-section-head text-center wp-animate">
                    <p class="sp-eyebrow">Trusted By</p>
                    <h2 class="sp-title">Our Partners &amp; Sponsors</h2>
                </div>

                <div class="sp-carousel-wrap wp-animate" style="--wp-delay: .1s;">
                    <div class="owl-carousel owl-theme sp-carousel" id="partners-carousel">
                        @for ($i = 0; $i < 5; $i++)
                            <div class="item">
                                <div class="partner-item sp-partner-card">
                                    <a href="#" class="sp-partner-link">
                                        <img src="{{ asset('uploads/img/dummy/170x75.jpg') }}" alt="sponsor image"
                                            class="img-fluid sp-partner-logo">
                                    </a>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </section>
        <!--// Partners Section End  //-->
    @endif
@endif

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
                <input type="hidden" name="route" value="sponsor.create">
                <input type="hidden" name="style" value="style1">
                <input type="hidden" name="site_url" value="{{ $modified_url }}">
                <button type="submit" class="custom-btn text-white">
                    <i class="fa fa-plus text-white"></i> {{ __('content.add_sponsor') }}
                </button>
            </form>
        </div>
        </div>
    @endcan
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var spSection = document.querySelector('.sp-modern');
        if (spSection) {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) {
                spSection.querySelectorAll('.wp-animate').forEach(function (el) {
                    el.classList.add('wp-visible');
                });
            } else {
                var revealObserver = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('wp-visible');
                            revealObserver.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.15 });
                spSection.querySelectorAll('.wp-animate').forEach(function (el) {
                    revealObserver.observe(el);
                });
            }
        }

        class AutoSlider {
            constructor(containerId, options = {}) {
                this.container = document.getElementById(containerId);
                if (!this.container) return;

                this.speed = options.speed || 3000;
                this.animationSpeed = options.animationSpeed || 800;
                this.itemsToShow = options.itemsToShow || {
                    desktop: 6,
                    tablet: 4,
                    mobile: 2
                };

                this.currentPosition = 0;
                this.isAnimating = false;
                this.autoPlayInterval = null;

                this.init();
            }

            init() {
                this.track = this.container.querySelector('.owl-stage');
                if (!this.track) {
                    this.track = document.createElement('div');
                    this.track.className = 'owl-stage';

                    const items = Array.from(this.container.children);
                    items.forEach(item => {
                        this.track.appendChild(item.cloneNode(true));
                    });

                    this.container.innerHTML = '';
                    this.container.appendChild(this.track);
                }

                this.cloneItems();
                this.setupStyles();

                window.addEventListener('resize', () => this.updateVisibleItems());
                this.updateVisibleItems();

                this.startAutoPlay();

                this.container.addEventListener('mouseenter', () => this.pauseAutoPlay());
                this.container.addEventListener('mouseleave', () => this.startAutoPlay());
            }

            cloneItems() {
                const items = Array.from(this.track.children);
                const totalItems = items.length;

                for (let i = 0; i < totalItems; i++) {
                    const clone = items[i].cloneNode(true);
                    this.track.appendChild(clone);
                }
            }

            setupStyles() {
                this.container.style.overflow = 'hidden';
                this.container.style.position = 'relative';

                this.track.style.display = 'flex';
                this.track.style.transition = `transform ${this.animationSpeed}ms ease-in-out`;

                const items = this.track.children;
                Array.from(items).forEach(item => {
                    item.style.flex = '0 0 auto';
                    item.style.margin = '0 15px';
                });
            }

            updateVisibleItems() {
                const containerWidth = this.container.clientWidth;
                const itemsToShow = this.getItemsToShow();

                this.itemWidth = containerWidth / itemsToShow;

                const items = this.track.children;
                Array.from(items).forEach(item => {
                    item.style.width = `${this.itemWidth - 30}px`;
                });
            }

            getItemWidth() {
                const items = this.track.children;
                if (items.length > 0) {
                    return items[0].clientWidth;
                }
                return 200;
            }

            getItemsToShow() {
                const width = window.innerWidth;
                if (width >= 1200) return this.itemsToShow.desktop;
                if (width >= 768) return this.itemsToShow.tablet;
                return this.itemsToShow.mobile;
            }

            slideRightToLeft() {
                if (this.isAnimating) return;

                this.isAnimating = true;

                const items = this.track.children;
                const itemWidth = this.getItemWidth() + 30;
                const totalItems = items.length;

                this.currentPosition -= itemWidth;

                this.track.style.transform = `translateX(${this.currentPosition}px)`;

                setTimeout(() => {
                    if (Math.abs(this.currentPosition) >= (itemWidth * (totalItems / 2))) {
                        this.track.style.transition = 'none';
                        this.currentPosition = 0;
                        this.track.style.transform = `translateX(0px)`;

                        this.track.offsetHeight;
                        this.track.style.transition =
                            `transform ${this.animationSpeed}ms ease-in-out`;
                    }
                    this.isAnimating = false;
                }, this.animationSpeed);
            }

            startAutoPlay() {
                if (this.autoPlayInterval) {
                    clearInterval(this.autoPlayInterval);
                }
                this.autoPlayInterval = setInterval(() => {
                    this.slideRightToLeft();
                }, this.speed);
            }

            pauseAutoPlay() {
                if (this.autoPlayInterval) {
                    clearInterval(this.autoPlayInterval);
                    this.autoPlayInterval = null;
                }
            }
        }

        const carousel = new AutoSlider('partners-carousel', {
            speed: 1000,
            animationSpeed: 800,
            itemsToShow: {
                desktop: 6,
                tablet: 4,
                mobile: 2
            }
        });
    });
</script>
