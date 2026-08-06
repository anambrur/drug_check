<?php

namespace App\Imports;

use App\Services\CollectionSiteImportService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CollectionSiteWorkbookImport implements WithMultipleSheets
{
    public function __construct(
        private readonly CollectionSiteImportService $importService
    ) {}

    public function sheets(): array
    {
        return [
            'CollSite_Export' => new CollectionSiteExcelImport($this->importService),
        ];
    }
}
