@extends('layouts.admin.master')

@section('content')
    <div class="container-fluid">
        <h1 class="page-header">Dashboard</h1>
        <div class="row">

            {{-- Stats Cards --}}
            @if (isset($user_type) && $user_type === 'super-admin')
                <div class="col-md-4">
                    <div class="card bg-primary text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Total Clients</h5>
                            <p class="card-text display-4">{{ number_format($stats['total_clients']) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Total Orders</h5>
                            <p class="card-text display-4">{{ number_format($stats['total_orders']) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Total Results</h5>
                            {{-- Fixed: was $stats['total_tests'], key is 'total_results' in controller --}}
                            <p class="card-text display-4">{{ number_format($stats['total_results']) }}</p>
                        </div>
                    </div>
                </div>
            @elseif(isset($user_type) && $user_type === 'company')
                <div class="col-md-4">
                    <div class="card bg-primary text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">My Employees</h5>
                            {{-- Fixed: was 'total_clients', company user key is 'my_employees' --}}
                            <p class="card-text display-4">{{ number_format($stats['my_employees']) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">My Orders</h5>
                            {{-- Fixed: key is 'my_orders' --}}
                            <p class="card-text display-4">{{ number_format($stats['my_orders']) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">My Results</h5>
                            {{-- Fixed: key is 'my_results' --}}
                            <p class="card-text display-4">{{ number_format($stats['my_results']) }}</p>
                        </div>
                    </div>
                </div>
                @if (isset($company_profile))
                    <div class="col-md-4">
                        <div class="card bg-info text-white mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Company Name</h5>
                                <p class="card-text display-4">{{ e($company_profile->company_name) }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Company User Specific Section --}}
            {{-- Fixed: was is_array() — controller returns a Collection, use isset() instead --}}
            @if (isset($company_employees))
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h4 class="mb-0">Company Employees</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Department</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($company_employees as $index => $employee)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ e($employee->first_name) }} {{ e($employee->last_name) }}</td>
                                            <td>{{ e($employee->user->email ?? '-') }}</td>
                                            <td>{{ e($employee->status ?? '-') }}</td>
                                            <td>{{ e($employee->department ?? '-') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Recent Orders --}}
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h4 class="mb-0">Recent Orders</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Reference</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Client</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recent_activities as $order)
                                        <tr>
                                            <td>{{ $order['id'] }}</td>
                                            <td>{{ $order['reference_id'] }}</td>
                                            <td>{{ e($order['status']) }}</td>
                                            <td>{{ $order['created_at'] }}</td>
                                            {{-- Fixed: client_name is already a string, not an object --}}
                                            <td>{{ e($order['client_name'] ?? '-') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Recent Test Results --}}
                @if (isset($recent_results))
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header bg-warning text-white">
                                <h4 class="mb-0">Recent Test Results</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Result</th>
                                            <th>Laboratory</th>
                                            <th>MRO</th>
                                            <th>Completed</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recent_results as $result)
                                            <tr>
                                                <td>{{ $result->id }}</td>
                                                <td>{{ e($result->status) }}</td>
                                                <td>{{ e($result->laboratory->name ?? '-') }}</td>
                                                <td>{{ e($result->mro->name ?? '-') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($result->created_at)->format('Y-m-d') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Super Admin Sections --}}
            @if (isset($user_type) && $user_type === 'super-admin')

                {{-- Top Clients --}}
                @if (isset($top_clients))
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h4 class="mb-0">Top Clients</h4>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group mb-0">
                                    @foreach ($top_clients as $client)
                                        <li
                                            class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                                            <div>
                                                <strong>{{ e($client->company_name) }}</strong>
                                                <span class="badge badge-primary badge-pill">
                                                    {{ $client->orders_count }} Orders
                                                </span>
                                            </div>
                                            <span class="badge badge-light px-2 py-1">
                                                {{ number_format($client->employees_count) }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Monthly Trends Chart --}}
                @if (isset($monthly_trends) && $monthly_trends->isNotEmpty())
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white mb-3">
                                <h4 class="mb-0">Monthly Test Trends</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="monthlyTrendChart" style="min-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                @endif

            @endif

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if (isset($monthly_trends) && $monthly_trends->isNotEmpty())
                    {{-- Fixed: was using array_column + |json (invalid in Blade). --}}
                    {{-- $monthly_trends is a keyed Collection from mapWithKeys, so keys = months --}}
                    const monthlyTrends = @json($monthly_trends);
                    const labels = Object.keys(monthlyTrends);
                    const ordersData = labels.map(k => monthlyTrends[k].orders);
                    const completedData = labels.map(k => monthlyTrends[k].completed);

                    const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                    label: 'Orders',
                                    data: ordersData,
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 2,
                                    fill: true
                                },
                                {
                                    label: 'Completed',
                                    data: completedData,
                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 2,
                                    fill: true
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                @endif
            });
        </script>
    @endpush
@endsection
