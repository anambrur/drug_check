@extends('layouts.admin.master')

@section('content')
    <!-- Form row -->

    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">Edit Result Recording</h4>
                @if ($demo_mode == 'on')
                    <!-- Include Alert Blade -->
                    @include('admin.demo_mode.demo-mode')
                @else
                    <form action="{{ route('result-recording.update', $recoding_result->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="company_id" class="col-form-label">Company Name <span
                                    class="text-red">*</span></label>
                            <select class="form-control select2" name="company_id" id="company_id">
                                <option value="">{{ __('content.select_your_option') }}</option>
                                @foreach ($clientProfiles as $clientProfile)
                                    <option value="{{ $clientProfile->id }}"
                                        {{ $recoding_result->company_id == $clientProfile->id ? 'selected' : '' }}>
                                        {{ $clientProfile->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="reason_for_test" class="col-form-label">Reason For Test <span
                                    class="text-red">*</span></label>
                            <select class="form-control select2" name="reason_for_test" id="reason_for_test">
                                <option value="" disabled>Choose an option</option>
                                @foreach (['Follow Up Test', 'Pre Employment', 'Random', 'Return to Duty', 'Post Accident', 'Promotion', 'Reasonable Cause/Suspicion', 'Other'] as $reason)
                                    <option value="{{ $reason }}"
                                        {{ $recoding_result->reason_for_test == $reason ? 'selected' : '' }}>
                                        {{ $reason }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="test_admin_id" class="col-form-label">Perform Test <span
                                    class="text-red">*</span></label>
                            <select class="form-control select2" name="test_admin_id" id="test_admin_id">
                                <option value="">{{ __('content.select_your_option') }}</option>
                                @foreach ($test_admins as $test_admin)
                                    <option value="{{ $test_admin->id }}"
                                        {{ $recoding_result->test_admin_id == $test_admin->id ? 'selected' : '' }}>
                                        {{ $test_admin->test_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="laboratory_id" class="col-form-label">Laboratory Name</label>
                            <select class="form-control select2" name="laboratory_id" id="laboratory_id">
                                <option value="">{{ __('content.select_your_option') }}</option>
                                @foreach ($laboratories as $laboratory)
                                    <option value="{{ $laboratory->id }}"
                                        {{ $recoding_result->laboratory_id == $laboratory->id ? 'selected' : '' }}>
                                        {{ $laboratory->laboratory_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="mro_id" class="col-form-label">MRO Name</label>
                            <select class="form-control select2" name="mro_id" id="mro_id">
                                <option value="">{{ __('content.select_your_option') }}</option>
                                @foreach ($mros as $mro)
                                    <option value="{{ $mro->id }}"
                                        {{ $recoding_result->mro_id == $mro->id ? 'selected' : '' }}>
                                        {{ $mro->doctor_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="collection_location" class="col-form-label">Collection Location</label>
                            <select class="form-control select2" name="collection_location" id="collection_location">
                                <option value="" disabled>Choose an option</option>
                                @foreach (['Branch Office', 'Main Office', 'Mobile Collection Site', 'Other'] as $location)
                                    <option value="{{ $location }}"
                                        {{ $recoding_result->collection_location == $location ? 'selected' : '' }}>
                                        {{ $location }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3" id="employee_section"
                        style="{{ $recoding_result->employee_id ? '' : 'display: none;' }}">
                        <div class="form-group">
                            <label for="employee_id" class="col-form-label">Employee Name <span
                                    class="text-red">*</span></label>
                            <select class="form-control select2" name="employee_id" id="employee_id">
                                <option value="">{{ __('content.select_your_option') }}</option>
                                @if ($recoding_result->employee_id)
                                    <option value="{{ $recoding_result->employee_id }}" selected>
                                        {{ $recoding_result->employee->first_name }}
                                        {{ $recoding_result->employee->last_name }}
                                    </option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_of_collection">Date of Collection<span class="text-red">*</span></label>
                            <input id="date_of_collection" name="date_of_collection"
                                value="{{ $recoding_result->date_of_collection }}" type="date" class="form-control"
                                required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="time_of_collection">Time of Collection<span class="text-red">*</span></label>
                            <input id="time_of_collection" name="time_of_collection"
                                value="{{ substr($recoding_result->time_of_collection, 0, 5) }}" type="time"
                                step="60" class="form-control" required>
                        </div>
                    </div>

                    <input type="hidden" id="collection_datetime" name="collection_datetime"
                        value="{{ $recoding_result->collection_datetime }}">

                    <div class="col-md-12 mt-3" id="panel_test">
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
                                        <tbody>
                                            @foreach ($recoding_result->resultPanel as $panel)
                                                <tr>
                                                    <td>{{ $panel->drug_name }}</td>
                                                    <td>{{ $panel->drug_code }}</td>
                                                    <td>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio"
                                                                name="panel_results[{{ $panel->panel_id }}][result]"
                                                                id="panel_{{ $panel->panel_id }}_negative"
                                                                value="negative"
                                                                {{ $panel->result == 'negative' ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="panel_{{ $panel->panel_id }}_negative">Negative</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio"
                                                                name="panel_results[{{ $panel->panel_id }}][result]"
                                                                id="panel_{{ $panel->panel_id }}_positive"
                                                                value="positive"
                                                                {{ $panel->result == 'positive' ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="panel_{{ $panel->panel_id }}_positive">Positive</label>
                                                        </div>
                                                    </td>
                                                    <td>{{ $panel->cut_off_level }} ng/mL</td>
                                                    <td>{{ $panel->conf_level }} ng/mL</td>
                                                    <input type="hidden"
                                                        name="panel_results[{{ $panel->panel_id }}][panel_id]"
                                                        value="{{ $panel->panel_id }}">
                                                    <input type="hidden"
                                                        name="panel_results[{{ $panel->panel_id }}][drug_name]"
                                                        value="{{ $panel->drug_name }}">
                                                    <input type="hidden"
                                                        name="panel_results[{{ $panel->panel_id }}][drug_code]"
                                                        value="{{ $panel->drug_code }}">
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status" class="col-form-label">{{ __('content.status') }}</label>
                            <select class="form-control" name="status" id="status">
                                @foreach (['positive', 'negative', 'refused', 'excused', 'cancelled', 'pending', 'saved', 'collection_only'] as $status)
                                    <option value="{{ $status }}"
                                        {{ $recoding_result->status == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- PDF Upload Section -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pdf_file">Upload PDF Report</label>
                            <input type="file" class="form-control-file" id="pdf_file" name="pdf_file"
                                accept=".pdf,.PDF">
                            <small class="form-text text-muted">
                                Upload a PDF file (Max: 10MB). Leave empty to keep existing file.
                            </small>

                            @if ($recoding_result->pdf_path)
                                <div class="mt-2">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf text-danger mr-2"></i>
                                        <a href="{{ asset($recoding_result->pdf_path) }}" target="_blank"
                                            class="mr-3">
                                            View Current PDF
                                        </a>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remove_pdf"
                                                id="remove_pdf" value="1">
                                            <label class="form-check-label text-danger ml-3" for="remove_pdf">
                                                Remove PDF
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="note">Note</label>
                            <textarea id="note" name="note" class="form-control" rows="3">{{ $recoding_result->note }}</textarea>
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

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                placeholder: "{{ __('content.select_your_option') }}",
                allowClear: true
            });

            // Update datetime field when date or time changes
            $('#date_of_collection, #time_of_collection').change(function() {
                $('#collection_datetime').val(
                    $('#date_of_collection').val() + ' ' + $('#time_of_collection').val() + ':00'
                );
            });

            // Show/hide remove PDF checkbox based on file input
            $('#pdf_file').change(function() {
                if (this.files.length > 0) {
                    $('#remove_pdf').prop('checked', false);
                }
            });


            // Load employees when company is selected
            $('#company_id').change(function() {
                let company_id = $(this).val();
                const $employeeSection = $('#employee_section');
                const $employeeSelect = $('#employee_id');

                if (!company_id) {
                    $employeeSection.hide();
                    $employeeSelect.val('').trigger('change');
                    return;
                }

                $employeeSection.show();
                $employeeSelect.html('<option value="">Loading employees...</option>');

                $.ajax({
                    url: "{{ route('result-recording.get-empoyees') }}",
                    type: "GET",
                    data: {
                        company_id: company_id
                    },
                    success: function(data) {
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

                        // Select the current employee if exists
                        @if ($recoding_result->employee_id)
                            $employeeSelect.val({{ $recoding_result->employee_id }}).trigger(
                                'change');
                        @endif
                    }
                });
            });

            // Test Admin Panel Loading
            $('#test_admin_id').change(function() {
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
                        if (response && response.panel && response.panel.length > 0) {
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
                                // Check if this panel exists in current results
                                const currentResult = @json($recoding_result->resultPanel->keyBy('panel_id')->toArray());
                                const isChecked = currentResult[panel.id] &&
                                    currentResult[panel.id].result === 'positive' ?
                                    'checked' : '';

                                tableHtml += `
                                    <tr>
                                        <td>${panel.drug_name}</td>
                                        <td>${panel.drug_code}</td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                    name="panel_results[${panel.id}][result]"
                                                    id="panel_${panel.id}_negative" value="negative"
                                                    ${!isChecked ? 'checked' : ''}>
                                                <label class="form-check-label"
                                                    for="panel_${panel.id}_negative">Negative</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                    name="panel_results[${panel.id}][result]"
                                                    id="panel_${panel.id}_positive" value="positive"
                                                    ${isChecked ? 'checked' : ''}>
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

            // Trigger change if test admin is already selected
            @if ($recoding_result->test_admin_id)
                $('#test_admin_id').trigger('change');
            @endif

            // Trigger change if company is already selected
            @if ($recoding_result->company_id)
                $('#company_id').trigger('change');
            @endif
        });
    </script>
@endpush
