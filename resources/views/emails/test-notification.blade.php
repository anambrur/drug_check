<!DOCTYPE html>
<html>

<head>
    <title>Test Results Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .email-container {
            background-color: #f8f9fa;
            padding: 25px;
            border-radius: 5px;
        }

        .company-info {
            border-right: 1px solid #eee;
            padding-right: 20px;
        }

        .client-info {
            background-color: #f9f9f9;
            padding: 10px;
            border-left: 3px solid #007bff;
            margin-top: 15px;
        }

        .test-results {
            padding-left: 20px;
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

        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .result-table th,
        .result-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .result-table th {
            background-color: #f2f2f2;
        }

        .header-image {
            max-width: 200px;
            height: auto;
            margin-bottom: 15px;
        }

        h2 {
            color: #2c3e50;
            margin-top: 0;
        }

        h3 {
            color: #2c3e50;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            font-size: 0.9em;
            color: #777;
        }

        .badge {
            display: inline-block;
            padding: 3px 7px;
            font-size: 12px;
            font-weight: bold;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            border-radius: 10px;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-danger {
            background-color: #dc3545;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }

        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 15px;
        }

        .text-left {
            text-align: left;
        }

        .text-primary {
            color: #007bff;
        }

        .rounded {
            border-radius: 0.25rem;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .mb-1 {
            margin-bottom: 0.25rem;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <h2>You have new test results from Skyros Drug Checks Inc</h2>

        <p>Hello {{ $type === 'company' ? $data['company_name'] : $data['employee_name'] }},</p>

        <p>Skyros Drug Checks Inc has added new test results to your portal.</p>


        @if (!empty($data['additional_text']))
            <h3>Additional Information:</h3>
            <p>{{ $data['additional_text'] }}</p>
        @endif

        <p>
            <a href="{{ route('result-recording.index') }}" style="color: #007bff; text-decoration: none;">
                Click here to view all results
            </a>
        </p>

        <div class="row">
            <div class="col-md-6">
                @if (isset($data['contact_info_widget']))
                    <div class="company-info">
                        <h3>{{ $data['contact_info_widget']['description'] ?? '' }}</h3>
                        <p class="mb-1">{{ $data['contact_info_widget']['address'] ?? '' }}</p>
                        <p class="mb-1">Phone: {{ $data['contact_info_widget']['phone'] ?? '' }}</p>
                        <p>Email: {{ $data['contact_info_widget']['email'] ?? '' }}</p>
                    </div>
                @endif


                <div class="client-info">
                    <h4>{{ $data['company_name'] ?? '' }}</h4>
                    <p class="mb-1">{{ $data['address'] ?? '' }}</p>
                    <p class="mb-1">
                        {{ $data['city'] ?? '' }},
                        {{ $data['state'] ?? '' }},
                        {{ $data['zip'] ?? '' }}
                    </p>
                    <p>{{ $data['phone'] ?? '' }}</p>
                </div>

            </div>

            <div class="col-md-6">
                {{-- @if (isset($data['header_image']))
                    <div class="text-right">
                        <img src="{{ $data['header_image'] }}" alt="Company Logo" class="header-image rounded">
                    </div>
                @endif --}}

                <div style="display: flex; margin-top: 20px;">
                    <div style="flex: 1;">
                        <div class="view_results_blue">
                            <strong>Overall Result</strong>
                        </div>
                        <div class="view_results_blue">
                            <strong>Date/Time Collected</strong>
                        </div>
                        <div class="view_results_blue">
                            <strong>Donor Reported</strong>
                        </div>
                        <div class="view_results_blue">
                            <strong>Test Detail</strong>
                        </div>
                        <div class="view_results_blue">
                            <strong>Specimen</strong>
                        </div>
                        <div class="view_results_blue">
                            <strong>Result ID</strong>
                        </div>
                        @if (isset($data['collection_location']))
                            <div class="view_results_blue">
                                <strong>Location</strong>
                            </div>
                        @endif
                    </div>

                    <div style="flex: 2;">
                        <div class="view_results">
                            <strong>
                                <span class="badge badge-{{ $data['overall_result'] === 'Negative' ? 'success' : 'danger' }}">
                                    {{ strtoupper($data['overall_result'] ?? 'N/A') }}
                                </span>
                            </strong>
                        </div>
                        <div class="view_results">
                            <strong>{{ $data['test_date'] ?? 'N/A' }} {{ $data['test_time'] ?? '' }}</strong>
                        </div>
                        <div class="view_results">
                            <strong>{{ $data['employee_name'] ?? 'N/A' }}
                                </strong>
                        </div>
                        <div class="view_results">
                            <strong>{{ $data['reason_for_test'] ?? 'N/A' }} -
                                <span class="text-primary">{{ $data['test_name'] ?? 'N/A' }}</span> :
                                {{ $data['test_method'] ?? 'N/A' }}, {{ $data['test_regulation'] ?? 'N/A' }}</strong>
                        </div>
                        <div class="view_results">
                            <strong>{{ $data['specimen'] ?? 'N/A' }}</strong>
                        </div>
                        <div class="view_results">
                            <strong>{{ $data['result_id_formatted'] ?? 'N/A' }}</strong>
                        </div>
                        @if (isset($data['collection_location']))
                            <div class="view_results">
                                <strong>{{ $data['collection_location'] }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <hr>


        @if(isset($data['test_panels']) && count($data['test_panels']) > 0)
        <h3>Test Panel Results</h3>
        <table class="result-table">
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
                @foreach ($data['test_panels'] as $panel)
                <tr>
                    <td>{{ $panel['drug_name'] ?? 'N/A' }}</td>
                    <td>{{ $panel['drug_code'] ?? 'N/A' }}</td>
                    <td>{{ ucfirst($panel['result'] ?? 'N/A') }}</td>
                    <td>{{ $panel['cut_off_level'] ?? 'N/A' }} ng/mL</td>
                    <td>{{ $panel['conf_level'] ?? 'N/A' }} ng/mL</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif











        <div class="footer">
            <p>Thank you,</p>
            <p><strong>Skyros Drug Checks Inc</strong></p>
            <p>
                <small>
                    (This is an automated notification. Please do not reply to this email.)
                </small>
            </p>
        </div>
    </div>
</body>

</html>
