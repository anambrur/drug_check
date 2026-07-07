@push('styles')
    <style>
        /* Inner scroll — Popper sees fixed height, Bootstrap dropdown still works */
        .quest-order-actions-menu {
            padding: 0;
            min-width: 240px;
        }

        .quest-order-actions-scroll {
            max-height: 360px;
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

        /* Stop table/card from clipping the menu on top rows */
        .quest-order-table-wrap,
        .quest-order-table-wrap .dataTables_wrapper,
        .quest-order-table-wrap .dataTables_scrollBody {
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
    </style>
@endpush
