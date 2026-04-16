<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Blog;
use App\Models\Admin\ClientProfile;
use App\Models\Admin\Employee;
use App\Models\Admin\Favicon;
use App\Models\Admin\Laboratory;
use App\Models\Admin\MRO;
use App\Models\Admin\PanelImage;
use App\Models\Admin\QuestOrder;
use App\Models\Admin\RandomSelection;
use App\Models\Admin\ResultRecording;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get authenticated user
        $user = Auth::user()->roles()->first();

        // Basic static content
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();
        $blogs_count = Blog::count();

        // Dynamic data based on user type
        if ($user->name === 'super-admin') {
            $dashboardData = $this->getSuperAdminData();
            $view = 'admin.dashboard';
        } elseif ($user->name === 'company') {
            $dashboardData = $this->getCompanyUserData();
            $view = 'admin.dashboard';
        } else {
            $dashboardData = [];
            $view = 'admin.dashboard';
        }

        // Merge all data
        $data = array_merge([
            'favicon' => $favicon,
            'panel_image' => $panel_image,
            'blogs_count' => $blogs_count,
            'auth_user' => Auth::user(),
        ], $dashboardData);


        return view($view, $data);
    }

    /**
     * Get comprehensive data for Super Admin
     */
    protected function getSuperAdminData()
    {
        $now = Carbon::now();
        $today = Carbon::today();
        $thisWeekStart = $now->copy()->startOfWeek();
        $lastWeekStart = $now->copy()->subWeek()->startOfWeek();
        $lastWeekEnd = $now->copy()->subWeek()->endOfWeek();
        $thisMonthStart = $now->copy()->startOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();

        // System-wide statistics
        $stats = [
            'total_clients' => ClientProfile::count(),
            'total_employees' => Employee::count(),
            'total_orders' => QuestOrder::count(),
            'total_results' => ResultRecording::count(),
            'today_orders' => QuestOrder::whereDate('created_at', $today)->count(),
            'today_results' => ResultRecording::whereDate('created_at', $today)->count(),
            'total_laboratories' => Laboratory::count(),
            'total_mros' => MRO::count(),
        ];

        // Growth percentages (this week vs last week)
        $thisWeekOrders = QuestOrder::whereBetween('created_at', [$thisWeekStart, $now])->count();
        $lastWeekOrders = QuestOrder::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->count();
        $ordersGrowth = $lastWeekOrders > 0 ? round((($thisWeekOrders - $lastWeekOrders) / $lastWeekOrders) * 100, 1) : 0;

        $thisMonthClients = ClientProfile::whereBetween('created_at', [$thisMonthStart, $now])->count();
        $lastMonthClients = ClientProfile::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $clientsGrowth = $lastMonthClients > 0 ? round((($thisMonthClients - $lastMonthClients) / $lastMonthClients) * 100, 1) : 0;

        $thisMonthResults = ResultRecording::whereBetween('created_at', [$thisMonthStart, $now])->count();
        $lastMonthResults = ResultRecording::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $resultsGrowth = $lastMonthResults > 0 ? round((($thisMonthResults - $lastMonthResults) / $lastMonthResults) * 100, 1) : 0;

        $growth = [
            'orders' => $ordersGrowth,
            'clients' => $clientsGrowth,
            'results' => $resultsGrowth,
            'this_week_orders' => $thisWeekOrders,
            'this_month_clients' => $thisMonthClients,
            'this_month_results' => $thisMonthResults,
        ];

        // Order status distribution (for doughnut chart)
        $orderStatusDistribution = QuestOrder::selectRaw('order_status, count(*) as count')
            ->whereNotNull('order_status')
            ->groupBy('order_status')
            ->get()
            ->pluck('count', 'order_status')
            ->toArray();

        // Order result distribution
        $orderResultDistribution = QuestOrder::selectRaw(
            'CASE WHEN order_result IS NOT NULL THEN order_result ELSE "Pending" END as result_type, count(*) as count'
        )
            ->groupBy('result_type')
            ->get()
            ->pluck('count', 'result_type')
            ->toArray();

        // Weekly daily orders (last 7 days for bar chart)
        $weeklyOrders = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $weeklyOrders[$date->format('D')] = QuestOrder::whereDate('created_at', $date)->count();
        }

        // Recent activities
        $recentActivities = QuestOrder::with(['user'])
            ->latest()
            ->take(8)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'reference_id' => $order->client_reference_id,
                    'status' => $order->order_status,
                    'result' => $order->order_result,
                    'created_at' => $order->created_at->format('M d, Y H:i'),
                    'time_ago' => $order->created_at->diffForHumans(),
                    'client_name' => optional($order->user)->name,
                    'donor_name' => trim($order->first_name . ' ' . $order->last_name),
                    'portfolio_name' => $order->portfolio_name,
                ];
            });

        // Top clients by order count
        $topClients = ClientProfile::withCount(['orders', 'employees', 'resultRecordings'])
            ->orderBy('orders_count', 'desc')
            ->take(5)
            ->get();

        // Monthly trends
        $monthlyTrends = $this->getMonthlyTrends();

        // Test results status distribution
        $testStatusDistribution = ResultRecording::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Recent results
        $recentResults = ResultRecording::with(['employee', 'laboratory', 'mro', 'clientProfile'])
            ->latest()
            ->take(5)
            ->get();

        // Reason for test distribution
        $reasonForTestDistribution = ResultRecording::selectRaw('reason_for_test, count(*) as count')
            ->whereNotNull('reason_for_test')
            ->where('reason_for_test', '!=', '')
            ->groupBy('reason_for_test')
            ->orderByDesc('count')
            ->take(6)
            ->get()
            ->pluck('count', 'reason_for_test')
            ->toArray();

        // Payment summary stats
        $paymentStats = [
            'total_revenue'   => Payment::where('status', 'succeeded')->sum('amount'),
            'today_revenue'   => Payment::where('status', 'succeeded')->whereDate('paid_at', today())->sum('amount'),
            'succeeded_count' => Payment::where('status', 'succeeded')->count(),
            'pending_count'   => Payment::whereIn('status', ['processing', 'requires_payment_method'])->count(),
            'refunded_count'  => Payment::where('status', 'refunded')->count(),
        ];

        $recentPayments = Payment::latest()->take(5)->get();

        return [
            'stats'                       => $stats,
            'growth'                      => $growth,
            'order_status_distribution'   => $orderStatusDistribution,
            'order_result_distribution'   => $orderResultDistribution,
            'weekly_orders'               => $weeklyOrders,
            'recent_activities'           => $recentActivities,
            'top_clients'                 => $topClients,
            'monthly_trends'              => $monthlyTrends,
            'test_status_distribution'    => $testStatusDistribution,
            'reason_for_test_distribution'=> $reasonForTestDistribution,
            'recent_results'              => $recentResults,
            'payment_stats'               => $paymentStats,
            'recent_payments'             => $recentPayments,
            'user_type'                   => 'super-admin',
        ];
    }

    /**
     * Get company-specific data for Company User
     */
    protected function getCompanyUserData()
    {
        $userId = Auth::user()->id;
        $clientProfile = ClientProfile::where('user_id', $userId)->first();

        if (!$clientProfile) {
            return [
                'error' => 'No company profile found',
                'user_type' => 'company',
            ];
        }

        $companyId = $clientProfile->id;
        $now = Carbon::now();
        $today = Carbon::today();
        $thisWeekStart = $now->copy()->startOfWeek();
        $lastWeekStart = $now->copy()->subWeek()->startOfWeek();
        $lastWeekEnd = $now->copy()->subWeek()->endOfWeek();

        // Company-specific statistics
        $stats = [
            'my_employees' => Employee::where('client_profile_id', $companyId)->count(),
            'my_orders' => QuestOrder::where('user_id', $userId)->count(),
            'my_results' => ResultRecording::where('company_id', $companyId)->count(),
            'pending_orders' => QuestOrder::where('order_status', 'PENDING')
                ->where('user_id', $userId)
                ->count(),
            'completed_tests' => ResultRecording::where('status', 'completed')
                ->where('company_id', $companyId)
                ->count(),
            'today_orders' => QuestOrder::where('user_id', $userId)
                ->whereDate('created_at', $today)
                ->count(),
        ];

        // Growth
        $thisWeekOrders = QuestOrder::where('user_id', $userId)
            ->whereBetween('created_at', [$thisWeekStart, $now])->count();
        $lastWeekOrders = QuestOrder::where('user_id', $userId)
            ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->count();
        $ordersGrowth = $lastWeekOrders > 0 ? round((($thisWeekOrders - $lastWeekOrders) / $lastWeekOrders) * 100, 1) : 0;

        $growth = [
            'orders' => $ordersGrowth,
            'this_week_orders' => $thisWeekOrders,
        ];

        // Company order status distribution
        $orderStatusDistribution = QuestOrder::where('user_id', $userId)
            ->selectRaw('order_status, count(*) as count')
            ->whereNotNull('order_status')
            ->groupBy('order_status')
            ->get()
            ->pluck('count', 'order_status')
            ->toArray();

        // Weekly daily orders
        $weeklyOrders = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $weeklyOrders[$date->format('D')] = QuestOrder::where('user_id', $userId)
                ->whereDate('created_at', $date)->count();
        }

        // Recent company activities
        $recentActivities = QuestOrder::with(['user'])
            ->where('user_id', $userId)
            ->latest()
            ->take(8)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'reference_id' => $order->client_reference_id,
                    'status' => $order->order_status,
                    'result' => $order->order_result,
                    'created_at' => $order->created_at->format('M d, Y H:i'),
                    'time_ago' => $order->created_at->diffForHumans(),
                    'client_name' => optional($order->user)->name,
                    'donor_name' => trim($order->first_name . ' ' . $order->last_name),
                    'portfolio_name' => $order->portfolio_name,
                ];
            });

        // Company employees
        $companyEmployees = Employee::with(['user'])
            ->where('client_profile_id', $companyId)
            ->latest()
            ->take(8)
            ->get();

        // Employee status breakdown
        $employeeStatusBreakdown = Employee::where('client_profile_id', $companyId)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Recent test results
        $recentResults = ResultRecording::with(['employee', 'laboratory', 'mro'])
            ->where('company_id', $companyId)
            ->latest()
            ->take(8)
            ->get();

        // Test results status distribution
        $testStatusDistribution = ResultRecording::where('company_id', $companyId)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        return [
            'stats' => $stats,
            'growth' => $growth,
            'order_status_distribution' => $orderStatusDistribution,
            'weekly_orders' => $weeklyOrders,
            'recent_activities' => $recentActivities,
            'company_employees' => $companyEmployees,
            'employee_status_breakdown' => $employeeStatusBreakdown,
            'recent_results' => $recentResults,
            'test_status_distribution' => $testStatusDistribution,
            'company_profile' => $clientProfile,
            'user_type' => 'company',
        ];
    }

    /**
     * Get monthly trends for charts
     */
    protected function getMonthlyTrends()
    {
        return QuestOrder::selectRaw(
            'DATE_FORMAT(created_at, "%Y-%m") as month,
            COUNT(*) as orders_count,
            SUM(CASE WHEN order_result IS NOT NULL THEN 1 ELSE 0 END) as completed_count'
        )
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->month => [
                        'orders' => $item->orders_count,
                        'completed' => $item->completed_count,
                    ]
                ];
            });
    }
}
