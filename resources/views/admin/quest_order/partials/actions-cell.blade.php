<div class="quest-order-actions-cell">
    @include('admin.quest_order.partials.actions-dropdown', ['order' => $order])

    @can('quest-order delete')
        <div class="modal fade" id="deleteModal{{ $order->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        Are you sure you want to delete this quest order?
                    </div>
                    <div class="modal-footer">
                        <form class="d-inline-block" action="{{ route('quest-order.destroy', $order->id) }}" method="POST">
                            @method('DELETE')
                            @csrf
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Yes, Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
</div>
