@extends('layouts.admin.master')

@section('content')
<div class="row">
    <div class="col-12 box-margin">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fa fa-clock fa-3x text-muted mb-3"></i>
                <h4 class="mb-2">{{ $pageTitle }}</h4>
                <span class="badge badge-pill badge-secondary mb-3">Coming soon</span>
                <p class="text-muted mb-0 mx-auto" style="max-width: 520px;">{{ $message }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
