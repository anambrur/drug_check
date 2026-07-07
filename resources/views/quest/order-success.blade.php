@extends('layouts.frontend.master2')

@section('content')
    <div class="pf-show-page svc-page ch-page" style="padding: 6rem 0 4rem;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="pf-card">
                        <div class="pf-header">
                            <span class="pill">Quest Diagnostics</span>
                            <h4>Test Successfully Scheduled!</h4>
                            <p>Your Quest Diagnostics order has been created successfully.</p>
                        </div>
                        <div class="pf-body">
                            <div class="pf-alert pf-alert-success mb-3">
                                <i class="fas fa-check-circle mt-1"></i>
                                <div>Your order is confirmed. Save your order IDs below for future reference.</div>
                            </div>

                            <div class="pf-section">
                                <div class="pf-section-head">
                                    <div class="icon-wrap"><i class="fas fa-clipboard-check"></i></div>
                                    <h6>Order Details</h6>
                                </div>
                                <div class="pf-section-body">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-5">Quest Order ID</dt>
                                        <dd class="col-sm-7">{{ $questOrderId }}</dd>
                                        <dt class="col-sm-5">Reference Test ID</dt>
                                        <dd class="col-sm-7">{{ $referenceTestId }}</dd>
                                        <dt class="col-sm-5">Order Date</dt>
                                        <dd class="col-sm-7">{{ now()->format('m/d/Y h:i A') }}</dd>
                                    </dl>
                                </div>
                            </div>

                            <div class="pf-section">
                                <div class="pf-section-head">
                                    <div class="icon-wrap"><i class="fas fa-tasks"></i></div>
                                    <h6>Next Steps</h6>
                                </div>
                                <div class="pf-section-body">
                                    <ul class="pf-checklist mb-0">
                                        <li><i class="fas fa-envelope"></i> You'll receive an email with your QPassport</li>
                                        <li><i class="fas fa-id-card"></i> Bring your government-issued ID to the collection site</li>
                                        <li><i class="fas fa-file-medical"></i> Check your email for test results when available</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="pf-section">
                                <div class="pf-section-head">
                                    <div class="icon-wrap"><i class="fas fa-download"></i></div>
                                    <h6>Download Documents</h6>
                                </div>
                                <div class="pf-section-body">
                                    <div class="pf-btn-stack">
                                        <a href="{{ route('quest.get-document', ['id' => $questOrderId, 'docType' => 'QPassport']) }}"
                                            class="pf-btn-submit text-decoration-none mb-2">
                                            <i class="fas fa-file-alt"></i>
                                            Download QPassport
                                        </a>
                                        @php $screenService = app(\App\Services\Quest\QuestOrderScreenService::class); @endphp
                                        @if (isset($order) && $screenService->isResultAvailable($order))
                                            <a href="{{ route('quest.download-result', ['quest_order_id' => $questOrderId]) }}"
                                                class="pf-btn-submit text-decoration-none mb-2">
                                                <i class="fas fa-file-pdf"></i>
                                                Download Test Result (PDF)
                                            </a>
                                        @endif
                                        <a href="{{ route('quest.order-details.direct', ['questOrderId' => $questOrderId, 'referenceTestId' => $referenceTestId]) }}"
                                            class="pf-btn-submit pf-btn-secondary text-decoration-none mb-2">
                                            <i class="fas fa-eye"></i>
                                            View Order Details
                                        </a>
                                        {{-- <button type="button" class="pf-btn-submit pf-btn-secondary mb-2" onclick="printOrderDetails()">
                                            <i class="fas fa-print"></i>
                                            Print This Page
                                        </button> --}}
                                    </div>
                                    <p class="pf-hint text-center mt-3 mb-0">
                                        Some documents may not be available until later stages of the testing process.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="printable-area" class="d-none">
        <div class="print-header text-center mb-4">
            <h2>Quest Diagnostics Order Confirmation</h2>
            <p>Order successfully scheduled on {{ now()->format('m/d/Y h:i A') }}</p>
            <hr>
        </div>

        <div class="order-details-print">
            <h4>Order Details</h4>
            <table>
                <tr>
                    <th>Quest Order ID</th>
                    <td>{{ $questOrderId }}</td>
                </tr>
                <tr>
                    <th>Reference Test ID</th>
                    <td>{{ $referenceTestId }}</td>
                </tr>
                <tr>
                    <th>Order Date</th>
                    <td>{{ now()->format('m/d/Y h:i A') }}</td>
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
            <p>Generated on {{ now()->format('m/d/Y h:i A') }}</p>
        </div>
    </div>

    <script>
        function printOrderDetails() {
            const printContent = document.getElementById('printable-area').innerHTML;
            const originalContent = document.body.innerHTML;

            document.body.innerHTML = printContent;
            window.print();
            document.body.innerHTML = originalContent;
            window.location.reload();
        }
    </script>
@endsection

@push('styles')
    <style>
        .pf-show-page .pf-checklist {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .pf-show-page .pf-checklist li {
            display: flex;
            align-items: flex-start;
            gap: .65rem;
            padding: .55rem 0;
            font-size: .88rem;
            color: var(--pf-text);
            border-bottom: 1px dashed var(--pf-border);
        }

        .pf-show-page .pf-checklist li:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .pf-show-page .pf-checklist li i {
            color: var(--main-color);
            margin-top: .15rem;
            width: 1rem;
            text-align: center;
            flex-shrink: 0;
        }

        .pf-show-page .pf-btn-stack {
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }

        .pf-show-page .pf-btn-secondary {
            background: var(--pf-surface-2);
            color: var(--pf-primary-dark);
            border: 1.5px solid color-mix(in srgb, var(--main-color) 22%, transparent);
            box-shadow: none;
        }

        .pf-show-page .pf-btn-secondary:hover {
            background: var(--pf-primary-light);
            color: var(--pf-primary-dark);
            box-shadow: 0 6px 20px color-mix(in srgb, var(--main-color) 18%, transparent);
        }

        @media print {
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
                background: #fff;
            }

            @page {
                margin: 0;
                size: auto;
            }

            body {
                margin: 1.6cm;
                padding: 0;
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
                background: #f8f9fa;
                font-weight: 700;
                width: 30%;
                text-align: left;
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
                color: #64748b;
            }
        }
    </style>
@endpush
