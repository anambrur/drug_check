@php
    $allSelections = collect()
        ->merge($primary ?? [])
        ->merge($extra ?? [])
        ->merge($sub ?? [])
        ->merge($alternates ?? []);
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
                <th>Employee ID</th>
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
                @endphp
                <tr data-selection-type="{{ $type }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $employee->employee_id ?? 'N/A' }}</td>
                    <td class="rs-runs-table__strong">
                        {{ trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')) ?: 'N/A' }}
                    </td>
                    <td>{{ $employee->clientProfile->company_name ?? 'N/A' }}</td>
                    <td>{{ $employee->department ?? 'N/A' }}</td>
                    <td>{{ $employee->shift ?? 'N/A' }}</td>
                    <td>{{ $selection->test->test_name ?? 'N/A' }}</td>
                    <td><span class="rs-tag {{ $tagClass }}">{{ $label }}</span></td>
                    <td>{{ $selection->random_number }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">No employees were selected in this run.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

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
