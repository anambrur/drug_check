@extends('layouts.admin.master')

@section('content')
    @include('admin.result_recording.partials.list-section', ['hideCompanyFilter' => true])
@endsection

@push('scripts')
@include('admin.result_recording.partials.datatable-scripts')
@endpush
