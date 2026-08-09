@php
    $primaryCount = $counts['primary'] ?? count($primary);
    $extraCount = $counts['extra'] ?? count($extra);
    $subCount = $counts['sub'] ?? count($sub);
    $altCount = $counts['alternate'] ?? count($alternates);
    $alternateMode = $alternateMode ?? ($protocol->alternate_mode ?? 'immediate');
    $offlineList = $offlineList ?? $event->offlineList;
@endphp

<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white; }
        .card, .rs-metric { border: none !important; box-shadow: none !important; }
    }
</style>

<div class="container-fluid rs-page">
    <div class="rs-page__toolbar no-print">
        <div>
            <nav class="rs-page__crumb" aria-label="Breadcrumb">
                <a href="{{ route('random-selection.index') }}">Random Selection</a>
                <span>/</span>
                <a href="{{ route('random-selection.executions', $protocol) }}">History</a>
                <span>/</span>
                <span>Run detail</span>
            </nav>
            <h3 class="rs-page__title">Selection run</h3>
            <p class="rs-page__subtitle">
                {{ $protocol->name }} · {{ $event->selection_date->format('M j, Y g:i A') }}
                · Alternate mode:
                {{ str_replace('_', ' ', $alternateMode) }}
            </p>
        </div>
        <div class="rs-page__actions">
            <button type="button" onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-print mr-1"></i> Print
            </button>
            @if ($alternateMode === 'offline_list' && $offlineList)
                <a href="{{ route('random-selection.offline-list.print', $event) }}"
                    class="btn btn-outline-primary btn-sm" target="_blank">
                    <i class="fas fa-list mr-1"></i> Print offline list
                </a>
            @endif
            <a href="{{ route('random-selection.executions', $protocol) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-history mr-1"></i> History
            </a>
            <a href="{{ route('random-selection.index') }}" class="btn btn-outline-secondary btn-sm">
                Protocols
            </a>
        </div>
    </div>

    @if (!empty($warning))
        <div class="alert alert-warning no-print">{{ $warning }}</div>
    @endif

    <div class="row rs-history__metrics">
        <div class="col-6 col-xl-2">
            <div class="rs-metric">
                <div class="rs-metric__icon rs-metric__icon--pool"><i class="fas fa-users"></i></div>
                <div class="rs-metric__body">
                    <span class="rs-metric__label">Pool</span>
                    <span class="rs-metric__value">{{ $event->pool_size }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="rs-metric">
                <div class="rs-metric__icon rs-metric__icon--pool"><i class="fas fa-user-check"></i></div>
                <div class="rs-metric__body">
                    <span class="rs-metric__label">Primary</span>
                    <span class="rs-metric__value">{{ $primaryCount }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="rs-metric">
                <div class="rs-metric__icon rs-metric__icon--last"><i class="fas fa-vial"></i></div>
                <div class="rs-metric__body">
                    <span class="rs-metric__label">Extra</span>
                    <span class="rs-metric__value">{{ $extraCount }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="rs-metric">
                <div class="rs-metric__icon" style="background: rgba(245,158,11,.12); color:#b45309;"><i class="fas fa-filter"></i></div>
                <div class="rs-metric__body">
                    <span class="rs-metric__label">Sub</span>
                    <span class="rs-metric__value">{{ $subCount }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="rs-metric">
                <div class="rs-metric__icon rs-metric__icon--email-off"><i class="fas fa-user-clock"></i></div>
                <div class="rs-metric__body">
                    <span class="rs-metric__label">Alternates</span>
                    <span class="rs-metric__value">{{ $altCount }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-2">
            <div class="rs-metric">
                <div class="rs-metric__icon {{ $protocol->is_email_send ? 'rs-metric__icon--email-on' : 'rs-metric__icon--email-off' }}">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="rs-metric__body">
                    <span class="rs-metric__label">Emails</span>
                    <span class="rs-metric__value rs-metric__value--sm">
                        @if ($protocol->is_email_send)
                            {{ $emailsSent ?? 0 }} sent
                        @else
                            Off
                        @endif
                    </span>
                    <span class="rs-metric__hint">
                        {{ (($event->trigger ?? 'manual') === 'scheduled') ? 'Scheduled' : 'Manual' }}
                        · {{ $event->status }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="rs-glossary no-print">
        <div class="rs-glossary__item">
            <strong class="rs-type-primary">Primary</strong>
            <span>Main random picks for the protocol test</span>
        </div>
        <div class="rs-glossary__item">
            <strong class="rs-type-extra">Extra</strong>
            <span>Additional test type drawn for the same run size</span>
        </div>
        <div class="rs-glossary__item">
            <strong class="rs-type-sub">Sub</strong>
            <span>Drawn only from primary picks</span>
        </div>
        <div class="rs-glossary__item">
            <strong class="rs-type-alternate">Alternate</strong>
            <span>Backups if someone cannot complete the test</span>
        </div>
    </div>

    @if ($alternateMode === 'offline_list' && $offlineList)
        <div class="card rs-history__runs mb-3 no-print">
            <div class="card-header rs-history__runs-header">
                <div>
                    <h5 class="mb-0">Offline shuffled list</h5>
                    <span class="text-muted small">
                        Single-use randomly sorted full pool for on-site use without internet.
                        {{ $offlineList->remainingCount() }} remaining of {{ count($offlineList->shuffled_donor_ids ?? []) }}.
                        @if ($offlineList->printed_at)
                            Printed {{ $offlineList->printed_at->format('M j, Y g:i A') }}.
                        @endif
                    </span>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('random-selection.offline-list.consume', $offlineList) }}"
                    class="form-inline"
                    onsubmit="return confirm('Consume the next unused DonorID from the offline list?');">
                    @csrf
                    <div class="form-group mr-2 mb-2">
                        <label class="mr-2 mb-0" for="replaces_selected_employee_id">Optional replace primary</label>
                        <select name="replaces_selected_employee_id" id="replaces_selected_employee_id" class="form-control form-control-sm">
                            <option value="">None — add as alternate</option>
                            @foreach ($primary ?? [] as $primarySelection)
                                @if (!$primarySelection->replacementAlternate)
                                    <option value="{{ $primarySelection->id }}">
                                        {{ $primarySelection->donor_id ?: optional($primarySelection->employee)->employee_id }}
                                        —
                                        {{ trim((optional($primarySelection->employee)->first_name ?? '') . ' ' . (optional($primarySelection->employee)->last_name ?? '')) }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm mb-2">
                        Use next from offline list
                    </button>
                </form>
            </div>
        </div>
    @endif

    <div class="card rs-history__runs">
        <div class="card-header rs-history__runs-header">
            <div>
                <h5 class="mb-0">Selected employees</h5>
                <span class="text-muted small">Filter by selection type, then print or review the list. Open Audit to see the DonorID pool for each pick.</span>
            </div>
        </div>
        <div class="card-body">
            @include('admin.random_selection.partials.results_table')
        </div>
    </div>
</div>
