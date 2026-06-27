@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col-12 box-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-20">
                        <h6 class="card-title mb-0">Consortium Enrollments</h6>
                    </div>

                    @if (count($enrollments) > 0)
                        <table id="basic-datatable" class="table table-striped dt-responsive w-100">
                            <thead>
                                <tr>
                                    <th>Ref ID</th>
                                    <th>Company</th>
                                    <th>USDOT</th>
                                    <th>Contact</th>
                                    <th>Selected Plan</th>
                                    <th>Drivers</th>
                                    <th>Total Paid</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($enrollments as $enrollment)
                                    <tr>
                                        <td>#{{ str_pad($enrollment->id, 6, '0', STR_PAD_LEFT) }}</td>
                                        <td>
                                            <div class="font-weight-bold">{{ $enrollment->company_name }}</div>
                                            @if ($enrollment->dba_name)
                                                <small class="text-muted">DBA: {{ $enrollment->dba_name }}</small>
                                            @endif
                                        </td>
                                        <td><code>{{ $enrollment->dot_number }}</code></td>
                                        <td>
                                            <div>{{ $enrollment->first_name }} {{ $enrollment->last_name }}</div>
                                            <small class="text-muted">{{ $enrollment->email }}</small>
                                        </td>
                                        <td>{{ $enrollment->selected_plan }}</td>
                                        <td>{{ $enrollment->driver_count }}</td>
                                        <td class="font-weight-bold text-primary">{{ $enrollment->formatted_amount }}</td>
                                        <td>
                                            @if ($enrollment->status == 'active')
                                                <span class="badge badge-pill badge-success">Active</span>
                                            @elseif ($enrollment->status == 'Payment Completed')
                                                <span class="badge badge-pill badge-info">Payment
                                                    Completed</span>
                                            @elseif ($enrollment->status == 'Under Review')
                                                <span class="badge badge-pill badge-warning">Under
                                                    Review</span>
                                            @elseif ($enrollment->status == 'Credentials Sent')
                                                <span class="badge badge-pill badge-primary">Credentials
                                                    Sent</span>
                                            @elseif ($enrollment->status == 'Contacted')
                                                <span class="badge badge-pill badge-secondary">Contacted</span>
                                            @elseif ($enrollment->status == 'Pending Payment')
                                                <span class="badge badge-pill badge-danger">Pending
                                                    Payment</span>
                                            @elseif ($enrollment->status == 'Cancelled')
                                                <span class="badge badge-pill badge-dark">Cancelled</span>
                                            @endif
                                        </td>

                                        <td>{{ $enrollment->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('consortium-enrollments.show', ['id' => $enrollment->id]) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fa fa-eye"></i> View Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Pagination links -->
                        <div class="mt-4">
                            {{ $enrollments->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-info-circle fa-3x text-muted mb-3"></i>
                            <h5>No consortium enrollments found</h5>
                            <p class="text-muted">Once clients complete dynamic Stripe payments, submissions will list here.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
