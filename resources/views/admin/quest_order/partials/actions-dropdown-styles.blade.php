@push('styles')
    <style>
        .quest-order-actions-menu {
            padding: 0;
            min-width: 240px;
            max-width: min(320px, calc(100vw - 1rem));
        }

        .quest-order-actions-menu.quest-order-actions-menu--floating {
            position: absolute;
            z-index: 1065;
        }

        .quest-order-actions-scroll {
            max-height: min(360px, 70vh);
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0.5rem 0;
            -webkit-overflow-scrolling: touch;
        }

        .quest-order-actions-scroll .dropdown-header {
            position: sticky;
            top: 0;
            z-index: 1;
            background: #fff;
            margin-bottom: 0;
        }

        .quest-order-table-wrap,
        .quest-order-table-wrap .dataTables_wrapper,
        .quest-order-table-wrap table.dataTable,
        .quest-order-table-wrap .dataTables_scroll,
        .quest-order-table-wrap .dataTables_scrollBody,
        .quest-order-table-wrap .dtr-details,
        .quest-order-table-wrap td.child {
            overflow: visible !important;
        }

        .quest-order-actions-cell .quest-order-actions-group {
            position: static;
        }

        .quest-order-actions-cell {
            overflow: visible;
            vertical-align: middle;
            min-width: 110px;
            white-space: nowrap;
        }

        /* Keep Actions usable inside DataTables responsive child rows */
        table.dataTable > tbody > tr.child ul.dtr-details {
            width: 100%;
        }

        table.dataTable > tbody > tr.child span.dtr-data {
            display: block;
            width: 100%;
            text-align: left;
        }

        table.dataTable > tbody > tr.child .quest-order-actions-cell {
            display: block;
            width: 100%;
            text-align: left;
        }

        @media (max-width: 767.98px) {
            .quest-order-actions-menu {
                min-width: min(260px, calc(100vw - 1rem));
            }

            .quest-order-actions-scroll {
                max-height: min(280px, 60vh);
            }

            #questOrderDeleteModal .modal-dialog,
            #questOrderCancelModal .modal-dialog {
                margin: 0.75rem auto;
                max-width: calc(100% - 1.5rem);
            }
        }
    </style>
@endpush
