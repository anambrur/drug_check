@extends('layouts.admin.master')

@section('content')

    <style>
        @media print {

            .card-header,
            .card-tools,
            .btn {
                display: none !important;
            }

            body {
                background: white;
            }

            .card {
                border: none;
                box-shadow: none;
            }
        }
    </style>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">Selection Results</h3>
                        <div class="card-tools">
                            <a href="{{ route('random-selection.index') }}" class="btn btn-sm btn-light">
                                <i class="fas fa-arrow-left"></i> Back to Protocols
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Protocol Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h4>Protocol Details</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Protocol Name</th>
                                        <td>{{ $protocol->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Client</th>
                                        <td>{{ $protocol->client->company_name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Test</th>
                                        <td>{{ $protocol->test->test_name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Selection Date</th>
                                        <td>{{ $event->selection_date->format('M d, Y h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Pool Size</th>
                                        <td>{{ $event->pool_size }} employees</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4>Selection Summary</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Primary Selections</th>
                                        <td>{{ count($primary) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Extra Tests</th>
                                        <td>{{ count($extra) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Sub Selections</th>
                                        <td>{{ count($sub) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Alternates</th>
                                        <td>{{ count($alternates) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total Selected</th>
                                        <td>{{ count($primary) + count($extra) + count($sub) + count($alternates) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Primary Selections -->
                        <div class="card card-primary card-outline mb-4">
                            <div class="card-header">
                                <h3 class="card-title">Primary Selections</h3>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Employee ID</th>
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th>Shift</th>
                                            <th>Random Number</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      
                                        @foreach ($primary as $index => $selection)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $selection->employee->employee_id ?? 'N/A' }}</td>
                                                <td>{{ $selection->employee->full_name ?? 'N/A' }}</td>
                                                <td>{{ $selection->employee->department ?? 'N/A' }}</td>
                                                <td>{{ $selection->employee->shift ?? 'N/A' }}</td>
                                                <td>{{ $selection->random_number }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Extra Tests -->
                        @if (count($extra) > 0)
                            <div class="card card-info card-outline mb-4">
                                <div class="card-header">
                                    <h3 class="card-title">Extra Tests</h3>
                                </div>
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Employee ID</th>
                                                <th>Name</th>
                                                <th>Test</th>
                                                <th>Random Number</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($extra as $index => $selection)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $selection->employee->employee_id ?? 'N/A' }}</td>
                                                    <td>{{ $selection->employee->full_name ?? 'N/A' }}</td>
                                                    <td>{{ $selection->test->name ?? 'N/A' }}</td>
                                                    <td>{{ $selection->random_number }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <!-- Sub Selections -->
                        @if (count($sub) > 0)
                            <div class="card card-warning card-outline mb-4">
                                <div class="card-header">
                                    <h3 class="card-title">Sub Selections</h3>
                                </div>
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Employee ID</th>
                                                <th>Name</th>
                                                <th>Test</th>
                                                <th>Random Number</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($sub as $index => $selection)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $selection->employee->employee_id ?? 'N/A' }}</td>
                                                    <td>{{ $selection->employee->full_name ?? 'N/A' }}</td>
                                                    <td>{{ $selection->test->name ?? 'N/A' }}</td>
                                                    <td>{{ $selection->random_number }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <!-- Alternates -->
                        @if (count($alternates) > 0)
                            <div class="card card-secondary card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Alternate Selections</h3>
                                </div>
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Employee ID</th>
                                                <th>Name</th>
                                                <th>Department</th>
                                                <th>Shift</th>
                                                <th>Random Number</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($alternates as $index => $selection)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $selection->employee->employee_id ?? 'N/A' }}</td>
                                                    <td>{{ $selection->employee->full_name ?? 'N/A' }}</td>
                                                    <td>{{ $selection->employee->department ?? 'N/A' }}</td>
                                                    <td>{{ $selection->employee->shift ?? 'N/A' }}</td>
                                                    <td>{{ $selection->random_number }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <!-- Print/Download Button -->
                        <div class="mt-4 text-center">
                            <button onclick="window.print()" class="btn btn-default">
                                <i class="fas fa-print"></i> Print Results
                            </button>
                            <a href="#" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Export to Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
