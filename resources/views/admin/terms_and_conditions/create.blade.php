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
                        <form action="{{ route('terms-and-conditions.update', $item_section->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @method('PUT')
                            @csrf
                    @endif

                    <input name="style" type="hidden" value="{{ $style }}">

                    <div class="row">

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
                                <label for="summernote3">Terms and Conditions</label>
                                <textarea id="summernote3" name="content" class="form-control">{{ $item_section->content }}</textarea>
                            </div>
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
                <form action="{{ route('terms-and-conditions.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
            @endif

            <input name="style" type="hidden" value="{{ $style }}">

            <div class="row">

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
                        <label for="summernote3">Terms and Conditions</label>
                        <textarea id="summernote3" name="content" class="form-control"></textarea>
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
