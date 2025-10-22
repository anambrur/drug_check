@extends('layouts.frontend.master2')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-search me-2"></i>Retrieve Order Details</h5>
                    </div>
                    <div class="card-body p-4">
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('quest.order-details.submit') }}">
                            @csrf

                            <div class="mb-4">
                                <p class="text-muted">Enter either Quest Order ID or Reference Test ID to retrieve order
                                    details.</p>
                            </div>

                            <div class="mb-3">
                                <label for="quest_order_id" class="form-label">Quest Order ID</label>
                                <input type="text" class="form-control @error('quest_order_id') is-invalid @enderror"
                                    name="quest_order_id" id="quest_order_id" value="{{ old('quest_order_id') }}"
                                    placeholder="Enter Quest Order ID">
                                @error('quest_order_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="reference_test_id" class="form-label">Reference Test ID</label>
                                <input type="text" class="form-control @error('reference_test_id') is-invalid @enderror"
                                    name="reference_test_id" id="reference_test_id" value="{{ old('reference_test_id') }}"
                                    placeholder="Enter Reference Test ID">
                                @error('reference_test_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">At least one of the above fields is required.</div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-search me-2"></i> Retrieve Order Details
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="text-muted mb-2">Don't have your order details?</p>
                            {{-- <a href="{{ route('contact') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-envelope me-2"></i> Contact Support
                            </a> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
