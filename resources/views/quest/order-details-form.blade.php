@extends('layouts.frontend.master2')

@section('content')
    <div class="pf-show-page svc-page ch-page" style="padding: 6rem 0 4rem;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="pf-card">
                        <div class="pf-header">
                            <span class="pill">Order Lookup</span>
                            <h4>Retrieve Order Details</h4>
                            <p>Enter either Quest Order ID or Reference Test ID to retrieve your order details.</p>
                        </div>
                        <div class="pf-body">
                            @if (session('error'))
                                <div class="pf-alert pf-alert-danger mb-3">
                                    <i class="fas fa-exclamation-circle mt-1"></i>
                                    <div>{{ session('error') }}</div>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('quest.order-details.submit') }}">
                                @csrf

                                <div class="pf-section">
                                    <div class="pf-section-head">
                                        <div class="icon-wrap"><i class="fas fa-search"></i></div>
                                        <h6>Order Identifiers</h6>
                                    </div>
                                    <div class="pf-section-body">
                                        <div class="mb-3">
                                            <label for="quest_order_id" class="pf-label">Quest Order ID</label>
                                            <input type="text"
                                                class="pf-control @error('quest_order_id') is-invalid @enderror"
                                                name="quest_order_id" id="quest_order_id"
                                                value="{{ old('quest_order_id') }}"
                                                placeholder="Enter Quest Order ID">
                                            @error('quest_order_id')
                                                <div class="pf-hint danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label for="reference_test_id" class="pf-label">Reference Test ID</label>
                                            <input type="text"
                                                class="pf-control @error('reference_test_id') is-invalid @enderror"
                                                name="reference_test_id" id="reference_test_id"
                                                value="{{ old('reference_test_id') }}"
                                                placeholder="Enter Reference Test ID">
                                            @error('reference_test_id')
                                                <div class="pf-hint danger">{{ $message }}</div>
                                            @enderror
                                            <p class="pf-hint mt-2 mb-0">At least one of the above fields is required.</p>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="pf-btn-submit">
                                    <i class="fas fa-search"></i>
                                    Retrieve Order Details
                                </button>
                            </form>

                            <p class="pf-secure mt-3">
                                Don't have your order details? Contact support for assistance.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
