@if(Auth::user())
    <div class="easier-mode">
        <div class="easier-section-area">
@endif

<!--// Footer Start //-->
<footer class="footer ft-modern" aria-label="Site footer">
    <div class="ft-modern-bg" aria-hidden="true"></div>
    <div class="ft-modern-glow ft-modern-glow--left" aria-hidden="true"></div>
    <div class="ft-modern-glow ft-modern-glow--right" aria-hidden="true"></div>

    <div class="footer-top ft-top">
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

            <div class="row g-4 g-xl-5">
                <div class="col-md-6 col-lg-4 footer-widget-resp ft-animate" style="--ft-delay: 0s;">
                    <div class="footer-widget ft-widget ft-widget--about">
                        <span class="ft-eyebrow">{{ __('frontend.about_us') }}</span>
                        <h6 class="footer-title ft-title">{{ __('frontend.about_us') }}</h6>

                        @isset ($footer_image_style1)
                            @if (!empty($footer_image_style1->section_image))
                                <a href="{{ url('/') }}" class="ft-logo-link">
                                    <img src="{{ asset('uploads/img/general/'.$footer_image_style1->section_image) }}" alt="footer logo" class="img-fluid footer-logo ft-logo">
                                </a>
                            @endif
                        @else
                            @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                                <a href="#" class="ft-logo-link">
                                    <img src="{{ asset('uploads/img/dummy/your-logo.jpg') }}" alt="footer logo" class="img-fluid footer-logo ft-logo">
                                </a>
                            @endif
                        @endisset

                        @isset($site_info)
                            <div class="footer-desc ft-desc">@php echo html_entity_decode($site_info->description); @endphp</div>
                        @else
                            @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                                <p class="footer-desc ft-desc">
                                    It is a long established fact that a reader will be
                                    distracted by the readable content..
                                </p>
                            @endif
                        @endisset

                        @if (is_countable($socials) && count($socials) > 0)
                            <div class="footer-social-links ft-social">
                                @foreach ($socials as $social)
                                    @if ($social->social_media == 'fab fa-twitter')
                                        <a class="ft-social-link border border-0" href="{{ $social->url }}" target="_blank" rel="noopener noreferrer" aria-label="X (Twitter)">
                                            <img src="{{ asset('uploads/img/dummy/x-twitter-white.svg') }}" alt="x icon">
                                        </a>
                                    @elseif ($social->social_media == 'fas fa-tiktok')
                                        <a class="ft-social-link border border-0" href="{{ $social->url }}" target="_blank" rel="noopener noreferrer" aria-label="TikTok">
                                            <img src="{{ asset('uploads/img/dummy/tik-tok-white.svg') }}" alt="tiktok icon" class="custom-max-width-16">
                                        </a>
                                    @else
                                        <a class="ft-social-link" href="{{ $social->url }}" target="_blank" rel="noopener noreferrer" aria-label="Social link">
                                            <i class="{{ $social->social_media }}" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                @endforeach
                                @unset ($social)
                            </div>
                        @else
                            @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                            {{-- <div class="footer-social-links ft-social">
                                <a class="ft-social-link" href="javascript:void(0)"><i class="fab fa-facebook-f"></i></a>
                                <a class="ft-social-link" href="javascript:void(0)"><i class="fab fa-twitter"></i></a>
                                <a class="ft-social-link" href="javascript:void(0)"><i class="fab fa-instagram"></i></a>
                                <a class="ft-social-link" href="javascript:void(0)"><i class="fab fa-youtube"></i></a>
                            </div> --}}
                            @endif
                        @endif
                    </div>
                </div>

                @if (is_countable($footer_categories) && count($footer_categories) > 0)
                    @foreach ($footer_categories as $footer_category)
                        <div class="col-md-6 col-lg-4 footer-widget-resp ft-animate" style="--ft-delay: {{ ($loop->index + 1) * 0.1 }}s;">
                            <div class="footer-widget footer-widget-pl ft-widget ft-widget--links">
                                <span class="ft-eyebrow">{{ $footer_category->category_name }}</span>
                                <h6 class="footer-title ft-title">{{ $footer_category->category_name }}</h6>
                                <ul class="footer-links ft-links">
                                    @foreach ($footers as $footer)
                                        @if ($footer_category->category_name == $footer->category_name)
                                            <li><a href="{{ $footer->url }}"><span class="ft-link-arrow" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>{{ $footer->title }}</a></li>
                                        @endif
                                    @endforeach
                                    @unset ($footer)
                                </ul>
                            </div>
                        </div>
                    @endforeach
                    @unset ($footer_category)
                @else
                    @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                        <div class="col-md-6 col-lg-4 footer-widget-resp ft-animate" style="--ft-delay: 0.1s;">
                            <div class="footer-widget footer-widget-pl ft-widget ft-widget--links">
                                <span class="ft-eyebrow">Quick Links</span>
                                <h6 class="footer-title ft-title">Usefull Links</h6>
                                <ul class="footer-links ft-links">
                                    <li><a href="javascript:void(0)"><span class="ft-link-arrow" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>My Team</a></li>
                                    <li><a href="javascript:void(0)"><span class="ft-link-arrow" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>My Services</a></li>
                                    <li><a href="javascript:void(0)"><span class="ft-link-arrow" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>My Resume</a></li>
                                    <li><a href="javascript:void(0)"><span class="ft-link-arrow" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>My Works</a></li>
                                    <li><a href="javascript:void(0)"><span class="ft-link-arrow" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>Get in Touch</a></li>
                                    <li><a href="javascript:void(0)"><span class="ft-link-arrow" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>Privacy Policy</a></li>
                                </ul>
                            </div>
                        </div>
                    @endif
                @endif

                @isset ($contact_info_widget_style1)
                    <div class="col-md-6 col-lg-4 footer-widget-resp ft-animate" style="--ft-delay: 0.2s;">
                        <div class="footer-widget ft-widget ft-widget--contact">
                            <span class="ft-eyebrow">Get in Touch</span>
                            <h6 class="footer-title ft-title">{{ $contact_info_widget_style1->title }}</h6>
                            <div class="footer-contact-info-wrap ft-contact-wrap">
                                <ul class="footer-contact-info-list ft-contact-list">
                                    @if (!empty($contact_info_widget_style1->address))
                                        <li class="ft-contact-item">
                                            <p class="ft-contact-lead">@php echo html_entity_decode($contact_info_widget_style1->description); @endphp</p>
                                        </li>
                                    @endif
                                    @if (!empty($contact_info_widget_style1->address))
                                        <li class="ft-contact-item">
                                            <div class="ft-contact-icon" aria-hidden="true"><i class="far fa-map custom-color-orange"></i></div>
                                            <div class="ft-contact-body">
                                                <h6>{{ __('frontend.address') }}</h6>
                                                <p>@php echo html_entity_decode($contact_info_widget_style1->address); @endphp</p>
                                            </div>
                                        </li>
                                    @endif
                                    @if (!empty($contact_info_widget_style1->email))
                                        <li class="ft-contact-item">
                                            <div class="ft-contact-icon" aria-hidden="true"><i class="far fa-envelope custom-color-orange"></i></div>
                                            <div class="ft-contact-body">
                                                <h6>{{ __('frontend.email') }}</h6>
                                                <p><a class="ft-contact-link" href="mailto:{{ $contact_info_widget_style1->email }}">{{ $contact_info_widget_style1->email }}</a></p>
                                            </div>
                                        </li>
                                    @endif
                                    @if (!empty($contact_info_widget_style1->phone))
                                        <li class="ft-contact-item">
                                            <div class="ft-contact-icon" aria-hidden="true"><i class="fas fa-phone custom-color-orange"></i></div>
                                            <div class="ft-contact-body">
                                                <h6>{{ __('frontend.phone') }}</h6>
                                                <p><a class="ft-contact-link" href="tel:{{ $contact_info_widget_style1->phone }}">{{ $contact_info_widget_style1->phone }}</a></p>
                                            </div>
                                        </li>
                                    @endif
                                    @if (!empty($contact_info_widget_style1->working_hour))
                                        <li class="ft-contact-item">
                                            <div class="ft-contact-icon" aria-hidden="true"><i class="fas fa-clock custom-color-orange"></i></div>
                                            <div class="ft-contact-body">
                                                <h6>{{ __('frontend.working_hour') }}</h6>
                                                <p>{{ $contact_info_widget_style1->working_hour }}</p>
                                            </div>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                @else
                    @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                        <div class="col-md-6 col-lg-4 footer-widget-resp ft-animate" style="--ft-delay: 0.2s;">
                            <div class="footer-widget ft-widget ft-widget--contact">
                                <span class="ft-eyebrow">Reach Us</span>
                                <h6 class="footer-title ft-title">Contact Info</h6>
                                <div class="footer-contact-info-wrap ft-contact-wrap">
                                    <ul class="footer-contact-info-list ft-contact-list">
                                        <li class="ft-contact-item">
                                            <div class="ft-contact-icon" aria-hidden="true"><i class="far fa-map custom-color-orange"></i></div>
                                            <div class="ft-contact-body">
                                                <h6>Address</h6>
                                                <p>1395 Nixon Avenue Etowah, TN 37331<br>United States</p>
                                            </div>
                                        </li>
                                        <li class="ft-contact-item">
                                            <div class="ft-contact-icon" aria-hidden="true"><i class="fas fa-phone custom-color-orange"></i></div>
                                            <div class="ft-contact-body">
                                                <h6>E-Mail &amp; Phone</h6>
                                                <p><a class="ft-contact-link" href="tel:+14222005555">+1 422-200-5555</a></p>
                                                <p><a class="ft-contact-link" href="mailto:elsecolor@gmail.com">elsecolor@gmail.com</a></p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                @endisset
            </div>
        </div>
    </div>

    @isset ($site_info)
        @if (!empty($site_info->copyright))
            <div class="copyright ft-copyright">
                <div class="container">
                    <div class="ft-copyright-inner">
                        @php
                            $ftCopyright = html_entity_decode($site_info->copyright);
                            $ftCopyright = preg_replace('/\b(19|20)\d{2}\b/', date('Y'), $ftCopyright, 1);
                        @endphp
                        <p class="copyright-text ft-copyright-text">{!! $ftCopyright !!}</p>
                    </div>
                </div>
            </div>
        @endif
    @else
        @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
            <div class="copyright ft-copyright">
                <div class="container">
                    <div class="ft-copyright-inner">
                        <p class="copyright-text ft-copyright-text">© Copyright {{ date('Y') }}. Powered By ElseColor</p>
                    </div>
                </div>
            </div>
        @endif
    @endisset
