@extends('layouts.admin.master')

@section('content')
    <style>
        @media print {

            .card-header,
            .card-tools,
            .btn,
            .card-footer {
                display: none !important;
            }

            body {
                background: white;
            }

            .card {
                border: none;
                box-shadow: none;
            }

            .info-box {
                page-break-inside: avoid;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>


    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title text-white">Selection Results - {{ $protocol->name }}</h3>
                        <div class="card-tools">
                            <button onclick="window.print()" class="btn btn-sm btn-light">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Protocol Summary -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-box bg-light p-3" style="border-radius: 10px">
                                    <i class="fas fa-calendar-alt font-36"></i>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Selection Date</span>
                                        <span
                                            class="info-box-number">{{ $event->selection_date->format('M d, Y h:i A') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box bg-light p-3" style="border-radius: 10px">
                                    <i class="fas fa-users font-36"></i>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Pool Size</span>
                                        <span class="info-box-number">{{ $event->pool_size }} employees</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Consolidated Results Table -->
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">All Selected Employees</h3>
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

                                        <!-- Primary Selections -->
                                        @foreach ($primary as $selection)
                                            <tr>
                                                <td>{{ $counter++ }}</td>
                                                <td>{{ $selection->employee ? str_pad($selection->employee->id, 6, '0', STR_PAD_LEFT) : 'N/A' }}
                                                </td>
                                                <td>{{ $selection->employee->first_name . ' ' . $selection->employee->last_name ?? 'N/A' }}
                                                </td>
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
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Summary Statistics -->
                        <div class="row mt-4">
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box">
                                    <i class="fas fa-user-check"></i>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Primary</span>
                                        <span class="info-box-number">{{ count($primary) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box">
                                    <i class="fas fa-vial"></i>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Extra Tests</span>
                                        <span class="info-box-number">{{ count($extra) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box">
                                    <i class="fas fa-filter"></i>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Sub Selections</span>
                                        <span class="info-box-number">{{ count($sub) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box">
                                    <i class="fas fa-user-clock"></i>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Alternates</span>
                                        <span class="info-box-number">{{ count($alternates) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('random-selection.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to Protocols
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
