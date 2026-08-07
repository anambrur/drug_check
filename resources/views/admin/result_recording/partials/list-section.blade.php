<style>
    .orders-stat-card .card-body { min-height: 92px; }
    .orders-filter-card .form-label { font-size: 12px; color: #6c757d; margin-bottom: 4px; }
    .orders-filter-row {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 0.5rem;
    }
    .orders-filter-field { min-width: 0; }
    .orders-filter-search { flex: 1 1 180px; }
    .orders-filter-select { flex: 0 1 150px; min-width: 130px; }
    .orders-filter-date { flex: 0 1 150px; min-width: 135px; }
    .orders-filter-btn { flex: 0 0 100px; }
    @media (min-width: 1200px) {
        .orders-filter-row { flex-wrap: nowrap; }
        .orders-filter-search { flex: 1 1 auto; }
        .orders-filter-select { flex: 0 1 145px; }
        .orders-filter-date { flex: 0 1 145px; }
        .orders-filter-btn { flex: 0 0 96px; }
    }
    @media (max-width: 1199.98px) {
        .orders-filter-search,
        .orders-filter-select,
        .orders-filter-date { flex: 1 1 calc(50% - 0.5rem); }
        .orders-filter-btn { flex: 1 1 calc(50% - 0.5rem); }
    }
    @media (max-width: 575.98px) {
        .orders-filter-search,
        .orders-filter-select,
        .orders-filter-date,
        .orders-filter-btn { flex: 1 1 100%; }
    }
    #result-recording-datatable_wrapper .dataTables_filter { display: none; }
    #result-recording-datatable td { vertical-align: middle; }
    #result-recording-datatable thead th {
        white-space: nowrap;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .02em;
        background: #f8fafc;
        border-bottom-width: 1px;
    }
    .orders-table-card .card-title { font-size: 1rem; }
    .rr-actions a { text-decoration: none; }
</style>

