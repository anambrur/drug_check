<script>
(function ($) {
    'use strict';

    var table = $('#result-recording-datatable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 25,
        order: [[1, 'desc']],
        ajax: {
            url: @json($dataUrl),
            data: function (d) {
                d.company_id = $('#filter-company').val() || '';
                d.status = $('#filter-status').val();
                d.reason_for_test = $('#filter-reason').val();
                d.date_from = $('#filter-from').val();
                d.date_to = $('#filter-to').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '40px' },
            { data: 'collected_us', name: 'collection_datetime' },
            { data: 'company', name: 'company' },
            { data: 'employee_name', name: 'employee_name' },
            { data: 'reason', name: 'reason' },
            { data: 'test_name', name: 'test_name' },
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
    $('#rr-search').on('keyup', function () {
        clearTimeout(searchTimer);
        var value = this.value;
        searchTimer = setTimeout(function () {
            table.search(value).draw();
        }, 350);
    });

    $('#rr-filter-btn').on('click', function () {
        table.ajax.reload();
    });

    $('#filter-company, #filter-status, #filter-reason, #filter-from, #filter-to').on('change', function () {
        table.ajax.reload();
    });

    $('#rr-clear-btn').on('click', function () {
        $('#rr-search').val('');
        $('#filter-company, #filter-status, #filter-reason, #filter-from, #filter-to').val('');
        table.search('').ajax.reload();
    });

    $(document).on('click', '.rr-delete-btn', function (e) {
        e.preventDefault();
        $('#rr-delete-form').attr('action', $(this).data('url'));
        $('#rrDeleteModal').modal('show');
    });

    $(document).on('click', '.rr-notify-btn', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var company = $btn.data('company') || 'N/A';

        $('#rr-notify-form').attr('action', $btn.data('url'));
        $('#rr-notify-company-title, #rr-notify-company-1, #rr-notify-company-2, #rr-notify-company-3').text(company);
        $('#rr-notify-employee').text($btn.data('employee') || 'N/A');
        $('#rr-notify-phone').text($btn.data('phone') || 'N/A');
        $('#rr-notify-date').text($btn.data('date') || 'N/A');
        $('#rr-notify-der-name').text($btn.data('der-name') || 'N/A');
        $('#rr-notify-der-email').text($btn.data('der-email') || 'N/A');
        $('#rr-additional-text').val('');
        $('#rr-pdf-attachment').val('');
        $('#rr-pdf-attachment').next('.custom-file-label').text('Choose file');
        $('#rrNotifyModal').modal('show');
    });

    $('#rr-pdf-attachment').on('change', function (e) {
        var fileName = e.target.files[0] ? e.target.files[0].name : 'Choose file';
        $(this).next('.custom-file-label').text(fileName);
    });
})(jQuery);
</script>
