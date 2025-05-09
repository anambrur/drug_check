@extends('layouts.admin.master')

@section('content')
    <style>
        .custom-panel-wrapper {
            margin-top: 10px;
        }

        .form-check-label {
            margin-bottom: -4px;
        }

        .panel-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .panel-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .panel-item:last-child {
            margin-bottom: 0;
        }

        .form-check-label {
            margin-left: 8px;
            margin-right: 20px;
            min-width: 150px;
        }

        .cutoff-conf-wrapper {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .cutoff-conf-wrapper input {
            width: 100px;
        }

        .cutoff-conf-wrapper label {
            font-size: 12px;
            margin-bottom: 0;
            margin-right: 4px;
        }
    </style>

    <!-- Form row -->
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <h4 class="card-title">Add Test</h4>

                </h4>
                @if ($demo_mode == 'on')
                    <!-- Include Alert Blade -->
                    @include('admin.demo_mode.demo-mode')
                @else
                    <form action="{{ route('test-admin.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                @endif


                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="test_name">Test Name <span class="text-red">*</span></label>
                            <input id="test_name" name="test_name" type="text" class="form-control"
                                placeholder="Enter Test Name" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="specimen" class="col-form-label">Specimen <span class="text-red">*</span></label>
                            <select class="form-control" name="specimen" id="specimen">
                                <option value="" selected>{{ __('content.select_your_option') }}</option>
                                <option value="urine">Urine</option>
                                <option value="hair">Hair</option>
                                <option value="breath_alcohol">Breath Alcohol</option>
                                <option value="saliva_alcohol">Saliva Alcohol</option>
                                <option value="breath_drug">Breath Drug</option>
                                <option value="oral_fluid">Oral Fluid</option>
                                <option value="nasal_swab">Nasal Swab</option>
                                <option value="blood">Blood</option>
                                <option value="finger_nail">Finger Nail</option>
                                <option value="meconium">Meconium</option>
                                <option value="sweat">Sweat</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="method" class="col-form-label">Method <span class="text-red">*</span></label>
                            <select class="form-control" name="method" id="method">
                                <option value="" selected>{{ __('content.select_your_option') }}</option>
                                <option value="POCT">POCT</option>
                                <option value="LAB">LAB</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="regulation" class="col-form-label">Regulation <span class="text-red">*</span></label>
                            <select class="form-control" name="regulation" id="regulation">
                                <option value="" selected>{{ __('content.select_your_option') }}</option>
                                <option value="DOT">DOT</option>
                                <option value="Non-DOT">Non-DOT</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="description">{{ __('content.description') }}</label>
                            <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="laboratory" class="col-form-label">laboratory </label>
                            <select class="form-control" name="laboratory" id="laboratory">
                                <option value="" selected>{{ __('content.select_your_option') }}</option>
                                @foreach ($laboratories as $laboratory)
                                    @if ($laboratory)
                                        <option value="{{ $laboratory->id ?? '' }}">{{ $laboratory->laboratory_name ?? '' }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="mro" class="col-form-label">MRO </label>
                            <select class="form-control" name="mro" id="mro">
                                <option value="" selected>{{ __('content.select_your_option') }}</option>
                                @foreach ($mros as $mro)
                                    @if ($mro)
                                        <option value="{{ $mro->id ?? '' }}">{{ $mro->doctor_name ?? '' }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="panel_list" class="col-form-label">Test Panels: <span
                                    class="text-danger">*</span></label>
                        </div>
                        <!-- Use buttons instead of radio inputs -->
                        <div class="mb-2">
                            <button type="button" class="btn btn-sm btn-primary me-2"
                                onclick="selectAllPanels(true)">Select All</button>
                            <button type="button" class="btn btn-sm btn-warning" onclick="invertSelection()">Invert
                                Selection</button>
                        </div>

                        <div class="row custom-panel-wrapper">
                            @if ($panel_lists && count($panel_lists) > 0)
                                @foreach ($panel_lists->chunk(30) as $chunk)
                                    <div class="col-md-12 mb-4">
                                        <div class="panel-card">
                                            <div class="row">
                                                @foreach ($chunk as $panel_list)
                                                    <div class="col-md-6 panel-item">
                                                        <div class="form-check d-flex align-items-center">
                                                            <input class="form-check-input panel-checkbox mt-0"
                                                                type="checkbox" value="{{ $panel_list->id ?? '' }}"
                                                                id="panel_list_{{ $panel_list->id }}"
                                                                name="panel_list[]">
                                                            <label class="form-check-label ml-3"
                                                                for="panel_list_{{ $panel_list->id }}">
                                                                {{ $panel_list->drug_name ?? '' }}
                                                            </label>
                                                        </div>

                                                        <div class="cutoff-conf-wrapper ms-auto">
                                                            <div class="d-flex align-items-center">
                                                                <label
                                                                    for="cut_off_level_{{ $panel_list->id }}">Cut-Off</label>
                                                                <input type="text"
                                                                    name="cut_off_level[{{ $panel_list->id }}]"
                                                                    id="cut_off_level_{{ $panel_list->id }}"
                                                                    class="form-control form-control-sm"
                                                                    value="{{ $panel_list->cut_off_level ?? '' }} {{ 'ng/ml' }}"
                                                                    readonly>
                                                            </div>

                                                            <div class="d-flex align-items-center">
                                                                <label
                                                                    for="conf_level_{{ $panel_list->id }}">Conf.</label>
                                                                <input type="text"
                                                                    name="conf_level[{{ $panel_list->id }}]"
                                                                    id="conf_level_{{ $panel_list->id }}"
                                                                    class="form-control form-control-sm"
                                                                    value="{{ $panel_list->conf_level ?? '' }} {{ 'ng/ml' }}"
                                                                    readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-md-2">
                                    <div class="alert alert-info">
                                        No panel list available
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>




                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status" class="col-form-label">{{ __('content.status') }} </label>
                            <select class="form-control" name="status" id="status">
                                <option value="active" selected>{{ __('content.select_your_option') }}</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-center mt-3">
                        <button type="submit" class="btn btn-primary w-100">{{ __('content.submit') }}</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end row -->


    <!-- Form row -->
    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                <table id="basic-datatable" class="table table-striped dt-responsive w-100">
                    <thead>
                        <tr>
                            <th>Test Name</th>
                            <th>Specimen</th>
                            <th>Method</th>
                            <th>Regulation</th>
                            <th>Description</th>
                            <th>Laboratory</th>
                            <th>MRO</th>
                            <th>Panel List</th>
                            <th>{{ __('content.status') }}</th>
                            <th class="custom-width-action">{{ __('content.action') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($test_admins as $test_admin)
                            <tr>
                                <td>{{ $test_admin->test_name }}</td>
                                <td>{{ ucfirst($test_admin->specimen) }}</td>
                                <td>{{ ucfirst($test_admin->method) }}</td>
                                <td>{{ ucfirst($test_admin->regulation) }}</td>
                                <td>{{ $test_admin->description }}</td>
                                <td>{{ $test_admin->laboratory->laboratory_name }}</td>
                                <td>{{ $test_admin->mro->doctor_name }}</td>
                                <td>
                                    @if ($test_admin->panel->isNotEmpty())
                                        @foreach ($test_admin->panel as $item)
                                            <span class="badge badge-pill badge-info mb-1">{{ $item->drug_name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">NULL</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($test_admin->status == 'active')
                                        <span class="badge badge-pill badge-success">Active</span>
                                    @else
                                        <span class="badge badge-pill badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <a href="{{ route('test-admin.edit', $test_admin->id) }}" class="mr-2">
                                            <i class="fa fa-edit text-info font-18"></i>
                                        </a>

                                        <a href="#" data-toggle="modal"
                                            data-target="#deleteModal{{ $test_admin->id }}">
                                            <i class="fa fa-trash text-danger font-18"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal -->
                            <div class="modal fade" id="deleteModal{{ $test_admin->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="test_adminModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="test_adminModalCenterTitle">
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
                                                    action="{{ route('test-admin.destroy', $test_admin->id) }}"
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

            </div>
        </div>
    </div>
    <!-- end row -->

    <script>
        function selectAllPanels(selectAll) {
            const checkboxes = document.querySelectorAll('.panel-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll;
            });
        }

        function invertSelection() {
            const checkboxes = document.querySelectorAll('.panel-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = !checkbox.checked;
            });
        }
    </script>
@endsection
