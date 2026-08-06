<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Favicon;
use App\Models\Admin\PanelImage;
use App\Models\Admin\QuestOrder;
use App\Models\ConsortiumEnrollment;
use App\Models\PortfolioTestApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class OrdersAdminController extends Controller
{
    public function dotTesting(): View
    {
        return $this->applicationsIndex('dot', 'DOT Testing Orders', 'admin.orders.dot-testing');
    }

    public function nonDotTesting(): View
    {
        return $this->applicationsIndex('non_dot', 'Non-DOT Testing Orders', 'admin.orders.non-dot-testing');
    }

    public function applicationsData(Request $request, string $type): JsonResponse
    {
        abort_unless(in_array($type, ['dot', 'non_dot'], true), 404);

        $query = PortfolioTestApplication::query()
            ->with([
                'portfolio:id,title',
                'clientProfile:id,user_id,company_name',
                'user.clientProfile:id,user_id,company_name',
                'employee.clientProfile:id,user_id,company_name',
            ])
            ->where('test_type', $type)
            ->select('portfolio_test_applications.*')
            ->selectSub(function ($sub) {
                $sub->from('client_profiles')
                    ->join('employees', 'employees.client_profile_id', '=', 'client_profiles.id')
                    ->whereColumn('employees.id', 'portfolio_test_applications.employee_id')
                    ->select('client_profiles.company_name')
                    ->limit(1);
            }, 'employee_company_name')
            ->selectSub(function ($sub) {
                $sub->from('client_profiles')
                    ->whereColumn('client_profiles.user_id', 'portfolio_test_applications.user_id')
                    ->select('client_profiles.company_name')
                    ->limit(1);
            }, 'user_company_name');

        if ($paymentStatus = $request->input('payment_status')) {
            $query->where('payment_status', $paymentStatus);
        }

        if ($questStatus = $request->input('quest_status')) {
            $query->where('quest_submission_status', $questStatus);
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('applicant', function (PortfolioTestApplication $row) {
                $name = e($row->applicantDisplayName());
                $email = e($row->email ?: '—');
                $phone = e($row->phone ?: '');

                return '<div class="font-weight-bold">' . $name . '</div>'
                    . '<small class="text-muted d-block">' . $email . '</small>'
                    . ($phone !== '' ? '<small class="text-muted">' . $phone . '</small>' : '');
            })
            ->addColumn('company', function (PortfolioTestApplication $row) {
                $company = $row->company_name
                    ?: $row->employee_company_name
                    ?: $row->user_company_name
                    ?: $row->resolveCompanyName();

                return e(filled($company) && $company !== '—' ? $company : '—');
            })
            ->addColumn('test_name', fn (PortfolioTestApplication $row) => e($row->portfolio->title ?? '—'))
            ->addColumn('amount_display', fn (PortfolioTestApplication $row) => '<span class="font-weight-bold">' . e($row->formatted_amount) . '</span>')
            ->addColumn('payment_badge', function (PortfolioTestApplication $row) {
                if ($row->payment_status === 'completed') {
                    return '<span class="badge badge-pill badge-success">Completed</span>';
                }

                return '<span class="badge badge-pill badge-warning">' . e(ucfirst($row->payment_status ?? 'pending')) . '</span>';
            })
            ->addColumn('quest_badge', function (PortfolioTestApplication $row) {
                return match ($row->quest_submission_status) {
                    'submitted' => '<span class="badge badge-pill badge-success">Submitted</span>',
                    'failed' => '<span class="badge badge-pill badge-danger">Failed</span>',
                    default => '<span class="badge badge-pill badge-secondary">' . e(ucfirst($row->quest_submission_status ?? 'pending')) . '</span>',
                };
            })
            ->addColumn('status_badge', function (PortfolioTestApplication $row) {
                $status = $row->status ?: '—';
                $lower = strtolower($status);
                $class = match (true) {
                    str_contains($lower, 'submitted') => 'success',
                    str_contains($lower, 'paid'), str_contains($lower, 'completed') => 'info',
                    str_contains($lower, 'pending') => 'warning',
                    str_contains($lower, 'fail'), str_contains($lower, 'cancel') => 'danger',
                    default => 'secondary',
                };

                return '<span class="badge badge-pill badge-' . $class . '">' . e($status) . '</span>';
            })
            ->addColumn('guest_label', fn (PortfolioTestApplication $row) => $row->is_guest
                ? '<span class="badge badge-pill badge-info">Guest</span>'
                : '<span class="badge badge-pill badge-light">Account</span>')
            ->addColumn('created_us', fn (PortfolioTestApplication $row) => $row->created_at?->format('m/d/Y g:i A') ?? '—')
            ->addColumn('action', function (PortfolioTestApplication $row) {
                $url = route('admin.orders.applications.show', $row->id);

                return '<a href="' . $url . '" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i> View</a>';
            })
            ->filterColumn('applicant', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%")
                        ->orWhere('middle_name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('phone', 'like', "%{$keyword}%")
                        ->orWhere('employee_name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('company', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('portfolio_test_applications.company_name', 'like', "%{$keyword}%")
                        ->orWhereHas('clientProfile', function ($profile) use ($keyword) {
                            $profile->where('company_name', 'like', "%{$keyword}%");
                        })
                        ->orWhereHas('user.clientProfile', function ($profile) use ($keyword) {
                            $profile->where('company_name', 'like', "%{$keyword}%");
                        })
                        ->orWhereHas('employee.clientProfile', function ($profile) use ($keyword) {
                            $profile->where('company_name', 'like', "%{$keyword}%");
                        });
                });
            })
            ->filterColumn('test_name', function ($query, $keyword) {
                $query->whereHas('portfolio', function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['applicant', 'amount_display', 'payment_badge', 'quest_badge', 'status_badge', 'guest_label', 'action'])
            ->toJson();
    }

    public function showApplication(int $id): View
    {
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();

        $application = PortfolioTestApplication::with([
            'portfolio',
            'user.clientProfile',
            'clientProfile',
            'employee.clientProfile',
        ])->findOrFail($id);

        $questOrder = null;
        if ($application->quest_order_id) {
            $questOrder = QuestOrder::where('quest_order_id', $application->quest_order_id)->first();
        }

        $listRoute = $application->isDot()
            ? 'admin.orders.dot-testing'
            : 'admin.orders.non-dot-testing';

        view()->share('ordersActiveType', $application->test_type);

        return view('admin.orders.applications.show', compact(
            'favicon',
            'panel_image',
            'application',
            'questOrder',
            'listRoute'
        ));
    }

    public function consortium(): View
    {
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();
        $stats = $this->consortiumStats();

        return view('admin.orders.consortium.index', compact('favicon', 'panel_image', 'stats'));
    }

    public function consortiumData(Request $request): JsonResponse
    {
        $query = ConsortiumEnrollment::query()->select('consortium_enrollments.*');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('company', function (ConsortiumEnrollment $row) {
                $html = '<div class="font-weight-bold">' . e($row->company_name) . '</div>';
                if ($row->dba_name) {
                    $html .= '<small class="text-muted">DBA: ' . e($row->dba_name) . '</small>';
                }

                return $html;
            })
            ->addColumn('contact', function (ConsortiumEnrollment $row) {
                return '<div>' . e(trim($row->first_name . ' ' . $row->last_name)) . '</div>'
                    . '<small class="text-muted">' . e($row->email) . '</small>';
            })
            ->addColumn('plan', fn (ConsortiumEnrollment $row) => e($row->selected_plan ?: '—'))
            ->addColumn('drivers', fn (ConsortiumEnrollment $row) => e((string) $row->driver_count))
            ->addColumn('amount_display', fn (ConsortiumEnrollment $row) => '<span class="font-weight-bold text-primary">' . e($row->formatted_amount) . '</span>')
            ->addColumn('status_badge', function (ConsortiumEnrollment $row) {
                $map = [
                    'Active' => 'success',
                    'Payment Completed' => 'info',
                    'Under Review' => 'warning',
                    'Credentials Sent' => 'primary',
                    'Contacted' => 'secondary',
                    'Pending Payment' => 'danger',
                    'Cancelled' => 'dark',
                ];
                $class = $map[$row->status] ?? 'secondary';

                return '<span class="badge badge-pill badge-' . $class . '">' . e($row->status) . '</span>';
            })
            ->addColumn('created_us', fn (ConsortiumEnrollment $row) => $row->created_at?->format('m/d/Y g:i A') ?? '—')
            ->addColumn('action', function (ConsortiumEnrollment $row) {
                $url = route('consortium-enrollments.show', $row->id);

                return '<a href="' . $url . '" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i> View</a>';
            })
            ->filterColumn('company', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('company_name', 'like', "%{$keyword}%")
                        ->orWhere('dba_name', 'like', "%{$keyword}%")
                        ->orWhere('dot_number', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('contact', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('phone', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['company', 'contact', 'amount_display', 'status_badge', 'action'])
            ->toJson();
    }

    public function showConsortium(int $id): View
    {
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();
        $enrollment = ConsortiumEnrollment::findOrFail($id);

        return view('admin.orders.consortium.show', compact('favicon', 'panel_image', 'enrollment'));
    }

    public function updateConsortiumStatus(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Pending Payment,Payment Completed,Under Review,Contacted,Credentials Sent,Active,Cancelled',
        ]);

        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');

            return back();
        }

        $enrollment = ConsortiumEnrollment::findOrFail($id);
        $oldStatus = $enrollment->status;
        $newStatus = $request->input('status');

        $enrollment->update(['status' => $newStatus]);

        $timestamp = now()->format('m/d/Y g:i A');
        $statusLog = "\n[System - {$timestamp}]: Status updated from '{$oldStatus}' to '{$newStatus}'.";
        $enrollment->update([
            'internal_notes' => $enrollment->internal_notes . $statusLog,
        ]);

        toastr()->success('Enrollment status updated successfully.', 'content.success');

        return back();
    }

    public function updateConsortiumNotes(Request $request, int $id)
    {
        $enrollment = ConsortiumEnrollment::findOrFail($id);

        $newNote = $request->input('note');
        if ($newNote) {
            $timestamp = now()->format('m/d/Y g:i A');
            $author = auth()->user()?->name ?? 'Admin';
            $logEntry = "\n[{$author} - {$timestamp}]: {$newNote}";

            $enrollment->update([
                'internal_notes' => $enrollment->internal_notes . $logEntry,
            ]);

            toastr()->success('Note added successfully.', 'content.success');
        }

        return back();
    }

    public function clearingHouse(): View
    {
        return $this->comingSoon(
            'Clearing House Orders',
            'Clearing House order management is not available yet. It will appear here when the feature is ready.'
        );
    }

    public function dotSupervisorTraining(): View
    {
        return $this->comingSoon(
            'DOT Supervisor Training Orders',
            'DOT Supervisor Training order management is not available yet. It will appear here when the feature is ready.'
        );
    }

    private function applicationsIndex(string $testType, string $pageTitle, string $indexRoute): View
    {
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();
        $stats = $this->applicationStats($testType);
        $dataUrl = route('admin.orders.applications.data', $testType);

        return view('admin.orders.applications.index', compact(
            'favicon',
            'panel_image',
            'pageTitle',
            'testType',
            'indexRoute',
            'stats',
            'dataUrl'
        ));
    }

    private function applicationStats(string $testType): array
    {
        $base = PortfolioTestApplication::query()->where('test_type', $testType);

        return [
            'total' => (clone $base)->count(),
            'paid' => (clone $base)->where('payment_status', 'completed')->count(),
            'pending_payment' => (clone $base)->where('payment_status', 'pending')->count(),
            'quest_submitted' => (clone $base)->where('quest_submission_status', 'submitted')->count(),
            'quest_failed' => (clone $base)->where('quest_submission_status', 'failed')->count(),
            'revenue' => (clone $base)->where('payment_status', 'completed')->sum('amount'),
        ];
    }

    private function consortiumStats(): array
    {
        return [
            'total' => ConsortiumEnrollment::count(),
            'active' => ConsortiumEnrollment::where('status', 'Active')->count(),
            'pending_payment' => ConsortiumEnrollment::where('status', 'Pending Payment')->count(),
            'payment_completed' => ConsortiumEnrollment::where('status', 'Payment Completed')->count(),
            'under_review' => ConsortiumEnrollment::where('status', 'Under Review')->count(),
            'revenue' => ConsortiumEnrollment::where('payment_status', 'completed')->sum('amount'),
        ];
    }

    private function comingSoon(string $pageTitle, string $message): View
    {
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();

        return view('admin.orders.coming-soon', compact('favicon', 'panel_image', 'pageTitle', 'message'));
    }
}
