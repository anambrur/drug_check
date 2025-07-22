@extends('layouts.admin.master')

@section('content')


    <!-- Form row -->
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">Random Consortium
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

                </h4>
                @if (isset($item_section))
                    @if ($demo_mode == 'on')
                        <!-- Include Alert Blade -->
                        @include('admin.demo_mode.demo-mode')
                    @else
                        <form action="{{ route('random-consortium.update', $item_section->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @method('PUT')
                            @csrf
                    @endif

                    <input name="style" type="hidden" value="{{ $style }}">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" name="title" class="form-control" id="title"
                                    value="{{ $item_section->title }}">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="summernote2">Short Description</label>
                                <textarea id="summernote2" name="short_description" class="form-control">{{ $item_section->short_description }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="summernote">Description</label>
                                <textarea id="summernote" name="description" class="form-control">{{ $item_section->description }}</textarea>
                            </div>
                        </div>
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
                        <form action="{{ route('random-consortium.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                    @endif

                    <input name="style" type="hidden" value="{{ $style }}">

                    <div class="row">

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" name="title" class="form-control" id="title">
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="summernote2">Short Description</label>
                                <textarea id="summernote2" name="short_description" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="summernote">Description</label>
                                <textarea id="summernote" name="description" class="form-control"></textarea>
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
