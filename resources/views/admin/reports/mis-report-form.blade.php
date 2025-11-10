@extends('layouts.admin.master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Generate MIS Report (DOT Form F 1385)</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('report.mis-reports') }}" method="GET" id="misReportForm">
                            <div class="row">
                                <!-- Company Selection -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_id">Select Company <span class="text-danger">*</span></label>
                                        <select name="company_id" id="company_id" class="form-control" required>
                                            <option value="">-- Select Company --</option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}"
                                                    {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                                    {{ $company->company_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Year Selection -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="year">Calendar Year <span class="text-danger">*</span></label>
                                        <select name="year" id="year" class="form-control" required>
                                            @for ($y = date('Y'); $y >= 2020; $y--)
                                                <option value="{{ $y }}"
                                                    {{ request('year', date('Y')) == $y ? 'selected' : '' }}>
                                                    {{ $y }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Start Date -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control"
                                            value="{{ request('start_date', date('Y') . '-01-01') }}">
                                        <small class="form-text text-muted">Leave blank to use full calendar year</small>
                                    </div>
                                </div>

                                <!-- End Date -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_date">End Date</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control"
                                            value="{{ request('end_date', date('Y') . '-12-31') }}">
                                        <small class="form-text text-muted">Leave blank to use full calendar year</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-file-pdf"></i> Generate MIS Report (PDF)
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                        <i class="fas fa-redo"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Information Card -->
                <div class="card mt-3">
                    <div class="card-header bg-info text-white">
                        <h4 class="card-title mb-0">About MIS Reports</h4>
                    </div>
                    <div class="card-body">
                        <h5>What is an MIS Report?</h5>
                        <p>
                            The Management Information System (MIS) Report (DOT Form F 1385) is required by the U.S.
                            Department
                            of Transportation for employers with DOT-regulated employees. This report summarizes all drug
                            and
                            alcohol testing data for a calendar year.
                        </p>

                        <h5 class="mt-3">Report Includes:</h5>
                        <ul>
                            <li>Company and employer information</li>
                            <li>Number of safety-sensitive employees</li>
                            <li>Drug testing statistics by test type (Pre-employment, Random, Post-accident, etc.)</li>
                            <li>Alcohol testing statistics</li>
                            <li>Detailed list of all tests conducted</li>
                            <li>Positive results breakdown by substance</li>
                            <li>Refusal and cancelled test information</li>
                        </ul>

                        <h5 class="mt-3">Test Types Included:</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul>
                                    <li><strong>Pre-employment:</strong> Tests before hiring</li>
                                    <li><strong>Random:</strong> Unannounced periodic testing</li>
                                    <li><strong>Post-accident:</strong> Tests after incidents</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul>
                                    <li><strong>Reasonable Suspicion/Cause:</strong> Based on observed behavior</li>
                                    <li><strong>Return to Duty:</strong> Before returning after a positive test</li>
                                    <li><strong>Follow-up:</strong> After return to duty</li>
                                </ul>
                            </div>
                        </div>

                        <div class="alert alert-warning mt-3">
                            <strong><i class="fas fa-exclamation-triangle"></i> Important:</strong>
                            Make sure all test results for the selected period have been entered into the system before
                            generating the report. The report will only include tests with a collection date within the
                            specified date range.
                        </div>
                    </div>
                </div>

                <!-- Recent Reports (Optional) -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Quick Actions</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fas fa-vial"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Drug Tests</span>
                                        <span class="info-box-number">{{ $totalDrugTests ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success"><i class="fas fa-building"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Active Companies</span>
                                        <span class="info-box-number">{{ $companies->count() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-calendar"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Current Year</span>
                                        <span class="info-box-number">{{ date('Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-update date range when year changes
        document.getElementById('year').addEventListener('change', function() {
            const year = this.value;
            document.getElementById('start_date').value = year + '-01-01';
            document.getElementById('end_date').value = year + '-12-31';
        });

        function resetForm() {
            document.getElementById('misReportForm').reset();
            const currentYear = new Date().getFullYear();
            document.getElementById('start_date').value = currentYear + '-01-01';
            document.getElementById('end_date').value = currentYear + '-12-31';
        }

        // Form validation
        document.getElementById('misReportForm').addEventListener('submit', function(e) {
            const companyId = document.getElementById('company_id').value;
            if (!companyId) {
                e.preventDefault();
                alert('Please select a company');
                return false;
            }
        });
    </script>
@endsection
