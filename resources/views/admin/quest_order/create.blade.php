@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <div class="d-md-flex justify-content-between align-items-center mb-20">
                    <h4 class="card-title">Create Quest Order</h4>
                    <div>
                        <a href="{{ route('quest-order.index') }}" class="btn btn-primary">
                            <i class="fas fa-angle-left"></i> Back
                        </a>
                    </div>
                </div>

                <form action="{{ route('quest-order.store') }}" method="POST">
                    @csrf

                    @include('admin.quest_order.partials.form')

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <small class="form-text text-muted">Fields marked with <span class="text-red">*</span> are required.</small>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary mr-2">Create Quest Order</button>
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
