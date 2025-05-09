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
                <div class="d-md-flex justify-content-between align-items-center mb-20">
                    <h4 class="card-title">Edit Test</h4>

                    <div>
                        <a href="{{ url()->previous() }}" class="btn btn-primary"><i class="fas fa-angle-left"></i>
                            {{ __('content.back') }}</a>
                    </div>
                </div>
                @if ($demo_mode == 'on')
                    <!-- Include Alert Blade -->
                    @include('admin.demo_mode.demo-mode')
                @else
                    <form action="{{ route('test-admin.update', $test_admin->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                @endif

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="test_name">Test Name <span class="text-red">*</span></label>
                            <input id="test_name" name="test_name" type="text" class="form-control"
                                placeholder="Enter Test Name" value="{{ old('test_name', $test_admin->test_name) }}"
                                required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="specimen" class="col-form-label">Specimen </label>
                            <select class="form-control" name="specimen" id="specimen">
                                <option value="" {{ old('specimen', $test_admin->specimen) == '' ? 'selected' : '' }}>
                                    {{ __('content.select_your_option') }}</option>
                                <option value="urine"
                                    {{ old('specimen', $test_admin->specimen) == 'urine' ? 'selected' : '' }}>
                                    Urine</option>
                                <option value="hair"
                                    {{ old('specimen', $test_admin->specimen) == 'hair' ? 'selected' : '' }}>
                                    Hair</option>
                                <option value="breath_alcohol"
                                    {{ old('specimen', $test_admin->specimen) == 'breath_alcohol' ? 'selected' : '' }}>
                                    Breath
                                    Alcohol</option>
                                <option value="saliva_alcohol"
                                    {{ old('specimen', $test_admin->specimen) == 'saliva_alcohol' ? 'selected' : '' }}>
                                    Saliva
                                    Alcohol</option>
                                <option value="breath_drug"
                                    {{ old('specimen', $test_admin->specimen) == 'breath_drug' ? 'selected' : '' }}>Breath
                                    Drug
                                </option>
                                <option value="oral_fluid"
                                    {{ old('specimen', $test_admin->specimen) == 'oral_fluid' ? 'selected' : '' }}>Oral
                                    Fluid
                                </option>
                                <option value="nasal_swab"
                                    {{ old('specimen', $test_admin->specimen) == 'nasal_swab' ? 'selected' : '' }}>Nasal
                                    Swab
                                </option>
                                <option value="blood"
                                    {{ old('specimen', $test_admin->specimen) == 'blood' ? 'selected' : '' }}>
                                    Blood</option>
                                <option value="finger_nail"
                                    {{ old('specimen', $test_admin->specimen) == 'finger_nail' ? 'selected' : '' }}>Finger
                                    Nail
                                </option>
                                <option value="meconium"
                                    {{ old('specimen', $test_admin->specimen) == 'meconium' ? 'selected' : '' }}>Meconium
                                </option>
                                <option value="sweat"
                                    {{ old('specimen', $test_admin->specimen) == 'sweat' ? 'selected' : '' }}>
                                    Sweat</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="method" class="col-form-label">Method </label>
                            <select class="form-control" name="method" id="method">
                                <option value="" {{ old('method', $test_admin->method) == '' ? 'selected' : '' }}>
                                    {{ __('content.select_your_option') }}</option>
                                <option value="POCT"
                                    {{ old('method', $test_admin->method) == 'POCT' ? 'selected' : '' }}>POCT
                                </option>
                                <option value="LAB" {{ old('method', $test_admin->method) == 'LAB' ? 'selected' : '' }}>
                                    LAB
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="regulation" class="col-form-label">Regulation </label>
                            <select class="form-control" name="regulation" id="regulation">
                                <option value=""
                                    {{ old('regulation', $test_admin->regulation) == '' ? 'selected' : '' }}>
                                    {{ __('content.select_your_option') }}</option>
                                <option value="DOT"
                                    {{ old('regulation', $test_admin->regulation) == 'DOT' ? 'selected' : '' }}>DOT
                                </option>
                                <option value="Non-DOT"
                                    {{ old('regulation', $test_admin->regulation) == 'Non-DOT' ? 'selected' : '' }}>Non-DOT
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="description">{{ __('content.description') }}</label>
                            <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $test_admin->description) }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="laboratory" class="col-form-label">laboratory </label>
                            <select class="form-control" name="laboratory" id="laboratory">
                                <option value="">{{ __('content.select_your_option') }}</option>
                                @foreach ($laboratories as $laboratory)
                                    @if ($laboratory)
                                        <option value="{{ $laboratory->id ?? '' }}"
                                            {{ old('laboratory', $test_admin->laboratory->id ?? '') == $laboratory->id ? 'selected' : '' }}>
                                            {{ $laboratory->laboratory_name ?? '' }}
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
                                <option value="">{{ __('content.select_your_option') }}</option>
                                @foreach ($mros as $mro)
                                    @if ($mro)
                                        <option value="{{ $mro->id ?? '' }}"
                                            {{ old('mro', $test_admin->mro->id ?? '') == $mro->id ? 'selected' : '' }}>
                                            {{ $mro->doctor_name ?? '' }}
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
                            @foreach ($panel_lists->chunk(30) as $chunk)
                                <div class="col-md-12 mb-4">
                                    <div class="panel-card">
                                        <div class="row">
                                            @foreach ($chunk as $index => $panel_list)
                                                <div class="col-md-6 panel-item">
                                                    <div class="form-check d-flex align-items-center">
                                                        <input class="form-check-input panel-checkbox mt-0"
                                                            type="checkbox" value="{{ $panel_list->id ?? '' }}"
                                                            id="panel_list_{{ $panel_list->id }}" name="panel_list[]"
                                                            {{ in_array($panel_list->id, old('panel_list', $test_admin->panel->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}>
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
                                                            <label for="conf_level_{{ $panel_list->id }}">Conf.</label>
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
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status" class="col-form-label">{{ __('content.status') }} </label>
                            <select class="form-control" name="status" id="status">
                                <option value="active"
                                    {{ old('status', $test_admin->status) == 'active' ? 'selected' : '' }}>
                                    Active</option>
                                <option value="inactive"
                                    {{ old('status', $test_admin->status) == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-center mt-3">
                        <button type="submit" class="btn btn-primary w-100">Update</button>
                    </div>
                </div>


                </form>
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
