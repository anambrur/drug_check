@extends('layouts.admin.master')

@section('title', 'Employee Report')

@section('content')
    <!-- Form row -->
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h3 class="card-title">Employee Report</h3>
                    <button id="printButton" class="btn btn-primary">
                        <i class="fa fa-print"></i> Print
                    </button>
                </div>
                <div id="printable-section">
                    <div class="row print-header">

                        <div class="col-md-4 print-image">
                            <div class="media">
                                @if (!empty($header_image->section_image))
                                    <a class="d-block mx-auto" href="#" data-toggle="tooltip" data-placement="top"
                                        data-original-title="{{ __('content.current_image') }}">
                                        <img src="{{ asset('uploads/img/general/' . $header_image->section_image) }}"
                                            alt="logo image" class="rounded">
                                    </a>
                                @else
                                    <a class="d-block mx-auto" href="#" data-toggle="tooltip" data-placement="top"
                                        data-original-title="{{ __('content.not_yet_created') }}">
                                        <img src="{{ asset('uploads/img/dummy/no-image.jpg') }}" alt="no image"
                                            class="rounded w-25">
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="table-responsive">
                        <table id="" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Middle Name</th>
                                    <th>Employee ID</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>DOB</th>
                                    <th>Status</th>
                                    <th>Dot</th>
                                    <th>Created</th>
                                    <th>Modified</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $employee)
                                    <tr>
                                        <td>{{ $employee->clientProfile->company_name }}</td>
                                        <td>{{ $employee->first_name }}</td>
                                        <td>{{ $employee->last_name }}</td>
                                        <td>{{ $employee->middle_name }}</td>
                                        <td>{{ $employee->employee_id }}</td>
                                        <td>{{ $employee->email }}</td>
                                        <td>{{ $employee->phone }}</td>
                                        <td>{{ $employee->date_of_birth }}</td>
                                        <td>
                                            @if ($employee->status == 'active')
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $employee->dot }}</td>
                                        <td>{{ $employee->created_at }}</td>
                                        <td>{{ $employee->updated_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <style>
        /* Print-specific styles */
        @media print {

            /* Hide everything initially */
            body * {
                visibility: hidden;
            }

            /* Ensure the printable section and all its children are visible */
            #printable-section,
            #printable-section * {
                visibility: visible;
            }

            /* Position the printable section to take full page */
            #printable-section {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 20px !important;
                background: white !important;
            }

            /* Print header layout */
            .print-header {
                display: flex !important;
                justify-content: space-between !important;
                align-items: flex-start !important;
                margin-bottom: 20px !important;
                width: 100% !important;
            }

            .print-info {
                width: 60% !important;
                float: left !important;
            }

            .print-image {
                width: 40% !important;
                float: right !important;
                text-align: right !important;
            }

            .print-image img {
                max-width: 150px !important;
                height: auto !important;
            }

            /* Fix for the info columns */
            .print-info .row {
                display: flex !important;
                width: 100% !important;
            }

            .print-info .col-md-4,
            .print-info .col-md-8 {
                padding: 0 !important;
                margin: 0 !important;
            }

            .print-info .col-md-4 {
                width: 40% !important;
            }

            .print-info .col-md-8 {
                width: 60% !important;
            }

            /* Hide unnecessary elements */
            #printButton,
            .sidebar,
            .navbar,
            .breadcrumb,
            .footer,
            .btn,
            .no-print {
                display: none !important;
            }

            /* Ensure table takes full width */
            .table {
                width: 100% !important;
                font-size: 12px !important;
                border-collapse: collapse !important;
            }

            .table th,
            .table td {
                padding: 8px !important;
                border: 1px solid #ddd !important;
            }

            /* Remove card styling for print */
            .card {
                border: none !important;
                box-shadow: none !important;
                background: transparent !important;
            }

            .card-body {
                padding: 0 !important;
            }

            /* Adjust spacing for print */
            .row {
                margin: 0 !important;
            }
        }

        /* Screen styles */
        @media screen {
            .print-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
            }

            .print-image img {
                max-width: 200px;
                height: auto;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('printButton').addEventListener('click', function() {
                window.print();
            });
        });
    </script>
@endsection
