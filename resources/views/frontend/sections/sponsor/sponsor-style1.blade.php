@if (Auth::user())
    @can('section view')
        <div class="easier-mode">
            <div class="easier-section-area">
            @endcan
@endif

@if (is_countable($sponsors_style1) && count($sponsors_style1) > 0)
    <!--// Partners Section Start //-->
    <div class="partners-section section">
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
            <div class="owl-carousel owl-theme" id="partners-carousel">
                @foreach ($sponsors_style1 as $item)
                    <div class="item">
                        @if (Auth::user())
                            @can('section view')
                                @php
                                    $url = request()->path();
                                    $modified_url = str_replace('/', '-bracket-', $url);
                                @endphp
                                <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                                    @csrf
                                    <input type="hidden" name="route" value="sponsor.edit">
                                    <input type="hidden" name="single_id" value="{{ $item->id }}">
                                    <input type="hidden" name="site_url" value="{{ $modified_url }}">
                                    <button type="submit" class="me-2 custom-pure-button ">
                                        <i class="fa fa-edit text-info easier-custom-font-size-24"></i>
                                    </button>
                                </form>
                            @endcan
                        @endif
                        <div class="partner-item">
                            <a href="{{ $item->url }}">
                                @if (!empty($item->section_image))
                                    <img src="{{ asset('uploads/img/sponsor/' . $item->section_image) }}"
                                        alt="sponsor image" class="img-fluid">
                                @endif
                            </a>
                        </div>
                    </div>
                @endforeach
                @unset ($item)
            </div>
        </div>
    </div>
    <!--// Partners Section End  //-->
@else
    @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
        <!--// Partners Section Start //-->
        <div class="partners-section section">
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
                <div class="owl-carousel owl-theme" id="partners-carousel">
                    <div class="item">
                        <div class="partner-item">
                            <a href="#">
                                <img src="{{ asset('uploads/img/dummy/170x75.jpg') }}" alt="sponsor image"
                                    class="img-fluid">
                            </a>
                        </div>
                    </div>
                    <div class="item">
                        <div class="partner-item">
                            <a href="#">
                                <img src="{{ asset('uploads/img/dummy/170x75.jpg') }}" alt="sponsor image"
                                    class="img-fluid">
                            </a>
                        </div>
                    </div>
                    <div class="item">
                        <div class="partner-item">
                            <a href="#">
                                <img src="{{ asset('uploads/img/dummy/170x75.jpg') }}" alt="sponsor image"
                                    class="img-fluid">
                            </a>
                        </div>
                    </div>
                    <div class="item">
                        <div class="partner-item">
                            <a href="#">
                                <img src="{{ asset('uploads/img/dummy/170x75.jpg') }}" alt="sponsor image"
                                    class="img-fluid">
                            </a>
                        </div>
                    </div>
                    <div class="item">
                        <div class="partner-item">
                            <a href="#">
                                <img src="{{ asset('uploads/img/dummy/170x75.jpg') }}" alt="sponsor image"
                                    class="img-fluid">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
        // Pure vanilla JavaScript carousel
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
                // Wrap all items in a track
                this.track = this.container.querySelector('.owl-stage');
                if (!this.track) {
                    // Create track if it doesn't exist
                    this.track = document.createElement('div');
                    this.track.className = 'owl-stage';

                    const items = Array.from(this.container.children);
                    items.forEach(item => {
                        this.track.appendChild(item.cloneNode(true));
                    });

                    // Clear container and add track
                    this.container.innerHTML = '';
                    this.container.appendChild(this.track);
                }

                // Clone items for infinite effect
                this.cloneItems();

                // Set up styles
                this.setupStyles();

                // Handle window resize
                window.addEventListener('resize', () => this.updateVisibleItems());
                this.updateVisibleItems();

                // Start autoplay
                this.startAutoPlay();

                // Pause on hover
                this.container.addEventListener('mouseenter', () => this.pauseAutoPlay());
                this.container.addEventListener('mouseleave', () => this.startAutoPlay());
            }

            cloneItems() {
                const items = Array.from(this.track.children);
                const totalItems = items.length;

                // Clone first few items to end
                for (let i = 0; i < totalItems; i++) {
                    const clone = items[i].cloneNode(true);
                    this.track.appendChild(clone);
                }
            }

            setupStyles() {
                // Container styles
                this.container.style.overflow = 'hidden';
                this.container.style.position = 'relative';

                // Track styles
                this.track.style.display = 'flex';
                this.track.style.transition = `transform ${this.animationSpeed}ms ease-in-out`;

                // Item styles
                const items = this.track.children;
                Array.from(items).forEach(item => {
                    item.style.flex = '0 0 auto';
                    item.style.margin = '0 15px';
                });
            }

            updateVisibleItems() {
                const containerWidth = this.container.clientWidth;
                const itemWidth = this.getItemWidth();
                const itemsToShow = this.getItemsToShow();

                this.itemWidth = containerWidth / itemsToShow;

                // Update item widths
                const items = this.track.children;
                Array.from(items).forEach(item => {
                    item.style.width = `${this.itemWidth - 30}px`; // Subtract margin
                });
            }

            getItemWidth() {
                const items = this.track.children;
                if (items.length > 0) {
                    return items[0].clientWidth;
                }
                return 200; // Default width
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
                const itemWidth = this.getItemWidth() + 30; // Include margin
                const totalItems = items.length;

                this.currentPosition -= itemWidth;

                this.track.style.transform = `translateX(${this.currentPosition}px)`;

                // Handle infinite loop
                setTimeout(() => {
                    if (Math.abs(this.currentPosition) >= (itemWidth * (totalItems / 2))) {
                        this.track.style.transition = 'none';
                        this.currentPosition = 0;
                        this.track.style.transform = `translateX(0px)`;

                        // Force reflow
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

        // Initialize the carousel
        const carousel = new AutoSlider('partners-carousel', {
            speed: 1000, // 1 second between slides
            animationSpeed: 800, // 0.8 seconds animation
            itemsToShow: {
                desktop: 6,
                tablet: 4,
                mobile: 2
            }
        });
    });
</script>
