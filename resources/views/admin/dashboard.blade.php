@extends('layouts.admin.master')

@section('content')
    <style>
        /* ===== Welcome Banner ===== */
        .dash-welcome{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:16px;padding:28px 32px;color:#fff;margin-bottom:28px;position:relative;overflow:hidden}
        .dash-welcome::after{content:'';position:absolute;top:-40px;right:-40px;width:180px;height:180px;background:rgba(255,255,255,.08);border-radius:50%}
        .dash-welcome h2{font-weight:700;font-size:1.6rem;margin-bottom:4px}
        .dash-welcome p{opacity:.85;margin-bottom:0;font-size:.95rem}

        /* ===== Stat Cards Grid — Equal Width via CSS Grid ===== */
        .stat-grid{
            display:grid;
            grid-template-columns:repeat(6, 1fr);
            gap:16px;
            margin-bottom:20px;
        }
        .stat-grid .stat-card-wrapper{
            display:flex;
            animation:fadeInUp .5s ease forwards;
        }
        .stat-grid .stat-card-wrapper:nth-child(2){animation-delay:.1s}
        .stat-grid .stat-card-wrapper:nth-child(3){animation-delay:.15s}
        .stat-grid .stat-card-wrapper:nth-child(4){animation-delay:.2s}
        .stat-grid .stat-card-wrapper:nth-child(5){animation-delay:.25s}
        .stat-grid .stat-card-wrapper:nth-child(6){animation-delay:.3s}

        .stat-card{
            border:none;border-radius:14px;overflow:hidden;transition:all .3s ease;
            position:relative;display:flex;flex-direction:column;width:100%;
            box-shadow:0 2px 8px rgba(0,0,0,.05);
        }
        .stat-card:hover{transform:translateY(-4px);box-shadow:0 12px 28px rgba(0,0,0,.12)}
        .stat-card .card-body{padding:20px 22px;position:relative;z-index:1;flex:1;display:flex;flex-direction:column}
        .stat-card .stat-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;margin-bottom:12px;flex-shrink:0}
        .stat-card .stat-value{font-size:1.75rem;font-weight:800;line-height:1.1;margin-bottom:4px;white-space:nowrap}
        .stat-card .stat-label{font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;opacity:.7;font-weight:600;margin-top:auto;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .stat-card .stat-trend{font-size:.75rem;font-weight:600;margin-top:6px;display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:20px}
        .stat-card .stat-trend.up{background:rgba(16,185,129,.15);color:#10b981}
        .stat-card .stat-trend.down{background:rgba(239,68,68,.15);color:#ef4444}
        .stat-card .stat-trend.neutral{background:rgba(107,114,128,.12);color:#6b7280}

        /* Gradient Variants */
        .stat-card.gradient-blue{background:linear-gradient(135deg,#e0e7ff 0%,#c7d2fe 100%);color:#312e81}
        .stat-card.gradient-green{background:linear-gradient(135deg,#d1fae5 0%,#a7f3d0 100%);color:#064e3b}
        .stat-card.gradient-amber{background:linear-gradient(135deg,#fef3c7 0%,#fde68a 100%);color:#78350f}
        .stat-card.gradient-rose{background:linear-gradient(135deg,#ffe4e6 0%,#fecdd3 100%);color:#881337}
        .stat-card.gradient-purple{background:linear-gradient(135deg,#ede9fe 0%,#ddd6fe 100%);color:#4c1d95}
        .stat-card.gradient-teal{background:linear-gradient(135deg,#ccfbf1 0%,#99f6e4 100%);color:#134e4a}

        /* Icon Variants */
        .stat-card .stat-icon.icon-blue{background:rgba(99,102,241,.15);color:#6366f1}
        .stat-card .stat-icon.icon-green{background:rgba(16,185,129,.15);color:#10b981}
        .stat-card .stat-icon.icon-amber{background:rgba(245,158,11,.15);color:#f59e0b}
        .stat-card .stat-icon.icon-rose{background:rgba(244,63,94,.15);color:#f43f5e}
        .stat-card .stat-icon.icon-purple{background:rgba(139,92,246,.15);color:#8b5cf6}
        .stat-card .stat-icon.icon-teal{background:rgba(20,184,166,.15);color:#14b8a6}

        /* ===== Dashboard Cards ===== */
        .dash-card{border:none;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,.06);margin-bottom:24px;overflow:hidden}
        .dash-card .card-header{background:#fff;border-bottom:1px solid #f1f5f9;padding:16px 22px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px}
        .dash-card .card-header h5{margin:0;font-weight:700;font-size:1rem;color:#1e293b}
        .dash-card .card-header .badge{font-size:.7rem;padding:4px 10px;border-radius:20px;font-weight:600}
        .dash-card .card-body{padding:18px 22px}

        /* ===== Activity & Status ===== */
        .activity-item{display:flex;align-items:flex-start;padding:12px 0;border-bottom:1px solid #f1f5f9}
        .activity-item:last-child{border-bottom:none}
        .activity-dot{width:10px;height:10px;border-radius:50%;margin-top:5px;margin-right:14px;flex-shrink:0}
        .activity-dot.dot-green{background:#10b981}.activity-dot.dot-amber{background:#f59e0b}
        .activity-dot.dot-blue{background:#6366f1}.activity-dot.dot-red{background:#ef4444}
        .activity-dot.dot-gray{background:#9ca3af}
        .activity-info h6{font-size:.88rem;font-weight:600;color:#1e293b;margin-bottom:2px}
        .activity-info p{font-size:.78rem;color:#64748b;margin-bottom:0}
        .activity-info .activity-time{font-size:.72rem;color:#94a3b8}
        .status-badge{font-size:.72rem;padding:3px 10px;border-radius:20px;font-weight:600;display:inline-block}
        .status-badge.badge-completed,.status-badge.badge-negative{background:#d1fae5;color:#065f46}
        .status-badge.badge-pending,.status-badge.badge-in-progress{background:#fef3c7;color:#78350f}
        .status-badge.badge-positive{background:#fee2e2;color:#991b1b}
        .status-badge.badge-cancelled{background:#f1f5f9;color:#475569}

        /* ===== Clients & Actions ===== */
        .top-client-item{display:flex;align-items:center;padding:10px 0;border-bottom:1px solid #f8fafc}
        .top-client-item:last-child{border-bottom:none}
        .client-avatar{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;color:#fff;margin-right:14px;flex-shrink:0}
        .client-info h6{font-size:.85rem;font-weight:600;color:#1e293b;margin-bottom:1px}
        .client-info p{font-size:.75rem;color:#64748b;margin-bottom:0}
        .client-stats{text-align:right;margin-left:auto}
        .client-stats span{font-size:1rem;font-weight:700;color:#1e293b}
        .client-stats small{display:block;font-size:.7rem;color:#94a3b8}
        .progress-thin{height:4px;border-radius:4px;background:#f1f5f9}
        .quick-action{display:flex;align-items:center;padding:14px 18px;border-radius:12px;background:#f8fafc;transition:all .25s;text-decoration:none;color:#1e293b;margin-bottom:10px}
        .quick-action:hover{background:#e0e7ff;color:#4338ca;text-decoration:none;transform:translateX(3px)}
        .quick-action i{width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;margin-right:14px;font-size:.9rem}
        .quick-action span{font-weight:600;font-size:.88rem}

        /* ===== Tables ===== */
        .table-modern{border-collapse:separate;border-spacing:0;width:100%}
        .table-modern thead th{background:#f8fafc;border:none;font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;color:#64748b;font-weight:600;padding:10px 14px;white-space:nowrap}
        .table-modern tbody td{border:none;border-bottom:1px solid #f1f5f9;padding:12px 14px;font-size:.85rem;color:#334155;vertical-align:middle}
        .table-modern tbody tr:hover{background:#f8fafc}
        .emp-avatar{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:600;font-size:.7rem;margin-right:8px}

        /* ===== Animations ===== */
        @keyframes fadeInUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
        .animate-in{animation:fadeInUp .5s ease forwards}

        /* ===== Responsive Breakpoints ===== */

        /* Large desktops — 6 columns (default) */

        /* Medium-large screens */
        @media(max-width:1399.98px){
            .stat-grid{grid-template-columns:repeat(3, 1fr);gap:14px}
        }

        /* Tablets landscape */
        @media(max-width:1199.98px){
            .stat-grid{grid-template-columns:repeat(3, 1fr);gap:14px}
            .stat-card .stat-value{font-size:1.5rem}
        }

        /* Tablets portrait */
        @media(max-width:991.98px){
            .stat-grid{grid-template-columns:repeat(3, 1fr);gap:12px}
            .dash-welcome{padding:22px 24px;border-radius:12px;margin-bottom:22px}
            .dash-welcome h2{font-size:1.35rem}
            .stat-card .card-body{padding:16px 18px}
            .stat-card .stat-icon{width:42px;height:42px;font-size:1.1rem;margin-bottom:10px}
            .stat-card .stat-value{font-size:1.4rem}
            .stat-card .stat-label{font-size:.72rem}
            .dash-card .card-header{padding:14px 18px}
            .dash-card .card-body{padding:14px 18px}
        }

        /* Large phones / small tablets */
        @media(max-width:767.98px){
            .stat-grid{grid-template-columns:repeat(2, 1fr);gap:12px}
            .dash-welcome{padding:18px 20px;margin-bottom:18px}
            .dash-welcome h2{font-size:1.2rem}
            .dash-welcome p{font-size:.85rem}
            .stat-card .card-body{padding:14px 16px}
            .stat-card .stat-icon{width:40px;height:40px;font-size:1rem;border-radius:10px;margin-bottom:8px}
            .stat-card .stat-value{font-size:1.3rem}
            .stat-card .stat-label{font-size:.7rem;letter-spacing:.3px}
            .stat-card .stat-trend{font-size:.7rem;padding:2px 6px}
            .quick-action{padding:12px 14px}
            .quick-action i{width:32px;height:32px;margin-right:10px;font-size:.8rem}
            .quick-action span{font-size:.82rem}
            .table-modern thead th{padding:8px 10px;font-size:.7rem}
            .table-modern tbody td{padding:10px 10px;font-size:.8rem}
            .client-avatar{width:34px;height:34px;font-size:.75rem;margin-right:10px}
        }

        /* Small phones */
        @media(max-width:575.98px){
            .stat-grid{grid-template-columns:repeat(2, 1fr);gap:10px}
            .dash-welcome{padding:16px 16px;border-radius:10px;margin-bottom:16px}
            .dash-welcome h2{font-size:1.1rem}
            .dash-welcome p{font-size:.8rem}
            .stat-card{border-radius:10px}
            .stat-card .card-body{padding:12px 14px}
            .stat-card .stat-icon{width:36px;height:36px;font-size:.9rem;border-radius:8px;margin-bottom:6px}
            .stat-card .stat-value{font-size:1.15rem}
            .stat-card .stat-label{font-size:.68rem;letter-spacing:.2px}
            .stat-card .stat-trend{font-size:.65rem;margin-top:4px}
            .dash-card{border-radius:10px;margin-bottom:16px}
            .dash-card .card-header{padding:12px 14px}
            .dash-card .card-header h5{font-size:.9rem}
            .dash-card .card-body{padding:12px 14px}
            .top-client-item{padding:8px 0}
            .client-info h6{font-size:.8rem}
            .client-info p{font-size:.7rem}
            .client-stats span{font-size:.9rem}
        }

        /* Extra-small phones */
        @media(max-width:419.98px){
            .stat-grid{grid-template-columns:1fr 1fr;gap:8px}
            .stat-card .card-body{padding:10px 12px}
            .stat-card .stat-icon{width:32px;height:32px;font-size:.8rem;margin-bottom:5px}
            .stat-card .stat-value{font-size:1.05rem}
            .stat-card .stat-label{font-size:.64rem}
            .dash-welcome h2{font-size:1rem}
            .quick-action{padding:10px 12px;border-radius:8px}
        }
    </style>

    <div class="container-fluid">
        {{-- Welcome Header --}}
        <div class="dash-welcome animate-in">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2>
                        @php
                            $hour = now()->format('H');
                            $greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
                        @endphp
                        {{ $greeting }}, {{ $auth_user->name ?? 'Admin' }} 👋
                    </h2>
                    <p class="text-white">
                        @if(isset($user_type) && $user_type === 'super-admin')
                            Here's your system overview for {{ now()->format('l, F j, Y') }}
                        @elseif(isset($user_type) && $user_type === 'company')
                            Welcome to your company dashboard — {{ now()->format('l, F j, Y') }}
                        @else
                            Welcome to your dashboard
                        @endif
                    </p>
                </div>
                <div class="col-md-4 text-right d-none d-md-block">
                    <span style="font-size:2.5rem;opacity:.6">📊</span>
                </div>
            </div>
        </div>

        {{-- ============ SUPER ADMIN DASHBOARD ============ --}}
        @if(isset($user_type) && $user_type === 'super-admin')

            {{-- Stat Cards Row --}}
            <div class="stat-grid">
                <div class="stat-card-wrapper">
                    <div class="card stat-card gradient-blue">
                        <div class="card-body">
                            <div class="stat-icon icon-blue"><i class="fas fa-building"></i></div>
                            <div class="stat-value">{{ number_format($stats['total_clients']) }}</div>
                            <div class="stat-label">Total Clients</div>
                            @if(isset($growth['clients']))
                                <span class="stat-trend {{ $growth['clients'] >= 0 ? 'up' : 'down' }}">
                                    <i class="fas fa-arrow-{{ $growth['clients'] >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($growth['clients']) }}%
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="stat-card-wrapper">
                    <div class="card stat-card gradient-green">
                        <div class="card-body">
                            <div class="stat-icon icon-green"><i class="fas fa-file-alt"></i></div>
                            <div class="stat-value">{{ number_format($stats['total_orders']) }}</div>
                            <div class="stat-label">Total Orders</div>
                            @if(isset($growth['orders']))
                                <span class="stat-trend {{ $growth['orders'] >= 0 ? 'up' : 'down' }}">
                                    <i class="fas fa-arrow-{{ $growth['orders'] >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($growth['orders']) }}%
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="stat-card-wrapper">
                    <div class="card stat-card gradient-amber">
                        <div class="card-body">
                            <div class="stat-icon icon-amber"><i class="fas fa-vial"></i></div>
                            <div class="stat-value">{{ number_format($stats['total_results']) }}</div>
                            <div class="stat-label">Test Results</div>
                            @if(isset($growth['results']))
                                <span class="stat-trend {{ $growth['results'] >= 0 ? 'up' : 'down' }}">
                                    <i class="fas fa-arrow-{{ $growth['results'] >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($growth['results']) }}%
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="stat-card-wrapper">
                    <div class="card stat-card gradient-rose">
                        <div class="card-body">
                            <div class="stat-icon icon-rose"><i class="fas fa-users"></i></div>
                            <div class="stat-value">{{ number_format($stats['total_employees']) }}</div>
                            <div class="stat-label">Employees</div>
                        </div>
                    </div>
                </div>
                <div class="stat-card-wrapper">
                    <div class="card stat-card gradient-purple">
                        <div class="card-body">
                            <div class="stat-icon icon-purple"><i class="fas fa-flask"></i></div>
                            <div class="stat-value">{{ number_format($stats['total_laboratories']) }}</div>
                            <div class="stat-label">Labs</div>
                        </div>
                    </div>
                </div>
                <div class="stat-card-wrapper">
                    <div class="card stat-card gradient-teal">
                        <div class="card-body">
                            <div class="stat-icon icon-teal"><i class="fas fa-calendar-day"></i></div>
                            <div class="stat-value">{{ number_format($stats['today_orders']) }}</div>
                            <div class="stat-label">Today's Orders</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts Row --}}
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card dash-card">
                        <div class="card-header">
                            <h5><i class="fas fa-chart-bar mr-2" style="color:#6366f1"></i> Weekly Orders</h5>
                            <span class="badge badge-primary">Last 7 Days</span>
                        </div>
                        <div class="card-body">
                            <canvas id="weeklyOrdersChart" style="height:280px"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card dash-card">
                        <div class="card-header">
                            <h5><i class="fas fa-chart-pie mr-2" style="color:#f59e0b"></i> Order Status</h5>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <canvas id="orderStatusChart" style="max-height:260px"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Monthly Trends --}}
            @if(isset($monthly_trends) && $monthly_trends->isNotEmpty())
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card dash-card">
                        <div class="card-header">
                            <h5><i class="fas fa-chart-line mr-2" style="color:#10b981"></i> Monthly Trends — {{ now()->year }}</h5>
                            <span class="badge badge-success">Yearly Overview</span>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyTrendChart" style="height:280px"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Recent Activities & Top Clients --}}
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card dash-card">
                        <div class="card-header">
                            <h5><i class="fas fa-history mr-2" style="color:#8b5cf6"></i> Recent Orders</h5>
                            <a href="{{ url('admin/quest-order') }}" class="badge badge-primary" style="text-decoration:none">View All →</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-modern mb-0">
                                    <thead><tr><th>Donor</th><th>Reference</th><th>Status</th><th>Result</th><th>Time</th></tr></thead>
                                    <tbody>
                                    @forelse($recent_activities as $order)
                                        <tr>
                                            <td><strong>{{ $order['donor_name'] ?: '-' }}</strong></td>
                                            <td><code style="font-size:.8rem">{{ $order['reference_id'] ?? '-' }}</code></td>
                                            <td>
                                                @php $s = strtolower($order['status'] ?? ''); @endphp
                                                <span class="status-badge badge-{{ str_contains($s,'complet') ? 'completed' : (str_contains($s,'pend') ? 'pending' : 'cancelled') }}">{{ $order['status'] ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                @if($order['result'])
                                                    @php $r = strtolower($order['result']); @endphp
                                                    <span class="status-badge badge-{{ str_contains($r,'neg') ? 'negative' : (str_contains($r,'pos') ? 'positive' : 'pending') }}">{{ $order['result'] }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td><small class="text-muted">{{ $order['time_ago'] }}</small></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-4">No recent orders</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card dash-card">
                        <div class="card-header">
                            <h5><i class="fas fa-trophy mr-2" style="color:#f59e0b"></i> Top Clients</h5>
                        </div>
                        <div class="card-body">
                            @php $avatarColors = ['#6366f1','#10b981','#f59e0b','#ef4444','#8b5cf6']; @endphp
                            @forelse($top_clients as $i => $client)
                                <div class="top-client-item">
                                    <div class="client-avatar" style="background:{{ $avatarColors[$i % 5] }}">{{ strtoupper(substr($client->company_name ?? '?', 0, 2)) }}</div>
                                    <div class="client-info">
                                        <h6>{{ $client->company_name }}</h6>
                                        <p>{{ $client->employees_count }} employees</p>
                                    </div>
                                    <div class="client-stats">
                                        <span>{{ $client->orders_count }}</span>
                                        <small>orders</small>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center py-3">No clients yet</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="card dash-card">
                        <div class="card-header"><h5><i class="fas fa-bolt mr-2" style="color:#6366f1"></i> Quick Actions</h5></div>
                        <div class="card-body">
                            <a href="{{ url('admin/quest-order/create') }}" class="quick-action">
                                <i class="fas fa-plus" style="background:#e0e7ff;color:#6366f1"></i><span>New Order</span>
                            </a>
                            <a href="{{ url('admin/client-profile/create') }}" class="quick-action">
                                <i class="fas fa-user-plus" style="background:#d1fae5;color:#10b981"></i><span>Add Client</span>
                            </a>
                            <a href="{{ url('admin/result-recording') }}" class="quick-action">
                                <i class="fas fa-clipboard-list" style="background:#fef3c7;color:#f59e0b"></i><span>View Results</span>
                            </a>
                            <a href="{{ url('admin/report/mis-reports') }}" class="quick-action">
                                <i class="fas fa-chart-bar" style="background:#ede9fe;color:#8b5cf6"></i><span>MIS Reports</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Results --}}
            @if(isset($recent_results) && $recent_results->count())
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card dash-card">
                        <div class="card-header">
                            <h5><i class="fas fa-vial mr-2" style="color:#10b981"></i> Recent Test Results</h5>
                            <a href="{{ url('admin/result-recording') }}" class="badge badge-success" style="text-decoration:none">View All →</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-modern mb-0">
                                    <thead><tr><th>Employee</th><th>Company</th><th>Lab</th><th>MRO</th><th>Status</th><th>Reason</th><th>Date</th></tr></thead>
                                    <tbody>
                                    @foreach($recent_results as $result)
                                        <tr>
                                            <td>
                                                <span class="emp-avatar">{{ strtoupper(substr(optional($result->employee)->first_name ?? '?', 0, 1)) }}</span>
                                                {{ optional($result->employee)->first_name }} {{ optional($result->employee)->last_name }}
                                            </td>
                                            <td>{{ optional($result->clientProfile)->company_name ?? '-' }}</td>
                                            <td>{{ optional($result->laboratory)->laboratory_name ?? '-' }}</td>
                                            <td>{{ optional($result->mro)->name ?? '-' }}</td>
                                            <td>
                                                @php $rs = strtolower($result->status ?? ''); @endphp
                                                <span class="status-badge badge-{{ str_contains($rs,'complet') ? 'completed' : 'pending' }}">{{ ucfirst($result->status ?? 'N/A') }}</span>
                                            </td>
                                            <td><small>{{ $result->reason_for_test ?? '-' }}</small></td>
                                            <td><small class="text-muted">{{ \Carbon\Carbon::parse($result->created_at)->format('M d, Y') }}</small></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        @endif
        {{-- END SUPER ADMIN --}}

        {{-- ============ COMPANY DASHBOARD ============ --}}
        @if(isset($user_type) && $user_type === 'company')

            @if(isset($error))
                <div class="alert alert-warning">{{ $error }}</div>
            @else
                {{-- Company Info Banner --}}
                @if(isset($company_profile))
                <div class="row mb-3">
                    <div class="col-12">
                        <div style="background:linear-gradient(135deg,#0ea5e9,#6366f1);border-radius:14px;padding:18px 24px;color:#fff;display:flex;align-items:center;gap:18px">
                            <div style="width:50px;height:50px;border-radius:12px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:1.3rem;font-weight:700">
                                {{ strtoupper(substr($company_profile->company_name ?? '?', 0, 2)) }}
                            </div>
                            <div>
                                <h5 style="margin:0;font-weight:700">{{ $company_profile->company_name }}</h5>
                                <small style="opacity:.8">Account #{{ $company_profile->account_no ?? 'N/A' }} · {{ $company_profile->city ?? '' }}, {{ $company_profile->state ?? '' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Company Stat Cards --}}
                <div class="stat-grid">
                    <div class="stat-card-wrapper">
                        <div class="card stat-card gradient-blue"><div class="card-body">
                            <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
                            <div class="stat-value">{{ number_format($stats['my_employees']) }}</div>
                            <div class="stat-label">My Employees</div>
                        </div></div>
                    </div>
                    <div class="stat-card-wrapper">
                        <div class="card stat-card gradient-green"><div class="card-body">
                            <div class="stat-icon icon-green"><i class="fas fa-file-alt"></i></div>
                            <div class="stat-value">{{ number_format($stats['my_orders']) }}</div>
                            <div class="stat-label">My Orders</div>
                            @if(isset($growth['orders']))
                                <span class="stat-trend {{ $growth['orders'] >= 0 ? 'up' : 'down' }}">
                                    <i class="fas fa-arrow-{{ $growth['orders'] >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($growth['orders']) }}%
                                </span>
                            @endif
                        </div></div>
                    </div>
                    <div class="stat-card-wrapper">
                        <div class="card stat-card gradient-amber"><div class="card-body">
                            <div class="stat-icon icon-amber"><i class="fas fa-vial"></i></div>
                            <div class="stat-value">{{ number_format($stats['my_results']) }}</div>
                            <div class="stat-label">My Results</div>
                        </div></div>
                    </div>
                    <div class="stat-card-wrapper">
                        <div class="card stat-card gradient-rose"><div class="card-body">
                            <div class="stat-icon icon-rose"><i class="fas fa-clock"></i></div>
                            <div class="stat-value">{{ number_format($stats['pending_orders']) }}</div>
                            <div class="stat-label">Pending</div>
                        </div></div>
                    </div>
                    <div class="stat-card-wrapper">
                        <div class="card stat-card gradient-purple"><div class="card-body">
                            <div class="stat-icon icon-purple"><i class="fas fa-check-circle"></i></div>
                            <div class="stat-value">{{ number_format($stats['completed_tests']) }}</div>
                            <div class="stat-label">Completed</div>
                        </div></div>
                    </div>
                    <div class="stat-card-wrapper">
                        <div class="card stat-card gradient-teal"><div class="card-body">
                            <div class="stat-icon icon-teal"><i class="fas fa-calendar-day"></i></div>
                            <div class="stat-value">{{ number_format($stats['today_orders']) }}</div>
                            <div class="stat-label">Today</div>
                        </div></div>
                    </div>
                </div>

                {{-- Company Charts --}}
                <div class="row">
                    <div class="col-lg-8 mb-4">
                        <div class="card dash-card">
                            <div class="card-header"><h5><i class="fas fa-chart-bar mr-2" style="color:#6366f1"></i> Weekly Orders</h5><span class="badge badge-primary">Last 7 Days</span></div>
                            <div class="card-body"><canvas id="weeklyOrdersChart" style="height:260px"></canvas></div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4">
                        <div class="card dash-card">
                            <div class="card-header"><h5><i class="fas fa-chart-pie mr-2" style="color:#f59e0b"></i> Order Status</h5></div>
                            <div class="card-body d-flex align-items-center justify-content-center"><canvas id="orderStatusChart" style="max-height:240px"></canvas></div>
                        </div>
                    </div>
                </div>

                {{-- Company Employees & Recent Orders --}}
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card dash-card">
                            <div class="card-header"><h5><i class="fas fa-users mr-2" style="color:#10b981"></i> Employees</h5></div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-modern mb-0">
                                        <thead><tr><th>Name</th><th>Department</th><th>Status</th></tr></thead>
                                        <tbody>
                                        @forelse($company_employees as $emp)
                                            <tr>
                                                <td><span class="emp-avatar">{{ strtoupper(substr($emp->first_name ?? '?', 0, 1)) }}</span>{{ $emp->first_name }} {{ $emp->last_name }}</td>
                                                <td>{{ $emp->department ?? '-' }}</td>
                                                <td><span class="status-badge badge-{{ strtolower($emp->status ?? '') === 'active' ? 'completed' : 'pending' }}">{{ ucfirst($emp->status ?? 'N/A') }}</span></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-center text-muted py-4">No employees</td></tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="card dash-card">
                            <div class="card-header"><h5><i class="fas fa-history mr-2" style="color:#8b5cf6"></i> Recent Orders</h5></div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-modern mb-0">
                                        <thead>
                                            <tr>
                                                <th>Donor</th>
                                                <th>Status</th>
                                                <th>Result</th>
                                                <th>Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($recent_activities as $order)
                                            <tr>
                                                <td><strong>{{ $order['donor_name'] ?: '-' }}</strong></td>
                                                <td>
                                                    @php $s = strtolower($order['status'] ?? ''); @endphp
                                                    <span class="status-badge badge-{{ str_contains($s,'complet') ? 'completed' : 'pending' }}">{{ $order['status'] ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    @php $r = strtolower($order['result'] ?? ''); @endphp
                                                    <span class="status-badge badge-{{ str_contains($r,'positive') ? 'danger' : 'success' }}">{{ $order['result'] ?? 'N/A' }}</span>
                                                </td>
                                                <td><small class="text-muted">{{ $order['time_ago'] }}</small></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center text-muted py-4">No recent orders</td></tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Company Recent Results --}}
                @if(isset($recent_results) && $recent_results->count())
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card dash-card">
                            <div class="card-header"><h5><i class="fas fa-vial mr-2" style="color:#10b981"></i> Recent Test Results</h5></div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-modern mb-0">
                                        <thead><tr><th>Employee</th><th>Lab</th><th>MRO</th><th>Status</th><th>Date</th></tr></thead>
                                        <tbody>
                                        @foreach($recent_results as $result)
                                            <tr>
                                                <td>{{ optional($result->employee)->first_name }} {{ optional($result->employee)->last_name }}</td>
                                                <td>{{ optional($result->laboratory)->laboratory_name ?? '-' }}</td>
                                                <td>{{ optional($result->mro)->name ?? '-' }}</td>
                                                <td><span class="status-badge badge-{{ strtolower($result->status ?? '') === 'completed' ? 'completed' : 'pending' }}">{{ ucfirst($result->status ?? 'N/A') }}</span></td>
                                                <td><small>{{ \Carbon\Carbon::parse($result->created_at)->format('M d, Y') }}</small></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endif
        @endif
        {{-- END COMPANY --}}

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const palette = ['#6366f1','#10b981','#f59e0b','#ef4444','#8b5cf6','#0ea5e9','#f43f5e','#14b8a6','#e11d48','#84cc16'];

            // Weekly Orders Bar Chart
            @if(isset($weekly_orders))
                const weeklyData = @json($weekly_orders);
                new Chart(document.getElementById('weeklyOrdersChart'), {
                    type: 'bar',
                    data: {
                        labels: Object.keys(weeklyData),
                        datasets: [{
                            label: 'Orders',
                            data: Object.values(weeklyData),
                            backgroundColor: palette.slice(0, Object.keys(weeklyData).length).map(c => c + '33'),
                            borderColor: palette.slice(0, Object.keys(weeklyData).length),
                            borderWidth: 2, borderRadius: 8, borderSkipped: false
                        }]
                    },
                    options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true,grid:{color:'#f1f5f9'}},x:{grid:{display:false}}} }
                });
            @endif

            // Order Status Doughnut
            @if(isset($order_status_distribution) && count($order_status_distribution))
                const statusData = @json($order_status_distribution);
                new Chart(document.getElementById('orderStatusChart'), {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(statusData),
                        datasets: [{ data: Object.values(statusData), backgroundColor: palette, borderWidth: 0, hoverOffset: 6 }]
                    },
                    options: { responsive:true, maintainAspectRatio:false, cutout:'65%', plugins:{legend:{position:'bottom',labels:{padding:12,usePointStyle:true,pointStyle:'circle',font:{size:11}}}} }
                });
            @endif

            // Monthly Trends Line Chart
            @if(isset($monthly_trends) && $monthly_trends->isNotEmpty())
                const mt = @json($monthly_trends);
                const mtLabels = Object.keys(mt);
                new Chart(document.getElementById('monthlyTrendChart'), {
                    type: 'line',
                    data: {
                        labels: mtLabels,
                        datasets: [
                            { label:'Orders', data:mtLabels.map(k=>mt[k].orders), borderColor:'#6366f1', backgroundColor:'rgba(99,102,241,.1)', fill:true, tension:.4, borderWidth:2.5, pointRadius:4, pointBackgroundColor:'#6366f1' },
                            { label:'Completed', data:mtLabels.map(k=>mt[k].completed), borderColor:'#10b981', backgroundColor:'rgba(16,185,129,.1)', fill:true, tension:.4, borderWidth:2.5, pointRadius:4, pointBackgroundColor:'#10b981' }
                        ]
                    },
                    options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{labels:{usePointStyle:true,pointStyle:'circle',padding:16}}}, scales:{y:{beginAtZero:true,grid:{color:'#f1f5f9'}},x:{grid:{display:false}}} }
                });
            @endif
        });
        </script>
    @endpush
@endsection
