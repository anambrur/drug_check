@extends('layouts.frontend.master2')

@section('content')
    <div class="pf-show-page svc-page ch-page" style="padding: 6rem 0 4rem;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="pf-card">
                        <div class="pf-header">
                            <span class="pill">Quest Portal</span>
                            <h4>View Order Details</h4>
                            <p>Access your order status, documents, and collection site options in the Quest portal.</p>
                        </div>
                        <div class="pf-body">
                            <div class="pf-alert pf-alert-info mb-3">
                                <i class="fas fa-info-circle mt-1"></i>
                                <div>The order details URL will expire in 20 seconds. Click the button below to view immediately.</div>
                            </div>

                            <div class="pf-section">
                                <div class="pf-section-head">
                                    <div class="icon-wrap"><i class="fas fa-clipboard-check"></i></div>
                                    <h6>Order Information</h6>
                                </div>
                                <div class="pf-section-body">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-5">Quest Order ID</dt>
                                        <dd class="col-sm-7">{{ $orderDetails['quest_order_id'] ?? 'N/A' }}</dd>
                                        <dt class="col-sm-5">Reference Test ID</dt>
                                        <dd class="col-sm-7">{{ $orderDetails['reference_test_id'] ?? 'N/A' }}</dd>
                                        <dt class="col-sm-5">Client Reference ID</dt>
                                        <dd class="col-sm-7">{{ $orderDetails['client_reference_id'] ?? 'N/A' }}</dd>
                                    </dl>
                                </div>
                            </div>

                            <div class="pf-section">
                                <div class="pf-section-head">
                                    <div class="icon-wrap"><i class="fas fa-lightbulb"></i></div>
                                    <h6>What You Can Do</h6>
                                </div>
                                <div class="pf-section-body">
                                    <ul class="pf-checklist mb-0">
                                        <li><i class="fas fa-vial"></i> View testing status (Collected, At Lab, Pending MRO, etc.)</li>
                                        <li><i class="fas fa-file-alt"></i> Access Authorization Form (QPassport)</li>
                                        <li><i class="fas fa-map-marker-alt"></i> Choose a Collection Site (if not already selected)</li>
                                        <li><i class="fas fa-calendar-check"></i> Schedule an Appointment (at Quest Patient Service Centers)</li>
                                        <li><i class="fas fa-file-signature"></i> View Custody &amp; Control Form (after collection)</li>
                                        <li><i class="fas fa-file-medical"></i> View Lab Report or MRO Letter (when available)</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="text-center pt-2">
                                <a href="{{ $orderDetails['display_url'] }}" target="_blank" rel="noopener noreferrer"
                                    id="quest-portal-btn" class="pf-btn-submit text-decoration-none d-inline-flex"
                                    onclick="markUrlAsUsed()">
                                    <i class="fas fa-external-link-alt"></i>
                                    View Order Details in Quest Portal
                                </a>
                            </div>

                            <p class="pf-secure mt-3">
                                <a href="{{ route('quest.order-details.form') }}">
                                    Search another order
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function markUrlAsUsed() {
            setTimeout(function() {
                const btn = document.getElementById('quest-portal-btn');
                if (btn) {
                    btn.classList.add('disabled');
                    btn.style.pointerEvents = 'none';
                    btn.style.opacity = '0.7';
                    btn.innerHTML = '<i class="fas fa-check"></i> URL Opened';
                }
            }, 1000);
        }

        setTimeout(function() {
            const link = document.getElementById('quest-portal-btn');
            if (link && !link.classList.contains('disabled')) {
                link.click();
                markUrlAsUsed();
            }
        }, 5000);
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

        .pf-show-page #quest-portal-btn {
            width: auto;
            min-width: min(100%, 22rem);
            padding-left: 2rem;
            padding-right: 2rem;
        }
    </style>
@endpush
