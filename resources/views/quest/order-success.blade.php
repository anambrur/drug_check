@extends('layouts.frontend.master2')



@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
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
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('home') }}" class="btn btn-outline-primary px-4">
                            <i class="fas fa-home me-2"></i> Return Home
                        </a>
                        <button class="btn btn-primary px-4" onclick="window.print()">
                            <i class="fas fa-print me-2"></i> Print Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
</style>
@endpush