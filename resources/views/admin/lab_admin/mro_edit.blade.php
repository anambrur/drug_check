@extends('layouts.admin.master')

@section('content')
    <!-- Form row -->
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <div class="d-md-flex justify-content-between align-items-center mb-20">
                    <h4 class="card-title">Edit mro</h4>
                   
                    <div>
                        <a href="{{ url()->previous() }}" class="btn btn-primary"><i class="fas fa-angle-left"></i>
                            {{ __('content.back') }}</a>
                    </div>
                </div>
                @if ($demo_mode == 'on')
                    <!-- Include Alert Blade -->
                    @include('admin.demo_mode.demo-mode')
                @else
                    <form action="{{ route('mro-list.update', $mro->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                @endif

                <div class="row">
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="company_name">Company Name <span class="text-red">*</span></label>
                                    <input id="company_name" name="company_name"
                                        value="{{ $mro->company_name }}" type="text" class="form-control"
                                        placeholder="Enter Company Name" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="doctor_name">Doctor Name <span
                                            class="text-red">*</span></label>
                                    <input id="doctor_name" name="doctor_name"
                                        value="{{ $mro->doctor_name }}" type="text" class="form-control"
                                        placeholder="Enter Doctor Name" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="mro_address">MRO Address </label>
                                    <input id="mro_address" name="mro_address"
                                        value="{{ $mro->mro_address }}" type="text" class="form-control"
                                        placeholder="Enter MRO Address ">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="signature">{{ __('content.image') }} ({{ __('content.size') }} 320 x 80) (.svg, .jpg, .jpeg, .png, .webp)</label>
                            <input type="file" name="signature" class="form-control-file" id="signature">
                            <small class="form-text text-muted">{{ __('content.please_use_recommended_sizes') }}</small>
                        </div>
                        <div class="height-card box-margin">
                            <div class="card">
                                <div class="card-body">
                                    <div class="avatar-area text-center">
                                        <div class="media">
                                            @if (!empty($mro->signature))
                                                <a  class="d-block mx-auto" href="#" data-toggle="tooltip" data-placement="top" data-original-title="{{ __('content.current_image') }}">
                                                    <img src="{{ asset('uploads/img/blog/thumbnail/'.$mro->signature) }}" alt="blog image" class="rounded w-25">
                                                </a>
                                            @else
                                                <a class="d-block mx-auto" href="#" data-toggle="tooltip" data-placement="top" data-original-title="{{ __('content.not_yet_created') }}">
                                                    <img src="{{ asset('uploads/img/dummy/no-image.jpg') }}" alt="no image" class="rounded w-25">
                                                </a>
                                            @endif
                                        </div>
                                        @if (!empty($mro->signature))
                                            <a class="mt-3 d-block" href="#" data-toggle="modal" data-target="#deleteImageModal{{ $mro->id }}">
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status" class="col-form-label">{{ __('content.status') }}</label>
                            <select class="form-control" name="status" id="status">
                                <option value="active" selected>{{ __('content.select_your_option') }}</option>
                                <option value="active" {{ $mro->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $mro->status == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-center mt-2">
                        <button type="submit" class="btn btn-primary w-100">Update</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end row -->
@endsection
