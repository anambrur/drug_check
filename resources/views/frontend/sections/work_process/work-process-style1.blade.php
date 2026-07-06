@if (Auth::user())
    @can('section view')
        <div class="easier-mode">
            <div class="easier-section-area">
            @endcan
@endif

<!--// How I Work Section Start //-->
<section class="section wp-modern" id="wp-modern-section" aria-labelledby="wp-modern-heading">
    <div class="wp-modern-bg" aria-hidden="true"></div>

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

        @isset($work_process_section_style1)
            <div class="wp-section-head text-center wp-animate">
                <p class="wp-eyebrow">@php echo html_entity_decode($work_process_section_style1->section_title); @endphp</p>
                <h2 class="wp-title" id="wp-modern-heading">@php echo html_entity_decode($work_process_section_style1->title); @endphp</h2>
                <p class="wp-subtitle">@php echo html_entity_decode($work_process_section_style1->short_description); @endphp</p>
            </div>
        @else
            @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                <div class="wp-section-head text-center wp-animate">
                    <p class="wp-eyebrow">How Our Work</p>
                    <h2 class="wp-title" id="wp-modern-heading">Our prepare your projects in 3 stages</h2>
                </div>
            @endif
        @endisset

        @if (is_countable($work_processes_style1) && count($work_processes_style1) > 0)
            @php
                $i = 2;
                $t = 1;
            @endphp
            @foreach ($work_processes_style1->chunk(3) as $work_process)
                <div class="row g-4 g-xl-5 justify-content-center wp-steps-row">
                    @foreach ($work_process as $item)
                        <div class="col-md-6 col-lg-4 wp-animate" style="--wp-delay: {{ $loop->index * 0.1 }}s;">
                            <article class="wp-step-card {{ !$loop->last ? 'wp-step-card--has-connector' : '' }}">
                                <div class="wp-step-num" aria-hidden="true">
                                    <span>{{ str_pad($t++, 2, '0', STR_PAD_LEFT) }}</span>
                                </div>

                                @if (!empty($item->section_image))
                                    <div class="wp-step-media">
                                        <img src="{{ asset('uploads/img/work_process/' . $item->section_image) }}"
                                             class="img-fluid" alt="@php echo strip_tags(html_entity_decode($item->title)); @endphp">
                                    </div>
                                @endif

                                <div class="wp-step-body">
                                    @if (!empty($item->short_description))
                                        <p class="wp-step-desc">@php echo html_entity_decode($item->short_description); @endphp</p>
                                    @endif

                                    <div class="wp-step-title-row">
                                        @if (Auth::user())
                                            @can('section view')
                                                @php
                                                    $url = request()->path();
                                                    $modified_url = str_replace('/', '-bracket-', $url);
                                                @endphp
                                                <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                                                    @csrf
                                                    <input type="hidden" name="route" value="work-process.edit">
                                                    <input type="hidden" name="single_id" value="{{ $item->id }}">
                                                    <input type="hidden" name="site_url" value="{{ $modified_url }}">
                                                    <button type="submit" class="me-2 custom-pure-button" aria-label="Edit work process step">
                                                        <i class="fa fa-edit text-info easier-custom-font-size-24"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        @endif
                                        <h3 class="wp-step-title">@php echo html_entity_decode($item->title); @endphp</h3>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                    @unset ($item)
                </div>
            @endforeach
            @unset ($work_process)
        @else
            @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                <div class="row g-4 g-xl-5 justify-content-center wp-steps-row">
                    @foreach (['Thinking', 'Research', 'Design'] as $placeholderTitle)
                        <div class="col-md-6 col-lg-4 wp-animate" style="--wp-delay: {{ $loop->index * 0.1 }}s;">
                            <article class="wp-step-card {{ !$loop->last ? 'wp-step-card--has-connector' : '' }}">
                                <div class="wp-step-num" aria-hidden="true">
                                    <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div class="wp-step-media">
                                    <img src="{{ asset('uploads/img/dummy/328x328.jpg') }}" class="img-fluid" alt="{{ $placeholderTitle }}">
                                </div>
                                <div class="wp-step-body">
                                    <h3 class="wp-step-title">{{ $placeholderTitle }}</h3>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</section>
<!--// How I Work Section End //-->

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
                <input type="hidden" name="route" value="work-process.create">
                <input type="hidden" name="style" value="style1">
                <input type="hidden" name="site_url" value="{{ $modified_url }}">
                <button type="submit" class="custom-btn text-white me-2 mb-2">
                    <i class="fa fa-edit text-white"></i> {{ __('content.edit_section_title_description') }}
                </button>
            </form>
            <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                @csrf
                <input type="hidden" name="route" value="work-process.create">
                <input type="hidden" name="style" value="style1">
                <input type="hidden" name="site_url" value="{{ $modified_url }}">
                <button type="submit" class="custom-btn text-white">
                    <i class="fa fa-plus text-white"></i> {{ __('content.add_work_process') }}
                </button>
            </form>
        </div>
        </div>
    @endcan
@endif

<script>
(function () {
    var section = document.getElementById('wp-modern-section');
    if (!section) return;

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        section.querySelectorAll('.wp-animate').forEach(function (el) {
            el.classList.add('wp-visible');
        });
        return;
    }

    if (!('IntersectionObserver' in window)) {
        section.querySelectorAll('.wp-animate').forEach(function (el) {
            el.classList.add('wp-visible');
        });
        return;
    }

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('wp-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    section.querySelectorAll('.wp-animate').forEach(function (el) {
        observer.observe(el);
    });
})();
</script>
