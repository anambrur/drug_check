@extends('layouts.admin.master')

@section('content')
    <!-- Form row -->
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <div class="d-md-flex justify-content-between align-items-center mb-20">
                    <h4 class="card-title">Edit DOT Agency</h4>

                    <div>
                        <a href="{{ url()->previous() }}" class="btn btn-primary"><i class="fas fa-angle-left"></i>
                            {{ __('content.back') }}</a>
                    </div>
                </div>
                @if ($demo_mode == 'on')
                    <!-- Include Alert Blade -->
                    @include('admin.demo_mode.demo-mode')
                @else
                    <form action="{{ route('dot-agency-list.update', $dotAgency->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                @endif

                <div class="row">
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="full_name">Full Name <span class="text-red">*</span></label>
                                    <input id="full_name" name="full_name"
                                        value="{{ $dotAgency->full_name }}" type="text" class="form-control"
                                        placeholder="Enter Full Name" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="dot_agency_name">Dot Agency Name <span
                                            class="text-red">*</span></label>
                                    <input id="dot_agency_name" name="dot_agency_name"
                                        value="{{ $dotAgency->dot_agency_name }}" type="text" class="form-control"
                                        placeholder="Enter Dot Agency Name" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status" class="col-form-label">{{ __('content.status') }}</label>
                            <select class="form-control" name="status" id="status">
                                <option value="active" selected>{{ __('content.select_your_option') }}</option>
                                <option value="active" {{ $dotAgency->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $dotAgency->status == 'inactive' ? 'selected' : '' }}>Inactive
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
