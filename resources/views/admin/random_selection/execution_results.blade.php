@extends('layouts.admin.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title text-white">Execution Results - {{ $protocol->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('random-selection.executions', $protocol) }}" class="btn btn-sm btn-light">
                            <i class="fas fa-arrow-left"></i> Back to History
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Execution Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Execution Date</th>
                                    <td>{{ $event->selection_date->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Protocol</th>
                                    <td>{{ $protocol->name }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @switch($event->status)
                                            @case('PENDING') <span class="badge badge-warning">Pending</span> @break
                                            @case('COMPLETED') <span class="badge badge-success">Completed</span> @break
                                            @case('CANCELLED') <span class="badge badge-danger">Cancelled</span> @break
                                        @endswitch
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Pool Size</th>
                                    <td>{{ $event->pool_size }}</td>
                                </tr>
                                <tr>
                                    <th>Primary Selections</th>
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
                            </table>
                        </div>
                    </div>

                    <!-- Rest of your results table (same as your original results.blade.php) -->
                    @include('admin.random_selection.partials.results_table')
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection