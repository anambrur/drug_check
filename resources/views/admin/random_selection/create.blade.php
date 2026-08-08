@extends('layouts.admin.master')

@section('content')
    <div class="container-fluid rs-page">
        <div class="rs-page__toolbar">
            <div>
                <nav class="rs-page__crumb" aria-label="Breadcrumb">
                    <a href="{{ route('random-selection.index') }}">Random Selection</a>
                    <span>/</span>
                    <span>Create</span>
                </nav>
                <h3 class="rs-page__title">Add new selection protocol</h3>
                <p class="rs-page__subtitle">Configure pool filters, selection size, frequency, and notifications.</p>
            </div>
            <div class="rs-page__actions">
                <a href="{{ route('random-selection.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Protocols
                </a>
            </div>
        </div>

        @if ($demo_mode == 'on')
            @include('admin.demo_mode.demo-mode')
        @else
            <form action="{{ route('random-selection.store') }}" method="POST">
                @csrf
                @include('admin.random_selection.partials.protocol_form', [
                    'clients' => $clients,
                    'tests' => $tests,
                    'dotAgencies' => $dotAgencies ?? collect(),
                ])
                <div class="rs-form-footer">
                    <span class="text-muted small">Inactive or incomplete protocols can be edited later.</span>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check mr-1"></i> Create protocol
                    </button>
                </div>
            </form>
        @endif
    </div>
@endsection

@push('scripts')
    @include('admin.random_selection.partials.protocol_form_scripts')
@endpush
