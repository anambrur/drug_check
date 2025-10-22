@extends('layouts.frontend.master2')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Order Details</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            The order details URL will expire in 20 seconds. Click the button below to view immediately.
                        </div>

                        <div class="order-info mb-4">
                            <h6 class="text-muted">Order Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Quest Order ID:</strong> {{ $orderDetails['quest_order_id'] ?? 'N/A' }}</p>
                                    <p><strong>Reference Test ID:</strong> {{ $orderDetails['reference_test_id'] ?? 'N/A' }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Client Reference ID:</strong>
                                        {{ $orderDetails['client_reference_id'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mb-4">
                            <a href="{{ $orderDetails['display_url'] }}" target="_blank" class="btn btn-primary btn-lg"
                                onclick="markUrlAsUsed()">
                                <i class="fas fa-external-link-alt me-2"></i> View Order Details in Quest Portal
                            </a>
                        </div>

                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-lightbulb me-2"></i>What You Can Do:</h6>
                                <ul class="mb-0">
                                    <li>View testing status (Collected, At Lab, Pending MRO, etc.)</li>
                                    <li>Access Authorization Form (QPassport)</li>
                                    <li>Choose a Collection Site (if not already selected)</li>
                                    <li>Schedule an Appointment (at Quest Patient Service Centers)</li>
                                    <li>View Custody & Control Form (after collection)</li>
                                    <li>View Lab Report or MRO Letter (when available)</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <a href="{{ route('quest.order-details.form') }}" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-search me-2"></i> Search Another Order
                            </a>
                            {{-- <a href="{{ route('home') }}" class="btn btn-outline-primary">
                                <i class="fas fa-home me-2"></i> Return Home
                            </a> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function markUrlAsUsed() {
            // You can implement tracking here if needed
            console.log('Quest portal URL accessed');

            // Optionally disable the button after click
            setTimeout(function() {
                const btn = document.querySelector('.btn-primary');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-check me-2"></i> URL Used';
                }
            }, 1000);
        }

        // Auto-click the link after 5 seconds if user doesn't click it
        setTimeout(function() {
            const link = document.querySelector('a[target="_blank"]');
            if (link && !link.disabled) {
                link.click();
                markUrlAsUsed();
            }
        }, 5000);
    </script>
@endsection
