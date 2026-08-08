@php
    $questReady = $order->questActionsEnabled();
    $screenService = app(\App\Services\Quest\QuestOrderScreenService::class);
    $orderLabel = $order->quest_order_id
        ? 'Quest Order #' . $order->quest_order_id
        : 'Order #' . $order->id;
@endphp
<div class="btn-group quest-order-actions-group">
    <button type="button"
        class="btn btn-sm btn-primary dropdown-toggle quest-order-actions-toggle"
        data-toggle="dropdown"
        data-boundary="window"
        data-display="static"
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
            @can('quest-order delete')
                <a class="dropdown-item text-danger quest-order-delete-trigger"
                    href="#"
                    data-action="{{ route('quest-order.destroy', $order->id) }}"
                    data-label="{{ $orderLabel }}">
                    Delete
                </a>
            @endcan

            @if ($questReady)
                <div class="dropdown-divider"></div>
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
                    <a class="dropdown-item text-warning quest-order-cancel-trigger"
                        href="#"
                        data-action="{{ route('quest-order.cancel', $order->id) }}"
                        data-label="{{ $orderLabel }}">
                        Cancel on Quest
                    </a>
                @endcan
            @else
                <div class="dropdown-divider"></div>
                <span class="dropdown-item text-muted disabled">Quest actions unavailable</span>
            @endif
        </div>
    </div>
</div>
