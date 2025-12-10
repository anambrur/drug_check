@extends('layouts.admin.master')

@section('content')

    <div class="row">
        <div class="col-12 box-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-md-flex justify-content-between align-items-center mb-20">
                        <h6 class="card-title mb-0">{{ __('content.all_admin') }}</h6>
                        <div>
                            <a href="{{ url('admin/admin-user/create') }}" class="btn btn-primary float-right mb-3">+
                                {{ __('content.add_admin_user') }}</a>
                        </div>
                    </div>

                    @if (count($admin_users) > 0)
                        <div>
                            <label for="created_by_super_admin">{{ __('all_admin_created_by_super_admin') }}</label>
                        </div>

                        <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>{{ __('content.image') }}</th>
                                    <th>{{ __('content.role_name') }}</th>
                                    <th>{{ __('content.name') }}</th>
                                    <th>{{ __('content.email') }}</th>
                                    <th>{{ __('content.status') }}</th>
                                    <th class="custom-width-action">{{ __('content.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $desc = count($admin_users);
                                    $asc = 0;
                                @endphp
                                @foreach ($admin_users as $admin_user)
                                    @if ($admin_user->getRoleNames()->first() != 'super-admin')
                                        <tr>
                                            <td>

                                                @if (!empty($admin_user->profile_photo_path))
                                                    <img class="image-size img-fluid"
                                                        src="{{ asset('uploads/img/profile/' . $admin_user->profile_photo_path) }}"
                                                        alt="user image">
                                                @else
                                                    <img class="image-size img-fluid"
                                                        src="{{ asset('uploads/img/dummy/no-image.jpg') }}" alt="no image">
                                                @endif

                                            </td>
                                            <td>{{ $admin_user->getRoleNames()->first() }}</td>
                                            <td>{{ $admin_user->name }}</td>
                                            <td>{{ $admin_user->email }}</td>
                                            <td>
                                                <select name="status" class="status-select col-form-label"
                                                    data-user-id="{{ $admin_user->id }}">
                                                    <option value="1"
                                                        @if ($admin_user->status == '1') selected @endif>Active</option>
                                                    <option value="2"
                                                        @if ($admin_user->status == '2') selected @endif>Pending</option>
                                                    <option value="3"
                                                        @if ($admin_user->status == '3') selected @endif>Inactive</option>
                                                </select>
                                            </td>

                                            <td>
                                                <div>
                                                    <a href="{{ route('admin-user.edit', $admin_user->id) }}"
                                                        class="mr-2">
                                                        <i class="fa fa-edit text-info font-18"></i>
                                                    </a>
                                                    <a href="#" data-toggle="modal"
                                                        data-target="#deleteModel{{ $admin_user->id }}">
                                                        <i class="fa fa-trash text-danger font-18"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Modal -->
                                        <div class="modal fade" id="deleteModel{{ $admin_user->id }}" tabindex="-1"
                                            role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalCenterTitle">
                                                            {{ __('content.delete') }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="{{ __('content.close') }}">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        {{ __('content.you_wont_be_able_to_revert_this') }}
                                                    </div>
                                                    <div class="modal-footer">
                                                        @if ($demo_mode == 'on')
                                                            <!-- Include Alert Blade -->
                                                            @include('admin.demo_mode.demo-mode')
                                                        @else
                                                            <form class="d-inline-block"
                                                                action="{{ route('admin-user.destroy', $admin_user->id) }}"
                                                                method="POST">
                                                                @method('DELETE')
                                                                @csrf
                                                        @endif

                                                        <button type="button" class="btn btn-danger"
                                                            data-dismiss="modal">{{ __('content.cancel') }}</button>
                                                        <button type="submit"
                                                            class="btn btn-success">{{ __('content.yes_delete_it') }}</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <span>{{ __('content.not_yet_created') }}</span>
                    @endif

                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div><!-- end row-->

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Store previous value when select gets focus
            $(document).on('focus', '.status-select', function() {
                $(this).data('previous-value', $(this).val());
            });

            // Handle status change with event delegation
            $(document).on('change', '.status-select', function() {
                var $select = $(this);
                var userId = $select.data('user-id');
                var newStatus = $select.val();

                // Show loading indicator
                $select.prop('disabled', true);

                // Send AJAX request to update status
                $.ajax({
                    url: '{{ url('admin/admin-user') }}/' + userId + '/status',
                    method: 'POST',
                    data: {
                        status: newStatus,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            // Success
                            Swal.fire({
                                icon: 'success',
                                title: 'Status Updated!',
                                text: 'The status was changed successfully.',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        } else {
                            // Revert the change if failed
                            $select.val($select.data('previous-value'));
                            // Error
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Failed to update status!',
                                timer: 5000,
                                showConfirmButton: true
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        // Revert to previous value
                        $select.val($select.data('previous-value'));
                        // Error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update status!',
                            timer: 5000,
                            showConfirmButton: true
                        });
                    },
                    complete: function() {
                        $select.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endpush
