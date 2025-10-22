<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Admin\CollectionSite;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use App\Services\QuestCollectionService;
use Illuminate\Support\Facades\Validator;

class QuestSyncController extends Controller
{
    protected $questCollectionService;
    protected $firebaseService;

    public function __construct(QuestCollectionService $questCollectionService, FirebaseService $firebaseService)
    {
        $this->questCollectionService = $questCollectionService;
        $this->firebaseService = $firebaseService;
    }

    /**
     * Show sync dashboard
     */
    public function dashboard()
    {
        $sitesCount = $this->firebaseService->getSitesCount();

        return view('admin.quest-site.dashboard', [
            'sitesCount' => $sitesCount,
            'lastSync' => now()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Perform full sync
     */
    // public function fullSync(Request $request)
    // {
    //     set_time_limit(600);
    //     ini_set('max_execution_time', 600);

    //     try {
    //         $request->validate([
    //             'confirm' => 'required|accepted'
    //         ]);

    //         Log::info('Starting manual full sync of Quest collection sites');

    //         // Retrieve data from Quest API using the collection service
    //         $result = $this->questCollectionService->getFullCollectionSiteDetails();
    //         $sites = $result['sites'];

    //         Log::info('Retrieved ' . count($sites) . ' sites from Quest API');

    //         // Filter sites for drug testing capabilities
    //         $filteredSites = array_filter($sites, function ($site) {
    //             return $site['is_active'] &&
    //                 $site['open_to_public'] &&
    //                 ($site['nida_collections'] || $site['sap_collections']);
    //         });

    //         Log::info('Filtered to ' . count($filteredSites) . ' sites with drug testing capabilities');

    //         // Sync to Firebase
    //         $syncResult = $this->firebaseService->syncCollectionSites($filteredSites);

    //         // Log results
    //         Log::info('Quest collection sites sync completed', [
    //             'total_retrieved' => count($sites),
    //             'total_filtered' => count($filteredSites),
    //             'sync_success' => $syncResult['success'],
    //             'sync_errors' => $syncResult['errors']
    //         ]);

    //         return redirect()->route('quest-site.dashboard')
    //             ->with('success', 'Full sync completed successfully! ' .
    //                 $syncResult['success'] . ' sites processed, ' .
    //                 $syncResult['errors'] . ' errors.')
    //             ->with('details', $syncResult['details']);
    //     } catch (\Exception $e) {
    //         Log::error('Manual full sync failed: ' . $e->getMessage());

    //         return redirect()->route('quest-site.dashboard')
    //             ->with('error', 'Sync failed: ' . $e->getMessage());
    //     }
    // }

    public function fullSync(Request $request)
    {
        // Store original limits
        $originalTimeLimit = ini_get('max_execution_time');
        $originalMemoryLimit = ini_get('memory_limit');

        try {
            $request->validate([
                'confirm' => 'required|accepted'
            ]);

            // Increase limits for the sync operation
            set_time_limit(1800); // 30 minutes
            ini_set('max_execution_time', 1800);
            ini_set('memory_limit', '1024M');

            Log::info('Starting manual full sync of Quest collection sites with extended limits');

            // Store start time for progress tracking
            $startTime = microtime(true);
            cache()->put('sync_start_time', $startTime, 3600);
            cache()->put('sync_in_progress', true, 3600);
            cache()->put('sync_stage', 'retrieving_data', 3600);

            // Retrieve data from Quest API
            cache()->put('sync_stage', 'retrieving_quest_data', 3600);
            $result = $this->questCollectionService->getFullCollectionSiteDetails();
            $sites = $result['sites'];

            Log::info('Retrieved ' . count($sites) . ' sites from Quest API');
            cache()->put('sync_stage', 'filtering_sites', 3600);

            // Filter sites for drug testing capabilities
            $filteredSites = array_filter($sites, function ($site) {
                return $site['is_active'] &&
                    $site['open_to_public'] &&
                    ($site['nida_collections'] || $site['sap_collections']);
            });

            Log::info('Filtered to ' . count($filteredSites) . ' sites with drug testing capabilities');
            cache()->put('sync_stage', 'syncing_to_firebase', 3600);

            // Sync to Firebase
            $syncResult = $this->firebaseService->syncCollectionSites($filteredSites);

            // Calculate total time
            $totalTime = round(microtime(true) - $startTime, 2);
            Log::info("Total sync time: {$totalTime} seconds");

            // Store results in cache for display
            cache()->put('last_sync_result', [
                'success' => true,
                'message' => 'Full sync completed successfully!',
                'stats' => [
                    'total_retrieved' => count($sites),
                    'total_filtered' => count($filteredSites),
                    'sync_success' => $syncResult['success'],
                    'sync_errors' => $syncResult['errors'],
                    'total_time' => $totalTime
                ],
                'completed_at' => now()->toDateTimeString()
            ], 3600);

            // Clear progress indicators
            cache()->forget('sync_in_progress');
            cache()->forget('sync_stage');
            cache()->forget('sync_start_time');

            return redirect()->route('quest-site.dashboard')
                ->with('success', 'Full sync completed successfully! ' .
                    $syncResult['success'] . ' sites processed, ' .
                    $syncResult['errors'] . ' errors. Time: ' . $totalTime . ' seconds.')
                ->with('details', $syncResult['details']);
        } catch (\Exception $e) {
            Log::error('Manual full sync failed: ' . $e->getMessage());

            // Clear progress indicators on error
            cache()->forget('sync_in_progress');
            cache()->forget('sync_stage');
            cache()->forget('sync_start_time');

            // Store error result
            cache()->put('last_sync_result', [
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
                'completed_at' => now()->toDateTimeString()
            ], 3600);

            return redirect()->route('quest-site.dashboard')
                ->with('error', 'Sync failed: ' . $e->getMessage());
        } finally {
            // Restore original limits
            if ($originalTimeLimit !== false) {
                set_time_limit((int)$originalTimeLimit);
                ini_set('max_execution_time', $originalTimeLimit);
            }
            if ($originalMemoryLimit !== false) {
                ini_set('memory_limit', $originalMemoryLimit);
            }
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
            'last_result' => $lastResult
        ]);
    }


    /**
     * Perform incremental sync
     */
    public function incrementalSync(Request $request)
    {
        try {
            $request->validate([
                'since_date' => 'required|date|before_or_equal:today'
            ]);

            $sinceDate = $request->input('since_date');

            Log::info('Starting manual incremental sync of Quest collection sites since ' . $sinceDate);

            // Retrieve data from Quest API
            $result = $this->questCollectionService->getFullCollectionSiteDetails($sinceDate);
            $sites = $result['sites'];

            Log::info('Retrieved ' . count($sites) . ' updated sites from Quest API');

            // Filter sites for drug testing capabilities
            $filteredSites = array_filter($sites, function ($site) {
                return $site['is_active'] &&
                    $site['open_to_public'] &&
                    ($site['nida_collections'] || $site['sap_collections']);
            });

            Log::info('Filtered to ' . count($filteredSites) . ' updated sites with drug testing capabilities');

            // Sync to Firebase
            $syncResult = $this->firebaseService->syncCollectionSites($filteredSites);

            // Log results
            Log::info('Quest collection sites incremental sync completed', [
                'since_date' => $sinceDate,
                'total_retrieved' => count($sites),
                'total_filtered' => count($filteredSites),
                'sync_success' => $syncResult['success'],
                'sync_errors' => $syncResult['errors']
            ]);

            return redirect()->route('quest-site.dashboard')
                ->with('success', 'Incremental sync completed successfully! ' .
                    $syncResult['success'] . ' sites processed, ' .
                    $syncResult['errors'] . ' errors.')
                ->with('details', $syncResult['details']);
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
            $validator = Validator::make($request->all(), [
                'confirm' => 'required|accepted'
            ]);

            $result = $this->firebaseService->clearAllSites();

            if ($result) {
                Log::info('All collection site data cleared from Firebase');
                return redirect()->route('quest-site.dashboard')
                    ->with('success', 'All data cleared successfully!');
            } else {
                throw new \Exception('Failed to clear data from Firebase');
            }
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
            $sites = $this->firebaseService->getAllSites();

            return view('admin.quest-site.view-sites', [
                'sites' => $sites,
                'sitesCount' => count($sites)
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
            } else {
                return redirect()->route('quest-site.dashboard')
                    ->with('error', 'Connection test failed: ' . $result['message']);
            }
        } catch (\Exception $e) {
            return redirect()->route('quest-site.dashboard')
                ->with('error', 'Connection test failed: ' . $e->getMessage());
        }
    }

    public function collectionSiteInsert()
    {
        return view('admin.quest-site.collection-site-insert');
    }

    /**
     * Process Excel file upload and insert data in batches
     */
    public function processCollectionSites(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240' // 10MB max
        ]);

        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        try {
            $file = $request->file('excel_file');

            // Get sheet names to verify CollSite_Export exists
            $sheetNames = Excel::toArray(new \stdClass, $file)[0] ?? [];

            // Process the file
            $result = $this->processExcelFile($file);

            return redirect()->back()
                ->with('success', $result['message'])
                ->with('stats', $result['stats']);
        } catch (\Exception $e) {
            Log::error('Collection site import error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Error processing file: ' . $e->getMessage());
        }
    }

    /**
     * Process Excel file with optimized batch insertion using Laravel Excel
     */
    private function processExcelFile($file)
    {
        try {
            // First, get all sheet names to verify the sheet exists
            $sheetNames = Excel::toCollection(new \stdClass, $file)[0] ?? [];

            // Import data from CollSite_Export sheet using correct sheet name
            $data = Excel::toArray([], $file, null, \Maatwebsite\Excel\Excel::XLSX)[1] ?? []; // Index 1 for second sheet

            if (empty($data)) {
                // Alternative approach - try by sheet name
                $data = Excel::toArray([], $file, null, \Maatwebsite\Excel\Excel::XLSX, [
                    'sheetName' => 'CollSite_Export'
                ])[0] ?? [];
            }

            if (empty($data)) {
                throw new \Exception('No data found in CollSite_Export sheet. Please check the sheet name and format.');
            }

            $headers = array_shift($data); // Remove header row

            // Map column indexes
            $columnMap = $this->mapColumns($headers);

            $batchSize = 500;
            $batch = [];
            $processed = 0;
            $skipped = 0;

            DB::beginTransaction();

            try {
                foreach ($data as $row) {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        $skipped++;
                        continue;
                    }

                    if (empty($row[$columnMap['collection_site_code']])) {
                        $skipped++;
                        continue;
                    }

                    $siteData = $this->prepareSiteData($row, $columnMap);

                    if ($siteData) {
                        $batch[] = $siteData;
                        $processed++;

                        // Insert in batches
                        if (count($batch) >= $batchSize) {
                            CollectionSite::insert($batch);
                            $batch = [];
                        }
                    } else {
                        $skipped++;
                    }
                }

                // Insert remaining records
                if (!empty($batch)) {
                    CollectionSite::insert($batch);
                }

                DB::commit();

                return [
                    'message' => 'Collection sites imported successfully!',
                    'stats' => [
                        'processed' => $processed,
                        'skipped' => $skipped,
                        'total' => count($data)
                    ]
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                throw new \Exception('Database insertion failed: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            throw new \Exception('Excel file processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Map Excel columns to database fields
     */
    private function mapColumns($headers)
    {
        $columnMap = [];

        foreach ($headers as $index => $header) {
            $header = trim(strtolower($header ?? ''));

            switch ($header) {
                case 'collection site code':
                    $columnMap['collection_site_code'] = $index;
                    break;
                case 'name':
                    $columnMap['name'] = $index;
                    break;
                case 'last updated':
                    $columnMap['last_updated'] = $index;
                    break;
                case 'address 1':
                    $columnMap['address_1'] = $index;
                    break;
                case 'address 2':
                    $columnMap['address_2'] = $index;
                    break;
                case 'city':
                    $columnMap['city'] = $index;
                    break;
                case 'county':
                    $columnMap['county'] = $index;
                    break;
                case 'state':
                    $columnMap['state'] = $index;
                    break;
                case 'zip code':
                    $columnMap['zip_code'] = $index;
                    break;
                case 'phone number':
                    $columnMap['phone_number'] = $index;
                    break;
                case 'fax number':
                    $columnMap['fax_number'] = $index;
                    break;
            }
        }

        // Validate that all required columns are mapped
        $requiredColumns = ['collection_site_code', 'name', 'last_updated'];
        foreach ($requiredColumns as $col) {
            if (!isset($columnMap[$col])) {
                throw new \Exception("Required column '$col' not found in the Excel file");
            }
        }

        return $columnMap;
    }

    /**
     * Prepare site data for insertion
     */
    private function prepareSiteData($row, $columnMap)
    {
        try {
            // Parse date - handle different date formats
            $lastUpdated = null;
            if (!empty($row[$columnMap['last_updated']])) {
                $dateValue = $row[$columnMap['last_updated']];

                if ($dateValue instanceof \DateTime) {
                    $lastUpdated = $dateValue->format('Y-m-d');
                } else {
                    // Try different date formats
                    $formats = ['m/d/Y', 'Y-m-d', 'd/m/Y', 'Y/m/d'];
                    foreach ($formats as $format) {
                        $parsedDate = \DateTime::createFromFormat($format, $dateValue);
                        if ($parsedDate !== false) {
                            $lastUpdated = $parsedDate->format('Y-m-d');
                            break;
                        }
                    }

                    // If no format worked, use today's date
                    if (!$lastUpdated) {
                        $lastUpdated = now()->format('Y-m-d');
                    }
                }
            }

            return [
                'collection_site_code' => $row[$columnMap['collection_site_code']] ?? null,
                'name' => $row[$columnMap['name']] ?? null,
                'last_updated' => $lastUpdated,
                'address_1' => $row[$columnMap['address_1']] ?? null,
                'address_2' => $row[$columnMap['address_2']] ?? null,
                'city' => $row[$columnMap['city']] ?? null,
                'county' => $row[$columnMap['county']] ?? null,
                'state' => $row[$columnMap['state']] ?? null,
                'zip_code' => $row[$columnMap['zip_code']] ?? null,
                'phone_number' => $row[$columnMap['phone_number']] ?? null,
                'fax_number' => $row[$columnMap['fax_number']] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to prepare site data: ' . $e->getMessage());
            return null;
        }
    }
}
