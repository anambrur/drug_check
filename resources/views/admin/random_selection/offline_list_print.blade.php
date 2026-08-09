@extends('layouts.admin.master')

@section('title', 'Offline random list')

@section('content')
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white; }
        .card { border: none !important; box-shadow: none !important; }
    }
    .offline-list-table td, .offline-list-table th {
        font-size: 13px;
        vertical-align: middle;
    }
    .offline-list-used {
        text-decoration: line-through;
        color: #888;
    }
</style>

<div class="container-fluid rs-page">
    <div class="rs-page__toolbar no-print">
        <div>
            <h3 class="rs-page__title">Offline shuffled employee list</h3>
            <p class="rs-page__subtitle">
                {{ $protocol->name }} · Run {{ $event->selection_date->format('M j, Y g:i A') }}
                · Single-use
            </p>
        </div>
        <div class="rs-page__actions">
            <button type="button" onclick="window.print()" class="btn btn-primary btn-sm">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            <a href="{{ route('random-selection.results.view', $event) }}" class="btn btn-outline-secondary btn-sm">
                Back to run
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <p class="mb-3">
                Use this randomly sorted list on-site when internet access is unavailable.
                Start at the top and use the next unused employee as needed.
            </p>
            <div class="table-responsive">
                <table class="table table-bordered offline-list-table mb-0">
                    <thead>
                        <tr>
                            <th style="width:70px;">#</th>
                            <th>DonorID</th>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list->shuffled_donor_ids ?? [] as $index => $donorId)
                            @php
                                $employee = $employeesByDonor[(string) $donorId] ?? null;
                                $used = $index < (int) $list->cursor;
                            @endphp
                            <tr class="{{ $used ? 'offline-list-used' : '' }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $donorId }}</td>
                                <td>
                                    @if ($employee)
                                        {{ trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ optional(optional($employee)->clientProfile)->company_name ?? '—' }}</td>
                                <td>{{ $used ? 'Used' : 'Available' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
