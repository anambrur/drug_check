@extends('layouts.admin.master')

@section('content')
    <!-- Form row -->
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">

                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h3 class="card-title">File Report</h3>
                    <button id="printButton" class="btn btn-primary">
                        <i class="fa fa-print"></i> Print
                    </button>
                </div>
                <div id="printable-section">
                    <div class="row print-header">

                        <div class="col-md-4 print-image">
                            <div class="media">
                                @if (!empty($header_image->section_image))
                                    <a class="d-block mx-auto" href="#" data-toggle="tooltip" data-placement="top"
                                        data-original-title="{{ __('content.current_image') }}">
                                        <img src="{{ asset('uploads/img/general/' . $header_image->section_image) }}"
                                            alt="logo image" class="rounded">
                                    </a>
                                @else
                                    <a class="d-block mx-auto" href="#" data-toggle="tooltip" data-placement="top"
                                        data-original-title="{{ __('content.not_yet_created') }}">
                                        <img src="{{ asset('uploads/img/dummy/no-image.jpg') }}" alt="no image"
                                            class="rounded w-25">
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="table-responsive">
                        <table id="basic-datatable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Collected</th>
                                    <th>Client / Company</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Employee ID</th>
                                    <th>Department</th>
                                    <th>Shift</th>
                                    <th>DOT</th>
                                    <th>Reason</th>
                                    <th>{{ __('content.status') }}</th>
                                    <th>Result</th>
                                    <th>Test</th>
                                    {{-- <th class="custom-width-action">{{ __('content.action') }}</th> --}}
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($recoding_results as $result)
                                    <tr>
                                        <td>{{ $result->id ?? 'N/A' }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($result->collection_datetime)->format('Y-m-d h:i A') }}
                                        </td>

                                        <td>{{ $result->clientProfile->company_name ?? 'N/A' }}</td>
                                        <td>{{ $result->employee->first_name ?? 'N/A' }}</td>
                                        <td>{{ $result->employee->last_name ?? 'N/A' }}</td>
                                        <td>{{ $result->employee->employee_id ?? 'N/A' }}</td>
                                        <td>{{ $result->employee->department ?? 'N/A' }}</td>
                                        <td>{{ $result->employee->shift ?? 'N/A' }}</td>
                                        <td>{{ $result->employee->dot ?? 'N/A' }}</td>
                                        <td>{{ $result->reason_for_test ?? 'N/A' }}</td>
                                        <td>
                                            @if ($result->employee->status == 'active')
                                                <span class="badge badge-pill badge-success">Active</span>
                                            @else
                                                <span class="badge badge-pill badge-danger">Collection Only</span>
                                            @endif
                                        </td>
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
                                        <td>{{ $result->testAdmin->test_name ?? 'N/A' }}</td>
                                        {{-- <td>
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
                                        </td> --}}
                                    </tr>

                                    <!-- Modal -->
                                    <div class="modal fade" id="deleteModal{{ $result->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="resultModalCenterTitle" aria-hidden="true">
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
                                    <div class="modal fade" id="notifyModal{{ $result->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="notifyModalTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="notifyModalTitle">Notify Client:
                                                        {{ $result->clientProfile->company_name }} of Test Results and
                                                        Random
                                                        Selections</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form method="POST"
                                                    action="{{ route('result-recording.send-notification', $result->id) }}"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>The following email may be sent to the client:</p>

                                                        <div class="email-preview p-3 mb-3"
                                                            style="background-color: #f8f9fa; border-radius: 5px;">
                                                            <p><strong>Subject:</strong> You have new test results and new
                                                                random
                                                                selections from Skyros Drug Checks Inc</p>

                                                            <p>Hello {{ $result->employee->first_name ?? '' }}
                                                                {{ $result->employee->last_name ?? '' }},</p>

                                                            <p>Skyros Drug Checks Inc has added new test results to your
                                                                company
                                                                portal.
                                                            </p>

                                                            <p> <a href="{{ route('result-recording.index') }}">Click
                                                                    here</a>
                                                                to
                                                                view
                                                                all results for
                                                                <strong>{{ $result->clientProfile->company_name }}</strong>.
                                                            </p>

                                                            <p>Also:</p>

                                                            <p>Skyros Drug Checks Inc has added new random selections to
                                                                your
                                                                company
                                                                portal.</p>

                                                            <p><a href="{{ route('result-recording.index') }}">Click
                                                                    here</a>
                                                                to
                                                                view
                                                                all selections for
                                                                <strong>{{ $result->clientProfile->company_name }}</strong>.
                                                            </p>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="additionalText{{ $result->id }}">Add text you
                                                                would
                                                                like
                                                                to
                                                                include in the email</label>
                                                            <textarea name="additional_text" class="form-control" id="additionalText{{ $result->id }}" rows="3"></textarea>
                                                        </div>

                                                        <div class="text-muted small">
                                                            (No footer text has been configured to append all client
                                                            notification
                                                            emails)
                                                        </div>

                                                        <hr>

                                                        <div class="client-info">
                                                            <p><strong>Client:</strong>
                                                                {{ $result->clientProfile->company_name }}
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

                                                        <!-- Add this new section for file upload -->
                                                        <div class="form-group">
                                                            <label for="pdfAttachment{{ $result->id }}">Attach PDF File
                                                                (optional)</label>
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input"
                                                                    id="pdfAttachment{{ $result->id }}"
                                                                    name="pdf_attachment" accept=".pdf">
                                                                <label class="custom-file-label"
                                                                    for="pdfAttachment{{ $result->id }}">Choose
                                                                    file</label>
                                                            </div>
                                                            <small class="form-text text-muted">Maximum file size:
                                                                5MB</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Send
                                                            Notification</button>
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
        @endpush
