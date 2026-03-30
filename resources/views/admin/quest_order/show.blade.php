@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <div class="d-md-flex justify-content-between align-items-center mb-20">
                    <h4 class="card-title">Quest Order Details</h4>
                    <div>
                        <a href="{{ route('quest-order.edit', $questOrder->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('quest-order.index') }}" class="btn btn-secondary">
                            <i class="fas fa-angle-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">Donor Information</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="35%">Full Name:</th>
                                        <td>{{ $questOrder->first_name }} {{ $questOrder->middle_name }}
                                            {{ $questOrder->last_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Primary ID:</th>
                                        <td>{{ $questOrder->primary_id }} ({{ $questOrder->primary_id_type ?? 'N/A' }})</td>
                                    </tr>
                                    <tr>
                                        <th>Date of Birth:</th>
                                        <td>{{ $questOrder->dob ? $questOrder->dob->format('d M Y') : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Primary Phone:</th>
                                        <td>{{ $questOrder->primary_phone }}</td>
                                    </tr>
                                    <tr>
                                        <th>Secondary Phone:</th>
                                        <td>{{ $questOrder->secondary_phone ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td>{{ $questOrder->email ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Zip Code:</th>
                                        <td>{{ $questOrder->zip_code ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">Order Information</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="35%">Quest Order ID:</th>
                                        <td><strong>{{ $questOrder->quest_order_id ?? 'N/A' }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Reference Test ID:</th>
                                        <td>{{ $questOrder->reference_test_id ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Client Reference ID:</th>
                                        <td>{{ $questOrder->client_reference_id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Order Status:</th>
                                        <td>
                                            @if ($questOrder->order_status)
                                                <span class="badge badge-info">{{ $questOrder->order_status }}</span>
                                                @if ($questOrder->order_status_screen_type)
                                                    <small
                                                        class="text-muted">({{ $questOrder->order_status_screen_type }})</small>
                                                @endif
                                            @else
                                                <span class="badge badge-secondary">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Order Result:</th>
                                        <td>
                                            @if ($questOrder->order_result)
                                                <span
                                                    class="badge 
                                                    @if ($questOrder->order_result == 'Negative') badge-success
                                                    @elseif($questOrder->order_result == 'Positive') badge-danger
                                                    @else badge-warning @endif">
                                                    {{ $questOrder->order_result }}
                                                </span>
                                                @if ($questOrder->order_result_screen_type)
                                                    <small
                                                        class="text-muted">({{ $questOrder->order_result_screen_type }})</small>
                                                @endif
                                            @else
                                                <span class="badge badge-secondary">Not Available</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Specimen ID:</th>
                                        <td>{{ $questOrder->specimen_id ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Lab Accession Number:</th>
                                        <td>{{ $questOrder->lab_accession_number ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Collected Date/Time:</th>
                                        <td>{{ $questOrder->collected_datetime ? $questOrder->collected_datetime->format('d M Y H:i') : 'N/A' }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Test Information</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="35%">Portfolio:</th>
                                        <td>{{ $questOrder->portfolio_name ?? ($questOrder->portfolio_id ?? 'N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Unit Codes:</th>
                                        <td>
                                            @if ($questOrder->unit_codes)
                                                @if (is_array($questOrder->unit_codes))
                                                    {{ implode(', ', $questOrder->unit_codes) }}
                                                @else
                                                    {{ $questOrder->unit_codes }}
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>DOT Test:</th>
                                        <td>{{ $questOrder->dot_test == 'Y' ? 'Yes' : 'No' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Testing Authority:</th>
                                        <td>{{ $questOrder->testing_authority ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Reason for Test ID:</th>
                                        <td>{{ $questOrder->reason_for_test_id ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Physical Reason for Test ID:</th>
                                        <td>{{ $questOrder->physical_reason_for_test_id ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Collection Site ID:</th>
                                        <td>{{ $questOrder->collection_site_id ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Observed Requested:</th>
                                        <td>{{ $questOrder->observed_requested == 'Y' ? 'Yes' : 'No' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Split Specimen Requested:</th>
                                        <td>{{ $questOrder->split_specimen_requested == 'Y' ? 'Yes' : 'No' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Order Comments:</th>
                                        <td>{{ $questOrder->order_comments ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-warning text-white">
                                <h5 class="mb-0">Client & Timing Information</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="35%">Lab Account:</th>
                                        <td>{{ $questOrder->lab_account }}</td>
                                    </tr>
                                    <tr>
                                        <th>CSL:</th>
                                        <td>{{ $questOrder->csl ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Contact Name:</th>
                                        <td>{{ $questOrder->contact_name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Telephone Number:</th>
                                        <td>{{ $questOrder->telephone_number ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>End Date/Time:</th>
                                        <td>{{ $questOrder->end_datetime ? $questOrder->end_datetime->format('d M Y H:i') : 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Expired At:</th>
                                        <td>{{ $questOrder->expired_at ? $questOrder->expired_at->format('d M Y H:i') : 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created Date:</th>
                                        <td>{{ $questOrder->created_at->format('d M Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Last Updated:</th>
                                        <td>{{ $questOrder->updated_at->format('d M Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>API Response Status:</th>
                                        <td>
                                            @if ($questOrder->create_response_status)
                                                <span
                                                    class="badge 
                                                    @if ($questOrder->create_response_status == 'SUCCESS') badge-success
                                                    @elseif($questOrder->create_response_status == 'FAILURE') badge-danger
                                                    @else badge-secondary @endif">
                                                    {{ $questOrder->create_response_status }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($questOrder->physical_data)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0">Physical Data</h5>
                                </div>
                                <div class="card-body">
                                    <pre class="bg-light p-3">{{ json_encode($questOrder->physical_data, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($questOrder->status_raw_xml || $questOrder->result_raw_xml)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="mb-0">Raw Webhook Data</h5>
                                </div>
                                <div class="card-body">
                                    @if ($questOrder->status_raw_xml)
                                        <h6>Status XML:</h6>
                                        <pre class="bg-light p-3" style="max-height: 300px; overflow: auto;">{{ $questOrder->status_raw_xml }}</pre>
                                    @endif
                                    @if ($questOrder->result_raw_xml)
                                        <h6 class="mt-3">Result XML:</h6>
                                        <pre class="bg-light p-3" style="max-height: 300px; overflow: auto;">{{ $questOrder->result_raw_xml }}</pre>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
