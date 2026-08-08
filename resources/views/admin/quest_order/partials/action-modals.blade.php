@can('quest-order delete')
    <div class="modal fade" id="questOrderDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p class="mb-2">
                        Delete <strong class="quest-order-modal-label">this order</strong>?
                    </p>
                    <p class="mb-0 text-muted small">
                        Removes the local record and calls Quest CancelOrder when possible
                        (Ordered/Scheduled only; Physical orders cannot be cancelled).
                    </p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <form id="questOrderDeleteForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endcan

@can('quest-order edit')
    <div class="modal fade" id="questOrderCancelModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel on Quest</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    Cancel <strong class="quest-order-modal-label">this order</strong> on Quest?
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <form id="questOrderCancelForm" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning">Yes, Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endcan
