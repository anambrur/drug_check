@extends('layouts.admin.master')

@section('content')
    <!-- Form row -->
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h2 class="card-title">Client Profile</h2>
                @if ($demo_mode == 'on')
                    <!-- Include Alert Blade -->
                    @include('admin.demo_mode.demo-mode')
                @else
                    <form action="{{ route('client-profile.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                @endif

                <div class="row">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="label_title">Company Name:</p>
                            </div>
                            <div class="col-md-6">
                                <p>{{ $clientProfile->company_name }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="label_title">Short Description:</p>
                            </div>
                            <div class="col-md-6">
                                <p>{{ $clientProfile->short_description }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="label_title">Address:</p>
                            </div>
                            <div class="col-md-6">
                                <p>{{ $clientProfile->address }}, {{ $clientProfile->city }}, {{ $clientProfile->state }},
                                    {{ $clientProfile->zip }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="label_title">Phone:</p>
                            </div>
                            <div class="col-md-6">
                                <p>{{ $clientProfile->phone }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="label_title">Fax:</p>
                            </div>
                            <div class="col-md-6">
                                <p>{{ $clientProfile->fax }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="label_title">Shipping Address:</p>
                            </div>
                            <div class="col-md-6">
                                <p>{{ $clientProfile->shipping_address }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="label_title">Billing Contact Name:</p>
                            </div>
                            <div class="col-md-6">
                                <p>{{ $clientProfile->billing_contact_name }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="label_title">Billing Contact Email:</p>
                            </div>
                            <div class="col-md-6">
                                <p>{{ $clientProfile->billing_contact_email }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="label_title">Billing Contact Phone:</p>
                            </div>
                            <div class="col-md-6">
                                <p>{{ $clientProfile->billing_contact_phone }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="label_title">DER Contact Name:</p>
                            </div>
                            <div class="col-md-6">
                                <p>{{ $clientProfile->der_contact_name }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="label_title">DER Contact Email:</p>
                            </div>
                            <div class="col-md-6">
                                <p>{{ $clientProfile->der_contact_email }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="label_title">DER Contact Phone:</p>
                            </div>
                            <div class="col-md-6">
                                <p>{{ $clientProfile->der_contact_phone }}</p>
                            </div>
                        </div>
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
                <h4 class="card-title">Add Employee

                </h4>
                @if ($demo_mode == 'on')
                    <!-- Include Alert Blade -->
                    @include('admin.demo_mode.demo-mode')
                @else
                    <form action="{{ route('client-profile.employee_store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                @endif

                <input type="hidden" name="client_profile_id" value="{{ $clientProfile->id }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="first_name">First Name <span class="text-red">*</span></label>
                                    <input id="first_name" name="first_name" type="text" class="form-control"
                                        placeholder="Enter First Name" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="department">Department</label>
                                    <input id="department" name="department" type="text" class="form-control"
                                        placeholder="Enter Department">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="date_of_birth">Date of Birth</label>
                                    <input id="date_of_birth" name="date_of_birth" type="date" class="form-control">
                                </div>
                            </div>



                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="last_name">Last Name <span class="text-red">*</span></label>
                                    <input id="last_name" name="last_name" type="text" class="form-control"
                                        placeholder="Enter Last Name" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="shift">Shift</label>
                                    <input id="shift" name="shift" type="text" class="form-control"
                                        placeholder="Enter Shift">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input id="start_date" name="start_date" type="date" class="form-control">
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="middle_name">Middle Name / Initial</label>
                                    <input id="middle_name" name="middle_name" type="text" class="form-control"
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
                                    <input id="end_date" name="end_date" type="date" class="form-control">
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="employee_id">Employee ID <span class="text-red">*</span></label>
                                    <input id="employee_id" name="employee_id" type="text" class="form-control"
                                        placeholder="Enter Employee ID">
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="background_check_date">Background Check Date</label>
                                    <input id="background_check_date" name="background_check_date" type="date"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="ssn">SSN / SIN (reference only)</label>
                                    <input id="ssn" name="ssn" type="text" class="form-control"
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
                            <input id="email" name="email" type="email" class="form-control"
                                placeholder="Enter Email">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input id="phone" name="phone" type="text" class="form-control"
                                placeholder="Enter Phone">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="short_description">{{ __('content.short_description') }}</label>
                            <textarea id="short_description" name="short_description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cdl_state">CDL State/Province</label>
                            <select class="form-control" name="cdl_state" id="cdl_state">
                                <option selected>{{ __('content.select_your_option') }}</option>
                                @foreach(['AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois','IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine','MD'=>'Maryland','MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada','NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York','NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon','PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont','VA'=>'Virginia','WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming'] as $abbr => $state)
                                    <option value="{{ $abbr }}">{{ $state }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cdl_number">CDL Number</label>
                            <input id="cdl_number" name="cdl_number" type="text" class="form-control"
                                placeholder="Enter CDL Number">
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
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Middle Name</th>
                        <th>Department</th>
                        <th>Shift</th>
                        <th>Date Of Birth</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Employee ID</th>
                        <th>Background Check Date</th>
                        <th>SSN</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Description</th>
                        <th>CDL State</th>
                        <th>CDL Number</th>
                        <th>{{ __('content.status') }}</th>
                        <th class="custom-width-action">{{ __('content.action') }}</th>
                    </tr>
                    </thead>

                    <tbody>
                        
                    @foreach ($clientProfile->employees as $employee)
                        <tr>
                            <td>{{ $employee->first_name }}</td>
                            <td>{{ $employee->last_name }}</td>
                            <td>{{ $employee->middle_name }}</td>
                            <td>{{ $employee->department }}</td>
                            <td>{{ $employee->shift }}</td>
                            <td>{{ $employee->date_of_birth }}</td>
                            <td>{{ $employee->start_date }}</td>
                            <td>{{ $employee->end_date }}</td>
                            <td>{{ $employee->employee_id }}</td>
                            <td>{{ $employee->background_check_date }}</td>
                            <td>{{ $employee->ssn }}</td>
                            <td>{{ $employee->email }}</td>
                            <td>{{ $employee->phone }}</td>
                            <td>{{ $employee->short_description }}</td>
                            <td>{{ $employee->cdl_state }}</td>
                            <td>{{ $employee->cdl_number }}</td>
                            
                            <td>
                                @if ($employee->status == "active")
                                    <span class="badge badge-pill badge-success">Active</span>
                                @else
                                    <span class="badge badge-pill badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <a href="{{ route('client-profile.employee_edit', $employee->id) }}" class="mr-2">
                                        <i class="fa fa-edit text-info font-18"></i>
                                    </a>
                                    
                                    <a href="#" data-toggle="modal" data-target="#deleteModal{{ $employee->id }}">
                                        <i class="fa fa-trash text-danger font-18"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal -->
                        <div class="modal fade" id="deleteModal{{ $employee->id }}" tabindex="-1" role="dialog" aria-labelledby="employeeModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="employeeModalCenterTitle">{{ __('content.delete') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('content.close') }}">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-center">
                                        {{ __('content.you_wont_be_able_to_revert_this') }}
                                    </div>
                                    <div class="modal-footer">
                                        @if ($demo_mode == "on")
                                            <!-- Include Alert Blade -->
                                            @include('admin.demo_mode.demo-mode')
                                        @else
                                            <form class="d-inline-block" action="{{ route('client-profile.employee_destroy', $employee->id) }}" method="POST">
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
