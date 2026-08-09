@php
    $allSelections = collect()
        ->merge($primary ?? [])
        ->merge($extra ?? [])
        ->merge($sub ?? [])
        ->merge($alternates ?? []);
    $alternateMode = $alternateMode ?? ($protocol->alternate_mode ?? 'immediate');
@endphp

<ul class="nav rs-filter-pills no-print" id="selection-type-filter">
    <li class="nav-item">
        <a class="nav-link active" href="#" data-filter="all">All ({{ $allSelections->count() }})</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#" data-filter="PRIMARY">Primary ({{ ($primary ?? collect())->count() }})</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#" data-filter="EXTRA">Extra ({{ ($extra ?? collect())->count() }})</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#" data-filter="SUB">Sub ({{ ($sub ?? collect())->count() }})</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#" data-filter="ALTERNATE">Alternates ({{ ($alternates ?? collect())->count() }})</a>
    </li>
</ul>

<div class="table-responsive">
    <table class="table rs-runs-table mb-0" id="selected-employees-table">
        <thead>
            <tr>
                <th>#</th>
                <th>DonorID</th>
                <th>Name</th>
                <th>Company</th>
                <th>Department</th>
                <th>Shift</th>
                <th>Test</th>
                <th>Type</th>
                <th>
                    Pool index
                    <i class="fas fa-info-circle text-muted no-print"
                        title="Random index chosen from 0 to pool size − 1 for audit trail"></i>
                </th>
                <th class="no-print">Audit</th>
                @if (in_array($alternateMode, ['on_demand', 'offline_list'], true))
                    <th class="no-print">Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($allSelections as $selection)
                @php
                    $employee = $selection->employee;
                    $type = $selection->selection_type;
                    $tagClass = match ($type) {
                        'PRIMARY' => 'rs-tag--primary',
                        'EXTRA' => 'rs-tag--extra',
                        'SUB' => 'rs-tag--sub',
                        'ALTERNATE' => 'rs-tag--alternate',
                        default => 'rs-tag--manual',
                    };
                    $label = match ($type) {
                        'PRIMARY' => 'Primary',
                        'EXTRA' => 'Extra',
                        'SUB' => 'Sub',
                        'ALTERNATE' => 'Alternate',
                        default => $type,
                    };
                    $donorId = $selection->donor_id ?: ($employee->employee_id ?? 'N/A');
                    $canReplace = $type === 'PRIMARY'
                        && $alternateMode === 'on_demand'
                        && !$selection->is_excused
                        && !$selection->is_refused
                        && !$selection->replacementAlternate;
                @endphp
                <tr data-selection-type="{{ $type }}">
                    <td>
                        @if ($type === 'ALTERNATE' && $selection->print_order)
                            {{ $selection->print_order }}
                        @else
                            {{ $loop->iteration }}
                        @endif
                    </td>
                    <td>{{ $donorId }}</td>
                    <td class="rs-runs-table__strong">
                        {{ trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')) ?: 'N/A' }}
                        @if ($selection->is_excused)
                            <span class="badge badge-warning ml-1">Excused</span>
                        @endif
                        @if ($selection->is_refused)
                            <span class="badge badge-danger ml-1">Refused</span>
                        @endif
                        @if ($selection->alternate_replaces_id && $selection->alternateReplaces)
                            <div class="small text-muted">
                                Replaces {{ $selection->alternateReplaces->donor_id ?: optional($selection->alternateReplaces->employee)->employee_id }}
                                @if ($selection->replacement_reason)
                                    ({{ $selection->replacement_reason }})
                                @endif
                            </div>
                        @endif
                    </td>
                    <td>{{ $employee->clientProfile->company_name ?? 'N/A' }}</td>
                    <td>{{ $employee->department ?? 'N/A' }}</td>
                    <td>{{ $employee->shift ?? 'N/A' }}</td>
                    <td>{{ $selection->test->test_name ?? 'N/A' }}</td>
                    <td><span class="rs-tag {{ $tagClass }}">{{ $label }}</span></td>
                    <td>
                        {{ $selection->random_number }}
                        @if ($selection->pool_range_max !== null)
                            <span class="text-muted small">/ 0–{{ $selection->pool_range_max }}</span>
                        @endif
                    </td>
                    <td class="no-print">
                        <button type="button"
                            class="btn btn-link btn-sm p-0"
                            data-toggle="modal"
                            data-target="#audit-modal-{{ $selection->id }}">
                            View pool
                        </button>
                    </td>
                    @if (in_array($alternateMode, ['on_demand', 'offline_list'], true))
                        <td class="no-print">
                            @if ($canReplace)
                                <form method="POST"
                                    action="{{ route('random-selection.selections.replace', $selection) }}"
                                    class="d-inline"
                                    onsubmit="return confirm('Mark excused and select an on-demand alternate?');">
                                    @csrf
                                    <input type="hidden" name="reason" value="excused">
                                    <button type="submit" class="btn btn-outline-warning btn-sm mb-1">Excuse</button>
                                </form>
                                <form method="POST"
                                    action="{{ route('random-selection.selections.replace', $selection) }}"
                                    class="d-inline"
                                    onsubmit="return confirm('Mark refused and select an on-demand alternate?');">
                                    @csrf
                                    <input type="hidden" name="reason" value="refused">
                                    <button type="submit" class="btn btn-outline-danger btn-sm mb-1">Refuse</button>
                                </form>
                            @elseif ($type === 'PRIMARY' && ($selection->is_excused || $selection->is_refused))
                                <span class="text-muted small">Replaced</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ in_array($alternateMode, ['on_demand', 'offline_list'], true) ? 11 : 10 }}"
                        class="text-center text-muted py-4">
                        No employees were selected in this run.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@foreach ($allSelections as $selection)
    @php
        $drawPool = $selection->draw_pool ?? [];
    @endphp
    <div class="modal fade" id="audit-modal-{{ $selection->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Audit — DonorID {{ $selection->donor_id ?: optional($selection->employee)->employee_id }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">
                        <strong>Random number:</strong>
                        {{ $selection->random_number }}
                        (from 0 to {{ $selection->pool_range_max ?? '—' }})
                    </p>
                    <p class="mb-2">
                        <strong>Selection type:</strong> {{ $selection->selection_type }}
                    </p>
                    <p class="mb-3 text-muted small">
                        Ordered DonorID pool used for this individual draw (TestChecks audit requirement).
                    </p>
                    <div class="table-responsive" style="max-height: 320px; overflow:auto;">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Index</th>
                                    <th>DonorID</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($drawPool as $index => $poolDonorId)
                                    <tr class="{{ (int) $index === (int) $selection->random_number ? 'table-success' : '' }}">
                                        <td>{{ $index }}</td>
                                        <td>{{ $poolDonorId }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-muted">No draw pool recorded for this selection.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

@push('scripts')
    <script>
        (function() {
            var filter = document.getElementById('selection-type-filter');
            if (!filter) return;

            filter.addEventListener('click', function(e) {
                var link = e.target.closest('[data-filter]');
                if (!link) return;
                e.preventDefault();

                filter.querySelectorAll('.nav-link').forEach(function(el) {
                    el.classList.remove('active');
                });
                link.classList.add('active');

                var value = link.getAttribute('data-filter');
                document.querySelectorAll('#selected-employees-table tbody tr[data-selection-type]').forEach(function(row) {
                    row.style.display = (value === 'all' || row.getAttribute('data-selection-type') === value) ? '' : 'none';
                });
            });
        })();
    </script>
@endpush
