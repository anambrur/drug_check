@extends('layouts.admin.master')

@section('content')
<style>
    #quest-orders-datatable_wrapper .dataTables_filter { display: none; }
    #quest-orders-datatable td { vertical-align: middle; }
    #quest-orders-datatable thead th {
        white-space: nowrap;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .02em;
        background: #f8fafc;
        border-bottom-width: 1px;
    }
    .orders-table-card .card-title { font-size: 1rem; }
</style>

<div class="orders-page">
{{-- Stats --}}
<div class="row">
    <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
        <div class="card orders-stat-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="cp-stat-icon cp-stat-icon--blue mr-2"><i class="fas fa-list"></i></div>
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
                <div class="cp-stat-icon cp-stat-icon--cyan mr-2"><i class="fas fa-truck"></i></div>
                <div>
                    <p class="mb-0 text-muted font-12">DOT</p>
                    <h4 class="mb-0 font-weight-bold">{{ number_format($stats['dot']) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
        <div class="card orders-stat-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="cp-stat-icon cp-stat-icon--gray mr-2"><i class="fas fa-flask"></i></div>
                <div>
                    <p class="mb-0 text-muted font-12">Non-DOT</p>
                    <h4 class="mb-0 font-weight-bold">{{ number_format($stats['non_dot']) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
        <div class="card orders-stat-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="cp-stat-icon cp-stat-icon--green mr-2"><i class="fas fa-file-medical"></i></div>
                <div>
                    <p class="mb-0 text-muted font-12">With Result</p>
                    <h4 class="mb-0 font-weight-bold">{{ number_format($stats['with_result']) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
        <div class="card orders-stat-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="cp-stat-icon cp-stat-icon--green mr-2"><i class="fas fa-check-circle"></i></div>
                <div>
                    <p class="mb-0 text-muted font-12">Quest Success</p>
                    <h4 class="mb-0 font-weight-bold">{{ number_format($stats['success']) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
        <div class="card orders-stat-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="cp-stat-icon cp-stat-icon--red mr-2"><i class="fas fa-exclamation-triangle"></i></div>
                <div>
                    <p class="mb-0 text-muted font-12">Quest Failed</p>
                    <h4 class="mb-0 font-weight-bold">{{ number_format($stats['failed']) }}</h4>
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
                        <label for="qo-search" class="form-label">Search</label>
                        <input type="text" id="qo-search" class="form-control"
                               placeholder="Order ID, donor, email, company…">
                    </div>
                    <div class="orders-filter-field orders-filter-select">
                        <label for="filter-test-type" class="form-label">Test Type</label>
                        <select id="filter-test-type" class="form-control">
                            <option value="">All</option>
                            <option value="dot">DOT</option>
                            <option value="non_dot">Non-DOT</option>
                        </select>
                    </div>
                    <div class="orders-filter-field orders-filter-select">
                        <label for="filter-order-status" class="form-label">Status</label>
                        <select id="filter-order-status" class="form-control">
                            <option value="">All</option>
                            <option value="pending">Pending</option>
                            <option value="SITESELECTED">Site Selected</option>
                            <option value="COLLECTED">Collected</option>
                            <option value="ATLAB">At Lab</option>
                            <option value="PENDINGMRO">Pending MRO</option>
                            <option value="PENDINGFAX">Pending Fax</option>
                            <option value="PARTIAL">Partial</option>
                            <option value="SUSPENDED">Suspended</option>
                        </select>
                    </div>
                    <div class="orders-filter-field orders-filter-select">
                        <label for="filter-order-result" class="form-label">Result</label>
                        <select id="filter-order-result" class="form-control">
                            <option value="">All</option>
                            <option value="Negative">Negative</option>
                            <option value="Positive">Positive</option>
                            <option value="Cancelled">Cancelled</option>
                            <option value="No Show Expired">No Show Expired</option>
                            <option value="none">Not Available</option>
                        </select>
                    </div>
                    <div class="orders-filter-field orders-filter-select">
                        <label for="filter-create-status" class="form-label">Create Status</label>
                        <select id="filter-create-status" class="form-control">
                            <option value="">All</option>
                            <option value="SUCCESS">Success</option>
                            <option value="FAILURE">Failed</option>
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
                        <label class="form-label d-none d-xl-block">&nbsp;</label>
                        <button type="button" id="qo-filter-btn" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="orders-filter-field orders-filter-btn">
                        <label class="form-label d-none d-xl-block">&nbsp;</label>
                        <button type="button" id="qo-clear-btn" class="btn btn-outline-secondary w-100">Reset</button>
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
                    <h6 class="card-title mb-0">Quest Order List</h6>
                    @can('quest-order create')
                        <a href="{{ route('quest-order.create') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> Add Quest Order
                        </a>
                    @endcan
                </div>
                <div class="quest-order-table-wrap">
                    <table id="quest-orders-datatable" class="table table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Quest Order</th>
                                <th>Company</th>
                                <th>Donor</th>
                                <th>Test Type</th>
                                <th>Status</th>
                                <th>Result</th>
                                <th>Created</th>
                                <th class="text-center all">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.quest_order.partials.action-modals')
@include('admin.quest_order.partials.actions-dropdown-styles')
@include('admin.quest_order.partials.actions-scripts')
</div>
@endsection

@push('scripts')
<script>
(function ($) {
    'use strict';

    var table = $('#quest-orders-datatable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 25,
        order: [[7, 'desc']],
        ajax: {
            url: @json($dataUrl),
            data: function (d) {
                d.test_type = $('#filter-test-type').val();
                d.order_status = $('#filter-order-status').val();
                d.order_result = $('#filter-order-result').val();
                d.create_status = $('#filter-create-status').val();
                d.date_from = $('#filter-from').val();
                d.date_to = $('#filter-to').val();
            }
        },
        columnDefs: [
            {
                // Keep Actions always visible — never bury it under the + child row
                targets: -1,
                className: 'text-center all quest-order-actions-cell',
                responsivePriority: 1,
                orderable: false,
                searchable: false
            },
            { responsivePriority: 2, targets: 1 },
            { responsivePriority: 3, targets: 3 },
            { responsivePriority: 4, targets: 7 },
            { responsivePriority: 5, targets: 0 }
        ],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '40px' },
            { data: 'quest_id', name: 'quest_id' },
            { data: 'company', name: 'company' },
            { data: 'donor', name: 'donor' },
            { data: 'test_type_badge', name: 'dot_test', searchable: false },
            { data: 'status_badge', name: 'order_status', orderable: false, searchable: false },
            { data: 'result_badge', name: 'order_result', orderable: false, searchable: false },
            { data: 'created_us', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
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
    $('#qo-search').on('keyup', function () {
        clearTimeout(searchTimer);
        var value = this.value;
        searchTimer = setTimeout(function () {
            table.search(value).draw();
        }, 350);
    });

    $('#qo-filter-btn').on('click', function () {
        table.ajax.reload();
    });

    $('#filter-test-type, #filter-order-status, #filter-order-result, #filter-create-status, #filter-from, #filter-to')
        .on('change', function () {
            table.ajax.reload();
        });

    $('#qo-clear-btn').on('click', function () {
        $('#qo-search').val('');
        $('#filter-test-type, #filter-order-status, #filter-order-result, #filter-create-status, #filter-from, #filter-to').val('');
        table.search('').ajax.reload();
    });
})(jQuery);
</script>
@endpush
