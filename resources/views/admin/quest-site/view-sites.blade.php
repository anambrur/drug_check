@extends('layouts.admin.master')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Quest Diagnostics Collection Sites ({{ $sitesCount }} sites)
                    <a href="{{ route('quest-site.dashboard') }}" class="btn btn-sm btn-primary float-end">Back to Dashboard</a>
                </div>

                <div class="card-body">
                    @if($sitesCount > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Site Code</th>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Phone</th>
                                        <th>Type</th>
                                        <th>Active</th>
                                        <th>NIDA</th>
                                        <th>SAP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sites as $siteCode => $site)
                                        <tr>
                                            <td>{{ $siteCode }}</td>
                                            <td>{{ $site['address']['name'] ?? 'N/A' }}</td>
                                            <td>
                                                {{ $site['address']['address1'] ?? '' }}<br>
                                                {{ $site['address']['city'] ?? '' }}, {{ $site['address']['state'] ?? '' }} {{ $site['address']['zip'] ?? '' }}
                                            </td>
                                            <td>{{ $site['primary_phone'] ?? 'N/A' }}</td>
                                            <td>
                                                @if($site['site_type_id'] == 1)
                                                    Quest PSC
                                                @elseif($site['site_type_id'] == 2)
                                                    Quest Preferred
                                                @elseif($site['site_type_id'] == 3)
                                                    Third Party
                                                @else
                                                    Unknown
                                                @endif
                                            </td>
                                            <td>
                                                @if($site['is_active'])
                                                    <span class="badge bg-success">Yes</span>
                                                @else
                                                    <span class="badge bg-danger">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($site['nida_collections'])
                                                    <span class="badge bg-success">Yes</span>
                                                @else
                                                    <span class="badge bg-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($site['sap_collections'])
                                                    <span class="badge bg-success">Yes</span>
                                                @else
                                                    <span class="badge bg-secondary">No</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No collection sites found in the database.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection