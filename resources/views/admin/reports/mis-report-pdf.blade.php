<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>DOT MIS Report {{ $year }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            line-height: 1.3;
            color: #000;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            width: 8.5in;
            margin: 0 auto;
            background: white;
            padding: 15px 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 8pt;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .header-info {
            display: flex;
            justify-content: center;
            font-size: 8pt;
            margin-top: 5px;
        }

        .header-info div {
            display: inline-block;
        }

        .section {
            margin-bottom: 15px;
        }

        .section-padding {
            padding: 0 12px;
        }

        .section-title {
            font-weight: bold;
            font-size: 8pt;
            margin-bottom: 8px;
            padding-bottom: 2px;
        }

        .form-row {
            display: flex;
            margin-bottom: 8px;
            align-items: baseline;
        }

        .form-label {
            font-weight: normal;
            min-width: 180px;
            font-size: 8pt;
        }

        .form-value {
            flex: 1;
            border-bottom: 1px solid #000;
            padding: 0 5px 2px 5px;
            font-weight: bold;
            font-size: 8pt;
        }

        .form-value.no-border {
            border-bottom: none;
        }

        .two-column {
            display: flex;
            gap: 30px;
        }

        .two-column>div {
            flex: 1;
        }

        .agency-box {
            padding: 8px;
            margin: 10px 0;
        }

        .agency-title {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 8pt;
        }

        .checkbox-line {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .checkbox {
            width: 15px;
            height: 15px;
            border: 2px solid #000;
            display: inline-block;
            margin-right: 8px;
            position: relative;
            background: white;
        }

        .border-none,
        .border-none th,
        .border-none td {
            border: none;
            background: none;
        }

        .checkbox.checked {
            background: #0066cc;
        }

        .checkbox.checked::after {
            content: "✓";
            position: absolute;
            color: white;
            font-weight: bold;
            font-size: 8pt;
            top: -3px;
            left: 2px;
        }

        .inline-fields {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .inline-field {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .inline-field label {
            white-space: nowrap;
            font-size: 8pt;
        }

        .inline-field input {
            border-bottom: 1px solid #000;
            border-top: none;
            border-left: none;
            border-right: none;
            padding: 2px 5px;
            min-width: 100px;
            font-size: 8pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 8pt;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            padding: 5px 3px;
            text-align: center;
            font-size: 8pt;
        }

        td {
            padding: 5px 3px;
            text-align: center;
            font-size: 8pt;
        }

        .test-type-col {
            text-align: left;
            padding-left: 8px;
            font-weight: normal;
        }

        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .small-table {
            width: 50%;
            margin: 10px 0;
        }

        .info-box {
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 10px;
        }

        .verification-section {
            margin-top: 15px;
        }

        .verification-title {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 8pt;
        }

        .result-item {
            font-size: 8pt;
            margin-bottom: 5px;
            line-height: 1.4;
        }

        .footer {
            text-align: center;
            font-size: 8pt;
            color: #666;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
        }

        .right-align {
            text-align: right;
            font-size: 8pt;
        }

        hr {
            border: none;
            border-top: 1px solid #000;
            margin: 5px 0;
        }

        .print-actions {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .print-actions button {
            padding: 10px 20px;
            margin: 0 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .print-actions button:hover {
            background: #0056b3;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .container {
                box-shadow: none;
                width: 100%;
            }

            .print-actions {
                display: none;
            }
        }
    </style>
</head>

<body>
    <!-- Print Actions -->
    <div class="print-actions">
        <button onclick="window.print()">Print PDF</button>
        {{-- <button onclick="downloadPDF()">Download PDF</button> --}}
        {{-- <button onclick="window.close()">Close</button> --}}
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                U.S. DEPARTMENT OF TRANSPORTATION DRUG AND ALCOHOL TESTING
                MIS DATA COLLECTION FORM
            </h1>
            <div class="header-info">
                <div>
                    Calendar Year Covered by this Report:
                    <strong><u>&nbsp;&nbsp;{{ $year }}&nbsp;&nbsp;</u></strong>
                </div>
                <div>OMB No. 2105-0529</div>
            </div>
        </div>

        <!-- Section I: Employer -->
        <div class="section">
            <div class="section-title">I. Employer:</div>
            <div class="section-padding">
                <div class="right-align" style="margin-top: -20px; margin-bottom: 5px">
                    Form DOT F 1385 (Rev. 4/2019)
                </div>

                <div class="form-row">
                    <span class="form-label">Company Name:</span>
                    <span class="form-value">{{ $company->company_name ?? '' }}</span>
                </div>

                <div class="form-row">
                    <span class="form-label">Doing Business As (DBA) Name (if applicable):</span>
                    <span class="form-value">{{ $company->dba_name ?? '' }}</span>
                </div>

                <div class="two-column">
                    <div>
                        <div class="form-row">
                            <span class="form-label">Address:</span>
                            <span class="form-value">{{ $company->address ?? '' }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="form-row">
                            <span class="form-label">E-mail:</span>
                            <span class="form-value">{{ $company->email ?? '' }}</span>
                        </div>
                    </div>
                </div>

                <div class="two-column">
                    <div>
                        <div class="form-row">
                            <span class="form-label">Name of Certifying Official:</span>
                            <span class="form-value">{{ $company->certifying_official ?? '' }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="form-row">
                            <span class="form-label">Signature:</span>
                            <span class="form-value">{{ $company->signature ?? '' }}</span>
                        </div>
                    </div>
                </div>

                <div class="two-column">
                    <div>
                        <div class="form-row">
                            <span class="form-label">Telephone:</span>
                            <span class="form-value">{{ $company->phone ?? '' }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="form-row">
                            <span class="form-label">Date Certified:</span>
                            <span class="form-value">{{ $certifiedDate ?? now()->format('m/d/Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="two-column">
                    <div>
                        <div class="form-row">
                            <span class="form-label">Prepared by (if different):</span>
                            <span class="form-value">{{ $company->prepared_by ?? '' }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="form-row">
                            <span class="form-label">Telephone:</span>
                            <span class="form-value">{{ $company->prepared_phone ?? '' }}</span>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <span class="form-label">C/TPA Name and Telephone (if applicable):</span>
                    <span class="form-value">{{ $ctpaName }} {{ $ctpaPhone }}</span>
                </div>
            </div>

            <!-- Agency Selection Box -->
            <div class="agency-title">
                Check the DOT agency for which you are reporting MIS data;
                and complete the information on that same line as
                appropriate:
            </div>
            <div class="section-padding">
                <div class="checkbox-line">
                    <span class="checkbox checked"></span>
                    <span>FMCSA - Motor Carrier: DOT #:</span>
                    <span
                        style="
                            border-bottom: 1px solid #000;
                            padding: 0 50px;
                            margin: 0 10px;
                        ">{{ $company->dot_number ?? '' }}</span>
                    <span style="margin-left: 20px">Owner-operator: YES or NO</span>
                    <span style="margin-left: 20px">Exempt YES or NO</span>
                </div>

                <div class="checkbox-line">
                    <span class="checkbox"></span>
                    <span>FAA - Aviation: Certificate #:</span>
                    <span
                        style="
                            border-bottom: 1px solid #000;
                            padding: 0 50px;
                            margin: 0 10px;
                        ">{{ $company->faa_certificate ?? '' }}</span>
                    <span style="margin-left: 20px">Plan / Registration # (if applicable):</span>
                    <span
                        style="
                            border-bottom: 1px solid #000;
                            padding: 0 50px;
                            margin: 0 10px;
                        ">{{ $company->faa_plan_number ?? '' }}</span>
                </div>

                <div class="checkbox-line">
                    <span class="checkbox"></span>
                    <span>PHMSA - Pipeline: (Check)</span>
                    <span style="margin-left: 10px">
                        Gas Gathering[ ] Gas Transmission[ ] Gas Distribution[ ]
                        Transport Hazardous Liquids[ ] Transport Carbon Dioxide[ ]
                    </span>
                </div>

                <div class="checkbox-line">
                    <span class="checkbox"></span>
                    <span>FRA - Railroad:</span> Total Number of
                    observed/documented Part 219 "Rule G" Observations for
                    covered employees:
                    <span
                        style="
                            border-bottom: 1px solid #000;
                            padding: 0 30px;
                            margin: 0 5px;
                        ">{{ $company->fra_observations ?? '' }}</span>
                </div>

                <div class="checkbox-line">
                    <span class="checkbox"></span>
                    <span>USCG - Maritime:</span> Vessel ID # (USCG- or
                    State-Issued):
                    <span
                        style="
                            border-bottom: 1px solid #000;
                            padding: 0 100px;
                            margin: 0 5px;
                        ">{{ $company->uscg_vessel_id ?? '' }}</span>
                    (If more than one vessel, list separately.)
                </div>

                <div class="checkbox-line">
                    <span class="checkbox"></span>
                    <span>FTA - Transit</span>
                </div>
            </div>
        </div>

        <!-- Section II: Covered Employees -->
        <div class="section">
            <div class="section-title">II. Covered Employees:</div>
            <div class="section-padding">
                <div class="form-row">
                    <span class="form-label">(A) Enter Total Number Safety-Sensitive Employees
                        In All Employee Categories:</span>
                    <span class="form-value" style="max-width: 100px; text-align: center">{{ $totalEmployees }}</span>
                </div>

                <div class="form-row">
                    <span class="form-label">(B) Enter Total Number of Employee
                        Categories:</span>
                    <span class="form-value" style="max-width: 100px; text-align: center">1</span>
                </div>

                <div style="display: flex; gap: 20px; margin-top: 10px">
                    <div style="flex: 0 0 50%">
                        <table class="small-table" style="width: 100%">
                            <tr>
                                <th style="text-align: center">
                                    Employee Category
                                </th>
                                <th style="text-align: center">
                                    Total Number of Employees<br />in this
                                    Category
                                </th>
                            </tr>
                            <tr>
                                <td
                                    style="
                                        text-align: center;
                                        font-weight: bold;
                                    ">
                                    FMCSA
                                </td>
                                <td
                                    style="
                                        text-align: center;
                                        font-weight: bold;
                                    ">
                                    {{ $totalEmployees }}
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div
                        style="
                            flex: 1;
                            border: 1px solid #000;
                            padding: 8px;
                            font-size: 8pt;
                        ">
                        <strong>(C)</strong> If you have multiple employee
                        categories, complete Sections I and II (A) & (B).
                        Take that filled-in form and make one copy for each
                        employee category and complete Sections II (C), III,
                        and IV for each separate employee category.
                    </div>
                </div>
            </div>
        </div>

        <!-- Section III: Drug Testing Data -->
        <div class="section">
            <div class="section-title">III. Drug Testing Data:</div>

            <table>
                <thead>
                    <tr>
                        <th
                            style="
                                font-size: 8pt;
                                text-align: center;
                                border: none;
                                border-right: 2px solid #000;
                            ">
                            1
                        </th>
                        <th style="font-size: 8pt; text-align: center">
                            2
                        </th>
                        <th style="font-size: 8pt; text-align: center">
                            3
                        </th>
                        <th style="font-size: 8pt; text-align: center">
                            4
                        </th>
                        <th style="font-size: 8pt; text-align: center">
                            5
                        </th>
                        <th style="font-size: 8pt; text-align: center">
                            6
                        </th>
                        <th style="font-size: 8pt; text-align: center">
                            7
                        </th>
                        <th style="font-size: 8pt; text-align: center">
                            8
                        </th>
                        <th style="font-size: 8pt; text-align: center">
                            9
                        </th>
                        <th style="font-size: 8pt; text-align: center">
                            10
                        </th>
                        <th colspan="2" style="font-size: 8pt; text-align: center">
                            11
                        </th>
                        <th style="font-size: 8pt; text-align: center">
                            12
                        </th>
                        <th style="font-size: 8pt; text-align: center">
                            13
                        </th>
                    </tr>
                    <tr>
                        <th rowspan="2"
                            style="
                                width: 15%;
                                vertical-align: middle;
                                border-right: 2px solid #000;
                            ">
                        </th>
                        <th colspan="2" style="font-size: 7pt; padding: 3px"></th>
                        <th colspan="5" style="font-size: 7pt; padding: 3px"></th>
                        <th colspan="2" style="font-size: 7pt; padding: 3px"></th>
                        <th colspan="2"
                            style="
                                font-size: 7pt;
                                padding: 3px;
                                text-align: center;
                                font-weight: normal;
                            ">
                            Refusal Results
                        </th>
                        <th colspan="2" style="font-size: 7pt; padding: 3px"></th>
                    </tr>
                    <tr>
                        <th
                            style="
                                font-size: 6pt;
                                padding: 4px 2px;
                                writing-mode: vertical-rl;
                                text-orientation: mixed;
                                transform: rotate(180deg);
                                height: 120px;
                                font-weight: normal;
                            ">
                            Total Number Of Test Results<br />[Should equal
                            the sum of Columns 2, 3, 9, 10, 11, and 12]
                        </th>
                        <th
                            style="
                                font-size: 6pt;
                                padding: 4px 2px;
                                writing-mode: vertical-rl;
                                text-orientation: mixed;
                                transform: rotate(180deg);
                                height: 120px;
                                font-weight: normal;
                            ">
                            Verified Negative<br />Results
                        </th>
                        <th
                            style="
                                font-size: 6pt;
                                padding: 4px 2px;
                                writing-mode: vertical-rl;
                                text-orientation: mixed;
                                transform: rotate(180deg);
                                height: 120px;
                                font-weight: normal;
                            ">
                            Verified Positive Results ~ For One Or<br />More
                            Drugs
                        </th>
                        <th
                            style="
                                font-size: 6pt;
                                padding: 4px 2px;
                                writing-mode: vertical-rl;
                                text-orientation: mixed;
                                transform: rotate(180deg);
                                height: 120px;
                                font-weight: normal;
                            ">
                            Positive For<br />Marijuana
                        </th>
                        <th
                            style="
                                font-size: 6pt;
                                padding: 4px 2px;
                                writing-mode: vertical-rl;
                                text-orientation: mixed;
                                transform: rotate(180deg);
                                height: 120px;
                                font-weight: normal;
                            ">
                            Positive For<br />Cocaine
                        </th>
                        <th
                            style="
                                font-size: 6pt;
                                padding: 4px 2px;
                                writing-mode: vertical-rl;
                                text-orientation: mixed;
                                transform: rotate(180deg);
                                height: 120px;
                                font-weight: normal;
                            ">
                            Positive For<br />PCP
                        </th>
                        <th
                            style="
                                font-size: 6pt;
                                padding: 4px 2px;
                                writing-mode: vertical-rl;
                                text-orientation: mixed;
                                transform: rotate(180deg);
                                height: 120px;
                                font-weight: normal;
                            ">
                            Positive For<br />Opiates
                        </th>
                        <th
                            style="
                                font-size: 6pt;
                                padding: 4px 2px;
                                writing-mode: vertical-rl;
                                text-orientation: mixed;
                                transform: rotate(180deg);
                                height: 120px;
                                font-weight: normal;
                            ">
                            Positive For<br />Amphetamines
                        </th>
                        <th
                            style="
                                font-size: 6pt;
                                padding: 4px 2px;
                                writing-mode: vertical-rl;
                                text-orientation: mixed;
                                transform: rotate(180deg);
                                height: 120px;
                                font-weight: normal;
                            ">
                            Adulterated
                        </th>
                        <th
                            style="
                                font-size: 6pt;
                                padding: 4px 2px;
                                writing-mode: vertical-rl;
                                text-orientation: mixed;
                                transform: rotate(180deg);
                                height: 120px;
                                font-weight: normal;
                            ">
                            Substituted
                        </th>
                        <th
                            style="
                                font-size: 6pt;
                                padding: 4px 2px;
                                writing-mode: vertical-rl;
                                text-orientation: mixed;
                                transform: rotate(180deg);
                                height: 120px;
                                font-weight: normal;
                            ">
                            "Shy Bladder" ~<br />With No Medical<br />Explanation
                        </th>
                        <th
                            style="
                                font-size: 6pt;
                                padding: 4px 2px;
                                writing-mode: vertical-rl;
                                text-orientation: mixed;
                                transform: rotate(180deg);
                                height: 120px;
                                font-weight: normal;
                            ">
                            Other Refusals To<br />Submit To<br />Testing
                        </th>
                        <th
                            style="
                                font-size: 6pt;
                                padding: 4px 2px;
                                writing-mode: vertical-rl;
                                text-orientation: mixed;
                                transform: rotate(180deg);
                                height: 120px;
                                font-weight: normal;
                            ">
                            Cancelled Results
                        </th>
                    </tr>
                    <tr>
                        <th
                            style="
                                text-align: left;
                                padding-left: 8px;
                                font-size: 8pt;
                                border-right: 2px solid #000;
                            ">
                            Type of Test
                        </th>
                        <th style="font-size: 7pt">1</th>
                        <th style="font-size: 7pt">2</th>
                        <th style="font-size: 7pt">3</th>
                        <th style="font-size: 7pt">4</th>
                        <th style="font-size: 7pt">5</th>
                        <th style="font-size: 7pt">6</th>
                        <th style="font-size: 7pt">7</th>
                        <th style="font-size: 7pt">8</th>
                        <th style="font-size: 7pt">9</th>
                        <th style="font-size: 7pt">10</th>
                        <th style="font-size: 7pt">11</th>
                        <th style="font-size: 7pt">12</th>
                        <th style="font-size: 7pt">13</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (['Pre-employment', 'Random', 'Post-accident', 'Reasonable Suspicion/Cause', 'Return to Duty', 'Follow-up'] as $testType)
                        <tr>
                            <td
                                style="
                                    text-align: left;
                                    padding-left: 8px;
                                    font-weight: normal;
                                    border-right: 2px solid #000;
                                ">
                                {{ $testType }}
                            </td>
                            <td>{{ $drugTestingData[$testType]['total'] }}</td>
                            <td>{{ $drugTestingData[$testType]['negative'] }}</td>
                            <td>{{ $drugTestingData[$testType]['positive'] }}</td>
                            <td>{{ $drugTestingData[$testType]['marijuana'] }}</td>
                            <td>{{ $drugTestingData[$testType]['cocaine'] }}</td>
                            <td>{{ $drugTestingData[$testType]['pcp'] }}</td>
                            <td>{{ $drugTestingData[$testType]['opiates'] }}</td>
                            <td>{{ $drugTestingData[$testType]['amphetamines'] }}</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>{{ $drugTestingData[$testType]['refusal'] }}</td>
                            <td>0</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td
                            style="
                                text-align: left;
                                padding-left: 8px;
                                font-weight: bold;
                                border-right: 2px solid #000;
                            ">
                            Total
                        </td>
                        <td>{{ $totals['total'] }}</td>
                        <td>{{ $totals['negative'] }}</td>
                        <td>{{ $totals['positive'] }}</td>
                        <td>{{ $totals['marijuana'] }}</td>
                        <td>{{ $totals['cocaine'] }}</td>
                        <td>{{ $totals['pcp'] }}</td>
                        <td>{{ $totals['opiates'] }}</td>
                        <td>{{ $totals['amphetamines'] }}</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>{{ $totals['refusal'] }}</td>
                        <td>0</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Section IV: Alcohol Testing Data -->
        <div class="section">
            <div class="section-title">IV. Alcohol Testing Data:</div>

            <table>
                <thead>
                    <tr>
                        <th
                            style="
                                font-size: 8pt;
                                text-align: center;
                                border-right: 2px solid #000;
                            ">
                        </th>
                        <th style="font-size: 8pt; text-align: center">
                            1
                        </th>
                        <th style="font-size: 8pt; text-align: center">
                            2
                        </th>
                        <th style="font-size: 8pt; text-align: center">
                            3
                        </th>
                        <th style="font-size: 8pt; text-align: center">
                            4
                        </th>
                        <th style="font-size: 8pt; text-align: center">
                            5
                        </th>
                        <th style="font-size: 8pt; text-align: center">
                            6
                        </th>
                        <th style="font-size: 8pt; text-align: center">
                            7
                        </th>
                        <th style="font-size: 8pt; text-align: center">
                            8
                        </th>
                        <th style="font-size: 8pt; text-align: center">
                            9
                        </th>
                    </tr>
                    <tr>
                        <th rowspan="2"
                            style="
                                width: 18%;
                                vertical-align: middle;
                                border-right: 2px solid #000;
                            ">
                        </th>
                        <th colspan="4" style="font-size: 7pt; padding: 3px"></th>
                        <th style="font-size: 7pt; padding: 3px"></th>
                        <th style="font-size: 7pt; padding: 3px"></th>
                        <th colspan="2"
                            style="
                                font-size: 7pt;
                                text-align: center;
                                font-weight: normal;
                            ">
                            Refusal Results
                        </th>
                        <th style="font-size: 7pt; padding: 3px"></th>
                    </tr>
                    <tr>
                        <th
                            style="
                                font-size: 6pt;
                                padding: 4px 2px;
                                writing-mode: vertical-rl;
                                text-orientation: mixed;
                                transform: rotate(180deg);
                                height: 120px;
                                white-space: nowrap;
                                font-weight: normal;
                            ">
                            Total Number Of<br />Screening Test<br />Results
                            [Should equal<br />the sum of Columns<br />2, 3,
                            7, and 8]
                        </th>
                        <th
                            style="
                                font-size: 6pt;
                                padding: 4px 2px;
                                writing-mode: vertical-rl;
                                text-orientation: mixed;
                                transform: rotate(180deg);
                                height: 120px;
                                font-weight: normal;
                            ">
                            Screening Tests With<br />Results Below 0.02
                        </th>
                        <th
                            style="
                                font-size: 6pt;
                                padding: 4px 2px;
                                writing-mode: vertical-rl;
                                text-orientation: mixed;
                                transform: rotate(180deg);
                                height: 120px;
                                font-weight: normal;
                            ">
                            Screening Tests With<br />Results 0.02 Or<br />Greater
                        </th>
                        <th
                            style="
                                font-size: 6pt;
                                padding: 4px 2px;
                                writing-mode: vertical-rl;
                                text-orientation: mixed;
                                transform: rotate(180deg);
                                height: 120px;
                                font-weight: normal;
                            ">
                            Number Of<br />Confirmation Tests<br />Results
                        </th>
                        <th
                            style="
                                font-size: 6pt;
                                padding: 4px 2px;
                                writing-mode: vertical-rl;
                                text-orientation: mixed;
                                transform: rotate(180deg);
                                height: 120px;
                                font-weight: normal;
                            ">
                            Confirmation Tests<br />With Results 0.02<br />Through
                            0.039
                        </th>
                        <th
                            style="
                                font-size: 6pt;
                                padding: 4px 2px;
                                writing-mode: vertical-rl;
                                text-orientation: mixed;
                                transform: rotate(180deg);
                                height: 120px;
                                font-weight: normal;
                            ">
                            Confirmation Tests<br />With Results 0.04 Or<br />Greater
                        </th>
                        <th
                            style="
                                font-size: 6pt;
                                padding: 4px 2px;
                                writing-mode: vertical-rl;
                                text-orientation: mixed;
                                transform: rotate(180deg);
                                height: 120px;
                                font-weight: normal;
                            ">
                            "Shy Lung" ~<br />With No Medical<br />Explanation
                        </th>
                        <th
                            style="
                                font-size: 6pt;
                                padding: 4px 2px;
                                writing-mode: vertical-rl;
                                text-orientation: mixed;
                                transform: rotate(180deg);
                                height: 120px;
                                font-weight: normal;
                            ">
                            Other Refusals To<br />Submit To<br />Testing
                        </th>
                        <th
                            style="
                                font-size: 6pt;
                                padding: 4px 2px;
                                writing-mode: vertical-rl;
                                text-orientation: mixed;
                                transform: rotate(180deg);
                                height: 120px;
                                font-weight: normal;
                            ">
                            Cancelled Results
                        </th>
                    </tr>
                    <tr>
                        <th
                            style="
                                text-align: left;
                                padding-left: 8px;
                                font-size: 8pt;
                                border-right: 2px solid #000;
                            ">
                            Type of Test
                        </th>
                        <th style="font-size: 7pt">1</th>
                        <th style="font-size: 7pt">2</th>
                        <th style="font-size: 7pt">3</th>
                        <th style="font-size: 7pt">4</th>
                        <th style="font-size: 7pt">5</th>
                        <th style="font-size: 7pt">6</th>
                        <th style="font-size: 7pt">7</th>
                        <th style="font-size: 7pt">8</th>
                        <th style="font-size: 7pt">9</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (['Pre-employment', 'Random', 'Post-accident', 'Reasonable Suspicion/Cause', 'Return to Duty', 'Follow-up'] as $testType)
                        <tr>
                            <td
                                style="
                                    text-align: left;
                                    padding-left: 8px;
                                    font-weight: normal;
                                    border-right: 2px solid #000;
                                ">
                                {{ $testType }}
                            </td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td
                            style="
                                text-align: left;
                                padding-left: 8px;
                                font-weight: bold;
                                border-right: 2px solid #000;
                            ">
                            Total
                        </td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Verification Section -->
        <div class="verification-section">
            <div class="verification-title">
                For verification purposes; these are the test results
                included in the preceding form:
            </div>
            <div>Drug Testing Result List:</div>
            <table class="border-none">
                <tr>
                    <th>Collection Date</th>
                    <th>Result ID</th>
                    <th>Donor Name</th>
                    <th>Donor DOT</th>
                    <th>Test Detail</th>
                    <th>Test DOT</th>
                </tr>
                <tbody>
                    @if ($testList->count() > 0)
                        @foreach ($testList as $test)
                            <tr>
                                <td>{{ $test['collection_date'] }}</td>
                                <td>{{ $test['result_id'] }}</td>
                                <td>{{ strtoupper($test['donor_name']) }} ({{ $test['donor_id'] }})</td>
                                <td>DOT (FMCSA)</td>
                                <td>{{ $test['test_type'] }} - DOT 2018 (URINE) : LAB, DOT</td>
                                <td>DOT</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" style="text-align: center;">No drug test results for this period</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <div style="margin-top: 15px">
                <strong>Alcohol Testing Result List:</strong><br />
                <div class="result-item">0 results included</div>
            </div>
        </div>

        <div class="footer">
            Generated by {{ $ctpaName }} on {{ now()->format('m/d/Y H:i:s') }}
        </div>
    </div>

    <script>
        // function downloadPDF() {
        //     // Create a hidden iframe to trigger download
        //     const iframe = document.createElement('iframe');
        //     iframe.style.display = 'none';
        //     iframe.src = '{{ route('report.mis-reports.download', request()->all()) }}';
        //     document.body.appendChild(iframe);
        // }

        // Add keyboard shortcut for printing (Ctrl+P or Cmd+P)
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>

</html>
