@extends('layouts.admin.master')

@section('content')

    <div class="row">
        <div class="col-12 box-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-20">
                        <h6 class="card-title mb-0">Quest Order List</h6>
                        <div>
                            <a href="{{ route('quest-order.create') }}" class="btn btn-primary float-right mb-3">
                                + Add Quest Order
                            </a>
                        </div>
                    </div>

                    @if (count($questOrders) > 0)
                        <div>
                            <input id="check_all" type="checkbox" onclick="showHideDeleteButton(this)">
                            <label for="check_all">All</label>
                            <a id="deleteChecked" class="ml-2" href="#" data-toggle="modal"
                                data-target="#deleteCheckedModal">
                                <i class="fa fa-trash text-danger font-18"></i>
                            </a>
                        </div>
                        <form onsubmit="return btnCheckListGet()" action="{{ route('quest-order.destroy_checked') }}"
                            method="POST">
                            @method('DELETE')
                            @csrf
                            <input type="hidden" id="checked_lists" name="checked_lists" value="">

                            <!-- Modal -->
                            <div class="modal fade" id="deleteCheckedModal" tabindex="-1" role="dialog"
                                aria-labelledby="deleteCheckedModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteCheckedModalCenterTitle">Delete</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body text-center">
                                            Are you sure you want to delete selected items?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger"
                                                data-dismiss="modal">Cancel</button>
                                            <button onclick="btnCheckListGet()" type="submit" class="btn btn-success">Yes,
                                                Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <table id="basic-datatable" class="table table-striped dt-responsive w-100">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th>Quest Order ID</th>
                                    <th>Donor Name</th>
                                    <th>Client Reference</th>
                                    <th>Status</th>
                                    <th>Result</th>
                                    <th>Created Date</th>
                                    <th class="custom-width-action">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php $counter = 1; @endphp
                                @foreach ($questOrders as $order)
                                    <tr>
                                        <td>
                                            <input name="check_list[]" type="checkbox" value="{{ $order->id }}"
                                                onclick="showHideDeleteButton2(this)">
                                        </td>
                                        <td>{{ $order->quest_order_id ?? 'N/A' }}</td>
                                        <td>{{ $order->first_name }} {{ $order->last_name }}</td>
                                        <td>{{ $order->client_reference_id }}</td>
                                        <td>
                                            @if ($order->order_status)
                                                <span class="badge badge-pill badge-info">{{ $order->order_status }}</span>
                                            @else
                                                <span class="badge badge-pill badge-secondary">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($order->order_result)
                                                <span
                                                    class="badge badge-pill 
                                                    @if ($order->order_result == 'Negative') badge-success
                                                    @elseif($order->order_result == 'Positive') badge-danger
                                                    @else badge-warning @endif">
                                                    {{ $order->order_result }}
                                                </span>
                                            @else
                                                <span class="badge badge-pill badge-secondary">Not Available</span>
                                            @endif
                                        </td>
                                        <td>{{ Carbon\Carbon::parse($order->created_at)->format('m.d.Y') }}</td>
                                        <td>
                                            <div>
                                                @can('quest-order view')
                                                    <a href="{{ route('quest-order.show', $order->id) }}" class="mr-2"
                                                        title="View">
                                                        <i class="fa fa-eye text-primary font-18"></i>
                                                    </a>
                                                @endcan

                                                @can('quest-order edit')
                                                    <a href="{{ route('quest-order.edit', $order->id) }}" class="mr-2"
                                                        title="Edit">
                                                        <i class="fa fa-edit text-info font-18"></i>
                                                    </a>
                                                @endcan

                                                @can('quest-order delete')
                                                    <a href="#" data-toggle="modal"
                                                        data-target="#deleteModal{{ $order->id }}" title="Delete">
                                                        <i class="fa fa-trash text-danger font-18"></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal -->
                                    <div class="modal fade" id="deleteModal{{ $order->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="orderModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="orderModalCenterTitle">Delete</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    Are you sure you want to delete this quest order?
                                                </div>
                                                <div class="modal-footer">
                                                    <form class="d-inline-block"
                                                        action="{{ route('quest-order.destroy', $order->id) }}"
                                                        method="POST">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button type="button" class="btn btn-danger"
                                                            data-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-success">Yes,
                                                            Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center">
                            <p>No quest orders found.</p>
                            <a href="{{ route('quest-order.create') }}" class="btn btn-primary">Create First Quest
                                Order</a>
                        </div>
                    @endif

                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div><!-- end row-->
@endsection

@push('scripts')
    <script>
        function showHideDeleteButton(checkbox) {
            var deleteChecked = document.getElementById("deleteChecked");
            if (checkbox.checked) {
                deleteChecked.style.display = "inline-block";
            } else {
                deleteChecked.style.display = "none";
            }
        }

        function showHideDeleteButton2(checkbox) {
            var checkboxes = document.getElementsByName("check_list[]");
            var deleteChecked = document.getElementById("deleteChecked");
            var anyChecked = false;

            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].checked) {
                    anyChecked = true;
                    break;
                }
            }

            if (anyChecked) {
                deleteChecked.style.display = "inline-block";
            } else {
                deleteChecked.style.display = "none";
            }
        }

        function btnCheckListGet() {
            var selected = [];
            document.querySelectorAll('input[name="check_list[]"]:checked').forEach(function(checkbox) {
                selected.push(checkbox.value);
            });

            if (selected.length === 0) {
                alert('Please select at least one item to delete.');
                return false;
            }

            document.getElementById('checked_lists').value = selected.join(',');
            return true;
        }
    </script>
@endpush
