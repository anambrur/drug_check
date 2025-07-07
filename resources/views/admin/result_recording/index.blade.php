@extends('layouts.admin.master')

@section('content')
    <!-- Form row -->
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">Add Result Recording</h4>

                </h4>
                @if ($demo_mode == 'on')
                    <!-- Include Alert Blade -->
                    @include('admin.demo_mode.demo-mode')
                @else
                    <form action="{{ route('result-recording.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                @endif


                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="company_id" class="col-form-label">Company Name <span
                                    class="text-red">*</span></label>
                            <select class="form-control select2" name="company_id" id="company_id">
                                <option value="" selected>{{ __('content.select_your_option') }}</option>
                                @foreach ($clientProfiles as $clientProfile)
                                    @if ($clientProfile)
                                        <option value="{{ $clientProfile->id ?? '' }}">
                                            {{ $clientProfile->company_name ?? '' }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="reason_for_test" class="col-form-label">Reason For Test <span
                                    class="text-red">*</span></label>
                            <select class="form-control select2" name="reason_for_test" id="reason_for_test">
                                <option value="" disabled selected>Choose an option</option>
                                <option value="Follow Up Test">Follow Up Test</option>
                                <option value="Pre Employment">Pre Employment</option>
                                <option value="Random">Random</option>
                                <option value="Return to Duty">Return to Duty</option>
                                <option value="Post Accident">Post Accident</option>
                                <option value="Promotion">Promotion</option>
                                <option value="Reasonable Cause/Suspicion">Reasonable Cause/Suspicion</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="perform_test" class="col-form-label">Perform Test <span
                                    class="text-red">*</span></label>
                            <select class="form-control select2" name="perform_test" id="perform_test">
                                <option value="" selected>{{ __('content.select_your_option') }}</option>
                                @foreach ($test_admins as $test_admin)
                                    @if ($test_admin)
                                        <option value="{{ $test_admin->id ?? '' }}">
                                            {{ $test_admin->test_name ?? '' }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="laboratory_id" class="col-form-label">Laboratory Name</label>
                            <select class="form-control select2" name="laboratory_id" id="laboratory_id">
                                <option value="" selected>{{ __('content.select_your_option') }}</option>
                                @foreach ($laboratories as $laboratory)
                                    @if ($laboratory)
                                        <option value="{{ $laboratory->id ?? '' }}">
                                            {{ $laboratory->laboratory_name ?? '' }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="mro_id" class="col-form-label">MRO Name</label>
                            <select class="form-control select2" name="mro_id" id="mro_id">
                                <option value="" selected>{{ __('content.select_your_option') }}</option>
                                @foreach ($mros as $mro)
                                    @if ($mro)
                                        <option value="{{ $mro->id ?? '' }}">
                                            {{ $mro->doctor_name ?? '' }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="collection_location" class="col-form-label">Collection Location </label>
                            <select class="form-control select2" name="collection_location" id="collection_location">
                                <option value="" disabled selected>Choose an option</option>
                                <option value="Branch Office">Branch Office</option>
                                <option value="Main Office">Main Office</option>
                                <option value="Mobile Collection Site">Mobile Collection Site</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3" id="employee_section" style="display: none;">
                        <div class="form-group">
                            <label for="employee_id" class="col-form-label">Employee Name <span
                                    class="text-red">*</span></label>
                            <select class="form-control select2" name="employee_id" id="employee_id">
                                <!-- Options will be loaded dynamically -->
                            </select>
                        </div>
                    </div>

                    <!-- Your existing HTML -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_of_collection">Date of Collection<span class="text-red">*</span></label>
                            <input id="date_of_collection" name="date_of_collection" value="{{ date('Y-m-d') }}"
                                type="date" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="time_of_collection">Time of Collection<span class="text-red">*</span></label>
                            <input id="time_of_collection" name="time_of_collection" type="time" class="form-control"
                                value="{{ date('H:i') }}" required> <!-- Default to current time -->
                        </div>
                    </div>

                    <!-- Hidden field to store combined value -->
                    <input type="hidden" id="collection_datetime" name="collection_datetime">



                    <div class="col-md-12 mt-3" id="panel_test" style="display: none;">
                        <!-- Dynamic panel test content -->
                    </div>


                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status" class="col-form-label">{{ __('content.status') }} </label>
                            <select class="form-control" name="status" id="status">
                                <option value="pending" selected>{{ __('content.select_your_option') }}</option>
                                <option value="positive">Positive</option>
                                <option value="negative">Negative</option>
                                <option value="refused">Refused</option>
                                <option value="excused">Excused</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="pending">Pending</option>
                                <option value="saved">Saved</option>
                                <option value="collection_only">Collection Only</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="note">Note</label>
                            <textarea id="note" name="note" class="form-control" rows="3"></textarea>
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
                            <th>Collected</th>
                            <th>Client / Company</th>
                            <th>Employee</th>
                            <th>Reason</th>
                            <th>Test</th>
                            <th>{{ __('content.status') }}</th>
                            <th class="custom-width-action">{{ __('content.action') }}</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($recoding_results as $result)
                            <tr>
                                <td>
                                    {{ \Carbon\Carbon::parse($result->collection_datetime)->format('Y-m-d h:i A') }}
                                </td>

                                <td>{{ $result->clientProfile->company_name }}</td>
                                <td>{{ $result->employee->first_name }} {{ $result->employee->first_name }}</td>
                                <td>{{ $result->reason_for_test }}</td>
                                <td>{{ $result->testAdmin->test_name }}</td>
                                <td>
                                    @if ($result->status == 'positive')
                                        <span class="badge badge-pill badge-danger">Positive</span>
                                    @elseif ($result->status == 'negative')
                                        <span class="badge badge-pill badge-success">Negative</span>
                                    @elseif ($result->status == 'refused')
                                        <span class="badge badge-pill badge-dark">Refused</span>
                                    @elseif ($result->status == 'excused')
                                        <span class="badge badge-pill badge-warning">Excused</span>
                                    @elseif ($result->status == 'cancelled')
                                        <span class="badge badge-pill badge-secondary">Cancelled</span>
                                    @elseif ($result->status == 'pending')
                                        <span class="badge badge-pill badge-info">Pending</span>
                                    @elseif ($result->status == 'saved')
                                        <span class="badge badge-pill badge-primary">Saved</span>
                                    @elseif ($result->status == 'collection_only')
                                        <span class="badge badge-pill badge-light">Collection Only</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @can('result recording edit')
                                            <a href="{{ route('result-recording.edit', $result->id) }}" class="mr-2">
                                                <i class="fa fa-edit text-info font-18"></i>
                                            </a>
                                        @endcan

                                        @can('result recording view')
                                            <a href="{{ route('result-recording.show', $result->id) }}" class="mr-2">
                                                <i class="fa fa-eye text-success font-18"></i>
                                            </a>
                                        @endcan

                                        @can('result recording delete')
                                            <a href="#" data-toggle="modal"
                                                data-target="#deleteModal{{ $result->id }}">
                                                <i class="fa fa-trash text-danger font-18 mr-2"></i>
                                            </a>
                                        @endcan

                                        @can('result recording edit')
                                            <a href="#" data-toggle="modal"
                                                data-target="#notifyModal{{ $result->id }}">
                                                <i class="fa fa-send-o font-18"></i>
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal -->
                            <div class="modal fade" id="deleteModal{{ $result->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="resultModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="resultModalCenterTitle">
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
                                                    action="{{ route('result-recording.destroy', $result->id) }}"
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


                            <!-- Modal2 -->
                            <div class="modal fade" id="notifyModal{{ $result->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="notifyModalTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="notifyModalTitle">Notify Client:
                                                {{ $result->clientProfile->company_name }} of Test Results and Random
                                                Selections</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form method="POST"
                                            action="{{ route('result-recording.send-notification', $result->id) }}">
                                            @csrf
                                            <div class="modal-body">
                                                <p>The following email may be sent to the client:</p>

                                                <div class="email-preview p-3 mb-3"
                                                    style="background-color: #f8f9fa; border-radius: 5px;">
                                                    <p><strong>Subject:</strong> You have new test results and new random
                                                        selections from Skyros Drug Checks Inc</p>

                                                    <p>Hello {{ $result->employee->first_name }}
                                                        {{ $result->employee->last_name }},</p>

                                                    <p>Skyros Drug Checks Inc has added new test results to your company
                                                        portal.
                                                    </p>

                                                    <p> <a href="{{ route('result-recording.index') }}">Click here</a> to
                                                        view
                                                        all results for
                                                        <strong>{{ $result->clientProfile->company_name }}</strong>.
                                                    </p>

                                                    <p>Also:</p>

                                                    <p>Skyros Drug Checks Inc has added new random selections to your
                                                        company
                                                        portal.</p>

                                                    <p><a href="{{ route('result-recording.index') }}">Click here</a> to
                                                        view
                                                        all selections for
                                                        <strong>{{ $result->clientProfile->company_name }}</strong>.
                                                    </p>
                                                </div>

                                                <div class="form-group">
                                                    <label for="additionalText{{ $result->id }}">Add text you would like
                                                        to
                                                        include in the email</label>
                                                    <textarea name="additional_text" class="form-control" id="additionalText{{ $result->id }}" rows="3"></textarea>
                                                </div>

                                                <div class="text-muted small">
                                                    (No footer text has been configured to append all client notification
                                                    emails)
                                                </div>

                                                <hr>

                                                <div class="client-info">
                                                    <p><strong>Client:</strong> {{ $result->clientProfile->company_name }}
                                                        <strong>Phone:</strong>
                                                        {{ $result->clientProfile->phone ?? 'N/A' }}
                                                    </p>
                                                    <p><strong>Date:</strong>
                                                        {{ \Carbon\Carbon::parse($result->collection_datetime)->format('m/d/Y') }}
                                                    </p>
                                                    <p><strong>DER Contact:</strong>
                                                        {{ $result->clientProfile->der_contact_name ?? 'N/A' }}
                                                        <strong>Email:</strong>
                                                        {{ $result->clientProfile->der_contact_email ?? 'N/A' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Send Notification</button>
                                            </div>
                                        </form>
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

@push('script')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                placeholder: "{{ __('content.select_your_option') }}",
                allowClear: true
            });

            // Cache the DOM elements
            const $dateInput = $('#date_of_collection');
            const $timeInput = $('#time_of_collection');
            const $datetimeField = $('#collection_datetime');

            // Function to update the combined datetime
            function updateDateTime() {
                // Combine date + time (format: YYYY-MM-DD HH:MM:SS)
                $datetimeField.val($dateInput.val() + ' ' + $timeInput.val() + ':00');
            }

            // Update on change events
            $dateInput.on('change', updateDateTime);
            $timeInput.on('change', updateDateTime);

            // Initialize with default values
            updateDateTime();


            $('#company_id').change(function() {
                let company_id = $(this).val();
                const $employeeSection = $('#employee_section');
                const $employeeSelect = $('#employee_id');

                // Hide section if no company selected
                if (!company_id) {
                    $employeeSection.hide();
                    $employeeSelect.val('').trigger('change');
                    return;
                }

                // Show loading state
                $employeeSection.show();
                $employeeSelect.html('<option value="">Loading employees...</option>');

                $.ajax({
                    url: "{{ route('result-recording.get-empoyees') }}",
                    type: "GET",
                    data: {
                        company_id: company_id
                    },
                    success: function(data) {
                        // Show the section
                        $employeeSection.show();

                        // Populate employees
                        $employeeSelect.empty().append(
                            '<option value="">{{ __('content.select_your_option') }}</option>'
                        );

                        $.each(data, function(key, value) {
                            $employeeSelect.append(
                                '<option value="' + value.id + '">' +
                                value.first_name + ' ' + value.last_name +
                                '</option>'
                            );
                        });

                        // Refresh Select2 if you're using it
                        if ($.fn.select2) {
                            $employeeSelect.select2();
                        }
                    },
                    error: function() {
                        $employeeSelect.html(
                            '<option value="">Error loading employees</option>');
                    }
                });
            });

            $('#perform_test').change(function() {
                const testId = $(this).val();
                const $panelSection = $('#panel_test');

                // Hide panel section if no test selected
                if (!testId) {
                    $panelSection.hide();
                    return;
                }

                // Show loading state
                $panelSection.html('<div class="text-center p-4">Loading test panel...</div>')
                    .show();

                $.ajax({
                    url: "{{ route('result-recording.get-panel-test') }}",
                    type: "GET",
                    data: {
                        id: testId
                    },
                    success: function(response) {
                        if (response && response.panel && response.panel.length >
                            0) {
                            // Build the panel table HTML
                            let tableHtml = `
                        <div class="card">
                            <div class="card-header">
                                <h6>Test Panel Results</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Drug Name</th>
                                                <th>Drug Code</th>
                                                <th>Negative</th>
                                                <th>Positive</th>
                                                <th>Cut-Off Level</th>
                                                <th>Conf. Level</th>
                                            </tr>
                                        </thead>
                                        <tbody>`;

                            // Add rows for each panel item
                            response.panel.forEach(function(panel) {
                                tableHtml += `
                            <tr>
                                <td>${panel.drug_name}</td>
                                <td>${panel.drug_code}</td>
                                <td>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio"
                                            name="panel_results[${panel.id}][result]"
                                            id="panel_${panel.id}_negative" value="negative"
                                            >
                                        <label class="form-check-label"
                                            for="panel_${panel.id}_negative">Negative</label>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio"
                                            name="panel_results[${panel.id}][result]"
                                            id="panel_${panel.id}_positive" value="positive">
                                        <label class="form-check-label"
                                            for="panel_${panel.id}_positive">Positive</label>
                                    </div>
                                </td>
                                <td>${panel.cut_off_level} ng/mL</td>
                                <td>${panel.conf_level} ng/mL</td>
                                <input type="hidden"
                                    name="panel_results[${panel.id}][panel_id]"
                                    value="${panel.id}">
                                <input type="hidden"
                                    name="panel_results[${panel.id}][drug_name]"
                                    value="${panel.drug_name}">
                                <input type="hidden"
                                    name="panel_results[${panel.id}][drug_code]"
                                    value="${panel.drug_code}">
                            </tr>`;
                            });

                            tableHtml += `</tbody></table></div></div></div>`;

                            // Update the panel section
                            $panelSection.html(tableHtml).show();
                        } else {
                            $panelSection.html(
                                '<div class="alert alert-info">No panel tests available for this selection</div>'
                            ).show();
                        }
                    },
                    error: function() {
                        $panelSection.html(
                            '<div class="alert alert-danger">Error loading panel tests</div>'
                        ).show();
                    }
                });
            });

        });
    </script>
@endpush
