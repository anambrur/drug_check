<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ImportCollectionSitesJob;
use App\Models\Admin\CollectionSite;
use App\Services\QuestCollectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class QuestSyncController extends Controller
{
    protected $questCollectionService;

    public function __construct(QuestCollectionService $questCollectionService)
    {
        $this->questCollectionService = $questCollectionService;
    }

    /**
     * Show sync dashboard
     */
    public function dashboard()
    {
        return view('admin.quest-site.dashboard', [
            'sitesCount' => 0,
            'lastSync' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Perform full sync
     */
    public function fullSync(Request $request)
    {
        try {
            $request->validate([
                'confirm' => 'required|accepted',
            ]);

            Log::info('Full sync requested, but Firebase integration has been removed.');

            return redirect()->route('quest-site.dashboard')
                ->with('error', 'Full sync is not available because Firebase integration was removed.');
        } catch (\Exception $e) {
            Log::error('Manual full sync failed: ' . $e->getMessage());

            return redirect()->route('quest-site.dashboard')
                ->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Check sync status
     */
    public function syncStatus()
    {
        $inProgress = cache()->get('sync_in_progress', false);
        $stage = cache()->get('sync_stage', 'not_started');
        $startTime = cache()->get('sync_start_time');
        $lastResult = cache()->get('last_sync_result');

        $elapsedTime = $startTime ? round(microtime(true) - $startTime, 2) : 0;

        return response()->json([
            'in_progress' => $inProgress,
            'stage' => $stage,
            'elapsed_time' => $elapsedTime,
            'last_result' => $lastResult,
        ]);
    }

    /**
     * Perform incremental sync
     */
    public function incrementalSync(Request $request)
    {
        try {
            $request->validate([
                'since_date' => 'required|date|before_or_equal:today',
            ]);

            $sinceDate = $request->input('since_date');

            Log::info('Incremental sync requested since ' . $sinceDate . ', but Firebase integration has been removed.');

            return redirect()->route('quest-site.dashboard')
                ->with('error', 'Incremental sync is not available because Firebase integration was removed.');
        } catch (\Exception $e) {
            Log::error('Manual incremental sync failed: ' . $e->getMessage());

            return redirect()->route('quest-site.dashboard')
                ->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Clear all data from Firebase
     */
    public function clearData(Request $request)
    {
        try {
            Validator::make($request->all(), [
                'confirm' => 'required|accepted',
            ])->validate();

            Log::info('Clear data requested, but Firebase integration has been removed.');

            return redirect()->route('quest-site.dashboard')
                ->with('error', 'Clear data is not available because Firebase integration was removed.');
        } catch (\Exception $e) {
            Log::error('Clear data failed: ' . $e->getMessage());

            return redirect()->route('quest-site.dashboard')
                ->with('error', 'Clear data failed: ' . $e->getMessage());
        }
    }

    /**
     * View sites in Firebase
     */
    public function viewSites()
    {
        try {
            $sites = [];

            return view('admin.quest-site.view-sites', [
                'sites' => $sites,
                'sitesCount' => 0,
            ]);
        } catch (\Exception $e) {
            Log::error('View sites failed: ' . $e->getMessage());

            return redirect()->route('quest-site.dashboard')
                ->with('error', 'Failed to retrieve sites: ' . $e->getMessage());
        }
    }

    /**
     * Test connection to Quest Collection Site API
     */
    public function testConnection()
    {
        try {
            $result = $this->questCollectionService->testConnection();

            if ($result['success']) {
                return redirect()->route('quest-site.dashboard')
                    ->with('success', 'Connection test successful: ' . $result['message']);
            }

            return redirect()->route('quest-site.dashboard')
                ->with('error', 'Connection test failed: ' . $result['message']);
        } catch (\Exception $e) {
            return redirect()->route('quest-site.dashboard')
                ->with('error', 'Connection test failed: ' . $e->getMessage());
        }
    }

    public function collectionSiteInsert(Request $request)
    {
        $sites = CollectionSite::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $like = '%' . $request->input('search') . '%';

                $query->where(function ($q) use ($like) {
                    $q->where('collection_site_code', 'LIKE', $like)
                        ->orWhere('name', 'LIKE', $like)
                        ->orWhere('city', 'LIKE', $like)
                        ->orWhere('state', 'LIKE', $like)
                        ->orWhere('zip_code', 'LIKE', $like);
                });
            })
            ->when($request->filled('state'), fn ($query) => $query->where('state', $request->input('state')))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        $totalSites = CollectionSite::count();
        $states = CollectionSite::query()
            ->whereNotNull('state')
            ->where('state', '!=', '')
            ->distinct()
            ->orderBy('state')
            ->pluck('state');

        $importInProgress = cache()->get('collection_site_import_in_progress', false);
        $importStats = cache()->get('collection_site_import_stats');
        $importError = cache()->get('collection_site_import_error');

        return view('admin.quest-site.collection-site-insert', compact(
            'sites',
            'totalSites',
            'states',
            'importInProgress',
            'importStats',
            'importError'
        ));
    }

    /**
     * Process Excel file upload and queue background import
     */
    public function processCollectionSites(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:20480',
        ]);

        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');

            return back();
        }

        if (cache()->get('collection_site_import_in_progress', false)) {
            toastr()->warning('An import is already in progress. Please wait for it to finish.', 'content.warning');

            return back();
        }

        try {
            $file = $request->file('excel_file');
            $storedPath = $file->storeAs(
                'imports/collection-sites',
                now()->format('Ymd_His') . '_' . $file->getClientOriginalName()
            );

            ImportCollectionSitesJob::dispatch($storedPath, Auth::id());

            return redirect()->back()
                ->with('success', 'Import started. This page will update when processing completes.');
        } catch (\Exception $e) {
            Log::error('Collection site import dispatch error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Error starting import: ' . $e->getMessage());
        }
    }

    /**
     * Check collection site import status
     */
    public function importStatus()
    {
        return response()->json([
            'in_progress' => cache()->get('collection_site_import_in_progress', false),
            'stage' => cache()->get('collection_site_import_stage', 'not_started'),
            'stats' => cache()->get('collection_site_import_stats'),
            'error' => cache()->get('collection_site_import_error'),
        ]);
    }
}