</footer>
<!--// Footer End //-->

@if(Auth::user())
        </div>
        <div class="easier-middle">
            @php
                $url = request()->path();
                $modified_url = str_replace('/', '-bracket-', $url);
            @endphp
            @can('setting view')
                <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                    @csrf
                    <input type="hidden" name="route" value="footer-image.create">
                    <input type="hidden" name="style" value="style1">
                    <input type="hidden" name="site_url" value="{{ $modified_url }}">
                    <button type="submit" class="custom-btn text-white me-2 mb-2">
                        <i class="fa fa-edit text-white"></i> {{ __('content.edit_footer_image') }}
                    </button>
                </form>
                <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                    @csrf
                    <input type="hidden" name="route" value="site-info.create">
                    <input type="hidden" name="style" value="">
                    <input type="hidden" name="site_url" value="{{ $modified_url }}">
                    <button type="submit" class="custom-btn text-white me-2 mb-2">
                        <i class="fa fa-edit text-white"></i> {{ __('content.edit_site_info') }}
                    </button>
                </form>
            @endcan
            @can('section view')
                <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                    @csrf
                    <input type="hidden" name="route" value="footer.create">
                    <input type="hidden" name="style" value="">
                    <input type="hidden" name="site_url" value="{{ $modified_url }}">
                    <button type="submit" class="custom-btn text-white me-2 mb-2">
                        <i class="fa fa-plus text-white"></i> {{ __('content.add_footer') }}
                    </button>
                </form>
            @endcan
            @can('setting view')
                <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                    @csrf
                    <input type="hidden" name="route" value="contact-info-widget.create">
                    <input type="hidden" name="style" value="style1">
                    <input type="hidden" name="site_url" value="{{ $modified_url }}">
                    <button type="submit" class="custom-btn text-white me-2 mb-2">
                        <i class="fa fa-plus text-white"></i> {{ __('content.add_contact_info') }}
                    </button>
                </form>
                <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                    @csrf
                    <input type="hidden" name="route" value="social.create">
                    <input type="hidden" name="style" value="">
                    <input type="hidden" name="site_url" value="{{ $modified_url }}">
                    <button type="submit" class="custom-btn text-white">
                        <i class="fa fa-plus text-white"></i> {{ __('content.add_social') }}
                    </button>
                </form>
            @endcan
        </div>
    </div>
@endif

<script>
(function () {
    var items = document.querySelectorAll('.ft-modern .ft-animate');
    if (!items.length) return;

    function reveal(el) {
        el.classList.add('ft-visible');
    }

    function isInView(el) {
        var rect = el.getBoundingClientRect();
        return rect.top < window.innerHeight * 0.95 && rect.bottom > 0;
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
        }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });
        observer.observe(el);
    });
})();
</script>
