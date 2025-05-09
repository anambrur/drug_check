@extends('layouts.admin.master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title text-white">Execution History - {{ $protocol->name }}</h3>
                        <div class="card-tools">
                            <a href="{{ route('random-selection.index') }}" class="btn btn-sm btn-light">
                                <i class="fas fa-arrow-left"></i> Back to Protocols
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Protocol Summary -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="info-box bg-light p-3" style="border-radius: 10px">
                                    <i class="fas fa-calendar-alt font-36"></i>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Protocol Created</span>
                                        <span class="info-box-number">{{ $protocol->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="info-box bg-light p-3" style="border-radius: 10px">
                                    <i class="fas fa-users font-36"></i>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Current Pool Size</span>
                                        <span
                                            class="info-box-number">{{ App\Models\Admin\Employee::where('client_profile_id', $protocol->client_id)->count() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-light p-3" style="border-radius: 10px">
                                    <i class="fa fa-history text-primary font-36" title="View Execution History"></i>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Executions</span>
                                        <span class="info-box-number">{{ $protocol->selectionEvents()->count() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Executions Table -->
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Past Executions</h3>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr class="bg-gray">
                                            <th>#</th>
                                            <th>Execution Date</th>
                                            <th>Pool Size</th>
                                            <th>Selections</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($executions as $execution)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $execution->selection_date->format('M d, Y h:i A') }}</td>
                                                <td>{{ $execution->pool_size }}</td>
                                                <td>
                                                    @php
                                                        $counts = [
                                                            'primary' => 0,
                                                            'extra' => 0,
                                                            'sub' => 0,
                                                            'alternate' => 0,
                                                        ];

                                                        foreach ($execution->selectedEmployees as $selection) {
                                                            $counts[strtolower($selection->selection_type)]++;
                                                        }
                                                    @endphp
                                                    <span class="badge badge-primary">{{ $counts['primary'] }}
                                                        Primary</span>
                                                    <span class="badge badge-info">{{ $counts['extra'] }} Extra</span>
                                                    <span class="badge badge-warning">{{ $counts['sub'] }} Sub</span>
                                                    <span class="badge badge-secondary">{{ $counts['alternate'] }}
                                                        Alternate</span>
                                                </td>
                                                <td>
                                                    @switch($execution->status)
                                                        @case('PENDING')
                                                            <span class="badge badge-warning">Pending</span>
                                                        @break

                                                        @case('COMPLETED')
                                                            <span class="badge badge-success">Completed</span>
                                                        @break

                                                        @case('CANCELLED')
                                                            <span class="badge badge-danger">Cancelled</span>
                                                        @break
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <a href="{{ route('random-selection.results.view', $execution->id) }}"
                                                        class="btn btn-sm btn-primary" title="View Details">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if ($executions->hasPages())
                                <div class="card-footer">
                                    {{ $executions->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
