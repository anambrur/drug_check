@extends('layouts.admin.master')

@section('content')
<style>
    #clearing-house-orders-datatable_wrapper .dataTables_filter { display: none; }
    #clearing-house-orders-datatable td { vertical-align: middle; }
    #clearing-house-orders-datatable thead th {
        white-space: nowrap;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .02em;
        background: #f8fafc;
        border-bottom-width: 1px;
    }
</style>

<div class="orders-page">
<div class="row">
    <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
        <div class="card orders-stat-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="cp-stat-icon cp-stat-icon--blue mr-2"><i class="fas fa-list"></i></div>
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
                <div class="cp-stat-icon cp-stat-icon--green mr-2"><i class="fas fa-check-circle"></i></div>
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
                <div class="cp-stat-icon cp-stat-icon--amber mr-2"><i class="fas fa-clock"></i></div>
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
                <div class="cp-stat-icon cp-stat-icon--cyan mr-2"><i class="fas fa-credit-card"></i></div>
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
                <div class="cp-stat-icon cp-stat-icon--gray mr-2"><i class="fas fa-search"></i></div>
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
                <div class="cp-stat-icon cp-stat-icon--green mr-2"><i class="fas fa-dollar-sign"></i></div>
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
                        <label for="clearing-house-search" class="form-label">Search</label>
                        <input type="text" id="clearing-house-search" class="form-control"
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
                        <button type="button" id="clearing-house-filter-btn" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="orders-filter-field orders-filter-btn">
                        <label class="form-label d-none d-lg-block">&nbsp;</label>
                        <button type="button" id="clearing-house-clear-btn" class="btn btn-outline-secondary w-100">Reset</button>
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
                    <h6 class="card-title mb-0">Clearing House Orders</h6>
                </div>
                <div class="table-responsive">
                    <table id="clearing-house-orders-datatable" class="table table-hover w-100 mb-0">
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
</div>
@endsection

@push('scripts')
<script>
(function ($) {
    'use strict';

    var table = $('#clearing-house-orders-datatable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 25,
        order: [[1, 'desc']],
        ajax: {
            url: @json(route('clearing-house-enrollments.data')),
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
    $('#clearing-house-search').on('keyup', function () {
        clearTimeout(searchTimer);
        var value = this.value;
        searchTimer = setTimeout(function () {
            table.search(value).draw();
        }, 350);
    });

    $('#clearing-house-filter-btn').on('click', function () {
        table.ajax.reload();
    });

    $('#filter-status, #filter-from, #filter-to').on('change', function () {
        table.ajax.reload();
    });

    $('#clearing-house-clear-btn').on('click', function () {
        $('#clearing-house-search').val('');
        $('#filter-status, #filter-from, #filter-to').val('');
        table.search('').ajax.reload();
    });
})(jQuery);
</script>
@endpush
