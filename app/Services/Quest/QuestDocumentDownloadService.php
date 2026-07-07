<?php

namespace App\Services\Quest;

use App\Enums\QuestDocType;
use App\Models\Admin\QuestOrder;
use App\Models\Admin\QuestOrderDocument;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuestDocumentDownloadService
{
    public function __construct(
        private readonly QuestOrderLifecycleService $lifecycle,
        private readonly QuestOrderScreenService $screenService,
        private readonly QuestResponseParser $parser,
    ) {}

    public function downloadLabReport(QuestOrder $order, ?string $screenType = 'drug', bool $refresh = false): BinaryFileResponse|StreamedResponse|Response
    {
        return $this->downloadDocument($order, QuestDocType::LabReport, $screenType, $refresh);
    }

    public function downloadMroLetter(QuestOrder $order, ?string $screenType = 'drug', bool $refresh = false): BinaryFileResponse|StreamedResponse|Response
    {
        return $this->downloadDocument($order, QuestDocType::MROLetter, $screenType, $refresh);
    }

    public function downloadForScreen(QuestOrder $order, string $screenType, bool $refresh = false): BinaryFileResponse|StreamedResponse|Response
    {
        $docType = QuestDocType::resultDocTypeForScreen($screenType);

        try {
            return $this->downloadDocument($order, $docType, $screenType, $refresh);
        } catch (\RuntimeException $e) {
            $fallback = QuestDocType::fallbackDocTypeForScreen($screenType);
            if ($fallback) {
                return $this->downloadDocument($order, $fallback, $screenType, $refresh);
            }

            throw $e;
        }
    }

    public function downloadDocument(
        QuestOrder $order,
        QuestDocType $docType,
        ?string $screenType = 'drug',
        bool $refresh = false
    ): BinaryFileResponse|StreamedResponse|Response {
        $screenType = $screenType ?? 'drug';

        if (!$refresh) {
            $cached = $this->getCachedDocument($order, $screenType, $docType->value);
            if ($cached) {
                return response()->download(
                    Storage::disk('local')->path($cached->file_path),
                    $this->buildFilename($order, $docType, $cached->quest_specimen_id)
                );
            }
        }

        $screen = $this->screenService->resolveScreen($order, $screenType);
        $result = $this->lifecycle->getDocument(
            $order,
            $docType->value,
            $screen?->specimen_id,
            $order->lab_account
        );

        if ($result['status'] !== 'Success') {
            throw new \RuntimeException($this->mapDocumentError($result['error_detail'] ?? 'Document not available.'));
        }

        $content = $this->parser->decodeDocumentStream($result['doc_stream'] ?? '');
        if ($content === null) {
            Log::error('Quest document: invalid base64 stream', [
                'quest_order_id' => $order->quest_order_id,
                'doc_type' => $docType->value,
            ]);
            throw new \RuntimeException('Download failed. Contact support.');
        }

        $extension = strtolower($result['doc_format'] ?? 'pdf') === 'pdf' ? 'pdf' : 'tiff';
        $contentType = $extension === 'pdf' ? 'application/pdf' : 'image/tiff';
        $filename = $this->buildFilename($order, $docType, $screen?->specimen_id, $extension);

        $this->cacheDocument($order, $screenType, $docType->value, $content, $screen?->specimen_id);

        return response($content, 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function getCachedDocument(QuestOrder $order, string $screenType, string $docType): ?QuestOrderDocument
    {
        $cached = QuestOrderDocument::where('quest_order_id', $order->id)
            ->where('screen_type', $screenType)
            ->where('doc_type', $docType)
            ->first();

        if (!$cached || !Storage::disk('local')->exists($cached->file_path)) {
            return null;
        }

        if ($cached->file_hash && $cached->file_hash !== hash_file('sha256', Storage::disk('local')->path($cached->file_path))) {
            return null;
        }

        return $cached;
    }

    private function cacheDocument(
        QuestOrder $order,
        string $screenType,
        string $docType,
        string $content,
        ?string $specimenId
    ): void {
        $directory = 'quest-documents/' . $order->quest_order_id;
        $filename = $screenType . '_' . $docType . '.pdf';
        $path = $directory . '/' . $filename;

        Storage::disk('local')->put($path, $content);

        QuestOrderDocument::updateOrCreate(
            [
                'quest_order_id' => $order->id,
                'screen_type' => $screenType,
                'doc_type' => $docType,
            ],
            [
                'file_path' => $path,
                'file_hash' => hash('sha256', $content),
                'quest_specimen_id' => $specimenId,
                'downloaded_at' => now(),
                'downloaded_by' => Auth::id(),
            ]
        );
    }

    private function buildFilename(QuestOrder $order, QuestDocType $docType, ?string $specimenId = null, string $extension = 'pdf'): string
    {
        $base = strtoupper($order->last_name) . ', ' . strtoupper($order->first_name);

        if ($specimenId) {
            $base .= '-' . $specimenId;
        }

        return $base . '.pdf';
    }

    private function mapDocumentError(string $detail): string
    {
        $lower = strtolower($detail);

        if (str_contains($lower, 'not available')) {
            return 'Lab report is not available yet. Results are typically available after the lab processes your specimen.';
        }

        if (str_contains($lower, 'permission')) {
            return 'Your account cannot access lab reports directly. Open the Quest portal to view results.';
        }

        return $detail ?: 'Unable to retrieve report from Quest. Please try again.';
    }
}
