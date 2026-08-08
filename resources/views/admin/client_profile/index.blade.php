@extends('layouts.admin.master')

@push('styles')
    <style>
        .cp-page .orders-filter-card .form-label { font-size: 12px; color: #6c757d; margin-bottom: 4px; }
        .cp-page .orders-filter-grid {
            display: grid;
            grid-template-columns: minmax(180px, 2.2fr) repeat(4, minmax(0, 1fr)) minmax(150px, auto);
            gap: 0.5rem 0.55rem;
            align-items: end;
        }
        .cp-page .orders-filter-grid .orders-filter-field { min-width: 0; }
        .cp-page .orders-filter-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.45rem;
        }
        .cp-page .rs-list-card .card-body { padding: 0.85rem 1rem 1rem; }
        #client-profiles-table td { vertical-align: middle; }
        #client-profiles-table thead th {
            white-space: nowrap;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .02em;
            background: #f8fafc;
            border-bottom-width: 1px;
        }
        .cp-pagination {
            display: flex;
            justify-content: flex-end;
            margin-top: 1rem;
        }
        .cp-pagination .pagination { margin-bottom: 0; }
        .cp-company-name {
            font-weight: 700;
            color: var(--admin-text, #2e384d);
            line-height: 1.3;
        }
        .cp-company-meta {
            font-size: 0.78rem;
            color: var(--admin-text-muted, #858796);
            margin-top: 0.15rem;
        }
        .cp-driver-count {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-weight: 700;
            color: var(--admin-text, #2e384d);
        }
        .cp-driver-count small {
            font-weight: 500;
            color: var(--admin-text-muted, #858796);
        }
        .cp-contact-line {
            font-size: 0.86rem;
            line-height: 1.35;
        }
        .cp-contact-line .text-muted {
            font-size: 0.78rem;
        }
        .cp-empty {
            text-align: center;
            padding: 2.5rem 1rem;
            color: var(--admin-text-muted, #858796);
        }
        @media (max-width: 1199.98px) {
            .cp-page .orders-filter-grid {
                grid-template-columns: minmax(160px, 1.6fr) repeat(3, minmax(0, 1fr));
            }
            .cp-page .orders-filter-actions-field {
                grid-column: span 2;
            }
        }
        @media (max-width: 767.98px) {
            .cp-page .orders-filter-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .cp-page .orders-filter-grid .orders-filter-search,
            .cp-page .orders-filter-actions-field {
                grid-column: 1 / -1;
            }
        }
        @media (max-width: 575.98px) {
            .cp-page .orders-filter-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid rs-page cp-page">
        <div class="rs-page__toolbar">
            <div>
                <nav class="rs-page__crumb" aria-label="Breadcrumb">
                    <span>Clients</span>
                    <span>/</span>
                    <span>Client Profiles</span>
                </nav>
                <h3 class="rs-page__title">Client profiles</h3>
                <p class="rs-page__subtitle">
                    Overview of all companies and their drivers — filter by status, location, agency, or driver roster.
                </p>
            </div>
            <div class="rs-page__actions">
                @canany(['client profile create', 'client profile create_all'])
                    <a href="{{ route('client-profile.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> Add client
                    </a>
                @endcanany
            </div>
        </div>

        <div class="row">
            <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
                <div class="card orders-stat-card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="cp-stat-icon cp-stat-icon--blue mr-2"><i class="fas fa-building"></i></div>
                        <div>
                            <p class="mb-0 text-muted font-12">Total clients</p>
                            <h4 class="mb-0 font-weight-bold">{{ number_format($stats['total_clients']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
                <div class="card orders-stat-card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="cp-stat-icon cp-stat-icon--green mr-2"><i class="fas fa-check-circle"></i></div>
                        <div>
                            <p class="mb-0 text-muted font-12">Active clients</p>
                            <h4 class="mb-0 font-weight-bold">{{ number_format($stats['active_clients']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
                <div class="card orders-stat-card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="cp-stat-icon cp-stat-icon--gray mr-2"><i class="fas fa-pause-circle"></i></div>
                        <div>
                            <p class="mb-0 text-muted font-12">Inactive clients</p>
                            <h4 class="mb-0 font-weight-bold">{{ number_format($stats['inactive_clients']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
                <div class="card orders-stat-card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="cp-stat-icon cp-stat-icon--cyan mr-2"><i class="fas fa-id-card"></i></div>
                        <div>
                            <p class="mb-0 text-muted font-12">Total drivers</p>
                            <h4 class="mb-0 font-weight-bold">{{ number_format($stats['total_drivers']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
                <div class="card orders-stat-card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="cp-stat-icon cp-stat-icon--green mr-2"><i class="fas fa-user-check"></i></div>
                        <div>
                            <p class="mb-0 text-muted font-12">Active drivers</p>
                            <h4 class="mb-0 font-weight-bold">{{ number_format($stats['active_drivers']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6 box-margin">
                <div class="card orders-stat-card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="cp-stat-icon cp-stat-icon--amber mr-2"><i class="fas fa-user-slash"></i></div>
                        <div>
                            <p class="mb-0 text-muted font-12">No drivers</p>
                            <h4 class="mb-0 font-weight-bold">{{ number_format($stats['clients_without_drivers']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 box-margin">
                <div class="card orders-filter-card border-0 shadow-sm">
                    <div class="card-body">
                        <form method="GET" action="{{ route('client-profile.index') }}" id="client-profile-filters">
                            <div class="orders-filter-grid">
                                <div class="orders-filter-field orders-filter-search">
                                    <label for="filter-search" class="form-label">Search</label>
                                    <input type="text" id="filter-search" name="search" class="form-control"
                                           value="{{ $filters['search'] }}"
                                           placeholder="Company, account, DER, driver, CDL…">
                                </div>
                                <div class="orders-filter-field">
                                    <label for="filter-status" class="form-label">Status</label>
                                    <select id="filter-status" name="status" class="form-control">
                                        <option value="">All statuses</option>
                                        <option value="active" @selected($filters['status'] === 'active')>Active</option>
                                        <option value="inactive" @selected($filters['status'] === 'inactive')>Inactive</option>
                                    </select>
                                </div>
                                <div class="orders-filter-field">
                                    <label for="filter-state" class="form-label">State</label>
                                    <select id="filter-state" name="state" class="form-control">
                                        <option value="">All states</option>
                                        @foreach ($states as $state)
                                            <option value="{{ $state }}" @selected($filters['state'] === $state)>{{ $state }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="orders-filter-field">
                                    <label for="filter-agency" class="form-label">DOT agency</label>
                                    <select id="filter-agency" name="dot_agency_id" class="form-control">
                                        <option value="">All agencies</option>
                                        @foreach ($dotAgencies as $agency)
                                            <option value="{{ $agency->id }}" @selected($filters['dot_agency_id'] == $agency->id)>
                                                {{ $agency->dot_agency_name ?: $agency->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="orders-filter-field">
                                    <label for="filter-drivers" class="form-label">Drivers</label>
                                    <select id="filter-drivers" name="drivers" class="form-control">
                                        <option value="">All</option>
                                        <option value="with" @selected($filters['drivers'] === 'with')>With drivers</option>
                                        <option value="without" @selected($filters['drivers'] === 'without')>No drivers</option>
                                    </select>
                                </div>
                                <div class="orders-filter-field orders-filter-actions-field">
                                    <label class="form-label d-none d-xl-block">&nbsp;</label>
                                    <div class="orders-filter-actions">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                        <a href="{{ route('client-profile.index') }}" class="btn btn-outline-secondary">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card rs-list-card border-0 shadow-sm">
            <div class="card-body">
                @if ($clientProfiles->total() > 0)
                    <div class="table-responsive rs-list-scroll">
                        <table id="client-profiles-table" class="table table-hover rs-list-table mb-0 w-100">
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Location</th>
                                    <th>DER contact</th>
                                    <th>Drivers</th>
                                    <th>Agency</th>
                                    <th>Start date</th>
                                    <th>Status</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clientProfiles as $clientProfile)
                                    @php
                                        $startDate = $clientProfile->client_start_date
                                            ?? $clientProfile->created_at;
                                        $agencyName = $clientProfile->dotAgency?->dot_agency_name
                                            ?: $clientProfile->dotAgency?->full_name;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="cp-company-name">
                                                <a href="{{ route('client-profile.show', $clientProfile->id) }}">
                                                    {{ $clientProfile->company_name }}
                                                </a>
                                            </div>
                                            <div class="cp-company-meta">
                                                @if (filled($clientProfile->account_no))
                                                    Acc #{{ $clientProfile->account_no }}
                                                @else
                                                    ID #{{ $clientProfile->id }}
                                                @endif
                                                @if (filled($clientProfile->phone))
                                                    · {{ $clientProfile->phone }}
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="cp-contact-line">
                                                {{ collect([$clientProfile->city, $clientProfile->state])->filter()->join(', ') ?: '—' }}
                                            </div>
                                            @if (filled($clientProfile->zip))
                                                <div class="text-muted small">{{ $clientProfile->zip }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="cp-contact-line font-weight-bold">
                                                {{ $clientProfile->der_contact_name ?: '—' }}
                                            </div>
                                            @if (filled($clientProfile->der_contact_email))
                                                <div class="text-muted">{{ $clientProfile->der_contact_email }}</div>
                                            @endif
                                            @if (filled($clientProfile->der_contact_phone))
                                                <div class="text-muted">{{ $clientProfile->der_contact_phone }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="cp-driver-count">
                                                <i class="fas fa-users text-info"></i>
                                                {{ number_format($clientProfile->active_employees_count) }}
                                                <small>/ {{ number_format($clientProfile->employees_count) }}</small>
                                            </div>
                                            <div class="text-muted small">active / total</div>
                                        </td>
                                        <td>
                                            @if ($agencyName)
                                                <span class="rs-chip">{{ $agencyName }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            {{ $startDate ? \Illuminate\Support\Carbon::parse($startDate)->format('M j, Y') : '—' }}
                                        </td>
                                        <td>
                                            @if ($clientProfile->status === 'active')
                                                <span class="rs-status rs-status--on">Active</span>
                                            @else
                                                <span class="rs-status rs-status--off">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="rs-list-actions">
                                            <div class="rs-action-btns">
                                                @canany(['client profile view', 'client profile view_all'])
                                                    <a href="{{ route('client-profile.show', $clientProfile->id) }}"
                                                       title="View client & drivers">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                @endcanany
                                                @canany(['client profile edit', 'client profile edit_all'])
                                                    <a href="{{ route('client-profile.edit', $clientProfile->id) }}"
                                                       title="Edit client">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endcanany
                                                @canany(['client profile delete', 'client profile delete_all'])
                                                    <a href="#" class="rs-action--danger"
                                                       data-toggle="modal"
                                                       data-target="#deleteModal{{ $clientProfile->id }}"
                                                       title="Delete client">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                @endcanany
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @foreach ($clientProfiles as $clientProfile)
                        <div class="modal fade" id="deleteModal{{ $clientProfile->id }}" tabindex="-1"
                             role="dialog" aria-labelledby="clientProfileModalCenterTitle{{ $clientProfile->id }}"
                             aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"
                                            id="clientProfileModalCenterTitle{{ $clientProfile->id }}">
                                            {{ __('content.delete') }}
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                                aria-label="{{ __('content.close') }}">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <p class="mb-2">{{ __('content.you_wont_be_able_to_revert_this') }}</p>
                                        <p class="mb-0 font-weight-bold">{{ $clientProfile->company_name }}</p>
                                    </div>
                                    <div class="modal-footer">
                                        @if ($demo_mode == 'on')
                                            @include('admin.demo_mode.demo-mode')
                                        @else
                                            <form class="d-inline-block"
                                                  action="{{ route('client-profile.destroy', $clientProfile->id) }}"
                                                  method="POST">
                                                @method('DELETE')
                                                @csrf
                                        @endif
                                        <button type="button" class="btn btn-danger"
                                                data-dismiss="modal">{{ __('content.cancel') }}</button>
                                        <button type="submit"
                                                class="btn btn-success">{{ __('content.yes_delete_it') }}</button>
                                        @if ($demo_mode != 'on')
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if ($clientProfiles->hasPages())
                        <div class="cp-pagination">
                            {{ $clientProfiles->onEachSide(1)->links() }}
                        </div>
                    @endif
                @else
                    <div class="cp-empty">
                        <i class="fas fa-building fa-2x mb-3 d-block"></i>
                        @if ($hasActiveFilters)
                            <p class="mb-2 font-weight-bold">No clients match these filters</p>
                            <a href="{{ route('client-profile.index') }}" class="btn btn-sm btn-outline-primary">
                                Clear filters
                            </a>
                        @else
                            <p class="mb-2 font-weight-bold">{{ __('content.not_yet_created') }}</p>
                            @canany(['client profile create', 'client profile create_all'])
                                <a href="{{ route('client-profile.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus mr-1"></i> Add first client
                                </a>
                            @endcanany
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
