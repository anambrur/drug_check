@extends('layouts.admin.master')

@section('content')
    <!-- Form row -->
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <div class="d-md-flex justify-content-between align-items-center mb-20">
                    <h4 class="card-title">Edit Employee
                        <!-- Button -->
                        <a id="hoverButton" class="iyzi-btn"><i class="fas fa-camera"></i> {{ __('content.view_draft') }}</a>
                        <!-- Modal -->
                        <div id="imageModal" class="border border-success iyzi-modal">
                            <img class="img-fluid " src="{{ asset('uploads/img/dummy/style/employee-style1.jpg') }}"
                                alt="draft image">
                        </div>
                    </h4>
                    <div>
                        <a href="{{ url()->previous() }}" class="btn btn-primary"><i class="fas fa-angle-left"></i>
                            {{ __('content.back') }}</a>
                    </div>
                </div>
                @if ($demo_mode == 'on')
                    <!-- Include Alert Blade -->
                    @include('admin.demo_mode.demo-mode')
                @else
                    <form action="{{ route('client-profile.employee_update', $employee->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                @endif

                <input type="hidden" name="client_profile_id" value="{{ $employee->client_profile_id }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="first_name">First Name <span class="text-red">*</span></label>
                                    <input id="first_name" name="first_name" value="{{ $employee->first_name }}" type="text" class="form-control"
                                        placeholder="Enter First Name" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="department">Department</label>
                                    <input id="department" name="department" value="{{ $employee->department }}" type="text" class="form-control"
                                        placeholder="Enter Department">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="date_of_birth">Date of Birth</label>
                                    <input id="date_of_birth" name="date_of_birth" value="{{ $employee->date_of_birth }}" type="date" class="form-control">
                                </div>
                            </div>



                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="last_name">Last Name <span class="text-red">*</span></label>
                                    <input id="last_name" name="last_name" value="{{ $employee->last_name }}" type="text" class="form-control"
                                        placeholder="Enter Last Name" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="shift">Shift</label>
                                    <input id="shift" name="shift" value="{{ $employee->shift }}" type="text" class="form-control"
                                        placeholder="Enter Shift">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input id="start_date" name="start_date" value="{{ $employee->start_date }}" type="date" class="form-control">
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="middle_name">Middle Name / Initial</label>
                                    <input id="middle_name" name="middle_name" value="{{ $employee->middle_name }}" type="text" class="form-control"
                                        placeholder="Enter Middle Name">
                                </div>
                            </div>

                            <div class="col-xl-12">
                                <div class="form-group">
                                    <label for="dot" class="col-form-label">DOT</label>
                                    <select class="form-control" name="dot" id="dot">
                                        <option value="published" selected>{{ __('content.select_your_option') }}</option>
                                        <option value="yes">YES</option>
                                        <option value="no">FMCSA</option>
                                        <option value="no">FRA</option>
                                        <option value="no">FTA</option>
                                        <option value="no">FAA</option>
                                        <option value="no">PHMSA</option>
                                        <option value="no">RSPA</option>
                                        <option value="no">USCG</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input id="end_date" name="end_date" value="{{ $employee->end_date }}" type="date" class="form-control">
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="employee_id">Employee ID <span class="text-red">*</span></label>
                                    <input id="employee_id" name="employee_id" value="{{ $employee->employee_id }}" type="text" class="form-control"
                                        placeholder="Enter Employee ID">
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="background_check_date">Background Check Date</label>
                                    <input id="background_check_date" name="background_check_date" value="{{ $employee->background_check_date }}" type="date"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="ssn">SSN / SIN (reference only)</label>
                                    <input id="ssn" name="ssn" type="text" value="{{ $employee->ssn }}" class="form-control"
                                        placeholder="Enter SSN">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="email">Email <span class="text-red">*</span></label>
                            <input id="email" name="email" value="{{ $employee->email }}" type="email" class="form-control"
                                placeholder="Enter Email">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input id="phone" name="phone" value="{{ $employee->phone }}" type="text" class="form-control"
                                placeholder="Enter Phone">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="short_description">{{ __('content.short_description') }}</label>
                            <textarea id="short_description" name="short_description" class="form-control" rows="3"> {{ $employee->short_description }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cdl_state">CDL State/Province</label>
                            <select class="form-control" name="cdl_state" id="cdl_state">
                                <option disabled {{ $employee->cdl_state == null ? 'selected' : '' }}>{{ __('content.select_your_option') }}</option>
                                @foreach([
                                    'AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','FL'=>'Florida',
                                    'GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois','IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine',
                                    'MD'=>'Maryland','MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada',
                                    'NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York','NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma',
                                    'OR'=>'Oregon','PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah',
                                    'VT'=>'Vermont','VA'=>'Virginia','WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming'
                                ] as $abbr => $state)
                                    <option value="{{ $abbr }}" {{ $employee->cdl_state == $abbr ? 'selected' : '' }}>
                                        {{ $state }}
                                    </option>
                                @endforeach
                            </select>
                            
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cdl_number">CDL Number</label>
                            <input id="cdl_number" name="cdl_number" value="{{ $employee->cdl_number }}" type="text" class="form-control"
                                placeholder="Enter CDL Number">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status" class="col-form-label">{{ __('content.status') }}</label>
                            <select class="form-control" name="status" id="status">
                                <option value="active" selected>{{ __('content.select_your_option') }}</option>
                                <option value="active"
                                    {{ $employee->status == 'active' ? 'selected' : '' }}>
                                    Active</option>
                                <option value="inactive" {{ $employee->status == 'inactive"' ? 'selected' : '' }}>
                                    Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-center mt-3">
                        <button type="submit" class="btn btn-primary w-100">Update</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end row -->
@endsection
