@extends('layouts.admin.master')

@section('content')

    <div class="row">
        <div class="col-12 box-margin">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-20">
                        <h6 class="card-title mb-0">Clint Profile List
                            <!-- Button -->
                            <a id="hoverButton" class="iyzi-btn"><i class="fas fa-camera"></i>
                                {{ __('content.view_draft') }}</a>
                            <!-- Modal -->
                            <div id="imageModal" class="border border-success iyzi-modal">
                                <img class="img-fluid " src="{{ asset('uploads/img/dummy/style/blog-style1.jpg') }}"
                                    alt="draft image">
                            </div>
                        </h6>
                        <div>
                            @can('client profile create')
                                <a href="{{ url('admin/client-profile/create') }}" class="btn btn-primary float-right mb-3">+
                                    Add Client Profile</a>
                            @endcan

                        </div>
                    </div>

                    @if (count($clientProfiles) > 0)
                        <div>
                            <input id="check_all" type="checkbox" onclick="showHideDeleteButton(this)">
                            <label for="check_all">{{ __('content.all') }}</label>
                            <a id="deleteChecked" class="ml-2" href="#" data-toggle="modal"
                                data-target="#deleteCheckedModal">
                                <i class="fa fa-trash text-danger font-18"></i>
                            </a>
                        </div>
                        @if ($demo_mode == 'on')
                            <!-- Include Alert Blade -->
                            @include('admin.demo_mode.demo-mode')
                        @else
                            <form onsubmit="return btnCheckListGet()" action="{{ route('client-profile.destroy_checked') }}"
                                method="POST">
                                @method('DELETE')
                                @csrf
                        @endif

                        <input type="hidden" id="checked_lists" name="checked_lists" value="">

                        <!-- Modal -->
                        <div class="modal fade" id="deleteCheckedModal" tabindex="-1" role="dialog"
                            aria-labelledby="deleteCheckedModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteCheckedModalCenterTitle">
                                            {{ __('content.delete') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="{{ __('content.close') }}">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-center">
                                        {{ __('content.delete_selected') }}
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger"
                                            data-dismiss="modal">{{ __('content.cancel') }}</button>
                                        <button onclick="btnCheckListGet()" type="submit"
                                            class="btn btn-success">{{ __('content.yes_delete_it') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>
                        <table id="basic-datatable" class="table table-striped dt-responsive w-100">
                            <thead>
                                <tr>
                                    <th>Company Name</th>
                                    <th>Account No</th>
                                    <th>Address</th>
                                    <th>City</th>
                                    <th>State</th>
                                    <th>Zip</th>
                                    <th>Phone</th>
                                    <th>DER Contact Name</th>
                                    <th>DER Contact Email</th>
                                    <th>DER Contact Phone</th>
                                    <th>Fax</th>
                                    <th>Short Description</th>
                                    <th>Shipping Address</th>
                                    <th>Billing Contact Name</th>
                                    <th>Billing Contact Email</th>
                                    <th>Billing Contact Phone</th>
                                    <th>Client Start Date</th>
                                    <th>{{ __('content.status') }}</th>
                                    <th class="custom-width-action">{{ __('content.action') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($clientProfiles as $clientProfile)
                                    <tr>
                                        <td>{{ $clientProfile->company_name }}</td>
                                        <td>{{ $clientProfile->account_no }}</td>
                                        <td>{{ $clientProfile->address }}</td>
                                        <td>{{ $clientProfile->city }}</td>
                                        <td>{{ $clientProfile->state }}</td>
                                        <td>{{ $clientProfile->zip }}</td>
                                        <td>{{ $clientProfile->phone }}</td>
                                        <td>{{ $clientProfile->der_contact_name }}</td>
                                        <td>{{ $clientProfile->der_contact_email }}</td>
                                        <td>{{ $clientProfile->der_contact_phone }}</td>
                                        <td>{{ $clientProfile->fax }}</td>
                                        <td>{{ $clientProfile->short_description }}</td>
                                        <td>{{ $clientProfile->shipping_address }}</td>
                                        <td>{{ $clientProfile->billing_contact_name }}</td>
                                        <td>{{ $clientProfile->billing_contact_email }}</td>
                                        <td>{{ $clientProfile->billing_contact_phone }}</td>
                                        <td>{{ Carbon\Carbon::parse($clientProfile->created_at)->format('d.m.Y') }}</td>
                                        <td>
                                            @if ($clientProfile->status == 'active')
                                                <span class="badge badge-pill badge-success">Active</span>
                                            @else
                                                <span class="badge badge-pill badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                @can('client profile edit')
                                                    <a href="{{ route('client-profile.edit', $clientProfile->id) }}"
                                                        class="mr-2">
                                                        <i class="fa fa-edit text-info font-18"></i>
                                                    </a>
                                                @endcan

                                                @can('client profile view')
                                                    <a href="{{ route('client-profile.show', $clientProfile->id) }}"
                                                        class="mr-2">
                                                        <i class="fa fa-eye text-primary font-18"></i>
                                                    </a>
                                                @endcan
                                                @can('client profile delete')
                                                    <a href="#" data-toggle="modal"
                                                        data-target="#deleteModal{{ $clientProfile->id }}">
                                                        <i class="fa fa-trash text-danger font-18"></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal -->
                                    <div class="modal fade" id="deleteModal{{ $clientProfile->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="clientProfileModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="clientProfileModalCenterTitle">
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
                                                            action="{{ route('client-profile.destroy', $clientProfile->id) }}"
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
    <div class="modal fade" id="blogSectionModal" tabindex="-1" role="dialog" aria-labelledby="blogSectionModalLabel"
        aria-modal="false">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mt-0 font-16" id="blogSectionModalLabel">
                        {{ __('content.section_title_and_description') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    @if (isset($blog_section))
                        @if ($demo_mode == 'on')
                            <!-- Include Alert Blade -->
                            @include('admin.demo_mode.demo-mode')
                        @else
                            <form action="{{ route('blog-section.update', $blog_section->id) }}" method="POST">
                                @method('PUT')
                                @csrf
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="section_title">{{ __('content.section_title') }}</label>
                                    <input type="text" name="section_title" class="form-control" id="section_title"
                                        value="{{ $blog_section->section_title }}">
                                    <small class="form-text text-muted">{{ __('content.recommended_tags') }}
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('a', 'section_title')">{{ __('<a href="">') }}</span>
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('br', 'section_title')">{{ __('<br>') }}</span>
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('b', 'section_title')">{{ __('<b>') }}</span>
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('i', 'section_title')">{{ __('<i>') }}</span>
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('span', 'section_title')">{{ __('<span>') }}</span>
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('p', 'section_title')">{{ __('<p>') }}</span>
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="title">{{ __('content.title') }}</label>
                                    <input type="text" name="title" class="form-control" id="title"
                                        value="{{ $blog_section->title }}">
                                    <small class="form-text text-muted">{{ __('content.recommended_tags') }}
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('a', 'title')">{{ __('<a href="">') }}</span>
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('br', 'title')">{{ __('<br>') }}</span>
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('b', 'title')">{{ __('<b>') }}</span>
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('i', 'title')">{{ __('<i>') }}</span>
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('span', 'title')">{{ __('<span>') }}</span>
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('p', 'title')">{{ __('<p>') }}</span>
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="button_name">{{ __('content.button_name') }} </label>
                                    <input id="button_name" name="button_name" type="text" class="form-control"
                                        value="{{ $blog_section->button_name }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="button_url">{{ __('content.button_url') }} </label>
                                    <input id="button_url" name="button_url" type="text" class="form-control"
                                        value="{{ $blog_section->button_url }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="section_item">{{ __('content.section_item') }} <span
                                            class="text-red">*</span></label>
                                    <input type="number" name="section_item" class="form-control" id="section_item"
                                        value="{{ $blog_section->section_item }}" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="paginate_item">{{ __('content.paginate_item') }} <span
                                            class="text-red">*</span></label>
                                    <input type="number" name="paginate_item" class="form-control" id="paginate_item"
                                        value="{{ $blog_section->paginate_item }}" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary mr-2">{{ __('content.submit') }}</button>
                        <a href="#" class="btn btn-sm btn-primary" data-toggle="modal"
                            data-target="#blogSectionDestroyModal{{ $blog_section->id }}">
                            <i class="fa fa-trash"></i> {{ __('content.reset') }}
                        </a>
                        </form>

                        <!-- Modal -->
                        <div class="modal fade" id="blogSectionDestroyModal{{ $blog_section->id }}" tabindex="-1"
                            role="dialog" aria-labelledby="blogSectionDestroyModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="blogSectionDestroyModalCenterTitle">
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
                                                action="{{ route('blog-section.destroy', $blog_section->id) }}"
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
                    @else
                        @if ($demo_mode == 'on')
                            <!-- Include Alert Blade -->
                            @include('admin.demo_mode.demo-mode')
                        @else
                            <form action="{{ route('blog-section.store') }}" method="POST">
                                @csrf
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="section_title">{{ __('content.section_title') }}</label>
                                    <input type="text" name="section_title" class="form-control" id="section_title">
                                    <small class="form-text text-muted">{{ __('content.recommended_tags') }}
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('a', 'section_title')">{{ __('<a href="">') }}</span>
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('br', 'section_title')">{{ __('<br>') }}</span>
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('b', 'section_title')">{{ __('<b>') }}</span>
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('i', 'section_title')">{{ __('<i>') }}</span>
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('span', 'section_title')">{{ __('<span>') }}</span>
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('p', 'section_title')">{{ __('<p>') }}</span>
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="title">{{ __('content.title') }}</label>
                                    <input type="text" name="title" class="form-control" id="title">
                                    <small class="form-text text-muted">{{ __('content.recommended_tags') }}
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('a', 'title')">{{ __('<a href="">') }}</span>
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('br', 'title')">{{ __('<br>') }}</span>
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('b', 'title')">{{ __('<b>') }}</span>
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('i', 'title')">{{ __('<i>') }}</span>
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('span', 'title')">{{ __('<span>') }}</span>
                                        <span class="text-danger font-weight-bold custom-tag mr-1"
                                            onclick="insertTag('p', 'title')">{{ __('<p>') }}</span>
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="button_name">{{ __('content.button_name') }} </label>
                                    <input id="button_name" name="button_name" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="button_url">{{ __('content.button_url') }} </label>
                                    <input id="button_url" name="button_url" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="section_item">{{ __('content.section_item') }} <span
                                            class="text-red">*</span></label>
                                    <input type="number" name="section_item" class="form-control" id="section_item"
                                        value="3" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="paginate_item">{{ __('content.paginate_item') }} <span
                                            class="text-red">*</span></label>
                                    <input type="number" name="paginate_item" class="form-control" id="paginate_item"
                                        value="12" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">{{ __('content.submit') }}</button>
                        </form>
                    @endif
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection
