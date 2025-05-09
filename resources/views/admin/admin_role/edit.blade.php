@extends('layouts.admin.master')

@section('content')
    <!-- Include Alert Blade -->
    @include('admin.alert.alert')

    <!-- Form row -->
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">{{ __('content.edit_admin_role') }}</h4>
                @if ($demo_mode == 'on')
                    <!-- Include Alert Blade -->
                    @include('admin.demo_mode.demo-mode')
                @else
                    <form action="{{ route('admin-role.update', $role->id) }}" method="POST">
                        @method('PUT')
                        @csrf
                @endif
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="name">{{ __('content.role_name') }} <span class="text-red">*</span></label>
                            <input id="name" name="name" type="text" class="form-control"
                                value="{{ $role->name }}" required>
                        </div>
                    </div>
                    <!-- Master Checkbox -->
                    <div class="col-md-12 mb-3 ml-3">
                        <input type="checkbox" id="checkAll"> <label for="checkAll">All Permissions</label>
                    </div>
                    <!-- Grouped Permissions -->
                    @php
                        $role_permissions = $role->getAllPermissions();
                        $checked_permissions = [];
                        foreach ($role_permissions as $role_permission) {
                            $checked_permissions[] = $role_permission->name;
                        }
                    @endphp
                    @foreach ($permissions as $group => $groupPermissions)
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <input type="checkbox" class="group-check" id="groupCheck{{ $loop->index }}"
                                        data-group="{{ $group }}">
                                    <strong>{{ ucfirst($group) }} {{ __('content.permissions') }}</strong>
                                </div>
                                <div class="card-body">
                                    @foreach ($groupPermissions as $permission)
                                        <span class="badge badge-success mr-3 mb-3 font-16">
                                            <input type="checkbox" name="is_ok[]" value="{{ $permission->name }}"
                                                class="permission-check group-{{ $group }}"
                                                @if (in_array($permission->name, $checked_permissions)) checked @endif>
                                            {{ $permission->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="col-md-12">
                        <div class="form-group">
                            <small class="form-text text-muted">{{ __('content.required_fields') }}</small>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">{{ __('content.submit') }}</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end row -->
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            // Master Checkbox (Check All)
            const $checkAll = $('#checkAll');
            const $permissionChecks = $('.permission-check');
            const $groupChecks = $('.group-check');


            // Check/Uncheck all permissions
            $checkAll.on('change', function() {
                const isChecked = $(this).prop('checked');
                $permissionChecks.prop('checked', isChecked);
                $groupChecks.prop('checked', isChecked);
            });

            // Group Checkboxes
            $groupChecks.on('change', function() {
                const group = $(this).data('group');
                $(`.group-${group}`).prop('checked', $(this).prop('checked'));
                updateCheckAllStatus();
            });

            // Individual Permission Checkboxes
            $permissionChecks.on('change', function() {
                const groupClass = $(this).attr('class').split(' ').find(c => c.startsWith('group-'));
                if (groupClass) {
                    const group = groupClass.replace('group-', '');
                    const $groupCheck = $(`.group-check[data-group="${group}"]`);
                    const $groupPermissions = $(`.group-${group}`);
                    const allChecked = $groupPermissions.toArray().every(checkbox => $(checkbox).prop(
                        'checked'));

                    $groupCheck.prop('checked', allChecked);
                    updateCheckAllStatus();
                }
            });

            // Function to update "Check All" status
            function updateCheckAllStatus() {
                const allChecked = $permissionChecks.toArray().every(checkbox => $(checkbox).prop('checked'));
                $checkAll.prop('checked', allChecked);
            }

            // Initialize group checkboxes based on individual permissions
            $groupChecks.each(function() {
                const group = $(this).data('group');
                const $groupPermissions = $(`.group-${group}`);
                const allChecked = $groupPermissions.toArray().every(checkbox => $(checkbox).prop(
                    'checked'));
                $(this).prop('checked', allChecked);
            });

            // Initialize "Check All" status
            updateCheckAllStatus();
        });
    </script>
@endpush
