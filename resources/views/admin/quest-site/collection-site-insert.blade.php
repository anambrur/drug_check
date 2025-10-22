@extends('layouts.admin.master')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title mb-0">Collection Site Import</h2>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                @if (session('stats'))
                                    <hr>
                                    <div class="mt-2">
                                        <strong>Import Statistics:</strong><br>
                                        Total Rows: {{ session('stats')['total'] }}<br>
                                        Processed: {{ session('stats')['processed'] }}<br>
                                        Skipped: {{ session('stats')['skipped'] }}
                                    </div>
                                @endif
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('quest-site.process-collection-sites') }}" method="POST"
                            enctype="multipart/form-data" id="uploadForm">
                            @csrf

                            <div class="mb-3">
                                <label for="excel_file" class="form-label">Select Excel File</label>
                                <input type="file" class="form-control" id="excel_file" name="excel_file"
                                    accept=".xlsx,.xls,.csv" required>
                                <div class="form-text">
                                    Supported formats: .xlsx, .xls, .csv (Max: 10MB). File must contain "CollSite_Export"
                                    sheet.
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="spinner-border spinner-border-sm d-none" role="status"
                                    aria-hidden="true"></span>
                                Upload and Process
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('uploadForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            const spinner = submitBtn.querySelector('.spinner-border');

            submitBtn.disabled = true;
            spinner.classList.remove('d-none');
            submitBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
        });
    </script>
@endpush
