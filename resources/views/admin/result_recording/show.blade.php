@extends('layouts.admin.master')

@section('content')
    <!-- Form row -->


    <style>
        .company-info {
            border-right: 1px solid #eee;
            padding-right: 20px;
        }

        .client-info {
            background-color: #f9f9f9;
            padding: 10px;
            border-left: 3px solid #007bff;
        }

        .test-results {
            padding-left: 20px;
        }

        .result-status {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0;
        }

        .result-details {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }

        .view_results_blue {
            background-color: #dbf1ff !important;
            border: 2px solid #dbf1ff;
            margin-bottom: 5px;
            padding: 5px 5px 3px 10px;
            border-radius: 4px
        }

        .view_results {
            margin-bottom: 2px;
            padding: 5px 5px 3px 10px;
            border: 2px solid #d5d5d5;
            border-radius: 4px;
            margin-bottom: 5px;
        }
    </style>


    <div class="row">
        <div class="col-xl-12 box-margin height-card">
            <div class="card card-body">
                {{-- <h4 class="card-title">Edit Result Recording</h4> --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        @php
                            $contact_info_widget = App\Models\Admin\ContactInfoWidget::where(
                                'language_id',
                                $language->id,
                            )->first();
                        @endphp

                        <div class="row">
                            <div class="col-md-12">
                                @if ($contact_info_widget)
                                    <div class="company-info">
                                        <h3>{{ $contact_info_widget->description }}</h3>
                                        <p class="mb-1">{{ $contact_info_widget->address }}</p>
                                        <p class="mb-1">{{ $contact_info_widget->email }}</p>
                                        <p class="mb-1">Phone: {{ $contact_info_widget->phone }}</p>
                                        <p>Email: {{ $contact_info_widget->email }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-12">
                                @if ($recoding_result->clientProfile)
                                    <div class="mt-3 client-info">
                                        <h4>{{ $recoding_result->clientProfile->company_name }}</h4>
                                        <p class="mb-1">{{ $recoding_result->clientProfile->address }}</p>
                                        <p class="mb-1">{{ $recoding_result->clientProfile->city }},
                                            {{ $recoding_result->clientProfile->state }},
                                            {{ $recoding_result->clientProfile->zip }}</p>
                                        <p>{{ $recoding_result->clientProfile->phone }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mt-auto">
                        <div class="test-results text-right">
                            @php
                                $header_image = App\Models\Admin\HeaderImage::first();
                            @endphp
                            <img src="{{ asset('uploads/img/general/' . $header_image->section_image) }}" alt="logo image"
                                class="rounded">
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="text-left view_results_blue">
                                        <strong>Overall Result</strong>
                                    </div>
                                    <div class="text-left view_results_blue">
                                        <strong>Date/Time Collected</strong>
                                    </div>
                                    <div class="text-left view_results_blue">
                                        <strong>Donor Reported</strong>
                                    </div>
                                    <div class="text-left view_results_blue">
                                        <strong>Test Detail</strong>
                                    </div>
                                    <div class="text-left view_results_blue">
                                        <strong>Specimen</strong>
                                    </div>
                                    <div class="text-left view_results_blue">
                                        <strong>Result ID</strong>
                                    </div>
                                    @if ($recoding_result->collection_location)
                                        <div class="text-left view_results_blue">
                                            <strong>Location</strong>
                                        </div>
                                    @endif

                                </div>
                                <div class="col-md-8">
                                    <div class="text-left view_results">
                                        <strong> NEGATIVE</strong>
                                    </div>
                                    <div class="text-left view_results">
                                        <strong>
                                            {{ \Carbon\Carbon::parse($recoding_result->collection_datetime)->format('Y-m-d h:i A') }}</strong>
                                    </div>
                                    <div class="text-left view_results">
                                        <strong> {{ $recoding_result->employee->first_name }}
                                            {{ $recoding_result->employee->last_name }}
                                            ({{ str_pad($recoding_result->employee_id, 6, '0', STR_PAD_LEFT) }})</strong>
                                    </div>
                                    <div class="text-left view_results">
                                        <strong>{{ $recoding_result->reason_for_test }} - <span class="text-primary">
                                                {{ $recoding_result->testAdmin->test_name }}</span> :
                                            {{ $recoding_result->testAdmin->method }},{{ $recoding_result->testAdmin->regulation }}</strong>
                                    </div>
                                    <div class="text-left view_results">
                                        <strong> {{ $recoding_result->testAdmin->specimen }}</strong>
                                    </div>
                                    <div class="text-left view_results">
                                        <strong>
                                            {{ str_pad($recoding_result->id, 6, '0', STR_PAD_LEFT) }}</strong>
                                    </div>
                                    @if ($recoding_result->collection_location)
                                        <div class="text-left view_results">
                                            <strong>
                                                {{ $recoding_result->collection_location }}</strong>
                                        </div>
                                    @endif

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <hr>

                @if ($demo_mode == 'on')
                    <!-- Include Alert Blade -->
                    @include('admin.demo_mode.demo-mode')
                @else
                    <form action="{{ route('result-recording.update', $recoding_result->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-12 mt-3" id="panel_test">
                        <div class="card">
                            <div class="card-header">
                                <h6>Test Panel Results</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Drug Name</th>
                                                <th>Drug Code</th>
                                                <th>Result</th>
                                                <th>Cut-Off Level</th>
                                                <th>Conf. Level</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($recoding_result->resultPanel as $panel)
                                                <tr>
                                                    <td>{{ $panel->drug_name }}</td>
                                                    <td>{{ $panel->drug_code }}</td>
                                                    <td>{{ ucfirst($panel->result ?? 'N/A') }}</td>
                                                    <td>{{ $panel->cut_off_level }} ng/mL</td>
                                                    <td>{{ $panel->conf_level }} ng/mL</td>
                                                    <input type="hidden"
                                                        name="panel_results[{{ $panel->panel_id }}][panel_id]"
                                                        value="{{ $panel->panel_id }}">
                                                    <input type="hidden"
                                                        name="panel_results[{{ $panel->panel_id }}][drug_name]"
                                                        value="{{ $panel->drug_name }}">
                                                    <input type="hidden"
                                                        name="panel_results[{{ $panel->panel_id }}][drug_code]"
                                                        value="{{ $panel->drug_code }}">
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="laboratory_id" class="col-form-label">Laboratory Name</label>
                            <select class="form-control select2" name="laboratory_id" id="laboratory_id">
                                <option value="">{{ __('content.select_your_option') }}</option>
                                @foreach ($laboratories as $laboratory)
                                    <option value="{{ $laboratory->id }}"
                                        {{ $recoding_result->laboratory_id == $laboratory->id ? 'selected' : '-None-' }}>
                                        {{ $laboratory->laboratory_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="mro_id" class="col-form-label">MRO Name</label>
                            <select class="form-control select2" name="mro_id" id="mro_id">
                                <option value="">{{ __('content.select_your_option') }}</option>
                                @foreach ($mros as $mro)
                                    <option value="{{ $mro->id }}"
                                        {{ $recoding_result->mro_id == $mro->id ? 'selected' : '-None-' }}>
                                        {{ $mro->doctor_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="note">Note</label>
                            <textarea id="note" name="note" class="form-control" rows="3">{{ $recoding_result->note }}</textarea>
                        </div>
                    </div>


                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end row -->
@endsection


