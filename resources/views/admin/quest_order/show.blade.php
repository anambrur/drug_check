@extends('layouts.admin.master')

@php
    $screenService = app(\App\Services\Quest\QuestOrderScreenService::class);
    $display = fn ($value) => filled($value) ? $value : '—';
    $isDot = in_array($questOrder->dot_test, ['T', 'Y'], true);
    $unitCodes = $questOrder->unit_codes
        ? (is_array($questOrder->unit_codes) ? implode(', ', $questOrder->unit_codes) : $questOrder->unit_codes)
        : null;
    $donorName = trim(implode(' ', array_filter([
        $questOrder->first_name,
        $questOrder->middle_name,
        $questOrder->last_name,
    ])));
@endphp

@push('styles')
    <style>
        .qo-show-page {
            --qo-border: #e3e6f0;
            --qo-muted: #858796;
            --qo-text: #2e384d;
            --qo-primary: #4e73df;
            --qo-radius: 10px;
        }

        .qo-show-page .qo-page-head {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding-bottom: 1.25rem;
            margin-bottom: 1.25rem;
            border-bottom: 1px solid var(--qo-border);
        }

        .qo-show-page .qo-page-title {
            margin: 0 0 0.25rem;
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--qo-text);
        }

        .qo-show-page .qo-page-sub {
            margin: 0;
            color: var(--qo-muted);
            font-size: 0.9rem;
        }

        .qo-show-page .qo-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
        }

        .qo-show-page .qo-toolbar .btn {
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .qo-show-page .qo-panel {
            border: 1px solid var(--qo-border);
            border-radius: var(--qo-radius);
            overflow: hidden;
            height: 100%;
            background: #fff;
            margin-bottom: 1.5rem;
        }

        .qo-show-page .qo-panel-head {
            padding: 0.85rem 1rem;
            background: #f8f9fc;
            border-bottom: 1px solid var(--qo-border);
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--qo-text);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .qo-show-page .qo-panel-head i {
            color: var(--qo-primary);
            width: 18px;
            text-align: center;
        }

        .qo-show-page .qo-detail-table {
            width: 100%;
            margin: 0;
            border-collapse: collapse;
        }

        .qo-show-page .qo-detail-table tr:not(:last-child) {
            border-bottom: 1px solid #f0f2f8;
        }

        .qo-show-page .qo-detail-table th,
        .qo-show-page .qo-detail-table td {
            padding: 0.7rem 1rem;
            vertical-align: top;
            font-size: 0.9rem;
            line-height: 1.45;
        }

        .qo-show-page .qo-detail-table th {
            width: 38%;
            font-weight: 600;
            color: var(--qo-muted);
            background: #fafbfe;
            border-right: 1px solid #f0f2f8;
        }

        .qo-show-page .qo-detail-table td {
            color: var(--qo-text);
            font-weight: 500;
            word-break: break-word;
        }

        .qo-show-page .qo-data-table-wrap {
            border: 1px solid var(--qo-border);
            border-radius: var(--qo-radius);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .qo-show-page .qo-data-table-wrap .qo-panel-head {
            border-bottom: 1px solid var(--qo-border);
        }

        .qo-show-page .qo-data-table {
            margin: 0;
        }

        .qo-show-page .qo-data-table thead th {
            background: #f8f9fc;
            border-top: none;
            border-bottom: 1px solid var(--qo-border);
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--qo-muted);
            font-weight: 700;
            white-space: nowrap;
            padding: 0.75rem 1rem;
        }

        .qo-show-page .qo-data-table tbody td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
            font-size: 0.9rem;
            color: var(--qo-text);
            border-top: 1px solid #f0f2f8;
        }

        .qo-show-page .qo-code-block {
            background: #f8f9fc;
            border: 1px solid var(--qo-border);
            border-radius: var(--qo-radius);
            padding: 1rem;
            margin: 0;
            font-size: 0.82rem;
            max-height: 400px;
            overflow: auto;
        }

        .qo-show-page .badge {
            font-weight: 600;
        }

        @media (max-width: 767.98px) {
            .qo-show-page .qo-detail-table th {
                width: 42%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body qo-show-page">
                <div class="qo-page-head">
                    <div>
                        <h4 class="qo-page-title">Quest Order Details</h4>
                        <p class="qo-page-sub">
                            {{ $display($donorName) }}
                            @if ($questOrder->quest_order_id)
                                · Quest ID: {{ $questOrder->quest_order_id }}
                            @endif
                        </p>
                    </div>
                    <div class="qo-toolbar">
                        @if ($questOrder->questActionsEnabled())
                            @if ($screenService->isResultAvailable($questOrder))
                                <a href="{{ route('quest-order.result', $questOrder->id) }}" class="btn btn-success">
                                    <i class="fas fa-file-pdf"></i> Download Test Result
                                </a>
                            @endif
                            <a href="{{ route('quest-order.portal', $questOrder->id) }}" class="btn btn-info" target="_blank">
                                <i class="fas fa-external-link-alt"></i> Quest Portal
                            </a>
                        @endif
                        @include('admin.quest_order.partials.actions-dropdown', ['order' => $questOrder])
                        <a href="{{ route('quest-order.edit', $questOrder->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('quest-order.index') }}" class="btn btn-secondary">
                            <i class="fas fa-angle-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="qo-panel">
                            <div class="qo-panel-head">
                                <i class="fas fa-user"></i> Donor Information
                            </div>
                            <table class="qo-detail-table">
                                <tr>
                                    <th>Full Name</th>
                                    <td>{{ $display($donorName) }}</td>
                                </tr>
                                <tr>
                                    <th>Primary ID</th>
                                    <td>{{ $questOrder->primary_id }} ({{ $questOrder->primary_id_type ?? 'N/A' }})</td>
                                </tr>
                                <tr>
                                    <th>Date of Birth</th>
                                    <td>{{ $questOrder->dob ? $questOrder->dob->format('m/d/Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Primary Phone</th>
                                    <td>{{ $display($questOrder->primary_phone) }}</td>
                                </tr>
                                <tr>
                                    <th>Secondary Phone</th>
                                    <td>{{ $display($questOrder->secondary_phone) }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $display($questOrder->email) }}</td>
                                </tr>
                                <tr>
                                    <th>Zip Code</th>
                                    <td>{{ $display($questOrder->zip_code) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="qo-panel">
                            <div class="qo-panel-head">
                                <i class="fas fa-clipboard-list"></i> Order Information
                            </div>
                            <table class="qo-detail-table">
                                <tr>
                                    <th>Quest Order ID</th>
                                    <td><strong>{{ $questOrder->quest_order_id ?? 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Reference Test ID</th>
                                    <td>{{ $display($questOrder->reference_test_id) }}</td>
                                </tr>
                                <tr>
                                    <th>Client Reference ID</th>
                                    <td>{{ $display($questOrder->client_reference_id) }}</td>
                                </tr>
                                <tr>
                                    <th>Order Status</th>
                                    <td>
                                        @if ($questOrder->order_status)
                                            <span class="badge badge-info">{{ $questOrder->order_status }}</span>
                                            @if ($questOrder->order_status_screen_type)
                                                <small class="text-muted">({{ $questOrder->order_status_screen_type }})</small>
                                            @endif
                                        @else
                                            <span class="badge badge-secondary">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Order Result</th>
                                    <td>
                                        @if ($questOrder->order_result)
                                            <span class="badge @if ($questOrder->order_result == 'Negative') badge-success @elseif($questOrder->order_result == 'Positive') badge-danger @else badge-warning @endif">
                                                {{ $questOrder->order_result }}
                                            </span>
                                            @if ($questOrder->order_result_screen_type)
                                                <small class="text-muted">({{ $questOrder->order_result_screen_type }})</small>
                                            @endif
                                        @else
                                            <span class="badge badge-secondary">Not Available</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Specimen ID</th>
                                    <td>{{ $display($questOrder->specimen_id) }}</td>
                                </tr>
                                <tr>
                                    <th>Lab Accession Number</th>
                                    <td>{{ $display($questOrder->lab_accession_number) }}</td>
                                </tr>
                                <tr>
                                    <th>Collected Date/Time</th>
                                    <td>{{ $questOrder->collected_datetime ? $questOrder->collected_datetime->format('m/d/Y h:i A') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-lg-6">
                        <div class="qo-panel">
                            <div class="qo-panel-head">
                                <i class="fas fa-vial"></i> Test Information
                            </div>
                            <table class="qo-detail-table">
                                <tr>
                                    <th>Portfolio</th>
                                    <td>{{ $questOrder->portfolio_name ?? ($questOrder->portfolio_id ?? 'N/A') }}</td>
                                </tr>
                                <tr>
                                    <th>Unit Codes</th>
                                    <td>{{ $display($unitCodes) }}</td>
                                </tr>
                                <tr>
                                    <th>DOT Test</th>
                                    <td>{{ $isDot ? 'Yes' : 'No' }}</td>
                                </tr>
                                <tr>
                                    <th>Testing Authority</th>
                                    <td>{{ $display($questOrder->testing_authority) }}</td>
                                </tr>
                                <tr>
                                    <th>Reason for Test ID</th>
                                    <td>{{ $display($questOrder->reason_for_test_id) }}</td>
                                </tr>
                                <tr>
                                    <th>Physical Reason for Test ID</th>
                                    <td>{{ $display($questOrder->physical_reason_for_test_id) }}</td>
                                </tr>
                                <tr>
                                    <th>Collection Site ID</th>
                                    <td>{{ $display($questOrder->collection_site_id) }}</td>
                                </tr>
                                <tr>
                                    <th>Observed Requested</th>
                                    <td>{{ $questOrder->observed_requested == 'Y' ? 'Yes' : 'No' }}</td>
                                </tr>
                                <tr>
                                    <th>Split Specimen Requested</th>
                                    <td>{{ $questOrder->split_specimen_requested == 'Y' ? 'Yes' : 'No' }}</td>
                                </tr>
                                <tr>
                                    <th>Order Comments</th>
                                    <td>{{ $display($questOrder->order_comments) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="qo-panel">
                            <div class="qo-panel-head">
                                <i class="fas fa-building"></i> Client &amp; Timing Information
                            </div>
                            <table class="qo-detail-table">
                                <tr>
                                    <th>Lab Account</th>
                                    <td>{{ $display($questOrder->lab_account) }}</td>
                                </tr>
                                <tr>
                                    <th>CSL</th>
                                    <td>{{ $display($questOrder->csl) }}</td>
                                </tr>
                                <tr>
                                    <th>Contact Name</th>
                                    <td>{{ $display($questOrder->contact_name) }}</td>
                                </tr>
                                <tr>
                                    <th>Telephone Number</th>
                                    <td>{{ $display($questOrder->telephone_number) }}</td>
                                </tr>
                                <tr>
                                    <th>End Date/Time</th>
                                    <td>{{ $questOrder->end_datetime ? $questOrder->end_datetime->format('m/d/Y h:i A') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Expired At</th>
                                    <td>{{ $questOrder->expired_at ? $questOrder->expired_at->format('m/d/Y h:i A') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Created Date</th>
                                    <td>{{ $questOrder->created_at->format('m/d/Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated</th>
                                    <td>{{ $questOrder->updated_at->format('m/d/Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>API Response Status</th>
                                    <td>
                                        @if ($questOrder->create_response_status)
                                            <span class="badge @if ($questOrder->create_response_status == 'SUCCESS') badge-success @elseif($questOrder->create_response_status == 'FAILURE') badge-danger @else badge-secondary @endif">
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

                @if ($questOrder->screens->count())
                    <div class="qo-data-table-wrap">
                        <div class="qo-panel-head">
                            <i class="fas fa-layer-group"></i> Per-Screen Status &amp; Results
                        </div>
                        <div class="table-responsive">
                            <table class="table qo-data-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Screen</th>
                                        <th>Status</th>
                                        <th>Result</th>
                                        <th>Specimen ID</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($questOrder->screens as $screen)
                                        <tr>
                                            <td>{{ ucfirst($screen->screen_type) }}</td>
                                            <td>{{ $screen->order_status ?? 'Pending' }}</td>
                                            <td>{{ $screen->order_result ?? 'N/A' }}</td>
                                            <td>{{ $screen->specimen_id ?? 'N/A' }}</td>
                                            <td>
                                                @if ($screenService->isResultAvailable($questOrder, $screen->screen_type))
                                                    <a href="{{ route('quest-order.result', [$questOrder->id, $screen->screen_type]) }}" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-file-pdf mr-1"></i> Result PDF
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if ($questOrder->physical_data)
                    <div class="qo-panel mb-0">
                        <div class="qo-panel-head">
                            <i class="fas fa-notes-medical"></i> Physical Data
                        </div>
                        <div class="p-3">
                            <pre class="qo-code-block">{{ json_encode($questOrder->physical_data, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('admin.quest_order.partials.actions-dropdown-styles')
@endsection
