<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Admin\CollectionSite;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\QuestCollectionService;
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
            'lastSync' => now()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Perform full sync
     */
    public function fullSync(Request $request)
    {
        try {
            $request->validate([
                'confirm' => 'required|accepted'
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
                'confirm' => 'required|accepted'
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
                'sitesCount' => 0
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
