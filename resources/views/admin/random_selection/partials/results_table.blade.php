<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Selected Employees</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr class="bg-gray">
                    <th>#</th>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Shift</th>
                    <th>Test Type</th>
                    <th>Selection Type</th>
                    <th>Random #</th>
                </tr>
            </thead>
            <tbody>
                @php $counter = 1; @endphp

                @foreach ($primary as $selection)
                    <tr>
                        <td>{{ $counter++ }}</td>
                        <td>{{ $selection->employee ? str_pad($selection->employee->id, 6, '0', STR_PAD_LEFT) : 'N/A' }}
                        </td>
                        <td>{{ $selection->employee->first_name . ' ' . $selection->employee->last_name ?? 'N/A' }}</td>
                        <td>{{ $selection->employee->department ?? 'N/A' }}</td>
                        <td>{{ $selection->employee->shift ?? 'N/A' }}</td>
                        <td>{{ $selection->test->test_name ?? 'Primary Test' }}</td>
                        <td><span class="badge badge-primary">Primary</span></td>
                        <td>{{ $selection->random_number }}</td>
                    </tr>
                @endforeach

                <!-- Extra Tests -->
                @foreach ($extra as $selection)
                    <tr>
                        <td>{{ $counter++ }}</td>
                        <td>{{ $selection->employee ? str_pad($selection->employee->id, 6, '0', STR_PAD_LEFT) : 'N/A' }}
                        </td>
                        <td>{{ $selection->employee->first_name . ' ' . $selection->employee->last_name ?? 'N/A' }}
                        </td>
                        <td>{{ $selection->employee->department ?? 'N/A' }}</td>
                        <td>{{ $selection->employee->shift ?? 'N/A' }}</td>
                        <td>{{ $selection->test->test_name ?? 'Extra Test' }}</td>
                        <td><span class="badge badge-info">Extra</span></td>
                        <td>{{ $selection->random_number }}</td>
                    </tr>
                @endforeach

                <!-- Sub Selections -->
                @foreach ($sub as $selection)
                    <tr>
                        <td>{{ $counter++ }}</td>
                        <td>{{ $selection->employee ? str_pad($selection->employee->id, 6, '0', STR_PAD_LEFT) : 'N/A' }}
                        </td>
                        <td>{{ $selection->employee->first_name . ' ' . $selection->employee->last_name ?? 'N/A' }}
                        </td>
                        <td>{{ $selection->employee->department ?? 'N/A' }}</td>
                        <td>{{ $selection->employee->shift ?? 'N/A' }}</td>
                        <td>{{ $selection->test->test_name ?? 'Sub Test' }}</td>
                        <td><span class="badge badge-warning">Sub</span></td>
                        <td>{{ $selection->random_number }}</td>
                    </tr>
                @endforeach


                <!-- Alternates -->
                @foreach ($alternates as $selection)
                    <tr>
                        <td>{{ $counter++ }}</td>
                        <td>{{ $selection->employee ? str_pad($selection->employee->id, 6, '0', STR_PAD_LEFT) : 'N/A' }}
                        </td>
                        <td>{{ $selection->employee->first_name . ' ' . $selection->employee->last_name ?? 'N/A' }}
                        </td>
                        <td>{{ $selection->employee->department ?? 'N/A' }}</td>
                        <td>{{ $selection->employee->shift ?? 'N/A' }}</td>
                        <td>{{ $selection->test->test_name ?? 'Primary Test' }}</td>
                        <td><span class="badge badge-secondary">Alternate</span></td>
                        <td>{{ $selection->random_number }}</td>
                    </tr>
                @endforeach

                <!-- Repeat for extra, sub, and alternates -->
            </tbody>
        </table>
    </div>
</div>
