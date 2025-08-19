@extends('layouts.admin.master')

@section('content')


    <!-- Form row -->
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">Clearing House
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
                        <form action="{{ route('clearing-house.update', $item_section->id) }}" method="POST"
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
                            <div class="form-group">
                                <label for="employer_pdf">Employer PDF</label>
                                <input id="employer_pdf" name="employer_pdf[]" type="file" multiple
                                    class="form-control-file" accept=".pdf">
                                <small class="form-text text-muted">Select one or more PDF files</small>
                                @if ($item_section->employer_pdf)
                                    @foreach (json_decode($item_section->employer_pdf) as $pdf)
                                        <div>
                                            <a href="{{ asset('uploads/pdf/employer_pdf/' . $pdf) }}"
                                                target="_blank">{{ $pdf }}</a>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="driver_pdf">Driver PDF</label>
                                <input id="driver_pdf" name="driver_pdf[]" type="file" multiple class="form-control-file"
                                    accept=".pdf">
                                <small class="form-text text-muted">Select one or more PDF files</small>
                                @if ($item_section->driver_pdf)
                                    @foreach (json_decode($item_section->driver_pdf) as $pdf)
                                        <div>
                                            <a href="{{ asset('uploads/pdf/driver_pdf/' . $pdf) }}"
                                                target="_blank">{{ $pdf }}</a>
                                        </div>
                                    @endforeach
                                @endif
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
                        <form action="{{ route('clearing-house.store') }}" method="POST" enctype="multipart/form-data">
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
                            <div class="form-group">
                                <label for="employer_pdf">Employer PDF</label>
                                <input id="employer_pdf" name="employer_pdf[]" type="file" multiple
                                    class="form-control-file" accept=".pdf">
                                <small class="form-text text-muted">Select one or more PDF files</small>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="driver_pdf">Driver PDF</label>
                                <input id="driver_pdf" name="driver_pdf[]" type="file" multiple
                                    class="form-control-file" accept=".pdf">
                                <small class="form-text text-muted">Select one or more PDF files</small>
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
