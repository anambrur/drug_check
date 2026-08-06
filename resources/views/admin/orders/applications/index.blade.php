@extends('layouts.admin.master')

@section('content')
<style>
    .orders-stat-card .card-body { min-height: 92px; }
    .orders-filter-card .form-label { font-size: 12px; color: #6c757d; margin-bottom: 4px; }
    .orders-filter-row {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 0.5rem;
    }
    .orders-filter-field { min-width: 0; }
    .orders-filter-search { flex: 1 1 180px; }
    .orders-filter-select { flex: 0 1 140px; min-width: 120px; }
    .orders-filter-date { flex: 0 1 150px; min-width: 135px; }
    .orders-filter-btn { flex: 0 0 100px; }
    @media (min-width: 992px) {
        .orders-filter-row { flex-wrap: nowrap; }
        .orders-filter-search { flex: 1 1 auto; }
        .orders-filter-select { flex: 0 1 130px; }
        .orders-filter-date { flex: 0 1 145px; }
        .orders-filter-btn { flex: 0 0 96px; }
    }
    @media (max-width: 991.98px) {
        .orders-filter-search,
        .orders-filter-select,
        .orders-filter-date { flex: 1 1 calc(50% - 0.5rem); }
        .orders-filter-btn { flex: 1 1 calc(50% - 0.5rem); }
    }
    @media (max-width: 575.98px) {
        .orders-filter-search,
        .orders-filter-select,
        .orders-filter-date,
        .orders-filter-btn { flex: 1 1 100%; }
    }
    #orders-datatable_wrapper .dataTables_filter { display: none; }
    #orders-datatable td { vertical-align: middle; }
    #orders-datatable thead th {
        white-space: nowrap;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .02em;
        background: #f8fafc;
        border-bottom-width: 1px;
    }
    .orders-table-card .card-title { font-size: 1rem; }
</style>

{{-- Stats --}}
<div class="row">
    <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
        <div class="card orders-stat-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3"><i class="fas fa-list fa-2x text-primary"></i></div>
                <div>
                    <p class="mb-0 text-muted font-12">Total Orders</p>
                    <h4 class="mb-0 font-weight-bold">{{ number_format($stats['total']) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
        <div class="card orders-stat-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3"><i class="fas fa-check-circle fa-2x text-success"></i></div>
                <div>
                    <p class="mb-0 text-muted font-12">Paid</p>
                    <h4 class="mb-0 font-weight-bold">{{ number_format($stats['paid']) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
        <div class="card orders-stat-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3"><i class="fas fa-clock fa-2x text-warning"></i></div>
                <div>
                    <p class="mb-0 text-muted font-12">Pending Pay</p>
                    <h4 class="mb-0 font-weight-bold">{{ number_format($stats['pending_payment']) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
        <div class="card orders-stat-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3"><i class="fas fa-flask fa-2x text-info"></i></div>
                <div>
                    <p class="mb-0 text-muted font-12">Quest Submitted</p>
                    <h4 class="mb-0 font-weight-bold">{{ number_format($stats['quest_submitted']) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
        <div class="card orders-stat-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3"><i class="fas fa-exclamation-triangle fa-2x text-danger"></i></div>
                <div>
                    <p class="mb-0 text-muted font-12">Quest Failed</p>
                    <h4 class="mb-0 font-weight-bold">{{ number_format($stats['quest_failed']) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
        <div class="card orders-stat-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3"><i class="fas fa-dollar-sign fa-2x text-success"></i></div>
                <div>
                    <p class="mb-0 text-muted font-12">Paid Revenue</p>
                    <h4 class="mb-0 font-weight-bold">${{ number_format($stats['revenue'] / 100, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="row">
    <div class="col-12 box-margin">
        <div class="card orders-filter-card border-0 shadow-sm">
            <div class="card-body">
                <div class="orders-filter-row">
                    <div class="orders-filter-field orders-filter-search">
                        <label for="orders-search" class="form-label">Search</label>
                        <input type="text" id="orders-search" class="form-control"
                               placeholder="Name, email, phone, company…">
                    </div>
                    <div class="orders-filter-field orders-filter-select">
                        <label for="filter-payment" class="form-label">Payment</label>
                        <select id="filter-payment" class="form-control">
                            <option value="">All</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div class="orders-filter-field orders-filter-select">
                        <label for="filter-quest" class="form-label">Quest Status</label>
                        <select id="filter-quest" class="form-control">
                            <option value="">All</option>
                            <option value="pending">Pending</option>
                            <option value="submitted">Submitted</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div class="orders-filter-field orders-filter-date">
                        <label for="filter-from" class="form-label">From</label>
                        <input type="date" id="filter-from" class="form-control">
                    </div>
                    <div class="orders-filter-field orders-filter-date">
                        <label for="filter-to" class="form-label">To</label>
                        <input type="date" id="filter-to" class="form-control">
                    </div>
                    <div class="orders-filter-field orders-filter-btn">
                        <label class="form-label d-none d-lg-block">&nbsp;</label>
                        <button type="button" id="orders-filter-btn" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="orders-filter-field orders-filter-btn">
                        <label class="form-label d-none d-lg-block">&nbsp;</label>
                        <button type="button" id="orders-clear-btn" class="btn btn-outline-secondary w-100">Reset</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="row">
    <div class="col-12 box-margin">
        <div class="card orders-table-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                    <h6 class="card-title mb-0">{{ $pageTitle }}</h6>
                </div>
                <div class="table-responsive">
                    <table id="orders-datatable" class="table table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Applicant</th>
                                <th>Company</th>
                                <th>Test</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Quest</th>
                                <th>Type</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function ($) {
    'use strict';

    var table = $('#orders-datatable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 25,
        order: [[1, 'desc']],
        ajax: {
            url: @json($dataUrl),
            data: function (d) {
                d.payment_status = $('#filter-payment').val();
                d.quest_status = $('#filter-quest').val();
                d.date_from = $('#filter-from').val();
                d.date_to = $('#filter-to').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '40px' },
            { data: 'created_us', name: 'created_at' },
            { data: 'applicant', name: 'applicant' },
            { data: 'company', name: 'company' },
            { data: 'test_name', name: 'test_name' },
            { data: 'amount_display', name: 'amount' },
            { data: 'payment_badge', name: 'payment_status', orderable: true, searchable: false },
            { data: 'quest_badge', name: 'quest_submission_status', orderable: true, searchable: false },
            { data: 'guest_label', name: 'is_guest', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        language: {
            paginate: {
                previous: "<i class='arrow_carrot-left'></i>",
                next: "<i class='arrow_carrot-right'></i>"
            },
            processing: '<i class="fa fa-spinner fa-spin"></i> Loading…'
        },
        drawCallback: function () {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
        }
    });

    var searchTimer = null;
    $('#orders-search').on('keyup', function () {
        clearTimeout(searchTimer);
        var value = this.value;
        searchTimer = setTimeout(function () {
            table.search(value).draw();
        }, 350);
    });

    $('#orders-filter-btn').on('click', function () {
        table.ajax.reload();
    });

    $('#filter-payment, #filter-quest, #filter-from, #filter-to').on('change', function () {
        table.ajax.reload();
    });

    $('#orders-clear-btn').on('click', function () {
        $('#orders-search').val('');
        $('#filter-payment, #filter-quest, #filter-from, #filter-to').val('');
        table.search('').ajax.reload();
    });
})(jQuery);
</script>
@endpush
