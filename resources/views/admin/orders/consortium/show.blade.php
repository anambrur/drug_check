@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <!-- Back Button -->
        <div class="col-12 mb-4">
            <a href="{{ route('consortium-enrollments.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left mr-2"></i> Back to Listing
            </a>
        </div>

        <!-- Left Column: Enrollment Information -->
        <div class="col-lg-7 box-margin">
            <div class="card card-body">
                <h4 class="card-title pb-3 border-bottom d-flex justify-content-between align-items-center">
                    <span>Company & DER Details</span>
                    <span class="badge badge-secondary font-14">Ref #{{ str_pad($enrollment->id, 6, '0', STR_PAD_LEFT) }}</span>
                </h4>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered mt-3">
                        <tbody>
                            <!-- Company -->
                            <tr>
                                <th style="width: 30%">Company Name:</th>
                                <td><strong>{{ $enrollment->company_name }}</strong></td>
                            </tr>
                            <tr>
                                <th>DBA Name:</th>
                                <td>{{ $enrollment->dba_name ?: '—' }}</td>
                            </tr>
                            <tr>
                                <th>USDOT Number:</th>
                                <td><code>{{ $enrollment->dot_number }}</code></td>
                            </tr>
                            <tr>
                                <th>MC Number:</th>
                                <td>{{ $enrollment->mc_number ?: '—' }}</td>
                            </tr>
                            <tr>
                                <th>EIN / Tax ID:</th>
                                <td>{{ $enrollment->ein_number ?: '—' }}</td>
                            </tr>

                            <!-- Contact -->
                            <tr class="table-info">
                                <th colspan="2" class="font-weight-bold">Designated Employer Representative (DER)</th>
                            </tr>
                            <tr>
                                <th>Contact Representative:</th>
                                <td>{{ $enrollment->first_name }} {{ $enrollment->last_name }}</td>
                            </tr>
                            <tr>
                                <th>DER Email:</th>
                                <td><a href="mailto:{{ $enrollment->email }}">{{ $enrollment->email }}</a></td>
                            </tr>
                            <tr>
                                <th>DER Phone:</th>
                                <td>{{ $enrollment->phone }}</td>
                            </tr>

                            <!-- Address -->
                            <tr class="table-info">
                                <th colspan="2" class="font-weight-bold">Company Address</th>
                            </tr>
                            <tr>
                                <th>Address Details:</th>
                                <td>
                                    {{ $enrollment->address_line_1 }}
                                    @if($enrollment->address_line_2)<br>{{ $enrollment->address_line_2 }}@endif
                                    <br>{{ $enrollment->city }}, {{ $enrollment->state }} {{ $enrollment->zip_code }}
                                </td>
                            </tr>

                            <!-- Order specs -->
                            <tr class="table-info">
                                <th colspan="2" class="font-weight-bold">Compliance Registration Info</th>
                            </tr>
                            <tr>
                                <th>Selected Plan:</th>
                                <td><strong>{{ $enrollment->selected_plan }}</strong></td>
                            </tr>
                            <tr>
                                <th>Registered Drivers:</th>
                                <td>{{ $enrollment->driver_count }} driver(s)</td>
                            </tr>
                            <tr>
                                <th>Grand Total Paid:</th>
                                <td class="font-weight-bold text-primary">{{ $enrollment->formatted_amount }}</td>
                            </tr>
                            <tr>
                                <th>Customer Notes:</th>
                                <td>
                                    <p class="mb-0 text-muted">{{ $enrollment->notes ?: 'No notes submitted by customer.' }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Status Editor, Stripe Info & Notes Log -->
        <div class="col-lg-5 box-margin">
            <!-- Status and Payment -->
            <div class="card card-body mb-4">
                <h4 class="card-title pb-3 border-bottom">Status & Payment</h4>

                <!-- Current Badge status -->
                <div class="my-3 text-center">
                    <span class="{{ $enrollment->status_badge_class }} font-16 px-4 py-2 rounded-pill d-inline-block">{{ $enrollment->status }}</span>
                </div>

                <!-- Update Status Form -->
                @if ($demo_mode == 'on')
                    @include('admin.demo_mode.demo-mode')
                @else
                    <form action="{{ route('consortium-enrollments.updateStatus', $enrollment->id) }}" method="POST" class="mb-4">
                        @method('PUT')
                        @csrf
                @endif
                    <div class="form-group">
                        <label for="status">Update Compliance Status:</label>
                        <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                            <option value="Pending Payment" @if($enrollment->status == 'Pending Payment') selected @endif>Pending Payment</option>
                            <option value="Payment Completed" @if($enrollment->status == 'Payment Completed') selected @endif>Payment Completed</option>
                            <option value="Under Review" @if($enrollment->status == 'Under Review') selected @endif>Under Review</option>
                            <option value="Contacted" @if($enrollment->status == 'Contacted') selected @endif>Contacted</option>
                            <option value="Credentials Sent" @if($enrollment->status == 'Credentials Sent') selected @endif>Credentials Sent</option>
                            <option value="Active" @if($enrollment->status == 'Active') selected @endif>Active</option>
                            <option value="Cancelled" @if($enrollment->status == 'Cancelled') selected @endif>Cancelled</option>
                        </select>
                    </div>
                </form>

                <!-- Stripe parameters -->
                <div class="alert alert-secondary p-3 mb-0">
                    <h6 class="fw-bold"><i class="fa fa-cc-stripe mr-2 text-primary"></i>Stripe Details</h6>
                    <div class="small mt-2">
                        <div><strong>Payment Status:</strong> 
                            <span class="badge @if($enrollment->payment_status == 'completed') badge-success @else badge-warning @endif">
                                {{ ucfirst($enrollment->payment_status) }}
                            </span>
                        </div>
                        <div class="mt-1 text-truncate"><strong>Checkout Session:</strong> <br><code>{{ $enrollment->stripe_checkout_session_id ?: '—' }}</code></div>
                        <div class="mt-1 text-truncate"><strong>Payment Intent ID:</strong> <br><code>{{ $enrollment->stripe_payment_intent_id ?: '—' }}</code></div>
                    </div>
                </div>
            </div>

            <!-- Notes Log -->
            <div class="card card-body">
                <h4 class="card-title pb-3 border-bottom">Internal Notes & History</h4>

                <!-- Add Note Form -->
                @if ($demo_mode == 'on')
                    @include('admin.demo_mode.demo-mode')
                @else
                    <form action="{{ route('consortium-enrollments.updateNotes', $enrollment->id) }}" method="POST" class="mb-4">
                        @method('PUT')
                        @csrf
                @endif
                    <div class="form-group">
                        <label for="note">Add new comment:</label>
                        <textarea name="note" id="note" class="form-control" placeholder="Type internal update notes here..." style="height: 80px;" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary">Add Note</button>
                </form>

                <!-- Internal Notes Log View -->
                <div class="form-group">
                    <label>Activity Log / History:</label>
                    <div class="p-3 bg-light rounded border" style="max-height: 250px; overflow-y: auto; white-space: pre-wrap; font-family: monospace; font-size: 12px; line-height: 1.5;">{{ trim($enrollment->internal_notes) ?: 'No logs recorded.' }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
