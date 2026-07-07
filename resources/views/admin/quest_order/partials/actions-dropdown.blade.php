@php
    $questReady = $order->questActionsEnabled();
    $screenService = app(\App\Services\Quest\QuestOrderScreenService::class);
@endphp
<div class="btn-group quest-order-actions-group">
    <button type="button"
        class="btn btn-sm btn-primary dropdown-toggle"
        data-toggle="dropdown"
        data-boundary="viewport"
        data-flip="true"
        aria-haspopup="true"
        aria-expanded="false">
        Actions
    </button>
    <div class="dropdown-menu dropdown-menu-right quest-order-actions-menu">
        <div class="quest-order-actions-scroll">
            @can('quest-order view')
                <a class="dropdown-item" href="{{ route('quest-order.show', $order->id) }}">View</a>
            @endcan
            @can('quest-order edit')
                <a class="dropdown-item" href="{{ route('quest-order.edit', $order->id) }}">Edit</a>
            @endcan
            <div class="dropdown-divider"></div>
            @if ($questReady)
                @can('quest-order view')
                    <a class="dropdown-item" href="{{ route('quest-order.portal', $order->id) }}" target="_blank">Open Quest Portal</a>
                    <a class="dropdown-item" href="{{ route('quest-order.qpassport', $order->id) }}">Download QPassport</a>
                    @if ($screenService->isResultAvailable($order))
                        <a class="dropdown-item" href="{{ route('quest-order.result', $order->id) }}">Download Test Result (PDF)</a>
                        <a class="dropdown-item" href="{{ route('quest-order.mro-letter', $order->id) }}">Download MRO Letter (PDF)</a>
                    @endif
                    <div class="dropdown-divider"></div>
                    <h6 class="dropdown-header">Documents</h6>
                    @foreach (\App\Enums\QuestDocType::cases() as $docType)
                        <a class="dropdown-item" href="{{ route('quest-order.document', [$order->id, $docType->value]) }}">{{ $docType->label() }}</a>
                    @endforeach
                @endcan
                @can('quest-order edit')
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#cancelModal{{ $order->id }}">Cancel on Quest</a>
                @endcan
            @else
                <span class="dropdown-item text-muted disabled">Quest actions unavailable</span>
            @endif
            @can('quest-order delete')
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#deleteModal{{ $order->id }}">Delete Local Record</a>
            @endcan
        </div>
    </div>
</div>

@if ($questReady)
    @can('quest-order edit')
        <div class="modal fade" id="cancelModal{{ $order->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cancel on Quest</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body text-center">Cancel this order on Quest Diagnostics?</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                        <form action="{{ route('quest-order.cancel', $order->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger">Yes, Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endif
