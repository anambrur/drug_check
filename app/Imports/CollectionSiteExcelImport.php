<?php

namespace App\Imports;

use App\Services\CollectionSiteImportService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CollectionSiteExcelImport implements ToCollection, WithChunkReading, WithStartRow
{
    public function __construct(
        private readonly CollectionSiteImportService $importService
    ) {}

    public function collection(Collection $rows): void
    {
        $this->importService->handleChunk($rows->toArray());
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function startRow(): int
    {
        return 2;
    }
}
