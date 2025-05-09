@extends('layouts.admin.master')

@section('content')
    <!-- Form row -->
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">Add New Selection Protocol</h4>

                </h4>
                @if ($demo_mode == 'on')
                    <!-- Include Alert Blade -->
                    @include('admin.demo_mode.demo-mode')
                @else
                    <form action="{{ route('random-selection.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                @endif

                <div class="card mb-4">
                    <div class="card-header">Basic Information</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">Protocol Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Enter Protocol Name" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="client_id">Client / Company</label>
                                    <select class="form-control" id="client_id" name="client_id" required>
                                        <option value="" selected>{{ __('content.select_your_option') }}</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="test_id">Test (T)</label>
                                    <select class="form-control" id="test_id" name="test_id" required>
                                        <option value="">Select Test--</option>
                                        @foreach ($tests as $test)
                                            <option value="{{ $test->id }}">{{ $test->test_name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        If the test you are looking for is not in the list, please
                                        <a href="#">add a new test</a>.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="card mb-4">
                    <div class="card-header">Group (G)</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Employee Group</label>
                                    <select class="form-control" name="group" required>
                                        <option value="ALL">All Employees</option>
                                        <option value="DOT">DOT Employees</option>
                                        <option value="NON_DOT">Non-DOT Employees</option>
                                        <option value="DOT_AGENCY">DOT Agency</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group" id="dot-agency-group" style="display: none;">
                                    <label for="dot_agency_id">DOT Agency</label>
                                    <select class="form-control" id="dot_agency_id" name="dot_agency_id">
                                        @foreach ($dotAgencies as $agency)
                                            <option value="{{ $agency->id }}">{{ $agency->dot_agency_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="department_filter">Department Filter (optional)</label>
                                    <input type="text" class="form-control" id="department_filter"
                                        name="department_filter" placeholder="All Departments">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="shift_filter">Shift Filter (optional)</label>
                                    <input type="text" class="form-control" id="shift_filter" name="shift_filter"
                                        placeholder="All Shifts">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="exclude_previously_selected"
                                        name="exclude_previously_selected">
                                    <label class="form-check-label ml-3" for="exclude_previously_selected">Exclude
                                        Previously
                                        Selected</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">Selection Requirements (SR)</div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-3 form-group">
                                <input type="number" class="form-control" name="selection_requirement_value"
                                    placeholder="1" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <select class="form-control" name="selection_requirement_type" required>
                                    <option value="NUMBER"># of employees</option>
                                    <option value="PERCENTAGE">% of employees</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <select class="form-control" name="selection_period" required>
                                    <option value="YEARLY">Per Year</option>
                                    <option value="QUARTERLY">Per Quarter</option>
                                    <option value="MONTHLY">Per Month</option>
                                    <option value="MANUAL">Manually Selected Dates</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" id="monthly-day-group" style="display: none;">
                            <label for="monthly_selection_day">Day of Month</label>
                            <select class="form-control" id="monthly_selection_day" name="monthly_selection_day">
                                @for ($i = 1; $i <= 28; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="form-group" id="manual-dates-group" style="display: none;">
                            <label>Manual Selection Dates</label>
                            <div id="manual-dates-container">
                                <div class="input-group mb-2">
                                    <input type="date" class="form-control" name="manual_dates[]">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary add-date" type="button">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">Selection Alternates (SA)</div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-2 form-group">
                                <input type="number" class="form-control" name="alternates_value" value="0"
                                    min="0">
                            </div>
                            <div class="col-md-3 form-group">
                                <select class="form-control" name="alternates_type">
                                    <option value="NUMBER"># of alternates</option>
                                    <option value="PERCENTAGE">% of alternates</option>
                                </select>
                            </div>

                            <div class="col-md-3">


                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input " id="automatic" name="automatic"
                                        checked>
                                    <label class="form-check-label ml-3" for="automatic">Automatic</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-check ">
                                    <input type="checkbox" class="form-check-input" id="calculate_pool_average"
                                        name="calculate_pool_average">
                                    <label class="form-check-label ml-3" for="calculate_pool_average">Calculate Pool
                                        Average</label>
                                    <small class="form-text text-muted">
                                        Decide if the percentage/number required will be based on the average number over
                                        the
                                        period
                                        instead of the current number at the time of the pick.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">Extra Tests</div>
                    <div class="card-body" id="extra-tests-container">
                        <button type="button" class="btn btn-secondary mb-3" id="add-extra-test">+ Add Extra
                            Test</button>

                        <template id="extra-test-template">
                            <div class="extra-test border p-3 mb-3">
                                <div class="form-group">
                                    <select class="form-control" name="extra_tests[]" required>
                                        @foreach ($tests as $test)
                                            <option value="{{ $test->id }}">{{ $test->test_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="button" class="btn btn-danger remove-extra-test">Remove</button>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">Sub-Selections (Max 3)</div>
                    <div class="card-body" id="sub-selections-container">
                        <button type="button" class="btn btn-secondary mb-3" id="add-sub-selection">+ Add
                            Sub-Selection</button>

                        <template id="sub-selection-template">
                            <div class="sub-selection border p-3 mb-3">
                                <div class="form-row">
                                    <div class="col-md-5 form-group">
                                        <select class="form-control" name="sub_selections[][test_id]" required>
                                            @foreach ($tests as $test)
                                                <option value="{{ $test->id }}">{{ $test->test_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <input type="number" class="form-control"
                                            name="sub_selections[][requirement_value]" placeholder="1" min="1"
                                            required>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <select class="form-control" name="sub_selections[][requirement_type]" required>
                                            <option value="NUMBER"># of employees</option>
                                            <option value="PERCENTAGE">% of employees</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1 form-group">
                                        <button type="button" class="btn btn-danger remove-sub-selection">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                    <label class="form-check-label ml-3" for="is_active">Protocol Active</label>
                    <small class="form-text text-muted">
                        Disabling will prevent new selections from being made and hide from client list.
                    </small>
                </div>

                <button type="submit" class="btn btn-primary">Create Protocol</button>
                </form>
            </div>
        </div>
    </div>
    <!-- end row -->
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOT agency field visibility
            document.querySelector('select[name="group"]').addEventListener('change', function() {
                document.getElementById('dot-agency-group').style.display =
                    this.value === 'DOT_AGENCY' ? 'block' : 'none';
            });

            // Monthly day field visibility
            document.querySelector('select[name="selection_period"]').addEventListener('change', function() {
                document.getElementById('monthly-day-group').style.display =
                    this.value === 'MONTHLY' ? 'block' : 'none';
                document.getElementById('manual-dates-group').style.display =
                    this.value === 'MANUAL' ? 'block' : 'none';
            });

            // Add extra test
            let extraTestCounter = 0;
            document.getElementById('add-extra-test').addEventListener('click', function() {
                if (document.querySelectorAll('.extra-test').length >= 5) {
                    alert('Maximum 5 extra tests allowed');
                    return;
                }

                const template = document.getElementById('extra-test-template');
                const clone = template.content.cloneNode(true);
                document.getElementById('extra-tests-container').appendChild(clone);
                extraTestCounter++;
            });

            // Add sub-selection
            let subSelectionCounter = 0;
            document.getElementById('add-sub-selection').addEventListener('click', function() {
                if (document.querySelectorAll('.sub-selection').length >= 3) {
                    alert('Maximum 3 sub-selections allowed');
                    return;
                }

                const template = document.getElementById('sub-selection-template');
                const clone = template.content.cloneNode(true);

                // Update names with counter
                const inputs = clone.querySelectorAll('[name]');
                inputs.forEach(input => {
                    const name = input.getAttribute('name').replace('[]',
                        `[${subSelectionCounter}]`);
                    input.setAttribute('name', name);
                });

                document.getElementById('sub-selections-container').appendChild(clone);
                subSelectionCounter++;
            });

            // Remove elements
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-extra-test')) {
                    e.target.closest('.extra-test').remove();
                }
                if (e.target.classList.contains('remove-sub-selection')) {
                    e.target.closest('.sub-selection').remove();
                }
                if (e.target.classList.contains('add-date')) {
                    const newDate = document.createElement('div');
                    newDate.className = 'input-group mb-2';
                    newDate.innerHTML = `
                    <input type="date" class="form-control" name="manual_dates[]">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary remove-date" type="button">-</button>
                    </div>
                `;
                    document.getElementById('manual-dates-container').appendChild(newDate);
                }
                if (e.target.classList.contains('remove-date')) {
                    e.target.closest('.input-group').remove();
                }
            });
        });
    </script>
@endpush
