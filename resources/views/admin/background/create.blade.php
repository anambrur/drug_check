@extends('layouts.admin.master')

@section('content')


    <!-- Form row -->
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">{{ __('content.about') }}
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            {{ __('content.' . $style) }}
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item"
                                href="{{ route('about.create', ['style' => 'style1']) }}">{{ __('content.style1') }}</a>
                        </div>
                    </div>
                    <!-- Button -->
                    <a id="hoverButton" class="iyzi-btn"><i class="fas fa-camera"></i> {{ __('content.view_draft') }}</a>
                    <!-- Modal -->
                    <div id="imageModal" class="border border-success iyzi-modal">
                        <img class="img-fluid " src="{{ asset('uploads/img/dummy/style/about-' . $style . '.jpg') }}"
                            alt="draft image">
                    </div>
                </h4>
                @if (isset($item_section))
                    @if ($demo_mode == 'on')
                        <!-- Include Alert Blade -->
                        @include('admin.demo_mode.demo-mode')
                    @else
                        <form action="{{ route('background.update', $item_section->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @method('PUT')
                            @csrf
                    @endif

                    <input name="style" type="hidden" value="{{ $style }}">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="section_image">{{ __('content.image') }} ({{ __('content.size') }} 480 x 600)
                                    (.svg, .jpg, .jpeg, .png, .webp, .gif)</label>
                                <input type="file" name="section_image" class="form-control-file" id="section_image">
                                <small
                                    class="form-text text-muted">{{ __('content.please_use_recommended_sizes') }}</small>
                            </div>
                            <div class="height-card box-margin">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="avatar-area text-center">
                                            <div class="media">
                                                @if (!empty($item_section->section_image))
                                                    <a class="d-block mx-auto" href="#" data-toggle="tooltip"
                                                        data-placement="top"
                                                        data-original-title="{{ __('content.current_image') }}">
                                                        <img src="{{ asset('uploads/img/background/' . $item_section->section_image) }}"
                                                            alt="image" class="rounded">
                                                    </a>
                                                @else
                                                    <a class="d-block mx-auto" href="#" data-toggle="tooltip"
                                                        data-placement="top"
                                                        data-original-title="{{ __('content.current_image') }}">
                                                        <img src="{{ asset('uploads/img/dummy/no-image.jpg') }}"
                                                            alt="no image" class="rounded w-25">
                                                    </a>
                                                @endif
                                            </div>
                                            @if (!empty($item_section->section_image))
                                                <a class="mt-3 d-block" href="#" data-toggle="modal"
                                                    data-target="#deleteModal{{ $item_section->id }}">
                                                    <i class="fa fa-trash text-danger font-18"></i>
                                                </a>
                                            @endif
                                        </div>
                                        <!--end card-body-->
                                    </div>
                                </div>
                                <!--end card-->
                            </div>
                            <!--end col-->
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="section_title">{{ __('content.section_title') }}</label>
                                <input type="text" name="section_title" class="form-control" id="section_title"
                                    value="{{ $item_section->section_title }}">
                                <small class="form-text text-muted">{{ __('content.recommended_tags') }} <span
                                        class="text-danger font-weight-bold custom-tag mr-1"
                                        onclick="insertTag('a', 'section_title')">{{ __('<a href="">') }}</span> <span
                                        class="text-danger font-weight-bold custom-tag mr-1"
                                        onclick="insertTag('br', 'section_title')">{{ __('<br>') }}</span> <span
                                        class="text-danger font-weight-bold custom-tag mr-1"
                                        onclick="insertTag('b', 'section_title')">{{ __('<b>') }}</span> <span
                                        class="text-danger font-weight-bold custom-tag mr-1"
                                        onclick="insertTag('i', 'section_title')">{{ __('<i>') }}</span> <span
                                        class="text-danger font-weight-bold custom-tag mr-1"
                                        onclick="insertTag('span', 'section_title')">{{ __('<span>') }}</span> <span
                                        class="text-danger font-weight-bold custom-tag mr-1"
                                        onclick="insertTag('p', 'section_title')">{{ __('<p>') }}</small>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="summernote2">Terms and Conditions</label>
                                <textarea id="summernote2" name="description2" class="form-control">{{ $item_section->description2 }}</textarea>
                            </div>
                        </div>



                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="title">Service Title</label>
                                <input type="text" name="title" class="form-control" id="title"
                                    value="{{ $item_section->title }}">
                                <small class="form-text text-muted">{{ __('content.recommended_tags') }} <span
                                        class="text-danger font-weight-bold custom-tag mr-1"
                                        onclick="insertTag('a', 'title')">{{ __('<a href="">') }}</span> <span
                                        class="text-danger font-weight-bold custom-tag mr-1"
                                        onclick="insertTag('br', 'title')">{{ __('<br>') }}</span> <span
                                        class="text-danger font-weight-bold custom-tag mr-1"
                                        onclick="insertTag('b', 'title')">{{ __('<b>') }}</span> <span
                                        class="text-danger font-weight-bold custom-tag mr-1"
                                        onclick="insertTag('i', 'title')">{{ __('<i>') }}</span> <span
                                        class="text-danger font-weight-bold custom-tag mr-1"
                                        onclick="insertTag('span', 'title')">{{ __('<span>') }}</span> <span
                                        class="text-danger font-weight-bold custom-tag mr-1"
                                        onclick="insertTag('p', 'title')">{{ __('<p>') }}</small>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="summernote">Service Description</label>
                                <textarea id="summernote" name="description" class="form-control">{{ $item_section->description }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="summernote3">Terms and Conditions</label>
                                <textarea id="summernote3" name="description3" class="form-control">{{ $item_section->description3 }}</textarea>
                            </div>
                        </div>



                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="breadcrumb_status"
                                    class="col-form-label">{{ __('content.please_use_recommended_sizes') }}</label>
                                <select name="breadcrumb_status" class="form-control" id="breadcrumb_status">
                                    <option value="no" selected>{{ __('content.select_your_option') }}</option>
                                    <option value="yes"
                                        {{ $item_section->breadcrumb_status == 'yes' ? 'selected' : '' }}>
                                        {{ __('content.yes') }}</option>
                                    <option value="no"
                                        {{ $item_section->breadcrumb_status == 'no' ? 'selected' : '' }}>
                                        {{ __('content.no') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="custom_breadcrumb_image">{{ __('content.custom_breadcrumb_image') }}
                                    ({{ __('content.size') }} 1920 x 400) (.svg, .jpg, .jpeg, .png, .webp,
                                    .gif)</label>
                                <input type="file" name="custom_breadcrumb_image" class="form-control-file"
                                    id="custom_breadcrumb_image">
                                <small id="custom_breadcrumb_image"
                                    class="form-text text-muted">{{ __('content.recommended_size') }}</small>
                            </div>
                            <div class="height-card box-margin">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="avatar-area text-center">
                                            <div class="media">
                                                @if (!empty($item_section->custom_breadcrumb_image))
                                                    <a class="d-block mx-auto" href="#" data-toggle="tooltip"
                                                        data-placement="top"
                                                        data-original-title="{{ __('content.current_image') }}">
                                                        <img src="{{ asset('uploads/img/background/breadcrumb/' . $item_section->custom_breadcrumb_image) }}"
                                                            alt="image" class="rounded">
                                                    </a>
                                                @else
                                                    <a class="d-block mx-auto" href="#" data-toggle="tooltip"
                                                        data-placement="top"
                                                        data-original-title="{{ __('content.not_yet_created') }}">
                                                        <img src="{{ asset('uploads/img/dummy/no-image.jpg') }}"
                                                            alt="no image" class="rounded w-25">
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <!--end card-body-->
                                    </div>
                                </div>
                                <!--end card-->
                            </div>
                            <!--end col-->
                        </div>


                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="custom_breadcrumb_image2">Package breadcrumb
                                    ({{ __('content.size') }} 1920 x 400) (.svg, .jpg, .jpeg, .png, .webp,
                                    .gif)</label>
                                <input type="file" name="custom_breadcrumb_image2" class="form-control-file"
                                    id="custom_breadcrumb_image2">
                                <small id="custom_breadcrumb_image2"
                                    class="form-text text-muted">{{ __('content.recommended_size') }}</small>
                            </div>
                            <div class="height-card box-margin">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="avatar-area text-center">
                                            <div class="media">
                                                @if (!empty($item_section->custom_breadcrumb_image2))
                                                    <a class="d-block mx-auto" href="#" data-toggle="tooltip"
                                                        data-placement="top"
                                                        data-original-title="{{ __('content.current_image') }}">
                                                        <img src="{{ asset('uploads/img/background/breadcrumb/' . $item_section->custom_breadcrumb_image2) }}"
                                                            alt="image" class="rounded">
                                                    </a>
                                                @else
                                                    <a class="d-block mx-auto" href="#" data-toggle="tooltip"
                                                        data-placement="top"
                                                        data-original-title="{{ __('content.not_yet_created') }}">
                                                        <img src="{{ asset('uploads/img/dummy/no-image.jpg') }}"
                                                            alt="no image" class="rounded w-25">
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <!--end card-body-->
                                    </div>
                                </div>
                                <!--end card-->
                            </div>
                            <!--end col-->
                        </div>

                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="custom_breadcrumb_image3">Terms & Conditions breadcrumb
                                    ({{ __('content.size') }} 1920 x 400) (.svg, .jpg, .jpeg, .png, .webp,
                                    .gif)</label>
                                <input type="file" name="custom_breadcrumb_image3" class="form-control-file"
                                    id="custom_breadcrumb_image3">
                                <small id="custom_breadcrumb_image3"
                                    class="form-text text-muted">{{ __('content.recommended_size') }}</small>
                            </div>
                            <div class="height-card box-margin">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="avatar-area text-center">
                                            <div class="media">
                                                @if (!empty($item_section->custom_breadcrumb_image3))
                                                    <a class="d-block mx-auto" href="#" data-toggle="tooltip"
                                                        data-placement="top"
                                                        data-original-title="{{ __('content.current_image') }}">
                                                        <img src="{{ asset('uploads/img/background/breadcrumb/' . $item_section->custom_breadcrumb_image3) }}"
                                                            alt="image" class="rounded">
                                                    </a>
                                                @else
                                                    <a class="d-block mx-auto" href="#" data-toggle="tooltip"
                                                        data-placement="top"
                                                        data-original-title="{{ __('content.not_yet_created') }}">
                                                        <img src="{{ asset('uploads/img/dummy/no-image.jpg') }}"
                                                            alt="no image" class="rounded w-25">
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <!--end card-body-->
                                    </div>
                                </div>
                                <!--end card-->
                            </div>
                            <!--end col-->
                        </div>
                    </div>
                    <!--end col-->

                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary mr-2">{{ __('content.submit') }}</button>
                        <a href="#" class="btn btn-primary" data-toggle="modal"
                            data-target="#aboutSectionDestroyModal{{ $item_section->id }}">
                            <i class="fa fa-trash"></i> {{ __('content.reset') }}
                        </a>
                    </div>
            </div>
            </form>
        @else
            @if ($demo_mode == 'on')
                <!-- Include Alert Blade -->
                @include('admin.demo_mode.demo-mode')
            @else
                <form action="{{ route('background.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
            @endif

            <input name="style" type="hidden" value="{{ $style }}">

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="section_image">{{ __('content.image') }} ({{ __('content.size') }} 480 x 600)
                            (.svg, .jpg, .jpeg, .png, .webp, .gif)</label>
                        <input type="file" name="section_image" class="form-control-file" id="section_image">
                        <small class="form-text text-muted">{{ __('content.please_use_recommended_sizes') }}</small>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="section_title">{{ __('content.section_title') }}</label>
                        <input type="text" name="section_title" class="form-control" id="section_title">
                        <small class="form-text text-muted">{{ __('content.recommended_tags') }} <span
                                class="text-danger font-weight-bold custom-tag mr-1"
                                onclick="insertTag('a', 'section_title')">{{ __('<a href="">') }}</span> <span
                                class="text-danger font-weight-bold custom-tag mr-1"
                                onclick="insertTag('br', 'section_title')">{{ __('<br>') }}</span> <span
                                class="text-danger font-weight-bold custom-tag mr-1"
                                onclick="insertTag('b', 'section_title')">{{ __('<b>') }}</span> <span
                                class="text-danger font-weight-bold custom-tag mr-1"
                                onclick="insertTag('i', 'section_title')">{{ __('<i>') }}</span> <span
                                class="text-danger font-weight-bold custom-tag mr-1"
                                onclick="insertTag('span', 'section_title')">{{ __('<span>') }}</span> <span
                                class="text-danger font-weight-bold custom-tag mr-1"
                                onclick="insertTag('p', 'section_title')">{{ __('<p>') }}</small>
                    </div>
                </div>


                <div class="col-md-12">
                    <div class="form-group">
                        <label for="summernote2">Short Description</label>
                        <textarea id="summernote2" name="description2" class="form-control"></textarea>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="title">Service Title</label>
                        <input type="text" name="title" class="form-control" id="title">
                        <small class="form-text text-muted">{{ __('content.recommended_tags') }} <span
                                class="text-danger font-weight-bold custom-tag mr-1"
                                onclick="insertTag('a', 'title')">{{ __('<a href="">') }}</span> <span
                                class="text-danger font-weight-bold custom-tag mr-1"
                                onclick="insertTag('br', 'title')">{{ __('<br>') }}</span> <span
                                class="text-danger font-weight-bold custom-tag mr-1"
                                onclick="insertTag('b', 'title')">{{ __('<b>') }}</span> <span
                                class="text-danger font-weight-bold custom-tag mr-1"
                                onclick="insertTag('i', 'title')">{{ __('<i>') }}</span> <span
                                class="text-danger font-weight-bold custom-tag mr-1"
                                onclick="insertTag('span', 'title')">{{ __('<span>') }}</span> <span
                                class="text-danger font-weight-bold custom-tag mr-1"
                                onclick="insertTag('p', 'title')">{{ __('<p>') }}</small>
                    </div>
                </div>



                <div class="col-md-12">
                    <div class="form-group">
                        <label for="summernote">Service Description</label>
                        <textarea id="summernote" name="description" class="form-control"></textarea>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="summernote3">Terms and Conditions</label>
                        <textarea id="summernote3" name="description3" class="form-control"></textarea>
                    </div>
                </div>


                <div class="col-md-12">
                    <div class="form-group">
                        <label for="breadcrumb_status"
                            class="col-form-label">{{ __('content.use_special_breadcrumb') }}</label>
                        <select name="breadcrumb_status" class="form-control" id="breadcrumb_status">
                            <option value="no" selected>{{ __('content.select_your_option') }}</option>
                            <option value="yes">{{ __('content.yes') }}</option>
                            <option value="no">{{ __('content.no') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="custom_breadcrumb_image">{{ __('content.custom_breadcrumb_image') }}
                            ({{ __('content.size') }} 1920 x 400) (.svg, .jpg, .jpeg, .png, .webp, .gif)</label>
                        <input type="file" name="custom_breadcrumb_image" class="form-control-file"
                            id="custom_breadcrumb_image">
                        <small id="custom_breadcrumb_image"
                            class="form-text text-muted">{{ __('content.please_use_recommended_sizes') }}</small>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="custom_breadcrumb_image2">Package breadcrumb
                            ({{ __('content.size') }} 1920 x 400) (.svg, .jpg, .jpeg, .png, .webp, .gif)</label>
                        <input type="file" name="custom_breadcrumb_image2" class="form-control-file"
                            id="custom_breadcrumb_image2">
                        <small id="custom_breadcrumb_image2"
                            class="form-text text-muted">{{ __('content.please_use_recommended_sizes') }}</small>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="custom_breadcrumb_image3">Terms and Conditions breadcrumb
                            ({{ __('content.size') }} 1920 x 400) (.svg, .jpg, .jpeg, .png, .webp, .gif)</label>
                        <input type="file" name="custom_breadcrumb_image3" class="form-control-file"
                            id="custom_breadcrumb_image3">
                        <small id="custom_breadcrumb_image3"
                            class="form-text text-muted">{{ __('content.please_use_recommended_sizes') }}</small>
                    </div>
                </div>


                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary mr-2">{{ __('content.submit') }}</button>
                </div>
            </div>
            </form>
            @endif
        </div>
    </div>
    </div>
    <!-- end row -->
@endsection
