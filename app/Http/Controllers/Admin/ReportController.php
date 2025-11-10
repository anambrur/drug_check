<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Admin\Employee;
use App\Models\Admin\HeaderImage;
use App\Models\Admin\ClientProfile;
use App\Http\Controllers\Controller;
use App\Models\Admin\ResultRecording;
use App\Models\Admin\ContactInfoWidget;

class ReportController extends Controller
{
    public function MISReport(Request $request)
    {
        // If no company selected, show the form
        if (!$request->has('company_id')) {
            $companies = ClientProfile::orderBy('company_name')->get();
            $totalDrugTests = ResultRecording::whereYear('date_of_collection', date('Y'))->count();

            return view('admin.reports.mis-report-form', compact('companies', 'totalDrugTests'));
        }

        // Get filter parameters
        $companyId = $request->input('company_id');
        $year = $request->input('year', date('Y'));
        $startDate = $request->input('start_date', "$year-01-01");
        $endDate = $request->input('end_date', "$year-12-31");

        // Get company information
        $company = ClientProfile::find($companyId);

        if (!$company) {
            return back()->with('error', 'Company not found');
        }

        // Get all test results for the company within date range
        $results = ResultRecording::where('company_id', $companyId)
            ->whereBetween('date_of_collection', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->with(['resultPanel', 'employee'])
            ->get();

        // Count unique employees tested
        $totalEmployees = $results->pluck('employee_id')->unique()->count();

        // Initialize drug testing data structure
        $drugTestingData = [
            'Pre-employment' => ['total' => 0, 'negative' => 0, 'positive' => 0, 'marijuana' => 0, 'cocaine' => 0, 'pcp' => 0, 'opiates' => 0, 'amphetamines' => 0, 'refusal' => 0],
            'Random' => ['total' => 0, 'negative' => 0, 'positive' => 0, 'marijuana' => 0, 'cocaine' => 0, 'pcp' => 0, 'opiates' => 0, 'amphetamines' => 0, 'refusal' => 0],
            'Post-accident' => ['total' => 0, 'negative' => 0, 'positive' => 0, 'marijuana' => 0, 'cocaine' => 0, 'pcp' => 0, 'opiates' => 0, 'amphetamines' => 0, 'refusal' => 0],
            'Reasonable Suspicion/Cause' => ['total' => 0, 'negative' => 0, 'positive' => 0, 'marijuana' => 0, 'cocaine' => 0, 'pcp' => 0, 'opiates' => 0, 'amphetamines' => 0, 'refusal' => 0],
            'Return to Duty' => ['total' => 0, 'negative' => 0, 'positive' => 0, 'marijuana' => 0, 'cocaine' => 0, 'pcp' => 0, 'opiates' => 0, 'amphetamines' => 0, 'refusal' => 0],
            'Follow-up' => ['total' => 0, 'negative' => 0, 'positive' => 0, 'marijuana' => 0, 'cocaine' => 0, 'pcp' => 0, 'opiates' => 0, 'amphetamines' => 0, 'refusal' => 0],
        ];

        // Process each result
        foreach ($results as $result) {
            $testType = $this->normalizeTestType($result->reason_for_test);

            if (!isset($drugTestingData[$testType])) {
                continue;
            }

            $drugTestingData[$testType]['total']++;

            // Handle refusals
            if ($result->status === 'refused') {
                $drugTestingData[$testType]['refusal']++;
                continue;
            }

            // Check if test is negative or positive
            $hasPositive = false;
            $positiveForDrugs = [
                'marijuana' => false,
                'cocaine' => false,
                'pcp' => false,
                'opiates' => false,
                'amphetamines' => false
            ];

            foreach ($result->resultPanel as $panel) {
                if (strtolower($panel->result) === 'positive') {
                    $hasPositive = true;

                    // Map drug codes to categories
                    $drugCode = strtoupper($panel->drug_code);

                    if (in_array($drugCode, ['THC', 'MARIJUANA'])) {
                        $positiveForDrugs['marijuana'] = true;
                    } elseif (in_array($drugCode, ['COC', 'COCAINE'])) {
                        $positiveForDrugs['cocaine'] = true;
                    } elseif (in_array($drugCode, ['PCP', 'PHENCYCLIDINE'])) {
                        $positiveForDrugs['pcp'] = true;
                    } elseif (in_array($drugCode, ['6-AM', 'HYC/HYM', 'OXY', 'OPIATES'])) {
                        $positiveForDrugs['opiates'] = true;
                    } elseif (in_array($drugCode, ['AMP/MAMP', 'AMP', 'MAMP', 'MDMA', 'AMPHETAMINES'])) {
                        $positiveForDrugs['amphetamines'] = true;
                    }
                }
            }

            if ($hasPositive) {
                $drugTestingData[$testType]['positive']++;

                // Count specific drug positives
                if ($positiveForDrugs['marijuana']) $drugTestingData[$testType]['marijuana']++;
                if ($positiveForDrugs['cocaine']) $drugTestingData[$testType]['cocaine']++;
                if ($positiveForDrugs['pcp']) $drugTestingData[$testType]['pcp']++;
                if ($positiveForDrugs['opiates']) $drugTestingData[$testType]['opiates']++;
                if ($positiveForDrugs['amphetamines']) $drugTestingData[$testType]['amphetamines']++;
            } else {
                $drugTestingData[$testType]['negative']++;
            }
        }

        // Calculate totals
        $totals = [
            'total' => 0,
            'negative' => 0,
            'positive' => 0,
            'marijuana' => 0,
            'cocaine' => 0,
            'pcp' => 0,
            'opiates' => 0,
            'amphetamines' => 0,
            'refusal' => 0
        ];

        foreach ($drugTestingData as $data) {
            $totals['total'] += $data['total'];
            $totals['negative'] += $data['negative'];
            $totals['positive'] += $data['positive'];
            $totals['marijuana'] += $data['marijuana'];
            $totals['cocaine'] += $data['cocaine'];
            $totals['pcp'] += $data['pcp'];
            $totals['opiates'] += $data['opiates'];
            $totals['amphetamines'] += $data['amphetamines'];
            $totals['refusal'] += $data['refusal'];
        }

        // Get detailed test list
        $testList = $results->map(function ($result) {
            return [
                'collection_date' => Carbon::parse($result->date_of_collection)->format('n/j/Y'),
                'result_id' => str_pad($result->id, 6, '0', STR_PAD_LEFT),
                'donor_name' => $result->employee->name ?? 'N/A',
                'donor_id' => $result->employee->employee_id ?? 'N/A',
                'test_type' => $this->normalizeTestType($result->reason_for_test),
                'status' => ucfirst($result->status)
            ];
        });

        // Prepare data for view/PDF
        $data = [
            'company' => $company,
            'year' => $year,
            'totalEmployees' => $totalEmployees,
            'drugTestingData' => $drugTestingData,
            'totals' => $totals,
            'testList' => $testList,
            'certifiedDate' => now()->format('m/d/Y'),
            'ctpaName' => 'Skyros Drug Checks Inc',
            'ctpaPhone' => '1-(800)-690-9034'
        ];

        // Check if download is requested
        if ($request->has('download')) {
            $pdf = PDF::loadView('admin.reports.mis-report-pdf', $data);
            $pdf->setPaper('letter', 'portrait');
            return $pdf->download('MIS_Report_' . $company->company_name . '_' . $year . '.pdf');
        }

        // Show in browser by default
        return view('admin.reports.mis-report-pdf', $data);
    }

    public function MISReportDownload(Request $request)
    {
        // This method handles the download request
        $request->merge(['download' => true]);
        return $this->MISReport($request);
    }

    private function normalizeTestType($testType)
    {
        $testType = trim(strtolower($testType));

        $mapping = [
            'pre employment' => 'Pre-employment',
            'pre-employment' => 'Pre-employment',
            'preemployment' => 'Pre-employment',
            'random' => 'Random',
            'post accident' => 'Post-accident',
            'post-accident' => 'Post-accident',
            'postaccident' => 'Post-accident',
            'reasonable suspicion' => 'Reasonable Suspicion/Cause',
            'reasonable cause' => 'Reasonable Suspicion/Cause',
            'return to duty' => 'Return to Duty',
            'return-to-duty' => 'Return to Duty',
            'follow up' => 'Follow-up',
            'follow-up' => 'Follow-up',
            'followup' => 'Follow-up',
        ];

        return $mapping[$testType] ?? 'Random';
    }

    public function consortiumCompanyReport(Request $request)
    {
        $companies = ClientProfile::all();
        $contact_info_widget = ContactInfoWidget::first();
        $header_image = HeaderImage::first();

        return view('admin.reports.consortium-company-report', compact('companies', 'contact_info_widget', 'header_image'));
    }
    public function consortiumEmployeeReport(Request $request)
    {
        $companies = ClientProfile::with('employees')->get();
        $contact_info_widget = ContactInfoWidget::first();
        $header_image = HeaderImage::first();

        return view('admin.reports.consortium-employee-report', compact('companies', 'contact_info_widget', 'header_image'));
    }
}
