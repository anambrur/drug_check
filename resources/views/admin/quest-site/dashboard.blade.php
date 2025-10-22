@extends('layouts.admin.master')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Quest Diagnostics Collection Sites Sync</div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if (session('details'))
                            <div class="alert alert-info">
                                <h5>Details:</h5>
                                <ul>
                                    @foreach (session('details') as $detail)
                                        <li>{{ $detail }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title">Sites in Database</h5>
                                        <h2 class="card-text">{{ $sitesCount }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title">Last Sync</h5>
                                        <p class="card-text">{{ $lastSync }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">Sync Status</div>
                            <div class="card-body">
                                <div id="sync-status">
                                    @if (cache()->get('sync_in_progress'))
                                        <div class="alert alert-warning">
                                            <i class="fas fa-sync fa-spin"></i>
                                            Sync in progress...
                                            <span id="elapsed-time">Calculating...</span>
                                            <br>
                                            <small>Stage: <span
                                                    id="sync-stage">{{ cache()->get('sync_stage', 'processing') }}</span></small>
                                        </div>
                                    @elseif($lastSyncResult = cache()->get('last_sync_result'))
                                        @if ($lastSyncResult['success'])
                                            <div class="alert alert-success">
                                                <i class="fas fa-check-circle"></i>
                                                {{ $lastSyncResult['message'] }}
                                                <br>
                                                <small>Completed: {{ $lastSyncResult['completed_at'] }}</small>
                                                @if (isset($lastSyncResult['stats']))
                                                    <br>
                                                    <small>
                                                        Retrieved: {{ $lastSyncResult['stats']['total_retrieved'] }} |
                                                        Filtered: {{ $lastSyncResult['stats']['total_filtered'] }} |
                                                        Success: {{ $lastSyncResult['stats']['sync_success'] }} |
                                                        Errors: {{ $lastSyncResult['stats']['sync_errors'] }} |
                                                        Time: {{ $lastSyncResult['stats']['total_time'] }}s
                                                    </small>
                                                @endif
                                            </div>
                                        @else
                                            <div class="alert alert-danger">
                                                <i class="fas fa-exclamation-circle"></i>
                                                {{ $lastSyncResult['message'] }}
                                                <br>
                                                <small>Failed at: {{ $lastSyncResult['completed_at'] }}</small>
                                            </div>
                                        @endif
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> No sync activity detected
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">Full Sync</div>
                                    <div class="card-body">
                                        <p>Download all collection sites from Quest API and replace all data in Firebase.
                                        </p>
                                        <form action="{{ route('quest-site.full') }}" method="POST">
                                            @csrf
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" name="confirm"
                                                    id="confirmFull" required>
                                                <label class="form-check-label" for="confirmFull">
                                                    I understand this will replace all existing data
                                                </label>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Run Full Sync</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header bg-info text-white">Incremental Sync</div>
                                    <div class="card-body">
                                        <p>Download only sites that have changed since a specific date.</p>
                                        <form action="{{ route('quest-site.incremental') }}" method="POST">
                                            @csrf
                                            <div class="form-group mb-3">
                                                <label for="sinceDate">Since Date:</label>
                                                <input type="date" class="form-control" id="sinceDate" name="since_date"
                                                    value="{{ date('Y-m-d', strtotime('-7 days')) }}" required>
                                            </div>
                                            <button type="submit" class="btn btn-info">Run Incremental Sync</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header bg-success text-white">View Data</div>
                                    <div class="card-body">
                                        <p>View all collection sites currently stored in Firebase.</p>
                                        <a href="{{ route('quest-site.view') }}" class="btn btn-success">View Sites</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header bg-danger text-white">Clear Data</div>
                                    <div class="card-body">
                                        <p>Remove all collection site data from Firebase.</p>
                                        <form action="{{ route('quest-site.clear') }}" method="POST">
                                            @csrf
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" name="confirm"
                                                    id="confirmClear" required>
                                                <label class="form-check-label" for="confirmClear">
                                                    I understand this will delete all data
                                                </label>
                                            </div>
                                            <button type="submit" class="btn btn-danger">Clear All Data</button>
                                        </form>
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
        function updateSyncStatus() {
            fetch('{{ route('quest-sync.status') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.in_progress) {
                        $('#elapsed-time').text(data.elapsed_time + ' seconds');
                        $('#sync-stage').text(data.stage);
                    }
                });
        }

        // Update every 5 seconds if sync is in progress
        @if (cache()->get('sync_in_progress'))
            setInterval(updateSyncStatus, 5000);
        @endif
    </script>

@endsection
