@if(Auth::user())
    @can('section view')
        <div class="easier-mode">
            <div class="easier-section-area">
    @endcan
@endif

@isset ($cta_section_style1)
    <section id="cta" class="cta-modern" aria-labelledby="cta-modern-title">
        <div class="cta-modern-bg" aria-hidden="true"></div>
        <div class="container position-relative">
            @if(Auth::user())
                @can('section view')
                    <div class="click-icon d-md-none text-center">
                        <button class="custom-btn text-white" type="button">
                            <i class="fa fa-mobile-alt text-white" aria-hidden="true"></i> {{ __('content.touch') }}
                        </button>
                    </div>
                @endcan
            @endif

            <div class="cta-modern-card">
                <div class="row align-items-center g-3">
                    <div class="col-lg-7 cta-modern-animate cta-modern-animate--left">
                        <div class="call-to-action-inner cta-modern-content">
                            <span class="cta-modern-badge"><i class="fas fa-bolt" aria-hidden="true"></i> Get Started</span>
                            <h2 id="cta-modern-title">@php echo html_entity_decode($cta_section_style1->title); @endphp</h2>
                        </div>
                    </div>
                    <div class="col-lg-5 cta-modern-animate cta-modern-animate--right">
                        <div class="call-to-action-btn cta-modern-actions">
                            @if (!empty($cta_section_style1->button_name))
                                <a href="{{ $cta_section_style1->button_url }}" class="cta-modern-btn">
                                    <span class="cta-btn-shimmer" aria-hidden="true"></span>
                                    <span class="text">{{ $cta_section_style1->button_name }}</span>
                                    <span class="icon"><i class="fa fa-arrow-right" aria-hidden="true"></i></span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@else
    @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
        <section id="cta" class="cta-modern" aria-labelledby="cta-modern-title">
            <div class="cta-modern-bg" aria-hidden="true"></div>
            <div class="container position-relative">
                @if(Auth::user())
                    @can('section view')
                        <div class="click-icon d-md-none text-center">
                            <button class="custom-btn text-white" type="button">
                                <i class="fa fa-mobile-alt text-white" aria-hidden="true"></i> {{ __('content.touch') }}
                            </button>
                        </div>
                    @endcan
                @endif

                <div class="cta-modern-card">
                    <div class="row align-items-center g-3">
                        <div class="col-lg-7 cta-modern-animate cta-modern-animate--left">
                            <div class="call-to-action-inner cta-modern-content">
                                <span class="cta-modern-badge"><i class="fas fa-bolt" aria-hidden="true"></i> Get Started</span>
                                <h2 id="cta-modern-title">Dou you need a new project ?</h2>
                            </div>
                        </div>
                        <div class="col-lg-5 cta-modern-animate cta-modern-animate--right">
                            <div class="call-to-action-btn cta-modern-actions">
                                <a href="#" data-scroll-nav="7" class="cta-modern-btn">
                                    <span class="cta-btn-shimmer" aria-hidden="true"></span>
                                    <span class="text">Contact Me</span>
                                    <span class="icon"><i class="fa fa-arrow-right" aria-hidden="true"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endisset

@if(Auth::user())
    @can('section view')
            </div>
            <div class="easier-middle">
                @php
                    $url = request()->path();
                    $modified_url = str_replace('/', '-bracket-', $url);
                @endphp
                <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                    @csrf
                    <input type="hidden" name="route" value="call-to-action.create">
                    <input type="hidden" name="style" value="style1">
                    <input type="hidden" name="site_url" value="{{ $modified_url }}">
                    <button type="submit" class="custom-btn text-white me-2 mb-2">
                        <i class="fa fa-edit text-white"></i> {{ __('content.edit_call_to_action') }}
                    </button>
                </form>
            </div>
        </div>
    @endcan
@endif

<script>
(function () {
    var items = document.querySelectorAll('.cta-modern .cta-modern-animate');
    if (!items.length) return;

    function reveal(el) {
        el.classList.add('cta-modern-visible');
    }

    function isInView(el) {
        var rect = el.getBoundingClientRect();
        return rect.top < window.innerHeight * 0.92 && rect.bottom > 0;
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        items.forEach(reveal);
        return;
    }

    items.forEach(function (el) {
        if (isInView(el)) {
            reveal(el);
            return;
        }
        if (!('IntersectionObserver' in window)) {
            reveal(el);
            return;
        }
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    reveal(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -20px 0px' });
        observer.observe(el);
    });
})();
</script>
