@if(Auth::user())
    @can('section view')
        <div class="easier-mode">
            <div class="easier-section-area">
                @endcan
                @endif

                <!--// Counter Section Start //-->
                <section class="section pb-minus-70" id="counters">
                    <div class="container">
                        @if(Auth::user())
                            @can('section view')
                                <!-- hover effect for mobile devices  -->
                                <div class="click-icon d-md-none text-center">
                                    <button class="custom-btn text-white">
                                        <i class="fa fa-mobile-alt text-white"></i> {{ __('content.touch') }}
                                    </button>
                                </div>
                            @endcan
                        @endif
                        @isset ($counter_section_style1)
                            <div class="row justify-content-center">
                                <div class="col-lg-6">
                                    <div class="section-heading light">
                                        <span>@php echo html_entity_decode($counter_section_style1->section_title); @endphp</span>
                                        <h2>@php echo html_entity_decode($counter_section_style1->title); @endphp</h2>
                                    </div>
                                </div>
                            </div>
                        @else
                            @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                           
                            @endif
                        @endisset
                        @if (is_countable($counters_style1) && count($counters_style1) > 0)
                            <div class="row">
                                @foreach ($counters_style1 as $item)
                                    <div class="col-md-4 wow fadeInUp" data-wow-duration="0.7s" data-wow-delay="0.1s">
                                        <div class="counter-item">
                                            @if(Auth::user())
                                                @can('section view')
                                                    @php
                                                        $url = request()->path();
                                                        $modified_url = str_replace('/', '-bracket-', $url);
                                                    @endphp
                                                    <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                                                        @csrf
                                                        <input type="hidden" name="route" value="counter.edit">
                                                        <input type="hidden" name="single_id" value="{{ $item->id }}">
                                                        <input type="hidden" name="site_url" value="{{ $modified_url }}">
                                                        <button type="submit" class="me-2 custom-pure-button ">
                                                            <i class="fa fa-edit text-info easier-custom-font-size-24"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endif
                                            <h3 class="counter">{{ $item->timer }}</h3>
                                            <p>{{ $item->title }}</p>
                                        </div>
                                    </div>
                                @endforeach
                                @unset ($item)
                            </div>
                        @else
                                @if (Auth::user() || $draft_view == null || $draft_view->status == 'enable')
                                <div class="row justify-content-center">
                      <div class="col-12 col-lg-8 justify-content-center align-items-center wow fadeInUp" data-wow-duration="2s" data-wow-delay=".1s">
                          <div class="service-provider-2 justify-content-center align-items-center ">
                              <div class="row">
                               
                                  <div class="col-12 col-lg-12">
                                      <div class="service-provider-text"> 
                                         <div > <h1>How to schedule your test </h1></div> 
                                         <div class="service-provider-margin"> </div>
                                          <div > <p>To schedule any type of test, in any city nationwide, 
                                            call our scheduling department at (800)-221-4291. You can also schedule your test online utilizing our express scheduling registration by selecting your test and
                                             completing the Donor Information/Registration Section. The zip code you enter will be used to determine the closest 
                                             drug testing service center which performs the type of test you have selected. A donor pass/registration form with the local testing service
                                              center address, hours of operation and instructions will be sent to the e-mail address you provided. Take this form with you or have available
                                               on your smart phone to provide to the testing center. 
                                            No appointment is necessary in most cases, however you must complete the donor information section and pay for the test at the time of registration.</p></div>
                                          <div class="service-provider-margin"> </div>
                                         <div><h2>The process is as easy as... <Strong>1, 2, 3!</Strong></h2></div> 
                                      </div>
                                    
                                  </div>
                              </div>
              
                          
                          </div>
                      </div>
                  </div>
                                @endif
                        @endif
                    </div>
                </section>
                <!--// Counter Section End //-->

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
                    <input type="hidden" name="route" value="counter.create">
                    <input type="hidden" name="style" value="style1">
                    <input type="hidden" name="site_url" value="{{ $modified_url }}">
                    <button type="submit" class="custom-btn text-white me-2 mb-2">
                        <i class="fa fa-edit text-white"></i> {{ __('content.edit_section_title_description') }}
                    </button>
                </form>
                <form method="POST" action="{{ route('site-url.index') }}" class="d-inline-block">
                    @csrf
                    <input type="hidden" name="route" value="counter.create">
                    <input type="hidden" name="style" value="style1">
                    <input type="hidden" name="site_url" value="{{ $modified_url }}">
                    <button type="submit" class="custom-btn text-white">
                        <i class="fa fa-plus text-white"></i> {{ __('content.add_counter') }}
                    </button>
                </form>
            </div>
        </div>
    @endcan
@endif
