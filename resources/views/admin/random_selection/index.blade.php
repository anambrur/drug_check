@extends('layouts.admin.master')

@section('content')
    <div class="container-fluid rs-page">
        <div class="rs-page__toolbar">
            <div>
                <nav class="rs-page__crumb" aria-label="Breadcrumb">
                    <span>Random Selection</span>
                    <span>/</span>
                    <span>Protocols</span>
                </nav>
                <h3 class="rs-page__title">Selection protocols</h3>
                <p class="rs-page__subtitle">
                    Run selections manually, or enable Automatic so the scheduler runs them on each protocol’s frequency.
                </p>
            </div>
            <div class="rs-page__actions">
                @can('random selection create')
                    <a href="{{ route('random-selection.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> New protocol
                    </a>
                @endcan
            </div>
        </div>

        <div class="card rs-list-card">
            <div class="card-body">
                <div class="table-responsive rs-list-scroll">
                    <table id="rs-protocols-table" class="table rs-list-table mb-0 w-100">
                        <thead>
                            <tr>
                                <th>Protocol</th>
                                <th>Clients</th>
                                <th>Test / Group</th>
                                <th>Schedule</th>
                                <th>Automatic</th>
                                <th>Status</th>
                                <th>Last run</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($protocols as $protocol)
                                @php
                                    $allClients = $protocol->clients;
                                    $clientPreviewLimit = 2;
                                    $extraClients = max(0, $allClients->count() - $clientPreviewLimit);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="rs-list-name">{{ $protocol->name }}</div>
                                        <div class="small text-muted mt-1">
                                            @if ($protocol->is_email_send)
                                                <i class="fas fa-envelope text-info"></i> Email on
                                            @else
                                                <i class="fas fa-envelope-open"></i> Email off
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="rs-list-clients" data-clients-wrap>
                                            @forelse ($allClients as $client)
                                                <span class="rs-chip {{ $loop->iteration > $clientPreviewLimit ? 'rs-chip--extra' : '' }}">
                                                    {{ $client->company_name }}
                                                </span>
                                            @empty
                                                <span class="text-muted">—</span>
                                            @endforelse
                                            @if ($extraClients > 0)
                                                <button type="button" class="rs-chip rs-chip--more" data-clients-toggle
                                                    title="Show all {{ $allClients->count() }} clients">
                                                    +{{ $extraClients }} more
                                                </button>
                                                <button type="button" class="rs-chip rs-chip--less" data-clients-toggle
                                                    title="Show fewer clients">
                                                    Show less
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="rs-list-name" style="font-weight:600;">{{ $protocol->test->test_name ?? 'N/A' }}</div>
                                        <span class="rs-tag rs-tag--manual mt-1">{{ $protocol->group }}</span>
                                    </td>
                                    <td>
                                        <div class="rs-list-name" style="font-weight:600; font-size:0.86rem;">
                                            {{ $schedule->frequencyLabel($protocol) }}
                                        </div>
                                        <div class="small text-muted">
                                            {{ $protocol->selection_requirement_value }}
                                            {{ $protocol->selection_requirement_type === 'PERCENTAGE' ? '%' : ' employees' }}
                                        </div>
                                    </td>
                                    <td>
                                        @if ($protocol->automatic)
                                            <span class="rs-status rs-status--on">On</span>
                                        @else
                                            <span class="rs-status rs-status--off">Off</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($protocol->is_active)
                                            <span class="rs-status rs-status--on">Active</span>
                                        @else
                                            <span class="rs-status rs-status--off">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        {{ $protocol->last_run_at ? \Illuminate\Support\Carbon::parse($protocol->last_run_at)->format('M j, Y') : '—' }}
                                    </td>
                                    <td class="rs-list-actions">
                                        <div class="rs-action-btns">
                                            <a href="{{ route('random-selection.edit', $protocol->id) }}" title="Edit protocol">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="#" class="rs-action--success"
                                                data-toggle="modal"
                                                data-target="#executeModal{{ $protocol->id }}"
                                                title="Run now">
                                                <i class="fa fa-play"></i>
                                            </a>
                                            <a href="{{ route('random-selection.executions', $protocol->id) }}"
                                                title="Selection history">
                                                <i class="fa fa-history"></i>
                                            </a>
                                            <a href="#" class="rs-action--danger"
                                                data-toggle="modal"
                                                data-target="#deleteModal{{ $protocol->id }}"
                                                title="Delete protocol">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @foreach ($protocols as $protocol)
        <div class="modal fade" id="executeModal{{ $protocol->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Run selection now</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Run a random selection for this protocol immediately?</p>
                        <p class="mb-0"><strong>{{ $protocol->name }}</strong></p>
                        @unless ($protocol->is_active)
                            <div class="alert alert-warning mt-3 mb-0">
                                This protocol is inactive and cannot be executed until it is activated.
                            </div>
                        @endunless
                    </div>
                    <div class="modal-footer">
                        <form method="POST" action="{{ route('random-selection.execute', $protocol->id) }}">
                            @csrf
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" @unless ($protocol->is_active) disabled @endunless>
                                Run now
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteModal{{ $protocol->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('content.delete') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('content.close') }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        {{ __('content.you_wont_be_able_to_revert_this') }}
                    </div>
                    <div class="modal-footer">
                        @if ($demo_mode == 'on')
                            @include('admin.demo_mode.demo-mode')
                        @else
                            <form class="d-inline-block"
                                action="{{ route('random-selection.destroy', $protocol->id) }}"
                                method="POST">
                                @method('DELETE')
                                @csrf
                        @endif
                        <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('content.cancel') }}</button>
                        <button type="submit" class="btn btn-success">{{ __('content.yes_delete_it') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    <script>
        document.addEventListener('click', function(e) {
            var btn = e.target.closest('[data-clients-toggle]');
            if (!btn) return;
            var wrap = btn.closest('[data-clients-wrap]');
            if (wrap) wrap.classList.toggle('is-expanded');
        });

        (function($) {
            if (!$ || !$.fn.DataTable) return;

            var $table = $('#rs-protocols-table');
            if (!$table.length || $.fn.DataTable.isDataTable($table)) return;

            $table.DataTable({
                responsive: false,
                autoWidth: false,
                scrollX: true,
                order: [],
                pageLength: 10,
                columnDefs: [
                    { orderable: false, targets: [1, 7] },
                    { className: 'text-nowrap', targets: [4, 5, 6, 7] }
                ],
                language: {
                    search: 'Search:',
                    lengthMenu: 'Show _MENU_ entries'
                }
            });
        })(window.jQuery);
    </script>
@endpush
