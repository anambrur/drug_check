@extends('layouts.admin.master')

@section('content')
    @php
        $clientCount = $protocol->clients->count();
        $clientPreviewLimit = 6;
        $lastRunLabel = $lastRun?->selection_date?->format('M j, Y g:i A') ?? 'Never';
        if (!$protocol->automatic || !$protocol->is_active) {
            $nextRunLabel = 'Manual only';
            $nextRunHint = 'Automatic scheduling is off for this protocol.';
        } elseif ($nextRun) {
            $nextRunLabel = $nextRun->format('M j, Y');
            $nextRunHint = $schedule->frequencyLabel($protocol);
        } else {
            $nextRunLabel = '—';
            $nextRunHint = 'No upcoming date configured.';
        }
        $emailLabel = $protocol->is_email_send ? 'Enabled' : 'Disabled';
        $emailHint = $protocol->is_email_send
            ? 'Primary picks are emailed after each run.'
            : 'No selection emails are sent.';
    @endphp

    <div class="container-fluid rs-history">
        <div class="rs-history__toolbar">
            <div>
                <nav class="rs-history__crumb" aria-label="Breadcrumb">
                    <a href="{{ route('random-selection.index') }}">Random Selection</a>
                    <span>/</span>
                    <span>History</span>
                </nav>
                <h3 class="rs-history__title">{{ $protocol->name }}</h3>
                <p class="rs-history__subtitle">
                    Selection history · {{ $protocol->test->test_name ?? 'N/A' }} · {{ $protocol->group }}
                </p>
            </div>
            <div class="rs-history__actions">
                @if ($protocol->is_active)
                    <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#runNowModal">
                        <i class="fas fa-play mr-1"></i> Run now
                    </a>
                @endif
                <a href="{{ route('random-selection.edit', $protocol->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <a href="{{ route('random-selection.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Protocols
                </a>
            </div>
        </div>

        <div class="row rs-history__metrics">
            <div class="col-6 col-xl-3">
                <div class="rs-metric">
                    <div class="rs-metric__icon rs-metric__icon--pool">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="rs-metric__body">
                        <span class="rs-metric__label">Current pool</span>
                        <span class="rs-metric__value">{{ number_format($currentPoolSize) }}</span>
                        <span class="rs-metric__hint">
                            Active {{ $protocol->group }} on linked clients
                            @if (($totalActiveOnClients ?? $currentPoolSize) !== $currentPoolSize)
                                ({{ number_format($totalActiveOnClients) }} active on those clients before group filter)
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="rs-metric">
                    <div class="rs-metric__icon rs-metric__icon--last">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="rs-metric__body">
                        <span class="rs-metric__label">Last run</span>
                        <span class="rs-metric__value rs-metric__value--sm">{{ $lastRunLabel }}</span>
                        <span class="rs-metric__hint">
                            {{ $lastRun ? ucfirst($lastRun->trigger ?? 'manual') . ' · ' . ($lastRun->status ?? '') : 'No runs yet' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="rs-metric">
                    <div class="rs-metric__icon rs-metric__icon--next">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="rs-metric__body">
                        <span class="rs-metric__label">Next scheduled</span>
                        <span class="rs-metric__value rs-metric__value--sm">{{ $nextRunLabel }}</span>
                        <span class="rs-metric__hint">{{ $nextRunHint }}</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="rs-metric">
                    <div class="rs-metric__icon {{ $protocol->is_email_send ? 'rs-metric__icon--email-on' : 'rs-metric__icon--email-off' }}">
                        <i class="fas {{ $protocol->is_email_send ? 'fa-envelope' : 'fa-envelope-open' }}"></i>
                    </div>
                    <div class="rs-metric__body">
                        <span class="rs-metric__label">Email notifications</span>
                        <span class="rs-metric__value rs-metric__value--sm">{{ $emailLabel }}</span>
                        <span class="rs-metric__hint">{{ $emailHint }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card rs-history__protocol mb-4">
            <div class="card-body">
                <div class="rs-protocol">
                    <div class="rs-protocol__meta">
                        <div class="rs-protocol__pill">
                            <span class="rs-protocol__pill-label">Frequency</span>
                            <span class="rs-protocol__pill-value">{{ $schedule->frequencyLabel($protocol) }}</span>
                        </div>
                        <div class="rs-protocol__pill">
                            <span class="rs-protocol__pill-label">Requirement</span>
                            <span class="rs-protocol__pill-value">
                                {{ $protocol->selection_requirement_value }}
                                {{ $protocol->selection_requirement_type === 'PERCENTAGE' ? '%' : 'employees' }}
                            </span>
                        </div>
                        <div class="rs-protocol__pill">
                            <span class="rs-protocol__pill-label">Automatic</span>
                            <span class="rs-protocol__pill-value">
                                @if ($protocol->automatic)
                                    <span class="rs-status rs-status--on">On</span>
                                @else
                                    <span class="rs-status rs-status--off">Off</span>
                                @endif
                            </span>
                        </div>
                        <div class="rs-protocol__pill">
                            <span class="rs-protocol__pill-label">Protocol</span>
                            <span class="rs-protocol__pill-value">
                                @if ($protocol->is_active)
                                    <span class="rs-status rs-status--on">Active</span>
                                @else
                                    <span class="rs-status rs-status--off">Inactive</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="rs-protocol__clients">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="rs-protocol__clients-label">
                                Clients
                                <span class="rs-protocol__count">{{ $clientCount }}</span>
                            </span>
                            @if ($clientCount > $clientPreviewLimit)
                                <button type="button" class="btn btn-link btn-sm p-0 rs-clients-toggle" data-expanded="0">
                                    Show all
                                </button>
                            @endif
                        </div>
                        <div class="rs-client-chips" data-limit="{{ $clientPreviewLimit }}">
                            @forelse ($protocol->clients as $client)
                                <span class="rs-chip {{ $loop->iteration > $clientPreviewLimit ? 'rs-chip--hidden' : '' }}">
                                    {{ $client->company_name }}
                                </span>
                            @empty
                                <span class="text-muted">No clients linked</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card rs-history__runs">
            <div class="card-header rs-history__runs-header">
                <div>
                    <h5 class="mb-0">Past runs</h5>
                    <span class="text-muted small">Each row is one selection execution for this protocol</span>
                </div>
                <span class="rs-history__runs-total">{{ $executions->total() }} total</span>
            </div>
            <div class="card-body p-0">
                @if ($executions->count() === 0)
                    <div class="rs-empty">
                        <div class="rs-empty__icon"><i class="fas fa-history"></i></div>
                        <h6>No selections yet</h6>
                        <p>Run this protocol to create the first selection history entry.</p>
                        @if ($protocol->is_active)
                            <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#runNowModal">
                                <i class="fas fa-play mr-1"></i> Run now
                            </a>
                        @endif
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table rs-runs-table mb-0">
                            <thead>
                                <tr>
                                    <th>Run date</th>
                                    <th>Trigger</th>
                                    <th>Pool</th>
                                    <th>Selected</th>
                                    <th>Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($executions as $execution)
                                    @php
                                        $counts = $execution->type_counts ?? [
                                            'primary' => 0,
                                            'extra' => 0,
                                            'sub' => 0,
                                            'alternate' => 0,
                                            'total' => 0,
                                        ];
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="rs-runs-table__date">
                                                {{ $execution->selection_date->format('M j, Y') }}
                                            </div>
                                            <div class="rs-runs-table__time">
                                                {{ $execution->selection_date->format('g:i A') }}
                                            </div>
                                        </td>
                                        <td>
                                            @if (($execution->trigger ?? 'manual') === 'scheduled')
                                                <span class="rs-tag rs-tag--scheduled">Scheduled</span>
                                            @else
                                                <span class="rs-tag rs-tag--manual">Manual</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="rs-runs-table__strong">{{ $execution->pool_size }}</span>
                                            <span class="text-muted"> in pool</span>
                                        </td>
                                        <td>
                                            <div class="rs-runs-table__strong">
                                                {{ $counts['primary'] }} primary
                                                @if ($counts['alternate'] > 0)
                                                    <span class="text-muted">· {{ $counts['alternate'] }} alt</span>
                                                @endif
                                            </div>
                                            <div class="rs-runs-table__breakdown">
                                                Extra {{ $counts['extra'] }}
                                                · Sub {{ $counts['sub'] }}
                                                · Alt {{ $counts['alternate'] }}
                                            </div>
                                        </td>
                                        <td>
                                            @switch($execution->status)
                                                @case('PENDING')
                                                    <span class="rs-tag rs-tag--pending">Pending</span>
                                                @break
                                                @case('COMPLETED')
                                                    <span class="rs-tag rs-tag--done">Completed</span>
                                                @break
                                                @case('CANCELLED')
                                                    <span class="rs-tag rs-tag--cancelled">Cancelled</span>
                                                @break
                                                @default
                                                    <span class="rs-tag">{{ $execution->status }}</span>
                                            @endswitch
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('random-selection.results.view', $execution->id) }}"
                                                class="btn btn-sm btn-outline-primary rs-view-btn"
                                                title="View selected employees">
                                                <i class="fas fa-eye mr-1"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            @if ($executions->hasPages())
                <div class="card-footer">
                    {{ $executions->links() }}
                </div>
            @endif
        </div>
    </div>

    @if ($protocol->is_active)
        <div class="modal fade" id="runNowModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Run selection now</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">Execute <strong>{{ $protocol->name }}</strong> immediately using the current employee pool?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <form method="POST" action="{{ route('random-selection.execute', $protocol->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-play mr-1"></i> Run now
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        (function() {
            var toggle = document.querySelector('.rs-clients-toggle');
            if (!toggle) return;

            toggle.addEventListener('click', function() {
                var expanded = toggle.getAttribute('data-expanded') === '1';
                var chips = document.querySelectorAll('.rs-client-chips .rs-chip--hidden, .rs-client-chips .rs-chip.is-revealed');

                chips.forEach(function(chip) {
                    if (expanded) {
                        chip.classList.add('rs-chip--hidden');
                        chip.classList.remove('is-revealed');
                    } else {
                        chip.classList.remove('rs-chip--hidden');
                        chip.classList.add('is-revealed');
                    }
                });

                toggle.setAttribute('data-expanded', expanded ? '0' : '1');
                toggle.textContent = expanded ? 'Show all' : 'Show less';
            });
        })();
    </script>
@endpush
