@extends('layouts.admin.master')

@section('content')
    <!-- Form row -->
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <div class="d-md-flex justify-content-between align-items-center mb-20">
                    <h4 class="card-title">Edit Panel</h4>
                   
                    <div>
                        <a href="{{ url()->previous() }}" class="btn btn-primary"><i class="fas fa-angle-left"></i>
                            {{ __('content.back') }}</a>
                    </div>
                </div>
                @if ($demo_mode == 'on')
                    <!-- Include Alert Blade -->
                    @include('admin.demo_mode.demo-mode')
                @else
                    <form action="{{ route('panel-list.update', $panel->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                @endif

                <div class="row">
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="drug_name">Drug Name <span class="text-red">*</span></label>
                                    <input id="drug_name" name="drug_name" value="{{ $panel->drug_name }}" type="text" class="form-control"
                                        placeholder="Enter Drug Name" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="drug_code">Drug Code <span
                                            class="text-red">*</span></label>
                                    <input id="drug_code" name="drug_code" value="{{ $panel->drug_code }}" type="text"
                                        class="form-control" placeholder="Enter Drug Code" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="cut_off_level">Default Cut of Level <span
                                        class="text-red">*</span></label>
                                    <input id="cut_off_level" name="cut_off_level" value="{{ $panel->cut_off_level }}" type="text"
                                        class="form-control" placeholder="Default Cut of Level ">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="conf_level">Default Confirmation Level <span
                                        class="text-red">*</span></label>
                                    <input id="conf_level" name="conf_level" value="{{ $panel->conf_level }}" type="text"
                                        class="form-control" placeholder="Default Confirmation Level ">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status" class="col-form-label">{{ __('content.status') }}</label>
                            <select class="form-control" name="status" id="status">
                                <option value="active" selected>{{ __('content.select_your_option') }}</option>
                                <option value="active" {{ $panel->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $panel->status == 'inactive' ? 'selected' : '' }}>Inactive
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
