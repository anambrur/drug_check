<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Blog;
use App\Models\Admin\ClientProfile;
use App\Models\Admin\Employee;
use App\Models\Admin\Favicon;
use App\Models\Admin\PanelImage;
use App\Models\Admin\QuestOrder;
use App\Models\Admin\ResultRecording;
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
        $blogs_count = Blog::all()->count();

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
        ], $dashboardData);


        return view($view, $data);
    }

    /**
     * Get comprehensive data for Super Admin
     */
    protected function getSuperAdminData()
    {
        // System-wide statistics
        $stats = [
            'total_clients' => ClientProfile::count(),
            'total_employees' => Employee::count(),
            'total_orders' => QuestOrder::count(),
            'total_results' => ResultRecording::count(),
            'today_orders' => QuestOrder::whereDate('created_at', Carbon::today())->count(),
            'today_results' => ResultRecording::whereDate('created_at', Carbon::today())->count(),
        ];

        // Recent activities
        $recentActivities = QuestOrder::with(['user'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'reference_id' => $order->client_reference_id,
                    'status' => $order->order_status,
                    'created_at' => $order->created_at->format('M d, Y H:i'),
                    'client_name' => optional($order->user)->name,
                ];
            });

        // Top clients by order count
        $topClients = ClientProfile::withCount(['orders', 'employees'])
            ->orderBy('orders_count', 'desc')
            ->take(5)
            ->get();

        // Monthly trends
        $monthlyTrends = $this->getMonthlyTrends();

        // Test results status distribution
        $testStatusDistribution = ResultRecording::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return [
            'stats' => $stats,
            'recent_activities' => $recentActivities,
            'top_clients' => $topClients,
            'monthly_trends' => $monthlyTrends,
            'test_status_distribution' => $testStatusDistribution,
            'user_type' => 'super-admin',
        ];
    }

    /**
     * Get company-specific data for Company User
     */
    protected function getCompanyUserData()
    {
        // Get company ID through employee relationship
        $userId = Auth::user()->id;
        $clientProfile = ClientProfile::where('user_id', $userId)->first();

        if ( !$clientProfile) {
            return [
                'error' => 'No company profile found',
                'user_type' => 'company',
            ];
        }

        $companyId = $clientProfile->id;

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
                ->whereDate('created_at', Carbon::today())
                ->count(),
        ];

        // Recent company activities
        $recentActivities = QuestOrder::with(['user'])
            ->where('user_id', $userId)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'reference_id' => $order->client_reference_id,
                    'status' => $order->order_status,
                    'created_at' => $order->created_at->format('M d, Y H:i'),
                    'client_name' => optional($order->user)->name,
                ];
            });

        // Company employees
        $companyEmployees = Employee::with(['user'])
            ->where('client_profile_id', $companyId)
            ->latest()
            ->take(10)
            ->get();

        // Recent test results
        $recentResults = ResultRecording::with(['employee', 'laboratory', 'mro'])
            ->where('company_id', $companyId)
            ->latest()
            ->take(10)
            ->get();


        return [
            'stats' => $stats,
            'recent_activities' => $recentActivities,
            'company_employees' => $companyEmployees,
            'recent_results' => $recentResults,
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
                return [$item->month => [
                    'orders' => $item->orders_count,
                    'completed' => $item->completed_count,
                ]];
            });
    }
}
