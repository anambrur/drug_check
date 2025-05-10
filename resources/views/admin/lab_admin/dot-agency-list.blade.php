@extends('layouts.admin.master')

@section('content')
    <!-- Form row -->
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">Add DOT Agency</h4>

                </h4>
                @if ($demo_mode == 'on')
                    <!-- Include Alert Blade -->
                    @include('admin.demo_mode.demo-mode')
                @else
                    <form action="{{ route('dot-agency-list.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                @endif


                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="full_name">Full Name </label>
                            <input id="full_name" name="full_name" type="text" class="form-control"
                                placeholder="Enter Full Name">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="dot_agency_name">Dot Agency Name <span class="text-red">*</span></label>
                            <input id="dot_agency_name" name="dot_agency_name" type="text" class="form-control"
                                placeholder="Enter Dot Agency Name" required>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status" class="col-form-label pt-0">{{ __('content.status') }} </label>
                            <select class="form-control" name="status" id="status">
                                <option value="active" selected>{{ __('content.select_your_option') }}</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-center mt-2">
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
                            <th>Full Name</th>
                            <th>Agency Name</th>
                            <th>{{ __('content.status') }}</th>
                            <th class="custom-width-action">{{ __('content.action') }}</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($dotAgencyLists as $agency)
                            <tr>
                                <td>{{ $agency->full_name }}</td>
                                <td>{{ $agency->dot_agency_name }}</td>
                                <td>
                                    @if ($agency->status == "active")
                                        <span class="badge badge-pill badge-success">Active</span>
                                    @else
                                        <span class="badge badge-pill badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <a href="{{ route('dot-agency-list.edit', $agency->id) }}" class="mr-2">
                                            <i class="fa fa-edit text-info font-18"></i>
                                        </a>

                                        <a href="#" data-toggle="modal"
                                            data-target="#deleteModal{{ $agency->id }}">
                                            <i class="fa fa-trash text-danger font-18"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal -->
                            <div class="modal fade" id="deleteModal{{ $agency->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="agencyModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="agencyModalCenterTitle">
                                                {{ __('content.delete') }}</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="{{ __('content.close') }}">
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
                                                <form class="d-inline-block"
                                                    action="{{ route('dot-agency-list.destroy', $agency->id) }}"
                                                    method="POST">
                                                    @method('DELETE')
                                                    @csrf
                                            @endif

                                            <button type="button" class="btn btn-danger"
                                                data-dismiss="modal">{{ __('content.cancel') }}</button>
                                            <button type="submit"
                                                class="btn btn-success">{{ __('content.yes_delete_it') }}</button>
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
