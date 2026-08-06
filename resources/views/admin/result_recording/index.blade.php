@extends('layouts.admin.master')

@section('content')
    <!-- Form row -->
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body border-0 shadow-sm">
                <h4 class="card-title mb-3">Add Result Recording</h4>
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="note">Note</label>
                            <textarea id="note" name="note" class="form-control" rows="3"></textarea>
                        </div>
                    </div>

                    <!-- PDF Upload Section -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="pdf_file">Upload PDF Report</label>
                            <input type="file" class="form-control-file" id="pdf_file" name="pdf_file"
                                accept=".pdf,.PDF">
                            <small class="form-text text-muted">
                                Upload a PDF file (Max: 10MB). Leave empty to keep existing file.
                            </small>

                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="send_notification_toggle">Send Notification</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="send_notification_toggle" 
                                    name="send_notification" value="1">
                                <label class="custom-control-label" for="send_notification_toggle">
                                    Send notification to client after creating result
                                </label>
                            </div>
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


    @include('admin.result_recording.partials.list-section')
@endsection

@push('scripts')
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

        document.querySelectorAll('.custom-file-input').forEach(function(input) {
            input.addEventListener('change', function(e) {
                var fileName = e.target.files[0] ? e.target.files[0].name : "Choose file";
                var nextSibling = e.target.nextElementSibling;
                nextSibling.innerText = fileName;
            });
        });
    </script>
    @include('admin.result_recording.partials.datatable-scripts')
@endpush
