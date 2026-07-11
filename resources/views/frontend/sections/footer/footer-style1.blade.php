@if(Auth::user())
    <div class="easier-mode">
        <div class="easier-section-area">
@endif

<!--// Footer Start //-->
<footer class="footer ft-modern" aria-label="Site footer">
    <div class="ft-modern-bg" aria-hidden="true"></div>

    <div class="footer-top ft-top">
        <div class="container position-relative">
            @if(Auth::user())
                @can('section view')
                    <div class="click-icon d-md-none text-center mb-2">
                        <button class="custom-btn text-white" type="button">
                            <i class="fa fa-mobile-alt text-white" aria-hidden="true"></i> {{ __('content.touch') }}
                        </button>
                    </div>
                @endcan
            @endif

            <div class="ft-bar">
                <div class="ft-bar-logo">
                    @isset ($footer_image_style1)
                        @if (!empty($footer_image_style1->section_image))
                            <a href="{{ url('/') }}" class="ft-logo-link">
                                <img src="{{ asset('uploads/img/general/'.$footer_image_style1->section_image) }}" alt="footer logo" class="img-fluid footer-logo ft-logo" style="border-radius: 5px;">
                            </a>
                        @endif
                    @else
                        @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                            <a href="#" class="ft-logo-link">
                                <img src="{{ asset('uploads/img/dummy/your-logo.jpg') }}" alt="footer logo" class="img-fluid footer-logo ft-logo" style="border-radius: 10px;">
                            </a>
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
                    @endif
                </div>

                <div class="ft-bar-about">
                    <h6 class="ft-about-title">{{ __('frontend.about_us') }}</h6>

                    @isset($site_info)
                        <div class="footer-desc ft-desc">@php echo html_entity_decode($site_info->description); @endphp</div>
                    @else
                        @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                            <p class="footer-desc ft-desc">
                                It is a long established fact that a reader will be distracted by the readable content..
                            </p>
                        @endif
                    @endisset
                </div>

                <div class="ft-card ft-card--contact">
                    <h6 class="ft-card-title">Contact Us</h6>
                    <div class="ft-bar-meta">
                        @isset ($contact_info_widget_style1)
                            @if (!empty($contact_info_widget_style1->email))
                                <a class="ft-meta-link" href="mailto:{{ $contact_info_widget_style1->email }}">
                                    <i class="far fa-envelope" aria-hidden="true"></i>
                                    <span>{{ $contact_info_widget_style1->email }}</span>
                                </a>
                            @endif
                            @if (!empty($contact_info_widget_style1->phone))
                                <a class="ft-meta-link" href="tel:{{ $contact_info_widget_style1->phone }}">
                                    <i class="fas fa-phone" aria-hidden="true"></i>
                                    <span>{{ $contact_info_widget_style1->phone }}</span>
                                </a>
                            @endif
                            @if (!empty($contact_info_widget_style1->address))
                                <div class="ft-meta-text">
                                    <i class="far fa-map" aria-hidden="true"></i>
                                    <span>@php echo html_entity_decode($contact_info_widget_style1->address); @endphp</span>
                                </div>
                            @endif
                        @else
                            @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                                <a class="ft-meta-link" href="mailto:info@drugcheckr.com">
                                    <i class="far fa-envelope" aria-hidden="true"></i>
                                    <span>info@drugcheckr.com</span>
                                </a>
                                <a class="ft-meta-link" href="tel:18006909034">
                                    <i class="fas fa-phone" aria-hidden="true"></i>
                                    <span>1(800) 690-9034</span>
                                </a>
                            @endif
                        @endisset
                    </div>
                </div>
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
