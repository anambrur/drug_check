<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $database;
    protected $collectionSitesRef;

    public function __construct()
    {
        try {
            // Get the credentials path from config
            // $credentialsPath = config('firebase.projects.app.credentials');
            $credentialsPath = storage_path('app/quest-site-api-firebase-adminsdk-fbsvc-1d13082873.json');

            // Check if the file exists
            if (!file_exists($credentialsPath)) {
                throw new \Exception("Firebase credentials file not found at: {$credentialsPath}");
            }

            // Get the database URL from config
            $databaseUrl = config('firebase.projects.app.database.url');

            $factory = (new Factory)
                ->withServiceAccount($credentialsPath)
                ->withDatabaseUri($databaseUrl);

            $this->database = $factory->createDatabase();
            $this->collectionSitesRef = $this->database->getReference('collection_sites');

            Log::info('Firebase initialized successfully');
        } catch (\Exception $e) {
            Log::error('Firebase initialization failed: ' . $e->getMessage());
            throw new \Exception('Failed to initialize Firebase: ' . $e->getMessage());
        }
    }

    /**
     * Store or update collection sites in Firebase
     */
    // public function syncCollectionSites(array $sites)
    // {
    //     set_time_limit(600); // 10 minutes
    //     ini_set('max_execution_time', 600);

    //     $batchSize = 100; // Smaller batch size for manual operations
    //     $batch = [];
    //     $results = ['success' => 0, 'errors' => 0, 'details' => []];

    //     $totalSites = count($sites);
    //     Log::info("Starting Firebase sync for {$totalSites} sites");

    //     foreach ($sites as $index => $site) {
    //         $siteCode = $site['site_code'];
    //         $batch[$siteCode] = $site;

    //         // Process batch when size is reached or at the end
    //         if (count($batch) >= $batchSize || $index === count($sites) - 1) {
    //             try {
    //                 $this->collectionSitesRef->update($batch);
    //                 $results['success'] += count($batch);
    //                 $results['details'][] = "Successfully processed batch with " . count($batch) . " sites";


    //                 // Log progress every 10 batches or 500 sites
    //                 if ($results['success'] % 500 === 0) {
    //                     Log::info("Firebase sync progress: {$results['success']}/{$totalSites} sites processed");
    //                 }
    //             } catch (\Exception $e) {
    //                 Log::error('Firebase batch update failed: ' . $e->getMessage());
    //                 $results['errors'] += count($batch);
    //                 $results['details'][] = "Failed to process batch: " . $e->getMessage();
    //             }

    //             $batch = []; // Reset batch
    //             gc_collect_cycles();


    //             // Add delay to avoid hitting Firebase limits
    //             if ($index < count($sites) - 1) {
    //                 usleep(200000); // 200ms delay
    //             }
    //         }
    //     }

    //     Log::info("Firebase sync completed: {$results['success']} successful, {$results['errors']} errors");
    //     return $results;
    // }

    public function syncCollectionSites(array $sites)
    {
        // Store original limits
        $originalTimeLimit = ini_get('max_execution_time');
        $originalMemoryLimit = ini_get('memory_limit');

        try {
            // Increase limits for large processing
            set_time_limit(1800); // 30 minutes
            ini_set('max_execution_time', 1800);
            ini_set('memory_limit', '1024M');

            $batchSize = 50; // Reduced batch size for better memory management
            $batch = [];
            $results = ['success' => 0, 'errors' => 0, 'details' => []];

            $totalSites = count($sites);
            Log::info("Starting Firebase sync for {$totalSites} sites");

            $processed = 0;
            foreach ($sites as $index => $site) {
                $siteCode = $site['site_code'];
                $batch[$siteCode] = $site;
                $processed++;

                // Process batch when size is reached or at the end
                if (count($batch) >= $batchSize || $processed === $totalSites) {
                    try {
                        $this->collectionSitesRef->update($batch);
                        $results['success'] += count($batch);
                        $results['details'][] = "Successfully processed batch with " . count($batch) . " sites";

                        // Log progress every 500 sites
                        if ($results['success'] % 500 === 0) {
                            Log::info("Firebase sync progress: {$results['success']}/{$totalSites} sites processed");
                            Log::info("Memory usage: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB");
                        }
                    } catch (\Exception $e) {
                        Log::error('Firebase batch update failed: ' . $e->getMessage());
                        $results['errors'] += count($batch);
                        $results['details'][] = "Failed to process batch: " . $e->getMessage();
                    }

                    $batch = []; // Reset batch

                    // Force garbage collection to free memory
                    gc_collect_cycles();

                    // Add small delay to avoid hitting Firebase limits
                    usleep(100000); // 100ms delay
                }
            }

            Log::info("Firebase sync completed: {$results['success']} successful, {$results['errors']} errors");
            Log::info("Peak memory usage: " . round(memory_get_peak_usage(true) / 1024 / 1024, 2) . " MB");

            return $results;
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
     * Get a specific collection site
     */
    public function getSite($siteCode)
    {
        try {
            return $this->collectionSitesRef->getChild($siteCode)->getValue();
        } catch (\Exception $e) {
            Log::error('Firebase get site failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all collection sites
     */
    public function getAllSites()
    {
        try {
            return $this->collectionSitesRef->getValue() ?: [];
        } catch (\Exception $e) {
            Log::error('Firebase get all sites failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get sites count
     */
    public function getSitesCount()
    {
        try {
            $sites = $this->collectionSitesRef->getValue();
            return is_array($sites) ? count($sites) : 0;
        } catch (\Exception $e) {
            Log::error('Firebase get sites count failed: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Clear all collection sites
     */
    public function clearAllSites()
    {
        try {
            $this->collectionSitesRef->remove();
            return true;
        } catch (\Exception $e) {
            Log::error('Firebase clear all sites failed: ' . $e->getMessage());
            return false;
        }
    }
}
