@extends('layouts.admin.master')

@section('content')
    <!-- Form row -->
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">Add MRO</h4>

                </h4>
                @if ($demo_mode == 'on')
                    <!-- Include Alert Blade -->
                    @include('admin.demo_mode.demo-mode')
                @else
                    <form action="{{ route('mro-list.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                @endif


                <div class="row">
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="company_name">Company Name <span class="text-red">*</span></label>
                                    <input id="company_name" name="company_name" type="text" class="form-control"
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
                                    <input id="doctor_name" name="doctor_name" type="text"
                                        class="form-control" placeholder="Enter Doctor Name" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="mro_address">MRO Address <span
                                        class="text-red">*</span></label>
                                    <input id="mro_address" name="mro_address" type="text"
                                        class="form-control" placeholder="Enter MRO Address ">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="image">Signature ({{ __('content.size') }} 320 x
                                80)(.svg, .png, .jpg, .jpeg)</label>
                            <input id="image" name="signature" type="file" class="form-control-file">
                            <small id="image"
                                class="form-text text-muted">{{ __('content.please_use_recommended_sizes') }}</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status" class="col-form-label">{{ __('content.status') }} </label>
                            <select class="form-control" name="status" id="status">
                                <option value="active" selected>{{ __('content.select_your_option') }}</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-center mt-3">
                        <button type="submit" class="btn btn-primary w-100">{{ __('content.submit') }}</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end row -->


    <!-- Form row -->
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <table id="basic-datatable" class="table table-striped dt-responsive w-100">
                    <thead>
                    <tr>
                        <th>Company Name</th>
                        <th>Doctor Name</th>
                        <th>MRO Address</th>
                        <th>Signature</th>
                        <th>{{ __('content.status') }}</th>
                        <th class="custom-width-action">{{ __('content.action') }}</th>
                    </tr>
                    </thead>

                    <tbody>
                        
                    @foreach ($mros as $mro)
                        <tr>
                            <td>{{ $mro->company_name }}</td>
                            <td>{{ $mro->doctor_name }}</td>
                            <td>{{ $mro->mro_address }}</td>
                            <td>
                                @if ($mro->status == "active")
                                    <span class="badge badge-pill badge-success">Active</span>
                                @else
                                    <span class="badge badge-pill badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @if (!empty($mro->signature))
                                    <img class="image-size img-fluid" src="{{ asset('uploads/img/blog/thumbnail/'.$mro->signature) }}" alt="blog image">
                                @else
                                    <img class="image-size img-fluid" src="{{ asset('uploads/img/dummy/no-image.jpg') }}" alt="no image">
                                @endif
                            </td>
                            <td>
                                <div>
                                    <a href="{{ route('mro-list.edit', $mro->id) }}" class="mr-2">
                                        <i class="fa fa-edit text-info font-18"></i>
                                    </a>
                                    
                                    <a href="#" data-toggle="modal" data-target="#deleteModal{{ $mro->id }}">
                                        <i class="fa fa-trash text-danger font-18"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal -->
                        <div class="modal fade" id="deleteModal{{ $mro->id }}" tabindex="-1" role="dialog" aria-labelledby="mroModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="mroModalCenterTitle">{{ __('content.delete') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('content.close') }}">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-center">
                                        {{ __('content.you_wont_be_able_to_revert_this') }}
                                    </div>
                                    <div class="modal-footer">
                                        @if ($demo_mode == 'on')
                                            <!-- Include Alert Blade -->
                                            @include('admin.demo_mode.demo-mode')
                                        @else
                                            <form class="d-inline-block" action="{{ route('mro-list.destroy', $mro->id) }}" method="POST">
                                                @method('DELETE')
                                                @csrf
                                                @endif

                                                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('content.cancel') }}</button>
                                                <button type="submit" class="btn btn-success">{{ __('content.yes_delete_it') }}</button>
                                            </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </tbody>
                </table>
               
            </div>
        </div>
    </div>
    <!-- end row -->
@endsection
