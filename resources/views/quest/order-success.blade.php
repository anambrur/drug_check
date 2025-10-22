@extends('layouts.frontend.master2')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center p-5">
                        <div class="checkmark-circle mb-4">
                            <div class="checkmark draw"></div>
                        </div>

                        <h2 class="mb-3 text-success">Test Successfully Scheduled!</h2>
                        <p class="lead mb-4">Your Quest Diagnostics order has been created successfully.</p>

                        <div class="order-details bg-light p-4 rounded-3 mb-4 text-start">
                            <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Order Details</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Quest Order ID:</strong> {{ $questOrderId }}</p>
                                    <p><strong>Reference Test ID:</strong> {{ $referenceTestId }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Next Steps:</strong></p>
                                    <ul class="mb-0">
                                        <li>You'll receive an email with your QPassport</li>
                                        <li>Bring your ID to the collection site</li>
                                        <li>Check your email for results</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="download-section mb-4">
                            <h5 class="mb-3"><i class="fas fa-download me-2"></i>Download Documents</h5>
                            <div class="d-flex flex-wrap justify-content-center gap-2">
                                {{-- QPassport Button --}}
                                <a href="{{ route('quest.get-document', ['id' => $questOrderId, 'docType' => 'QPassport']) }}"
                                    class="btn btn-info mb-2">
                                    <i class="fas fa-file-alt me-2"></i> QPassport
                                </a>


                                {{-- <a href="{{ route('quest.get-document', ['id' => $questOrderId, 'docType' => 'LabReport']) }}"
                                    class="btn btn-primary mb-2">
                                    <i class="fas fa-flask me-2"></i> Lab Report
                                </a>
                            --}}
                            </div>

                           

                            <div class="mt-3">
                                <a href="{{ route('quest.order-details.direct', ['questOrderId' => $questOrderId, 'referenceTestId' => $referenceTestId]) }}"
                                    class="btn btn-outline-info">
                                    <i class="fas fa-eye me-2"></i> View Order Details
                                </a>
                            </div>

                            <small class="text-muted mt-2 d-block">
                                Note: Some documents may not be available until later stages of the testing process.
                            </small>
                        </div>

                        <div class="d-flex justify-content-center gap-3">
                            <button class="btn btn-outline-primary px-4" onclick="printOrderDetails()">
                                <i class="fas fa-print me-2"></i> Print This Page
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden div for printing --}}
    <div id="printable-area" class="d-none">
        <div class="print-header text-center mb-4">
            <h2 class="text-success">Quest Diagnostics Order Confirmation</h2>
            <p class="text-muted">Order successfully scheduled on {{ now()->format('F j, Y \a\t g:i A') }}</p>
            <hr>
        </div>

        <div class="order-details-print">
            <h4>Order Details</h4>
            <table class="table table-bordered">
                <tr>
                    <th>Quest Order ID:</th>
                    <td>{{ $questOrderId }}</td>
                </tr>
                <tr>
                    <th>Reference Test ID:</th>
                    <td>{{ $referenceTestId }}</td>
                </tr>
                <tr>
                    <th>Order Date:</th>
                    <td>{{ now()->format('F j, Y \a\t g:i A') }}</td>
                </tr>
            </table>
        </div>

        <div class="next-steps-print mt-4">
            <h4>Next Steps</h4>
            <ul>
                <li>You'll receive an email with your QPassport</li>
                <li>Bring your government-issued ID to the collection site</li>
                <li>Check your email for test results when available</li>
                <li>Contact Quest Diagnostics if you have any questions</li>
            </ul>
        </div>

        <div class="print-footer text-center mt-4">
            <hr>
            <p class="text-muted">Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        </div>
    </div>
    <script>
        function printOrderDetails() {
            // Create a print-friendly version
            const printContent = document.getElementById('printable-area').innerHTML;
            const originalContent = document.body.innerHTML;

            // Replace body content with printable content
            document.body.innerHTML = printContent;

            // Print the page
            window.print();

            // Restore original content
            document.body.innerHTML = originalContent;

            // Reload the page to restore functionality (optional)
            window.location.reload();
        }
    </script>
@endsection

@push('styles')
    <style>
        .checkmark-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #28a745;
            margin: 0 auto 20px;
        }

        .checkmark {
            width: 50px;
            height: 50px;
        }

        .checkmark.draw:after {
            animation: checkmark 0.8s ease forwards;
            content: '';
            display: block;
            position: relative;
            left: 15px;
            top: 25px;
            width: 20px;
            height: 40px;
            border-right: 5px solid white;
            border-top: 5px solid white;
            transform: scaleX(-1) rotate(135deg);
        }

        @keyframes checkmark {
            0% {
                height: 0;
                width: 0;
                opacity: 1;
            }

            20% {
                height: 0;
                width: 20px;
                opacity: 1;
            }

            40% {
                height: 40px;
                width: 20px;
                opacity: 1;
            }

            100% {
                height: 40px;
                width: 20px;
                opacity: 1;
            }
        }

        .order-details {
            border-left: 4px solid #0d6efd;
        }

        .download-section {
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
        }

        /* Print-specific styles */
        @media print {

            /* Hide everything except the printable area */
            body * {
                visibility: hidden;
            }

            #printable-area,
            #printable-area * {
                visibility: visible;
            }

            #printable-area {
                display: block !important;
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                background: white;
            }

            /* Remove browser headers and footers */
            @page {
                margin: 0;
                size: auto;
            }

            body {
                margin: 1.6cm;
                /* Add some margin for content */
                padding: 0;
            }

            /* Hide buttons and other interactive elements in print */
            .btn,
            .download-section,
            .checkmark-circle {
                display: none !important;
            }

            /* Style for printable content */
            .print-header {
                margin-bottom: 20px;
            }

            .order-details-print table {
                width: 100%;
                border-collapse: collapse;
            }

            .order-details-print th,
            .order-details-print td {
                padding: 8px;
                border: 1px solid #ddd;
            }

            .order-details-print th {
                background-color: #f8f9fa;
                font-weight: bold;
                width: 30%;
            }

            .next-steps-print ul {
                padding-left: 20px;
            }

            .next-steps-print li {
                margin-bottom: 8px;
            }

            .print-footer {
                margin-top: 30px;
                font-size: 12px;
            }
        }
    </style>
@endpush
