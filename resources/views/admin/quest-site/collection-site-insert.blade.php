@extends('layouts.admin.master')

@section('content')
    <div class="row">
        <div class="col-12 box-margin">
            <div class="card card-body">
                <h4 class="card-title mb-3">Collection Site Import</h4>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div id="importStatusBanner" class="alert alert-info {{ $importInProgress ? '' : 'd-none' }}" role="alert">
                    <div class="d-flex align-items-center">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        <span id="importStatusText">Import in progress...</span>
                    </div>
                </div>

                <div id="importFailedBanner"
                    class="alert alert-danger {{ !$importInProgress && $importError ? '' : 'd-none' }}" role="alert">
                    <strong>Last import failed:</strong> <span id="importErrorText">{{ $importError }}</span>
                </div>

                @if ($importStats && !$importInProgress)
                    <div id="importStatsBanner" class="alert alert-success" role="alert">
                        <strong>Last Import Summary</strong>
                        @if (!empty($importStats['file']))
                            <span class="text-muted">({{ $importStats['file'] }})</span>
                        @endif
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-md-3"><strong>Total Rows:</strong> {{ $importStats['total'] ?? 0 }}</div>
                            <div class="col-md-3"><strong>Processed:</strong> {{ $importStats['processed'] ?? 0 }}</div>
                            <div class="col-md-3"><strong>Skipped:</strong> {{ $importStats['skipped'] ?? 0 }}</div>
                            <div class="col-md-3">
                                <strong>Finished:</strong>
                                {{ $importStats['finished_at'] ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('quest-site.process-collection-sites') }}" method="POST"
                    enctype="multipart/form-data" id="uploadForm">
                    @csrf

                    <div class="mb-3">
                        <label for="excel_file" class="form-label">Select Excel File</label>
                        <input type="file" class="form-control" id="excel_file" name="excel_file"
                            accept=".xlsx,.xls,.csv" required {{ $importInProgress ? 'disabled' : '' }}>
                        <div class="form-text">
                            Supported formats: .xlsx, .xls, .csv (Max: 20MB). File must contain a
                            <strong>CollSite_Export</strong> sheet. Each import replaces all existing collection sites.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" id="submitBtn"
                        {{ $importInProgress ? 'disabled' : '' }}>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Upload and Process
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 box-margin">
            <div class="card card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Collection Sites ({{ number_format($totalSites) }})</h4>
                </div>

                <form method="GET" action="{{ route('quest-site.collectionSiteInsert') }}" class="mb-4">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ request('search') }}"
                                placeholder="Site code, name, city, state, or ZIP">
                        </div>
                        <div class="col-md-3">
                            <label for="state" class="form-label">State</label>
                            <select class="form-control" id="state" name="state">
                                <option value="">All States</option>
                                @foreach ($states as $stateOption)
                                    <option value="{{ $stateOption }}"
                                        {{ request('state') === $stateOption ? 'selected' : '' }}>
                                        {{ $stateOption }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('quest-site.collectionSiteInsert') }}"
                                class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </div>
                </form>

                @if ($sites->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped dt-responsive w-100">
                            <thead>
                                <tr>
                                    <th>Site Code</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>City</th>
                                    <th>State</th>
                                    <th>ZIP</th>
                                    <th>Phone</th>
                                    <th>Last Updated</th>
                                    <th>Imported At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sites as $site)
                                    <tr>
                                        <td><code>{{ $site->collection_site_code }}</code></td>
                                        <td>{{ $site->name }}</td>
                                        <td>
                                            {{ $site->address_1 }}
                                            @if ($site->address_2)
                                                <br><small class="text-muted">{{ $site->address_2 }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $site->city }}</td>
                                        <td>{{ $site->state }}</td>
                                        <td>{{ $site->zip_code }}</td>
                                        <td>{{ $site->phone_number }}</td>
                                        <td>{{ $site->last_updated?->format('Y-m-d') ?? 'N/A' }}</td>
                                        <td>{{ $site->updated_at?->format('Y-m-d H:i') ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $sites->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fa fa-map-marker fa-3x text-muted mb-3"></i>
                        <h5>No collection sites found</h5>
                        <p class="text-muted mb-0">
                            @if (request()->filled('search') || request()->filled('state'))
                                No sites match your filters. Try adjusting your search criteria.
                            @else
                                No collection sites yet. Upload a Quest Excel file above to import sites.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            const uploadForm = document.getElementById('uploadForm');
            const submitBtn = document.getElementById('submitBtn');
            const statusBanner = document.getElementById('importStatusBanner');
            const statusText = document.getElementById('importStatusText');
            const failedBanner = document.getElementById('importFailedBanner');
            const errorText = document.getElementById('importErrorText');
            const importStatusUrl = @json(route('quest-site.import-status'));

            let importInProgress = @json($importInProgress);

            if (uploadForm) {
                uploadForm.addEventListener('submit', function() {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading...';
                });
            }

            function stageLabel(stage) {
                const labels = {
                    reading: 'Reading Excel file...',
                    inserting: 'Inserting collection sites...',
                    done: 'Import completed.',
                    failed: 'Import failed.',
                    not_started: 'Waiting to start...'
                };

                return labels[stage] || 'Processing import...';
            }

            function pollImportStatus() {
                fetch(importStatusUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.in_progress) {
                            statusBanner.classList.remove('d-none');
                            statusText.textContent = stageLabel(data.stage);
                            failedBanner.classList.add('d-none');
                        } else if (importInProgress && data.stage === 'done') {
                            window.location.reload();
                            return;
                        } else if (importInProgress && data.stage === 'failed') {
                            statusBanner.classList.add('d-none');
                            failedBanner.classList.remove('d-none');
                            errorText.textContent = data.error || 'Unknown error occurred.';
                            importInProgress = false;
                        } else {
                            statusBanner.classList.add('d-none');
                        }

                        if (data.in_progress || importInProgress) {
                            setTimeout(pollImportStatus, 4000);
                        }
                    })
                    .catch(() => {
                        if (importInProgress) {
                            setTimeout(pollImportStatus, 4000);
                        }
                    });
            }

            if (importInProgress) {
                pollImportStatus();
            }
        })();
    </script>
@endpush
