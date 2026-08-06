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
    .orders-filter-select { flex: 0 1 160px; min-width: 140px; }
    .orders-filter-date { flex: 0 1 150px; min-width: 135px; }
    .orders-filter-btn { flex: 0 0 100px; }
    @media (min-width: 992px) {
        .orders-filter-row { flex-wrap: nowrap; }
        .orders-filter-search { flex: 1 1 auto; }
        .orders-filter-select { flex: 0 1 160px; }
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
    #consortium-orders-datatable_wrapper .dataTables_filter { display: none; }
    #consortium-orders-datatable td { vertical-align: middle; }
    #consortium-orders-datatable thead th {
        white-space: nowrap;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .02em;
        background: #f8fafc;
        border-bottom-width: 1px;
    }
</style>

<div class="row">
    <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
        <div class="card orders-stat-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3"><i class="fas fa-list fa-2x text-primary"></i></div>
                <div>
                    <p class="mb-0 text-muted font-12">Total</p>
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
                    <p class="mb-0 text-muted font-12">Active</p>
                    <h4 class="mb-0 font-weight-bold">{{ number_format($stats['active']) }}</h4>
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
                <div class="mr-3"><i class="fas fa-credit-card fa-2x text-info"></i></div>
                <div>
                    <p class="mb-0 text-muted font-12">Paid</p>
                    <h4 class="mb-0 font-weight-bold">{{ number_format($stats['payment_completed']) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
        <div class="card orders-stat-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3"><i class="fas fa-search fa-2x text-secondary"></i></div>
                <div>
                    <p class="mb-0 text-muted font-12">Under Review</p>
                    <h4 class="mb-0 font-weight-bold">{{ number_format($stats['under_review']) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
        <div class="card orders-stat-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3"><i class="fas fa-dollar-sign fa-2x text-success"></i></div>
                <div>
                    <p class="mb-0 text-muted font-12">Revenue</p>
                    <h4 class="mb-0 font-weight-bold">${{ number_format($stats['revenue'] / 100, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 box-margin">
        <div class="card orders-filter-card border-0 shadow-sm">
            <div class="card-body">
                <div class="orders-filter-row">
                    <div class="orders-filter-field orders-filter-search">
                        <label for="consortium-search" class="form-label">Search</label>
                        <input type="text" id="consortium-search" class="form-control"
                               placeholder="Company, USDOT, contact, email…">
                    </div>
                    <div class="orders-filter-field orders-filter-select">
                        <label for="filter-status" class="form-label">Status</label>
                        <select id="filter-status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="Pending Payment">Pending Payment</option>
                            <option value="Payment Completed">Payment Completed</option>
                            <option value="Under Review">Under Review</option>
                            <option value="Contacted">Contacted</option>
                            <option value="Credentials Sent">Credentials Sent</option>
                            <option value="Active">Active</option>
                            <option value="Cancelled">Cancelled</option>
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
                        <button type="button" id="consortium-filter-btn" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="orders-filter-field orders-filter-btn">
                        <label class="form-label d-none d-lg-block">&nbsp;</label>
                        <button type="button" id="consortium-clear-btn" class="btn btn-outline-secondary w-100">Reset</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 box-margin">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                    <h6 class="card-title mb-0">Random Consortium Orders</h6>
                </div>
                <div class="table-responsive">
                    <table id="consortium-orders-datatable" class="table table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Company</th>
                                <th>USDOT</th>
                                <th>Contact</th>
                                <th>Plan</th>
                                <th>Drivers</th>
                                <th>Amount</th>
                                <th>Status</th>
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

    var table = $('#consortium-orders-datatable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 25,
        order: [[1, 'desc']],
        ajax: {
            url: @json(route('consortium-enrollments.data')),
            data: function (d) {
                d.status = $('#filter-status').val();
                d.date_from = $('#filter-from').val();
                d.date_to = $('#filter-to').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '40px' },
            { data: 'created_us', name: 'created_at' },
            { data: 'company', name: 'company' },
            { data: 'dot_number', name: 'dot_number', defaultContent: '—' },
            { data: 'contact', name: 'contact' },
            { data: 'plan', name: 'selected_plan' },
            { data: 'drivers', name: 'driver_count', searchable: false },
            { data: 'amount_display', name: 'amount' },
            { data: 'status_badge', name: 'status', searchable: false },
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
    $('#consortium-search').on('keyup', function () {
        clearTimeout(searchTimer);
        var value = this.value;
        searchTimer = setTimeout(function () {
            table.search(value).draw();
        }, 350);
    });

    $('#consortium-filter-btn').on('click', function () {
        table.ajax.reload();
    });

    $('#filter-status, #filter-from, #filter-to').on('change', function () {
        table.ajax.reload();
    });

    $('#consortium-clear-btn').on('click', function () {
        $('#consortium-search').val('');
        $('#filter-status, #filter-from, #filter-to').val('');
        table.search('').ajax.reload();
    });
})(jQuery);
</script>
@endpush