{{-- Stats --}}
<div class="row">
    <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
        <div class="card orders-stat-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3"><i class="fas fa-list fa-2x text-primary"></i></div>
                <div>
                    <p class="mb-0 text-muted font-12">Total Results</p>
                    <h4 class="mb-0 font-weight-bold">{{ number_format($stats['total']) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
        <div class="card orders-stat-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3"><i class="fas fa-check-circle fa-2x text-success"></i></div>
                <div>
                    <p class="mb-0 text-muted font-12">Negative</p>
                    <h4 class="mb-0 font-weight-bold">{{ number_format($stats['negative']) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
        <div class="card orders-stat-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3"><i class="fas fa-exclamation-circle fa-2x text-danger"></i></div>
                <div>
                    <p class="mb-0 text-muted font-12">Positive</p>
                    <h4 class="mb-0 font-weight-bold">{{ number_format($stats['positive']) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
        <div class="card orders-stat-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3"><i class="fas fa-clock fa-2x text-info"></i></div>
                <div>
                    <p class="mb-0 text-muted font-12">Pending</p>
                    <h4 class="mb-0 font-weight-bold">{{ number_format($stats['pending']) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
        <div class="card orders-stat-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3"><i class="fas fa-save fa-2x text-primary"></i></div>
                <div>
                    <p class="mb-0 text-muted font-12">Saved</p>
                    <h4 class="mb-0 font-weight-bold">{{ number_format($stats['saved']) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
        <div class="card orders-stat-card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3"><i class="fas fa-ellipsis-h fa-2x text-secondary"></i></div>
                <div>
                    <p class="mb-0 text-muted font-12">Other</p>
                    <h4 class="mb-0 font-weight-bold">{{ number_format($stats['other']) }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="row">
    <div class="col-12 box-margin">
        <div class="card orders-filter-card border-0 shadow-sm">
            <div class="card-body">
                <div class="orders-filter-row">
                    <div class="orders-filter-field orders-filter-search">
                        <label for="rr-search" class="form-label">Search</label>
                        <input type="text" id="rr-search" class="form-control"
                               placeholder="Company, employee, test, reason…">
                    </div>
                    @if (empty($hideCompanyFilter))
                        <div class="orders-filter-field orders-filter-select">
                            <label for="filter-company" class="form-label">Company</label>
                            <select id="filter-company" class="form-control">
                                <option value="">All</option>
                                @foreach ($clientProfiles as $clientProfile)
                                    <option value="{{ $clientProfile->id }}">{{ $clientProfile->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="orders-filter-field orders-filter-select">
                        <label for="filter-status" class="form-label">Status</label>
                        <select id="filter-status" class="form-control">
                            <option value="">All</option>
                            <option value="positive">Positive</option>
                            <option value="negative">Negative</option>
                            <option value="pending">Pending</option>
                            <option value="saved">Saved</option>
                            <option value="refused">Refused</option>
                            <option value="excused">Excused</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="collection only">Collection Only</option>
                        </select>
                    </div>
                    <div class="orders-filter-field orders-filter-select">
                        <label for="filter-reason" class="form-label">Reason</label>
                        <select id="filter-reason" class="form-control">
                            <option value="">All</option>
                            <option value="Follow Up Test">Follow Up Test</option>
                            <option value="Pre Employment">Pre Employment</option>
                            <option value="Random">Random</option>
                            <option value="Return to Duty">Return to Duty</option>
                            <option value="Post Accident">Post Accident</option>
                            <option value="Promotion">Promotion</option>
                            <option value="Reasonable Cause/Suspicion">Reasonable Cause/Suspicion</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="orders-filter-field orders-filter-date">
                        <label for="filter-from" class="form-label">From</label>
                        <input type="date" id="filter-from" class="form-control">
                    </div>
                    <div class="orders-filter-field orders-filter-date">
                        <label for="filter-to" class="form-label">To</label>
                        <input type="date" id="filter-to" class="form-control">
                    </div>
                    <div class="orders-filter-field orders-filter-btn">
                        <label class="form-label d-none d-xl-block">&nbsp;</label>
                        <button type="button" id="rr-filter-btn" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="orders-filter-field orders-filter-btn">
                        <label class="form-label d-none d-xl-block">&nbsp;</label>
                        <button type="button" id="rr-clear-btn" class="btn btn-outline-secondary w-100">Reset</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="row">
    <div class="col-12 box-margin">
        <div class="card orders-table-card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                    <h6 class="card-title mb-0">Result Recording List</h6>
                </div>
                <div class="table-responsive">
                    <table id="result-recording-datatable" class="table table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Collected</th>
                                <th>Company</th>
                                <th>Employee</th>
                                <th>Reason</th>
                                <th>Test</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Shared delete modal --}}
<div class="modal fade" id="rrDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('content.delete') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('content.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                {{ __('content.you_wont_be_able_to_revert_this') }}
            </div>
            <div class="modal-footer">
                @if ($demo_mode == 'on')
                    @include('admin.demo_mode.demo-mode')
                @else
                    <form id="rr-delete-form" class="d-inline-block" method="POST" action="#">
                        @method('DELETE')
                        @csrf
                        <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('content.cancel') }}</button>
                        <button type="submit" class="btn btn-success">{{ __('content.yes_delete_it') }}</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Shared notify modal --}}
<div class="modal fade" id="rrNotifyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notify Client: <span id="rr-notify-company-title"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="rr-notify-form" method="POST" action="#" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p>The following email may be sent to the client:</p>
                    <div class="email-preview p-3 mb-3" style="background-color: #f8f9fa; border-radius: 5px;">
                        <p><strong>Subject:</strong> You have new test results and new random selections from Drugcheckr</p>
                        <p>Hello <span id="rr-notify-employee"></span>,</p>
                        <p>Drugcheckr has added new test results to your company portal.</p>
                        <p>
                            <a href="{{ route('result-recording.index') }}">Click here</a>
                            to view all results for <strong id="rr-notify-company-1"></strong>.
                        </p>
                        <p>Also:</p>
                        <p>Drugcheckr has added new random selections to your company portal.</p>
                        <p>
                            <a href="{{ route('result-recording.index') }}">Click here</a>
                            to view all selections for <strong id="rr-notify-company-2"></strong>.
                        </p>
                    </div>
                    <div class="form-group">
                        <label for="rr-additional-text">Add text you would like to include in the email</label>
                        <textarea name="additional_text" class="form-control" id="rr-additional-text" rows="3"></textarea>
                    </div>
                    <div class="text-muted small mb-3">
                        (No footer text has been configured to append all client notification emails)
                    </div>
                    <hr>
                    <div class="client-info">
                        <p>
                            <strong>Client:</strong> <span id="rr-notify-company-3"></span>
                            <strong class="ml-2">Phone:</strong> <span id="rr-notify-phone"></span>
                        </p>
                        <p><strong>Date:</strong> <span id="rr-notify-date"></span></p>
                        <p>
                            <strong>DER Contact:</strong> <span id="rr-notify-der-name"></span>
                            <strong class="ml-2">Email:</strong> <span id="rr-notify-der-email"></span>
                        </p>
                    </div>
                    <div class="form-group">
                        <label for="rr-pdf-attachment">Attach PDF File (optional)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="rr-pdf-attachment" name="pdf_attachment" accept=".pdf">
                            <label class="custom-file-label" for="rr-pdf-attachment">Choose file</label>
                        </div>
                        <small class="form-text text-muted">Maximum file size: 5MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Notification</button>
                </div>
            </form>
        </div>
    </div>
</div>
