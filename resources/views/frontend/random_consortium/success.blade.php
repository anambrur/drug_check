@extends('layouts.frontend.master2')

@section('content')
    <style>
        .success-container {
            padding: 80px 0;
            background: #f8fafc;
        }
        .success-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            background: #fff;
            overflow: hidden;
        }
        .success-icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #d1fae5;
            color: #10b981;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 25px;
        }
        .receipt-table th {
            font-weight: 600;
            color: #64748b;
        }
        .receipt-table td {
            color: #0f172a;
        }
    </style>

    <section class="success-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="card success-card p-5 text-center mb-4">
                        <div class="success-icon-wrapper">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        
                        <h2 class="fw-bold text-slate-900 mb-2">Enrollment Completed!</h2>
                        <p class="text-secondary mb-4">
                            Thank you. Your paid enrollment in the Random Consortium has been registered successfully. A secure confirmation email has been dispatched to <strong>{{ $enrollment->email }}</strong>.
                        </p>

                        <div class="alert alert-info text-start mb-4">
                            <h5 class="fw-bold mb-2"><i class="fa fa-info-circle me-2"></i>What Happens Next?</h5>
                            <ol class="mb-0 ps-3">
                                <li class="mb-1">Our support representatives will review your consortium details.</li>
                                <li class="mb-1">Your DOT driver enrollment certificate will be generated.</li>
                                <li class="mb-0">You will receive your consortium certificate and official credentials via email within 24 business hours.</li>
                            </ol>
                        </div>

                        <!-- Itemized Receipt details -->
                        <div class="text-start">
                            <h4 class="fw-bold mb-3 pb-2 border-bottom"><i class="fa fa-file-text-o me-2"></i>Transaction Details</h4>
                            <table class="table receipt-table table-borderless">
                                <tbody>
                                    <tr>
                                        <th>Enrollment ID:</th>
                                        <td class="text-end fw-bold">#{{ str_pad($enrollment->id, 6, '0', STR_PAD_LEFT) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Company:</th>
                                        <td class="text-end">{{ $enrollment->company_name }} @if($enrollment->dba_name) (DBA: {{ $enrollment->dba_name }}) @endif</td>
                                    </tr>
                                    <tr>
                                        <th>USDOT Number:</th>
                                        <td class="text-end">{{ $enrollment->dot_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Selected Plan:</th>
                                        <td class="text-end">{{ $enrollment->selected_plan }}</td>
                                    </tr>
                                    <tr>
                                        <th>Drivers Registered:</th>
                                        <td class="text-end">{{ $enrollment->driver_count }} driver(s)</td>
                                    </tr>
                                    
                                    @if ($pricing)
                                         @foreach ($pricing->fees as $fee)
                                             <tr class="{{ $loop->first ? 'border-top' : '' }}">
                                                 <th>{{ $fee->fee_label }} @if($fee->fee_type == 'per_driver') (x{{ $enrollment->driver_count }}) @endif:</th>
                                                 <td class="text-end">
                                                     @if($fee->fee_type == 'per_driver')
                                                         ${{ number_format(($fee->fee_amount_in_dollars * $enrollment->driver_count), 2) }}
                                                     @else
                                                         ${{ number_format($fee->fee_amount_in_dollars, 2) }}
                                                     @endif
                                                 </td>
                                             </tr>
                                         @endforeach
                                     @endif

                                    <tr class="border-top table-light font-weight-bold">
                                        <th class="fw-bold">Total Paid (USD):</th>
                                        <td class="text-end text-primary fw-bold fs-5">{{ $enrollment->formatted_amount }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <a href="{{ url('/') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                Return to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
