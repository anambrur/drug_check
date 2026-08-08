@php
    $isEdit = isset($protocol) && $protocol;
    $hasOld = old('_token') !== null;
    $group = old('group', $isEdit ? $protocol->group : 'ALL');
    $period = old('selection_period', $isEdit ? $protocol->selection_period : 'YEARLY');
    $selectedClientIds = old('client_ids', $isEdit ? $protocol->clients->pluck('id')->all() : []);
    $check = function (string $field, $default) use ($hasOld) {
        return $hasOld ? (bool) old($field) : (bool) $default;
    };
@endphp

<div class="rs-section">
    <div class="rs-section__header">
        <div>
            <h5>Basic information</h5>
            <p>Name the protocol and choose clients plus the primary test.</p>
        </div>
    </div>
    <div class="rs-section__body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="name">Protocol name</label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="{{ old('name', $isEdit ? $protocol->name : '') }}"
                        placeholder="e.g. FMCSA Quarterly" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="client_ids">Client(s)</label>
                    <select class="form-control" id="client_ids" name="client_ids[]" multiple required size="6">
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}"
                                {{ in_array($client->id, $selectedClientIds) ? 'selected' : '' }}>
                                {{ $client->company_name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple clients.</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="test_id">Primary test</label>
                    <select class="form-control" id="test_id" name="test_id" required>
                        <option value="">Select test</option>
                        @foreach ($tests as $test)
                            <option value="{{ $test->id }}"
                                {{ (string) old('test_id', $isEdit ? $protocol->test_id : '') === (string) $test->id ? 'selected' : '' }}>
                                {{ $test->test_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="rs-section">
    <div class="rs-section__header">
        <div>
            <h5>Employee pool filters</h5>
            <p>Limit who can be drawn into the random selection pool.</p>
        </div>
    </div>
    <div class="rs-section__body">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Employee group</label>
                    <select class="form-control" name="group" required>
                        @foreach (['ALL' => 'All employees', 'DOT' => 'DOT employees', 'NON_DOT' => 'Non-DOT employees', 'FMCSA' => 'FMCSA', 'FRA' => 'FRA', 'FTA' => 'FTA', 'FAA' => 'FAA', 'PHMSA' => 'PHMSA', 'RSPA' => 'RSPA', 'USCG' => 'USCG'] as $value => $label)
                            <option value="{{ $value }}" {{ $group === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="department_filter">Department (optional)</label>
                    <input type="text" class="form-control" id="department_filter" name="department_filter"
                        placeholder="All departments"
                        value="{{ old('department_filter', $isEdit ? $protocol->department_filter : '') }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="shift_filter">Shift (optional)</label>
                    <input type="text" class="form-control" id="shift_filter" name="shift_filter"
                        placeholder="All shifts"
                        value="{{ old('shift_filter', $isEdit ? $protocol->shift_filter : '') }}">
                </div>
            </div>
        </div>
        <div class="rs-check-card mt-1">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="exclude_previously_selected"
                    name="exclude_previously_selected"
                    {{ $check('exclude_previously_selected', $isEdit ? $protocol->exclude_previously_selected : false) ? 'checked' : '' }}>
                <label class="form-check-label" for="exclude_previously_selected">Exclude previously selected</label>
                <small class="form-text text-muted">Skip employees already picked in this protocol’s period.</small>
            </div>
        </div>
    </div>
</div>

<div class="rs-section">
    <div class="rs-section__header">
        <div>
            <h5>Selection requirements</h5>
            <p>How many people to pick and how often the scheduler should run.</p>
        </div>
    </div>
    <div class="rs-section__body">
        <div class="form-row">
            <div class="col-md-3 form-group">
                <label>Amount</label>
                <input type="number" class="form-control" name="selection_requirement_value" min="1"
                    value="{{ old('selection_requirement_value', $isEdit ? $protocol->selection_requirement_value : 1) }}"
                    required>
            </div>
            <div class="col-md-3 form-group">
                <label>Type</label>
                <select class="form-control" name="selection_requirement_type" required>
                    <option value="NUMBER" {{ old('selection_requirement_type', $isEdit ? $protocol->selection_requirement_type : 'NUMBER') === 'NUMBER' ? 'selected' : '' }}># of employees</option>
                    <option value="PERCENTAGE" {{ old('selection_requirement_type', $isEdit ? $protocol->selection_requirement_type : '') === 'PERCENTAGE' ? 'selected' : '' }}>% of employees</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label>Frequency</label>
                <select class="form-control" name="selection_period" id="selection_period" required>
                    <option value="YEARLY" {{ $period === 'YEARLY' ? 'selected' : '' }}>Per year (Jan 1)</option>
                    <option value="QUARTERLY" {{ $period === 'QUARTERLY' ? 'selected' : '' }}>Per quarter (Jan/Apr/Jul/Oct 1)</option>
                    <option value="MONTHLY" {{ $period === 'MONTHLY' ? 'selected' : '' }}>Per month</option>
                    <option value="MANUAL" {{ $period === 'MANUAL' ? 'selected' : '' }}>Manual dates</option>
                </select>
            </div>
        </div>

        <div class="form-group" id="monthly-day-group" style="{{ $period === 'MONTHLY' ? '' : 'display:none;' }}">
            <label for="monthly_selection_day">Day of month</label>
            <select class="form-control" id="monthly_selection_day" name="monthly_selection_day" style="max-width: 200px;">
                @for ($i = 1; $i <= 28; $i++)
                    <option value="{{ $i }}"
                        {{ (int) old('monthly_selection_day', $isEdit ? $protocol->monthly_selection_day : 1) === $i ? 'selected' : '' }}>
                        {{ $i }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="form-group" id="manual-dates-group" style="{{ $period === 'MANUAL' ? '' : 'display:none;' }}">
            <label>Manual selection dates</label>
            <div id="manual-dates-container">
                @php
                    $manualDates = old('manual_dates', $isEdit && $period === 'MANUAL' ? ($protocol->manual_dates ?: []) : ['']);
                    if (empty($manualDates)) {
                        $manualDates = [''];
                    }
                @endphp
                @foreach ($manualDates as $date)
                    <div class="input-group mb-2" style="max-width: 320px;">
                        <input type="date" class="form-control" name="manual_dates[]"
                            value="{{ $date ? \Illuminate\Support\Carbon::parse($date)->format('Y-m-d') : '' }}">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary {{ $loop->first ? 'add-date' : 'remove-date' }}"
                                type="button">{{ $loop->first ? '+' : '−' }}</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="rs-section">
    <div class="rs-section__header">
        <div>
            <h5>Alternates & scheduling</h5>
            <p>Backup picks and whether the daily scheduler should run this protocol.</p>
        </div>
    </div>
    <div class="rs-section__body">
        <div class="form-row">
            <div class="col-md-3 form-group">
                <label>Alternates</label>
                <input type="number" class="form-control" name="alternates_value" min="0"
                    value="{{ old('alternates_value', $isEdit ? $protocol->alternates_value : 0) }}">
            </div>
            <div class="col-md-3 form-group">
                <label>Alternate type</label>
                <select class="form-control" name="alternates_type">
                    <option value="NUMBER" {{ old('alternates_type', $isEdit ? $protocol->alternates_type : 'NUMBER') === 'NUMBER' ? 'selected' : '' }}># of alternates</option>
                    <option value="PERCENTAGE" {{ old('alternates_type', $isEdit ? $protocol->alternates_type : '') === 'PERCENTAGE' ? 'selected' : '' }}>% of pool</option>
                </select>
            </div>
        </div>

        <div class="rs-check-grid mt-2">
            <div class="rs-check-card">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="automatic" name="automatic"
                        {{ $check('automatic', $isEdit ? $protocol->automatic : true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="automatic">Automatic</label>
                    <small class="form-text text-muted">
                        Scheduler runs on the frequency above. You can still use Run now anytime.
                    </small>
                </div>
            </div>
            <div class="rs-check-card">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="calculate_pool_average" name="calculate_pool_average"
                        {{ $check('calculate_pool_average', $isEdit ? $protocol->calculate_pool_average : false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="calculate_pool_average">Calculate pool average</label>
                    <small class="form-text text-muted">
                        Use average pool size over the period instead of the current pool (not implemented yet).
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="rs-section">
    <div class="rs-section__header">
        <div>
            <h5>Extra tests</h5>
            <p>Optional additional tests drawn at the same count as the primary selection.</p>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary" id="add-extra-test">+ Add extra test</button>
    </div>
    <div class="rs-section__body" id="extra-tests-container">
        @if ($isEdit)
            @foreach ($protocol->extraTests as $extraTest)
                <div class="extra-test rs-repeat-item">
                    <div class="form-row align-items-end">
                        <div class="col-md-10 form-group mb-md-0">
                            <label>Test</label>
                            <select class="form-control" name="extra_tests[]" required>
                                @foreach ($tests as $test)
                                    <option value="{{ $test->id }}" {{ $extraTest->test_id == $test->id ? 'selected' : '' }}>
                                        {{ $test->test_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 form-group mb-md-0 text-md-right">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-extra-test">Remove</button>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        <template id="extra-test-template">
            <div class="extra-test rs-repeat-item">
                <div class="form-row align-items-end">
                    <div class="col-md-10 form-group mb-md-0">
                        <label>Test</label>
                        <select class="form-control" name="extra_tests[]" required>
                            @foreach ($tests as $test)
                                <option value="{{ $test->id }}">{{ $test->test_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group mb-md-0 text-md-right">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-extra-test">Remove</button>
                    </div>
                </div>
            </div>
        </template>

        @if (!$isEdit || $protocol->extraTests->isEmpty())
            <p class="text-muted mb-0 small" id="extra-tests-empty">No extra tests added.</p>
        @endif
    </div>
</div>

<div class="rs-section">
    <div class="rs-section__header">
        <div>
            <h5>Sub-selections</h5>
            <p>Drawn only from primary picks (max 3). Example: alcohol subset of urine.</p>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary" id="add-sub-selection">+ Add sub-selection</button>
    </div>
    <div class="rs-section__body" id="sub-selections-container">
        @if ($isEdit)
            @foreach ($protocol->subSelections as $index => $subSelection)
                <div class="sub-selection rs-repeat-item">
                    <div class="form-row align-items-end">
                        <div class="col-md-5 form-group mb-md-0">
                            <label>Test</label>
                            <select class="form-control" name="sub_selections[{{ $index }}][test_id]" required>
                                @foreach ($tests as $test)
                                    <option value="{{ $test->id }}" {{ $subSelection->test_id == $test->id ? 'selected' : '' }}>
                                        {{ $test->test_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 form-group mb-md-0">
                            <label>Amount</label>
                            <input type="number" class="form-control"
                                name="sub_selections[{{ $index }}][requirement_value]"
                                value="{{ $subSelection->requirement_value }}" min="1" required>
                        </div>
                        <div class="col-md-3 form-group mb-md-0">
                            <label>Type</label>
                            <select class="form-control" name="sub_selections[{{ $index }}][requirement_type]" required>
                                <option value="NUMBER" {{ $subSelection->requirement_type == 'NUMBER' ? 'selected' : '' }}># of employees</option>
                                <option value="PERCENTAGE" {{ $subSelection->requirement_type == 'PERCENTAGE' ? 'selected' : '' }}>% of primary</option>
                            </select>
                        </div>
                        <div class="col-md-1 form-group mb-md-0 text-md-right">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-sub-selection">×</button>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        <template id="sub-selection-template">
            <div class="sub-selection rs-repeat-item">
                <div class="form-row align-items-end">
                    <div class="col-md-5 form-group mb-md-0">
                        <label>Test</label>
                        <select class="form-control" name="sub_selections[][test_id]" required>
                            @foreach ($tests as $test)
                                <option value="{{ $test->id }}">{{ $test->test_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group mb-md-0">
                        <label>Amount</label>
                        <input type="number" class="form-control" name="sub_selections[][requirement_value]"
                            value="1" min="1" required>
                    </div>
                    <div class="col-md-3 form-group mb-md-0">
                        <label>Type</label>
                        <select class="form-control" name="sub_selections[][requirement_type]" required>
                            <option value="NUMBER"># of employees</option>
                            <option value="PERCENTAGE">% of primary</option>
                        </select>
                    </div>
                    <div class="col-md-1 form-group mb-md-0 text-md-right">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-sub-selection">×</button>
                    </div>
                </div>
            </div>
        </template>

        @if (!$isEdit || $protocol->subSelections->isEmpty())
            <p class="text-muted mb-0 small" id="sub-selections-empty">No sub-selections added.</p>
        @endif
    </div>
</div>

<div class="rs-section">
    <div class="rs-section__header">
        <div>
            <h5>Status & notifications</h5>
            <p>Control whether the protocol can run and whether employees are emailed.</p>
        </div>
    </div>
    <div class="rs-section__body">
        <div class="rs-check-grid">
            <div class="rs-check-card">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                        {{ $check('is_active', $isEdit ? $protocol->is_active : true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Protocol active</label>
                    <small class="form-text text-muted">Inactive protocols cannot be executed by Run now or the scheduler.</small>
                </div>
            </div>
            <div class="rs-check-card">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="is_email_send" name="is_email_send"
                        {{ $check('is_email_send', $isEdit ? $protocol->is_email_send : false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_email_send">Send email notifications</label>
                    <small class="form-text text-muted">Primary picks receive an email after each successful run.</small>
                </div>
            </div>
        </div>
    </div>
</div>
