@extends('layouts.admin.master')

@section('content')
    <div class="container-fluid rs-page">
        <div class="rs-page__toolbar">
            <div>
                <nav class="rs-page__crumb" aria-label="Breadcrumb">
                    <a href="{{ route('random-selection.index') }}">Random Selection</a>
                    <span>/</span>
                    <span>Edit</span>
                </nav>
                <h3 class="rs-page__title">Edit protocol</h3>
                <p class="rs-page__subtitle">{{ $protocol->name }}</p>
            </div>
            <div class="rs-page__actions">
                <a href="{{ route('random-selection.executions', $protocol->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-history mr-1"></i> History
                </a>
                <a href="{{ route('random-selection.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Protocols
                </a>
            </div>
        </div>

        @if ($demo_mode == 'on')
            @include('admin.demo_mode.demo-mode')
        @else
            <form action="{{ route('random-selection.update', $protocol->id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.random_selection.partials.protocol_form', [
                    'protocol' => $protocol,
                    'clients' => $clients,
                    'tests' => $tests,
                    'dotAgencies' => $dotAgencies ?? collect(),
                ])
                <div class="rs-form-footer">
                    <span class="text-muted small">Changes apply to future runs only.</span>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Save changes
                    </button>
                </div>
            </form>
        @endif
    </div>
@endsection

@push('scripts')
    @include('admin.random_selection.partials.protocol_form_scripts', ['protocol' => $protocol])
@endpush
