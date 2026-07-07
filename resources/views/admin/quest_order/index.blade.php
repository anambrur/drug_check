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
                        <div class="quest-order-table-wrap">
                        <table id="" class="table table-striped dt-responsive w-100">
                            <thead>
                                <tr>
                                    {{-- <th scope="col">#</th> --}}
                                    <th>Quest Order ID</th>
                                    <th>Company</th>
                                    <th>Donor</th>
                                    <th>Email</th>
                                    <th>Test Type</th>
                                    <th>Status</th>
                                    <th>Result</th>
                                    <th>Created Date</th>
                                    <th class="custom-width-action">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php $counter = 1; @endphp
                                @foreach ($questOrders as $order)
                                    @php
                                        // dd($order);
                                    @endphp
                                    <tr>
                                        {{-- <td>
                                            <input name="check_list[]" type="checkbox" value="{{ $order->id }}"
                                                onclick="showHideDeleteButton2(this)">
                                        </td> --}}
                                        <td>{{ $order->quest_order_id ?? 'N/A' }}</td>
                                        <td>{{ $order->user->name ?? 'N/A' }}</td>
                                        <td>{{ $order->first_name }} {{ $order->last_name }}</td>
                                        <td>{{ $order->email }}</td>
                                        @if ($order->dot_test == 'T')
                                            <td><span class="badge badge-pill badge-primary">DOT</span></td>
                                        @else
                                            <td><span class="badge badge-pill badge-secondary">Non-DOT</span></td>
                                        @endif

                                        <td>
                                            @if ($order->screens->count())
                                                @foreach ($order->screens as $screen)
                                                    <span
                                                        class="badge badge-pill badge-info d-block mb-1">{{ $screen->screen_type }}:
                                                        {{ $screen->order_status ?? 'Pending' }}</span>
                                                @endforeach
                                            @elseif ($order->order_status)
                                                <span class="badge badge-pill badge-info">{{ $order->order_status }}</span>
                                            @else
                                                <span class="badge badge-pill badge-info">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($order->screens->count())
                                                @foreach ($order->screens as $screen)
                                                    @if ($screen->order_result)
                                                        <span
                                                            class="badge badge-pill d-block mb-1 @if ($screen->order_result == 'Negative') badge-success @elseif($screen->order_result == 'Positive') badge-danger @else badge-warning @endif">
                                                            {{ $screen->screen_type }}: {{ $screen->order_result }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                                @if (!$order->screens->whereNotNull('order_result')->count())
                                                    <span class="badge badge-pill badge-secondary">Not Available</span>
                                                @endif
                                            @elseif ($order->order_result)
                                                <span
                                                    class="badge badge-pill @if ($order->order_result == 'Negative') badge-success @elseif($order->order_result == 'Positive') badge-danger @else badge-warning @endif">
                                                    {{ $order->order_result }}
                                                </span>
                                            @else
                                                <span class="badge badge-pill badge-secondary">Not Available</span>
                                            @endif
                                        </td>
                                        <td>{{ Carbon\Carbon::parse($order->created_at)->format('m/d/Y') }}</td>
                                        <td class="quest-order-actions-cell">
                                            @include('admin.quest_order.partials.actions-dropdown', [
                                                'order' => $order,
                                            ])
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
                        </div>
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

    @include('admin.quest_order.partials.actions-dropdown-styles')
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
