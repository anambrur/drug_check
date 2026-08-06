<?php

namespace App\Services;

use App\Imports\CollectionSiteExcelImport;
use App\Imports\CollectionSiteWorkbookImport;
use App\Models\Admin\CollectionSite;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class CollectionSiteImportService
{
    private const BATCH_SIZE = 500;

    private ?array $columnMap = null;

    private int $processed = 0;

    private int $skipped = 0;

    private int $total = 0;

    /**
     * Import collection sites from an Excel file (replace-all).
     */
    public function import(string $filePath): array
    {
        $this->columnMap = null;
        $this->processed = 0;
        $this->skipped = 0;
        $this->total = 0;

        $fullPath = storage_path('app/' . $filePath);

        if (! is_file($fullPath)) {
            throw new \RuntimeException('Import file not found: ' . $filePath);
        }

        $headers = $this->readHeaders($fullPath);
        $this->columnMap = $this->mapColumns($headers);

        cache()->put('collection_site_import_stage', 'inserting');

        DB::table('collection_sites')->truncate();

        Excel::import(
            new CollectionSiteWorkbookImport($this),
            $fullPath,
            null,
            \Maatwebsite\Excel\Excel::XLSX
        );

        if ($this->processed === 0 && $this->total === 0) {
            throw new \RuntimeException('No data found in CollSite_Export sheet. Please check the sheet name and format.');
        }

        return [
            'processed' => $this->processed,
            'skipped' => $this->skipped,
            'total' => $this->total,
            'finished_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Handle a chunk of rows from the Excel import.
     */
    public function handleChunk(array $rows): void
    {
        $batch = [];

        foreach ($rows as $row) {
            $this->total++;

            if (empty(array_filter($row))) {
                $this->skipped++;

                continue;
            }

            if (empty($row[$this->columnMap['collection_site_code'] ?? -1] ?? null)) {
                $this->skipped++;

                continue;
            }

            $siteData = $this->prepareSiteData($row, $this->columnMap);

            if ($siteData === null) {
                $this->skipped++;

                continue;
            }

            $batch[] = $siteData;
            $this->processed++;

            if (count($batch) >= self::BATCH_SIZE) {
                $this->insertBatch($batch);
                $batch = [];
            }
        }

        if ($batch !== []) {
            $this->insertBatch($batch);
        }
    }

    /**
     * Read the header row from the CollSite_Export sheet.
     */
    private function readHeaders(string $fullPath): array
    {
        $sheets = Excel::toArray([], $fullPath, null, \Maatwebsite\Excel\Excel::XLSX);

        foreach ($sheets as $sheet) {
            if ($sheet === [] || ! isset($sheet[0][0])) {
                continue;
            }

            if (trim(strtolower((string) $sheet[0][0])) === 'collection site code') {
                return $sheet[0];
            }
        }

        throw new \RuntimeException('CollSite_Export sheet not found. Please check the sheet name and format.');
    }

    /**
     * Map Excel columns to database fields.
     */
    public function mapColumns(array $headers): array
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

        $requiredColumns = ['collection_site_code', 'name', 'last_updated'];

        foreach ($requiredColumns as $col) {
            if (! isset($columnMap[$col])) {
                throw new \RuntimeException("Required column '{$col}' not found in the Excel file");
            }
        }

        return $columnMap;
    }

    /**
     * Prepare site data for insertion.
     */
    public function prepareSiteData(array $row, array $columnMap): ?array
    {
        try {
            $lastUpdated = null;

            if (! empty($row[$columnMap['last_updated']])) {
                $dateValue = $row[$columnMap['last_updated']];

                if ($dateValue instanceof \DateTimeInterface) {
                    $lastUpdated = $dateValue->format('Y-m-d');
                } else {
                    $formats = ['m/d/Y', 'Y-m-d', 'd/m/Y', 'Y/m/d'];

                    foreach ($formats as $format) {
                        $parsedDate = \DateTime::createFromFormat($format, (string) $dateValue);

                        if ($parsedDate !== false) {
                            $lastUpdated = $parsedDate->format('Y-m-d');
                            break;
                        }
                    }

                    if ($lastUpdated === null) {
                        $lastUpdated = now()->format('Y-m-d');
                    }
                }
            }

            $now = now();

            return [
                'collection_site_code' => $row[$columnMap['collection_site_code']] ?? null,
                'name' => $row[$columnMap['name']] ?? null,
                'last_updated' => $lastUpdated,
                'address_1' => $row[$columnMap['address_1'] ?? -1] ?? null,
                'address_2' => $row[$columnMap['address_2'] ?? -1] ?? null,
                'city' => $row[$columnMap['city'] ?? -1] ?? null,
                'county' => $row[$columnMap['county'] ?? -1] ?? null,
                'state' => $row[$columnMap['state'] ?? -1] ?? null,
                'zip_code' => $row[$columnMap['zip_code'] ?? -1] ?? null,
                'phone_number' => $row[$columnMap['phone_number'] ?? -1] ?? null,
                'fax_number' => $row[$columnMap['fax_number'] ?? -1] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to prepare collection site data: ' . $e->getMessage());

            return null;
        }
    }

    private function insertBatch(array $batch): void
    {
        DB::transaction(function () use ($batch) {
            CollectionSite::insert($batch);
        });
    }
}
