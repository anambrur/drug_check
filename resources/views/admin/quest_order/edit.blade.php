@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <div class="d-md-flex justify-content-between align-items-center mb-20">
                    <h4 class="card-title">Edit Quest Order</h4>
                    <div>
                        <a href="{{ route('quest-order.index') }}" class="btn btn-primary">
                            <i class="fas fa-angle-left"></i> Back
                        </a>
                    </div>
                </div>

                <form action="{{ route('quest-order.update', $questOrder->id) }}" method="POST">
                    @method('PUT')
                    @csrf

                    @include('admin.quest_order.partials.form', ['questOrder' => $questOrder])

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="mt-3 mb-3 text-primary">Status Information</h5>
                            <hr>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="order_status">Order Status</label>
                                <input id="order_status" type="text" class="form-control" value="{{ $questOrder->order_status }}" readonly>
                                <small class="text-muted">Status is updated via webhook</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="order_result">Order Result</label>
                                <input id="order_result" type="text" class="form-control" value="{{ $questOrder->order_result }}" readonly>
                                <small class="text-muted">Result is updated via webhook</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="specimen_id">Specimen ID</label>
                                <input id="specimen_id" type="text" class="form-control" value="{{ $questOrder->specimen_id }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="mt-3 mb-3 text-primary">Quest Identifiers</h5>
                            <hr>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="client_reference_id">Client Reference ID</label>
                                <input id="client_reference_id" type="text" class="form-control" value="{{ $questOrder->client_reference_id }}" readonly>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="quest_order_id">Quest Order ID</label>
                                <input id="quest_order_id" type="text" class="form-control" value="{{ $questOrder->quest_order_id }}" readonly>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="reference_test_id">Reference Test ID</label>
                                <input id="reference_test_id" type="text" class="form-control" value="{{ $questOrder->reference_test_id }}" readonly>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="alert alert-light border">
                                <strong>Quest API Status:</strong>
                                <span class="badge @if($questOrder->create_response_status === 'SUCCESS') badge-success @elseif($questOrder->create_response_status === 'FAILURE') badge-danger @else badge-secondary @endif">
                                    {{ $questOrder->create_response_status ?? 'N/A' }}
                                </span>
                                @if ($questOrder->create_error)
                                    <div class="text-danger small mt-2">{{ is_array($questOrder->create_error) ? ($questOrder->create_error['detail'] ?? json_encode($questOrder->create_error)) : $questOrder->create_error }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <small class="form-text text-muted">Fields marked with <span class="text-red">*</span> are required.</small>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary mr-2">Update Quest Order</button>
                            <a href="{{ route('quest-order.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('admin.quest_order.partials.form-scripts')
@endpush
