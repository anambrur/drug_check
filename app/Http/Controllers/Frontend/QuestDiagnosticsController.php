<?php

namespace App\Http\Controllers\Frontend;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Admin\Portfolio;
use App\Models\Admin\QuestOrder;
use App\Models\PortfolioTestApplication;
use App\Models\Admin\CollectionSite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

/**
 * QuestDiagnosticsController
 *
 * Handles all integration with Quest Diagnostics ESP Web Services.
 *
 * =========================================================================
 * OUTBOUND — we call Quest:
 *   CreateOrder     → QuestOrderSubmissionService (portfolio checkout flow)
 *   GetOrderDetails → getOrderDetails()
 *   GetDocument     → getDocument()
 *
 * INBOUND — Quest calls us (Section 3):
 *   receiveWebhook()  ← single endpoint registered as ResponseURL
 *
 * HOW THE INBOUND (ResponseURL) WORKS:
 *   The spec (§4.41) allows providing a ResponseURL per order.
 *   Quest POSTs both OrderStatus and OrderResult payloads to this URL.
 *   We detect which type arrived by checking which root element is present:
 *     - <OrderStatusID>  → it is an OrderStatus message  (Section 3.1)
 *     - <OrderResultID>  → it is an OrderResult message  (Section 3.2)
 *
 * NOTE ON AUTHENTICATION:
 *   The spec method signatures include username/password parameters, but
 *   the spec gives no values or validation rules for the inbound direction.
 *   Section 3.1.3 states: "Quest Diagnostics Implementation Team will work
 *   with provider/vendor to determine how your endpoint defines success or
 *   failure." Authentication details (if any) are agreed with the Quest
 *   Implementation Team during onboarding — not specified in the spec itself.
 *   We therefore do NOT validate credentials here; add that only if the
 *   Quest team provides specific values for your integration.
 *
 * ROUTE SETUP:
 *   Add to App\Http\Middleware\VerifyCsrfToken::$except:
 *     'quest/webhook'
 *
 *   Route:
 *     POST  quest/webhook  → receiveWebhook
 *
 *   Register this URL with Quest as your ResponseURL (in the order XML or
 *   at the account level via the Quest Implementation Team).
 * =========================================================================
 */
class QuestDiagnosticsController extends Controller
{
    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------

    private const VALID_DOC_TYPES = [
        'QPassport',
        'LabReport',
        'MROLetter',
        'Copy1',
        'Copy2',
        'ATF',
        'AlcoholReport',
        'OHS',
    ];

    private const CIRCUIT_BREAKER_KEY       = 'quest_circuit_breaker';
    private const CIRCUIT_FAILURE_THRESHOLD = 3;
    private const CIRCUIT_RECOVERY_SECONDS  = 60;
    private const MAX_RESPONSE_BYTES        = 5 * 1024 * 1024;

    // -------------------------------------------------------------------------
    // Config helpers
    // -------------------------------------------------------------------------

    private function getApiUrl(): string
    {
        return app()->isProduction()
            ? config('services.quest.urls.production', 'https://qcs.questdiagnostics.com/services/ESPService.asmx')
            : config('services.quest.urls.staging',    'https://qcs-uat.questdiagnostics.com/services/ESPService.asmx');
    }

    private function getUsername(): string
    {
        return config('services.quest.username')
            ?? throw new \RuntimeException('QUEST_USERNAME is not configured.');
    }

    private function getPassword(): string
    {
        return config('services.quest.password')
            ?? throw new \RuntimeException('QUEST_PASSWORD is not configured.');
    }

    private function getNamespace(): string
    {
        return 'http://wssim.labone.com';
    }

    private function getSoapAction(string $action): string
    {
        return '"' . $this->getNamespace() . '/' . $action . '"';
    }

    // =========================================================================
    // SECTION 3 — INBOUND WEBHOOK (Quest → Our System via ResponseURL)
    // =========================================================================

    /**
     * Single endpoint that receives BOTH OrderStatus and OrderResult pushes
     * from Quest. Quest posts raw XML (or a SOAP envelope) to the ResponseURL
     * you included in the order.
     *
     * Detection strategy (per spec element names):
     *   <OrderStatusID> present → OrderStatus message (Section 3.1)
     *   <OrderResultID> present → OrderResult message (Section 3.2)
     *
     * IMPORTANT SPEC BEHAVIOURS:
     *   - Statuses may arrive out of order or be skipped entirely (§4.31).
     *   - Multi-panel orders (drug + alcohol) produce SEPARATE messages per
     *     ScreenType. We always overwrite with whatever Quest sends latest.
     *   - The known dual-specimen bug (§3.2) can send a result for an order
     *     ID that doesn't exist in our system — we log and skip gracefully.
     */
    public function receiveWebhook(Request $request): \Illuminate\Http\Response
    {
        $rawBody = $request->getContent();

        Log::info('Quest inbound webhook received', [
            'ip'    => $request->ip(),
            'bytes' => strlen($rawBody),
        ]);

        // Parse the raw body into a SimpleXMLElement
        $xml = $this->parseInboundXml($rawBody);

        if ($xml === null) {
            Log::error('Quest webhook: could not parse inbound XML', [
                'snippet' => substr($rawBody, 0, 400),
            ]);
            // Return 200 anyway — returning an error may cause Quest to retry
            // with the same unparseable payload indefinitely.
            return $this->webhookResponse('PARSE_ERROR');
        }

        // Detect message type by which key element is present
        if (isset($xml->OrderStatusID)) {
            // ---- OrderStatus (Section 3.1) ----
            $status = $this->extractOrderStatus($xml);

            if ($status === null) {
                return $this->webhookResponse('INVALID_STATUS');
            }

            try {
                $this->applyStatusToOrder($status, $rawBody);
            } catch (\Throwable $e) {
                Log::error('Quest webhook: failed to apply OrderStatus', [
                    'error'          => $e->getMessage(),
                    'quest_order_id' => $status['quest_order_id'],
                ]);
            }

            return $this->webhookResponse('SUCCESS');
        } elseif (isset($xml->OrderResultID)) {
            // ---- OrderResult (Section 3.2) ----
            $result = $this->extractOrderResult($xml);

            if ($result === null) {
                return $this->webhookResponse('INVALID_RESULT');
            }

            try {
                $this->applyResultToOrder($result, $rawBody);
            } catch (\Throwable $e) {
                Log::error('Quest webhook: failed to apply OrderResult', [
                    'error'          => $e->getMessage(),
                    'quest_order_id' => $result['quest_order_id'],
                ]);
            }

            return $this->webhookResponse('SUCCESS');
        }

        // Neither element found — unknown payload shape
        Log::warning('Quest webhook: unrecognised payload (no OrderStatusID or OrderResultID)', [
            'snippet' => substr($rawBody, 0, 400),
        ]);

        return $this->webhookResponse('UNKNOWN_PAYLOAD');
    }

    // =========================================================================
    // PRIVATE — Inbound: XML Parsing
    // =========================================================================

    /**
     * Parse the raw inbound body into a SimpleXMLElement.
     *
     * Quest may POST:
     *   a) A bare <OrderStatus> or <OrderResult> XML document, or
     *   b) A SOAP envelope wrapping the XML (when using SOAP endpoints).
     *
     * Since ResponseURL receives the raw payload directly, we first try to
     * extract the inner content from a SOAP wrapper, then fall back to
     * treating the body as plain XML.
     */
    private function parseInboundXml(string $rawBody): ?\SimpleXMLElement
    {
        $cleaned = $this->decodeAndCleanInboundXml($rawBody);

        // Try to unwrap a SOAP envelope if one is present
        $inner = $this->tryExtractSoapInnerXml($cleaned);
        $xmlString = $inner ?? $cleaned;

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlString);
        libxml_clear_errors();

        return $xml instanceof \SimpleXMLElement ? $xml : null;
    }

    /**
     * If the raw body is a SOAP envelope, extract the first meaningful child
     * inside <soap:Body>. Returns null if it is not a SOAP envelope.
     */
    private function tryExtractSoapInnerXml(string $xml): ?string
    {
        // Quick check — don't bother parsing if it's clearly not SOAP
        if (stripos($xml, 'Envelope') === false) {
            return null;
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $loaded = $dom->loadXML($xml, LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();

        if (!$loaded) {
            return null;
        }

        $xpath = new \DOMXPath($dom);
        // Look for the first child element inside any Body element
        $nodes = $xpath->query('//*[local-name()="Body"]/*[1]');

        if ($nodes && $nodes->length > 0) {
            return $dom->saveXML($nodes->item(0));
        }

        return null;
    }

    /**
     * Extract OrderStatus fields from a parsed XML element.
     * Returns null if mandatory fields are missing.
     */
    private function extractOrderStatus(\SimpleXMLElement $xml): ?array
    {
        $questOrderId = (string) ($xml->QuestOrderID  ?? '');
        $statusId     = (string) ($xml->OrderStatusID ?? '');

        if (empty($questOrderId) || empty($statusId)) {
            Log::error('Quest webhook: OrderStatus missing required fields', [
                'quest_order_id'  => $questOrderId,
                'order_status_id' => $statusId,
            ]);
            return null;
        }

        return [
            'quest_order_id'        => $questOrderId,
            'reference_test_id'     => (string) ($xml->ReferenceTestID      ?? ''),
            'client_reference_id'   => (string) ($xml->ClientReferenceID    ?? ''),
            'order_status_id'       => $statusId,
            'screen_type'           => (string) ($xml->ScreenType           ?? ''),
            'specimen_id'           => (string) ($xml->SpecimenID           ?? ''),
            'lab_accession_number'  => (string) ($xml->LabAccessionNumber   ?? ''),
            'collected_datetime'    => $this->parseQuestDatetime($xml->CollectedDateTime   ?? null),
            'order_status_datetime' => $this->parseQuestDatetime($xml->OrderStatusDateTime ?? null),
            // Physical sub-block (§4.32) — only present for physical orders
            'physical_data'         => isset($xml->Physical) ? $this->xmlToArray($xml->Physical) : null,
        ];
    }

    /**
     * Extract OrderResult fields from a parsed XML element.
     * Returns null if mandatory fields are missing.
     */
    private function extractOrderResult(\SimpleXMLElement $xml): ?array
    {
        $questOrderId = (string) ($xml->QuestOrderID  ?? '');
        $resultId     = (string) ($xml->OrderResultID ?? '');

        if (empty($questOrderId) || empty($resultId)) {
            Log::error('Quest webhook: OrderResult missing required fields', [
                'quest_order_id'  => $questOrderId,
                'order_result_id' => $resultId,
            ]);
            return null;
        }

        return [
            'quest_order_id'        => $questOrderId,
            'reference_test_id'     => (string) ($xml->ReferenceTestID     ?? ''),
            'client_reference_id'   => (string) ($xml->ClientReferenceID   ?? ''),
            'order_result_id'       => $resultId,
            'screen_type'           => (string) ($xml->ScreenType          ?? ''),
            'specimen_id'           => (string) ($xml->SpecimenID          ?? ''),
            'lab_accession_number'  => (string) ($xml->LabAccessionNumber  ?? ''),
            'collected_datetime'    => $this->parseQuestDatetime($xml->CollectedDateTime   ?? null),
            'order_result_datetime' => $this->parseQuestDatetime($xml->OrderResultDateTime ?? null),
            'physical_data'         => isset($xml->Physical) ? $this->xmlToArray($xml->Physical) : null,
        ];
    }

    // =========================================================================
    // PRIVATE — Inbound: Write to quest_orders
    // =========================================================================

    /**
     * Update the quest_orders row with the latest status from Quest.
     * We never create a new row here — statuses only enrich an existing order.
     */
    private function applyStatusToOrder(array $status, string $rawBody): void
    {
        $order = QuestOrder::where('quest_order_id', $status['quest_order_id'])->first();

        if (!$order) {
            // Can happen with the known dual-specimen Quest bug (§3.2)
            Log::warning('Quest webhook: OrderStatus arrived for unknown quest_order_id', [
                'quest_order_id' => $status['quest_order_id'],
                'status_id'      => $status['order_status_id'],
            ]);
            return;
        }

        $order->update([
            'order_status'             => $status['order_status_id'],
            'order_status_screen_type' => $status['screen_type']          ?: null,
            // Preserve existing specimen data if Quest sends nothing new
            'specimen_id'              => $status['specimen_id']           ?: $order->specimen_id,
            'lab_accession_number'     => $status['lab_accession_number']  ?: $order->lab_accession_number,
            'collected_datetime'       => $status['collected_datetime']    ?? $order->collected_datetime,
            'order_status_datetime'    => $status['order_status_datetime'],
            'order_status_updated_at'  => now(),
            'physical_data'            => $status['physical_data']
                ? json_encode($status['physical_data'])
                : $order->physical_data,
            'status_raw_xml'           => substr($rawBody, 0, 65535),
        ]);

        Log::info('Quest OrderStatus applied', [
            'id'             => $order->id,
            'quest_order_id' => $order->quest_order_id,
            'status'         => $status['order_status_id'],
            'screen_type'    => $status['screen_type'],
        ]);
    }

    /**
     * Update the quest_orders row with the final result from Quest.
     */
    private function applyResultToOrder(array $result, string $rawBody): void
    {
        $order = QuestOrder::where('quest_order_id', $result['quest_order_id'])->first();

        if (!$order) {
            Log::warning('Quest webhook: OrderResult arrived for unknown quest_order_id', [
                'quest_order_id' => $result['quest_order_id'],
                'result_id'      => $result['order_result_id'],
            ]);
            return;
        }

        $order->update([
            'order_result'             => $result['order_result_id'],
            'order_result_screen_type' => $result['screen_type']          ?: null,
            'specimen_id'              => $result['specimen_id']           ?: $order->specimen_id,
            'lab_accession_number'     => $result['lab_accession_number']  ?: $order->lab_accession_number,
            'collected_datetime'       => $result['collected_datetime']    ?? $order->collected_datetime,
            'order_result_datetime'    => $result['order_result_datetime'],
            'order_result_updated_at'  => now(),
            'physical_data'            => $result['physical_data']
                ? json_encode($result['physical_data'])
                : $order->physical_data,
            'result_raw_xml'           => substr($rawBody, 0, 65535),
        ]);

        Log::info('Quest OrderResult applied', [
            'id'             => $order->id,
            'quest_order_id' => $order->quest_order_id,
            'result'         => $result['order_result_id'],
            'screen_type'    => $result['screen_type'],
        ]);
    }

    // =========================================================================
    // PRIVATE — Inbound Helpers
    // =========================================================================

    /**
     * Decode HTML entities, strip control characters, and normalise
     * an inbound XML string so SimpleXML can parse it reliably.
     */
    private function decodeAndCleanInboundXml(string $xml): string
    {
        $xml = html_entity_decode($xml, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $xml = str_replace(['&#xD;', "\r"], '', $xml);
        $xml = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $xml);
        $xml = trim(preg_replace('/<\?xml[^?]*\?>/', '', $xml));

        return stripos($xml, '<?xml') === false
            ? '<?xml version="1.0" encoding="UTF-8"?>' . $xml
            : $xml;
    }

    /**
     * Build a simple plain-text response for the webhook.
     * The spec says Quest's Implementation Team will agree on what
     * constitutes success/failure — a plain string is sufficient.
     */
    private function webhookResponse(string $status): \Illuminate\Http\Response
    {
        return response($status, 200, ['Content-Type' => 'text/plain']);
    }

    // =========================================================================
    // ORDER FORM
    // =========================================================================

    public function showOrderForm(Request $request)
    {
        $applicationId = $request->session()->get('portfolio_test_application_id');

        if ($applicationId) {
            $application = PortfolioTestApplication::where('user_id', Auth::id())->find($applicationId);
            if ($application && $application->payment_status === 'completed') {
                return redirect()->route('frontend.portfolio-test.retry', $application->id);
            }
        }

        return redirect()->route('page-index')->with('error', 'Please complete your test order from the portfolio page.');
    }

    // =========================================================================
    // ORDER SUCCESS
    // =========================================================================

    public function orderSuccess(Request $request, string $questOrderId)
    {
        $order = QuestOrder::where('quest_order_id', $questOrderId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('quest.order-success', array_merge(getFrontendData(), [
            'questOrderId'    => $order->quest_order_id,
            'referenceTestId' => $order->reference_test_id,
        ]));
    }

    // =========================================================================
    // DOCUMENT DOWNLOAD
    // =========================================================================

    public function getDocument(Request $request, string $questOrderId, string $docType)
    {
        if (!in_array($docType, self::VALID_DOC_TYPES, true)) {
            return back()->with('error', 'Invalid document type requested.');
        }

        $order = QuestOrder::where('quest_order_id', $questOrderId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Spec §2.5.1: include both identifiers in DocXml
        $docXml = '<GetDocument>'
            . '<QuestOrderID>'    . $this->xmlEscape($questOrderId)                   . '</QuestOrderID>'
            . '<ReferenceTestID>' . $this->xmlEscape($order->reference_test_id ?? '') . '</ReferenceTestID>'
            . '<DocType>'         . $this->xmlEscape($docType)                        . '</DocType>'
            . '</GetDocument>';

        $soapBody = '<?xml version="1.0" encoding="utf-8"?>'
            . '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"'
            . ' xmlns:wss="http://wssim.labone.com/">'
            . '<soap:Body>'
            . '<wss:GetDocument>'
            . '<wss:username>' . $this->xmlEscape($this->getUsername()) . '</wss:username>'
            . '<wss:password>' . $this->xmlEscape($this->getPassword()) . '</wss:password>'
            . '<wss:DocXml><![CDATA[' . $docXml . ']]></wss:DocXml>'
            . '</wss:GetDocument>'
            . '</soap:Body>'
            . '</soap:Envelope>';

        try {
            $rawResponse = $this->sendCurlRequest($soapBody, $this->getSoapAction('GetDocument'));
            $result      = $this->parseDocumentResponse($rawResponse);

            if ($result['status'] !== 'Success') {
                return back()->with('error', 'Failed to retrieve document: ' . $result['error_detail']);
            }

            $fileContent = base64_decode($result['doc_stream']);
            $extension   = strtolower($result['doc_format']) === 'pdf' ? 'pdf' : 'tiff';
            $contentType = $extension === 'pdf' ? 'application/pdf' : 'image/tiff';

            return response($fileContent, 200, [
                'Content-Type'        => $contentType,
                'Content-Disposition' => 'attachment; filename="' . $docType . '-' . $questOrderId . '.' . $extension . '"',
            ]);
        } catch (\RuntimeException $e) {
            Log::error('Quest GetDocument failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Failed to retrieve document. Please try again.');
        }
    }

    // =========================================================================
    // ORDER DETAILS
    // =========================================================================

    public function getOrderDetails(Request $request, ?string $questOrderId = null, ?string $referenceTestId = null)
    {
        $questOrderId    = $questOrderId    ?: $request->input('quest_order_id', '');
        $referenceTestId = $referenceTestId ?: $request->input('reference_test_id', '');

        if (empty($questOrderId) && empty($referenceTestId)) {
            return back()->with('error', 'Quest Order ID or Reference Test ID is required.');
        }

        $this->assertUserOwnsQuestOrder($questOrderId ?: null, $referenceTestId ?: null);

        $soapBody = '<?xml version="1.0" encoding="utf-8"?>'
            . '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"'
            . ' xmlns:wss="http://wssim.labone.com/">'
            . '<soap:Body>'
            . '<wss:GetOrderDetails>'
            . '<wss:username>'        . $this->xmlEscape($this->getUsername())   . '</wss:username>'
            . '<wss:password>'        . $this->xmlEscape($this->getPassword())   . '</wss:password>'
            . '<wss:referenceTestId>' . $this->xmlEscape($referenceTestId ?: '') . '</wss:referenceTestId>'
            . '<wss:questOrderId>'    . $this->xmlEscape($questOrderId    ?: '') . '</wss:questOrderId>'
            . '</wss:GetOrderDetails>'
            . '</soap:Body>'
            . '</soap:Envelope>';

        try {
            $rawResponse = $this->sendCurlRequest($soapBody, $this->getSoapAction('GetOrderDetails'));
            $result      = $this->parseSoapResponse($rawResponse);

            if ($result['status'] === 'SUCCESS') {
                session()->flash('order_details', [
                    'display_url'         => $result['display_url'],
                    'quest_order_id'      => $result['quest_order_id'],
                    'reference_test_id'   => $result['reference_test_id'],
                    'client_reference_id' => $result['client_reference_id'],
                ]);
                return redirect()->route('quest.order-details.show');
            }

            return back()->with('error', 'Failed to retrieve order: ' . ($result['error']['detail'] ?? 'Unknown error.'));
        } catch (\RuntimeException $e) {
            Log::error('Quest GetOrderDetails failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Failed to retrieve order details. Please try again.');
        }
    }

    public function showOrderDetails()
    {
        $orderDetails = session('order_details');

        if (!$orderDetails) {
            return redirect()->route('quest.order-details.form')->with('error', 'No order details found.');
        }

        return view('quest.order-details', array_merge(getFrontendData(), compact('orderDetails')));
    }

    public function getOrderDetailsForm()
    {
        return view('quest.order-details-form', getFrontendData());
    }

    // =========================================================================
    // DOT TEST
    // =========================================================================

    public function dotTest(?int $portfolioId = null)
    {
        $portfolio = Portfolio::findOrFail($portfolioId);

        return redirect()
            ->route('default-portfolio-detail-show', [
                'portfolio_slug' => $portfolio->portfolio_slug,
            ])
            ->with('info', 'Complete your DOT test order details on this page, then proceed to secure checkout. Your order will be submitted to Quest Diagnostics automatically after payment.');
    }

    /**
     * Legacy admin redirect — old payment flow URLs land here.
     * Accepts application ID or Stripe payment_intent_id.
     */
    public function showDotOrderForm(Request $request, string $reference)
    {
        $application = PortfolioTestApplication::with('portfolio')
            ->where('user_id', Auth::id())
            ->where(function ($query) use ($reference) {
                if (ctype_digit($reference)) {
                    $query->where('id', (int) $reference);
                } else {
                    $query->where('stripe_payment_intent_id', $reference);
                }
            })
            ->first();

        if (!$application) {
            return redirect()
                ->route('page-index')
                ->with('error', 'Order not found. Please start a new test from the portfolio page.');
        }

        if ($application->isQuestSubmitted() && $application->quest_order_id) {
            $questOrder = QuestOrder::where('quest_order_id', $application->quest_order_id)->first();

            return redirect()->route('quest.order-success', [
                'quest_order_id' => $application->quest_order_id,
                'reference_test_id' => $questOrder?->reference_test_id ?? '',
            ]);
        }

        if ($application->payment_status === 'completed') {
            return redirect()->route('frontend.portfolio-test.retry', $application->id);
        }

        if ($application->portfolio) {
            return redirect()
                ->route('default-portfolio-detail-show', [
                    'portfolio_slug' => $application->portfolio->portfolio_slug,
                ])
                ->with('info', 'Please complete your order details and checkout from the portfolio page.');
        }

        return redirect()->route('page-index')->with('error', 'Please complete your test order from the portfolio page.');
    }

    // =========================================================================
    // COLLECTION SITE SEARCH
    // =========================================================================

    public function searchCollectionSites(Request $request)
    {
        $searchTerm = trim($request->get('q', ''));

        if (strlen($searchTerm) < 2) {
            return response()->json([]);
        }

        try {
            $like  = '%' . $searchTerm . '%';
            $sites = CollectionSite::where(function ($q) use ($like) {
                $q->where('name',       'LIKE', $like)
                    ->orWhere('address_1', 'LIKE', $like)
                    ->orWhere('city',      'LIKE', $like)
                    ->orWhere('state',     'LIKE', $like)
                    ->orWhere('zip_code',  'LIKE', $like);
            })
                ->orderBy('name')
                ->limit(30)
                ->get(['id', 'collection_site_code', 'name', 'address_1', 'city', 'state', 'zip_code']);

            return response()->json(
                $sites->map(fn($site) => [
                    'id'                   => $site->id,
                    'collection_site_code' => $site->collection_site_code,
                    'text'                 => $this->formatSiteLabel($site),
                ])
            );
        } catch (\Throwable $e) {
            Log::error('Collection site search failed', ['message' => $e->getMessage()]);
            return response()->json([], 500);
        }
    }

    // =========================================================================
    // PRIVATE — SOAP / HTTP
    // =========================================================================

    private function sendCurlRequest(string $soapBody, string $soapAction): string
    {
        $this->checkCircuitBreaker();

        $sslVerify = config('services.quest.ssl.verify_peer', true);
        $caBundle  = config('services.quest.ssl.ca_bundle');

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $this->getApiUrl(),
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $soapBody,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => config('services.quest.timeouts.request', 60),
            CURLOPT_CONNECTTIMEOUT => config('services.quest.timeouts.connect', 60),
            CURLOPT_SSL_VERIFYPEER => $sslVerify,
            CURLOPT_SSL_VERIFYHOST => $sslVerify ? 2 : 0,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_ENCODING       => '',
            CURLOPT_HTTPHEADER     => [
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: ' . $soapAction,
                'Content-Length: ' . strlen($soapBody),
                'Connection: Keep-Alive',
            ],
        ]);

        if ($sslVerify && $caBundle) {
            curl_setopt($ch, CURLOPT_CAINFO, $caBundle);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $timing   = curl_getinfo($ch);
        $error    = curl_error($ch);
        curl_close($ch);

        Log::debug('Quest cURL timing', [
            'action'        => $soapAction,
            'total_time'    => $timing['total_time'],
            'connect_time'  => $timing['connect_time'],
            'starttransfer' => $timing['starttransfer_time'],
        ]);

        if ($error) {
            $this->recordCircuitFailure();
            throw new \RuntimeException(match (true) {
                str_contains($error, 'timed out')              => 'Quest API timeout. Please try again.',
                str_contains($error, 'Could not resolve host') => 'Cannot reach the Quest Diagnostics server.',
                default                                        => 'Quest API connection failed: ' . $error,
            });
        }

        if ($httpCode !== 200) {
            $this->recordCircuitFailure();
            Log::error('Quest API non-200', ['action' => $soapAction, 'status' => $httpCode, 'body' => substr($response, 0, 400)]);
            throw new \RuntimeException("Quest Diagnostics returned HTTP {$httpCode}.");
        }

        $this->resetCircuitBreaker();
        return $response;
    }

    // =========================================================================
    // PRIVATE — RESPONSE PARSING (outbound Quest responses)
    // =========================================================================

    private function parseSoapResponse(string $rawResponse): array
    {
        if (strlen($rawResponse) > self::MAX_RESPONSE_BYTES) {
            return $this->buildFailureResult('RESPONSE_TOO_LARGE', 'Response exceeded maximum allowed size.', $rawResponse);
        }

        $innerXml = $this->extractInnerXml($this->cleanXml($rawResponse));

        if ($innerXml === null) {
            Log::error('Quest: could not extract inner XML', ['snippet' => substr($rawResponse, 0, 400)]);
            return $this->buildFailureResult('SOAP_EXTRACTION_FAILED', 'Could not extract response body.', $rawResponse);
        }

        return $this->parseQuestMethodResponse($innerXml, $rawResponse);
    }

    private function parseQuestMethodResponse(string $xml, string $rawResponse = ''): array
    {
        $xml = html_entity_decode(trim(preg_replace('/<\?xml[^?]*\?>/', '', $xml)), ENT_QUOTES | ENT_XML1, 'UTF-8');

        libxml_use_internal_errors(true);
        $parsed = simplexml_load_string($xml);
        libxml_clear_errors();

        if ($parsed === false) {
            return $this->buildFailureResult('XML_PARSE_ERROR', 'Failed to parse Quest response XML.', $rawResponse);
        }

        $result = [
            'method_id'           => (string) ($parsed->MethodID          ?? ''),
            'client_reference_id' => (string) ($parsed->ClientReferenceID ?? ''),
            'reference_test_id'   => (string) ($parsed->ReferenceTestID   ?? ''),
            'quest_order_id'      => (string) ($parsed->QuestOrderID      ?? '0'),
            'status'              => (string) ($parsed->ResponseStatusID  ?? 'FAILURE'),
            'display_url'         => (string) ($parsed->DisplayURL        ?? ''),
            'error'               => null,
            '_raw_response'       => $rawResponse,
        ];

        if ($result['status'] === 'FAILURE' && isset($parsed->Errors->Error)) {
            $error = $parsed->Errors->Error[0];
            $result['error'] = [
                'id'     => (string) ($error->ErrorID     ?? 'UNKNOWN'),
                'detail' => (string) ($error->ErrorDetail ?? 'An unknown error occurred.'),
            ];
        }

        return $result;
    }

    private function extractInnerXml(string $soapResponse): ?string
    {
        $current = $soapResponse;

        for ($depth = 0; $depth < 5; $depth++) {
            $current = str_replace(['&#xD;', "\r"], '', $current);
            $next    = $this->extractOneLayer($current);
            if ($next === null) break;

            $next = trim(html_entity_decode(str_replace(['&#xD;', "\r"], '', $next), ENT_QUOTES | ENT_XML1, 'UTF-8'));
            if ($next === $current) break;

            $current = $next;
            if ($this->isQuestPayload($current)) return $current;
        }

        if ($this->isQuestPayload($current)) return $current;

        if (preg_match('/<QuestMethodResponse[\s\S]*?<\/QuestMethodResponse>/i', $current, $m)) {
            return $m[0];
        }

        return null;
    }

    private function isQuestPayload(string $xml): bool
    {
        $trimmed = trim(preg_replace('/<\?xml[^?]*\?>\s*/i', '', $xml));

        if (stripos($trimmed, '<s:Envelope') === 0 || stripos($trimmed, '<soap:Envelope') === 0 || stripos($trimmed, '<SOAP-ENV:') === 0) {
            return false;
        }

        return stripos($trimmed, '<QuestMethodResponse') === 0 || stripos($trimmed, '<ResponseStatusID') !== false;
    }

    private function extractOneLayer(string $current): ?string
    {
        $fixEncoding = fn(string $s) => preg_replace('/(<\?xml[^?]*encoding\s*=\s*["\'])utf-16(["\'])/i', '${1}utf-8${2}', $s);
        $addDecl     = fn(string $s) => stripos($s, '<?xml') === false ? '<?xml version="1.0" encoding="UTF-8"?>' . $s : $s;

        // DOMDocument
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        libxml_use_internal_errors(true);
        $loaded = $dom->loadXML($fixEncoding($addDecl($current)), LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();

        if ($loaded) {
            $xpath = new \DOMXPath($dom);
            foreach (['soap', 's', 'SOAP-ENV'] as $prefix) {
                $xpath->registerNamespace($prefix, 'http://schemas.xmlsoap.org/soap/envelope/');
            }
            foreach (['//*[local-name()="CreateOrderResult"]', '//*[local-name()="GetOrderDetailsResult"]', '//*[local-name()="GetDocumentResult"]'] as $query) {
                $nodes = $xpath->query($query);
                if ($nodes && $nodes->length > 0) return $nodes->item(0)->nodeValue;
            }
        }

        // SimpleXML
        libxml_use_internal_errors(true);
        $sxml = simplexml_load_string($fixEncoding($addDecl($current)));
        libxml_clear_errors();

        if ($sxml !== false) {
            foreach (['http://schemas.xmlsoap.org/soap/envelope/', 'http://tempuri.org/', 'http://wssim.labone.com/'] as $ns) {
                try {
                    $body = $sxml->children($ns)->Body ?? null;
                } catch (\Throwable) {
                    $body = null;
                }
                if (!$body) continue;
                foreach ($body->children() as $responseNode) {
                    foreach ($responseNode->children() as $resultNode) {
                        if (str_ends_with($resultNode->getName(), 'Result')) return (string) $resultNode;
                    }
                }
            }
        }

        // Regex fallback
        if (preg_match('/<(?:\w+:)?(?:CreateOrder|GetOrderDetails|GetDocument)Result[^>]*>\s*(?:<!\[CDATA\[)?(.*?)(?:\]\]>)?\s*<\/(?:\w+:)?(?:CreateOrder|GetOrderDetails|GetDocument)Result>/is', $current, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    private function parseDocumentResponse(string $rawResponse): array
    {
        $empty = ['status' => 'Error', 'error_detail' => '', 'doc_type' => '', 'doc_format' => '', 'doc_stream' => ''];

        if (empty(trim($rawResponse))) return array_merge($empty, ['error_detail' => 'Empty response from Quest.']);

        $xmlToParse = html_entity_decode(
            trim(preg_replace('/<\?xml[^?]*\?>/', '', $this->extractInnerXml($this->cleanXml($rawResponse)) ?? $rawResponse)),
            ENT_QUOTES | ENT_XML1,
            'UTF-8'
        );

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlToParse);
        libxml_clear_errors();

        if ($xml === false) return array_merge($empty, ['error_detail' => 'Could not parse document response XML.']);

        $status = (string) ($xml->ResponseStatusId ?? '');

        if ($status !== 'Success') {
            return array_merge($empty, [
                'status'       => $status ?: 'Error',
                'error_detail' => (string) ($xml->ErrorDetail ?? 'Unknown document error.'),
                'doc_type'     => (string) ($xml->DocType     ?? ''),
                'doc_format'   => (string) ($xml->DocFormat   ?? ''),
            ]);
        }

        return [
            'status'       => 'Success',
            'error_detail' => '',
            'doc_type'     => (string) ($xml->DocType   ?? ''),
            'doc_format'   => (string) ($xml->DocFormat ?? ''),
            'doc_stream'   => (string) ($xml->DocStream ?? ''),
        ];
    }

    // =========================================================================
    // PRIVATE — CIRCUIT BREAKER
    // =========================================================================

    private function checkCircuitBreaker(): void
    {
        $state = $this->getCircuitState();
        if (!$state['open']) return;
        if ((time() - $state['last_failure']) < self::CIRCUIT_RECOVERY_SECONDS) {
            throw new \RuntimeException('Quest Diagnostics is temporarily unavailable. Please try again in a moment.');
        }
        $this->resetCircuitBreaker();
    }

    private function recordCircuitFailure(): void
    {
        $state = $this->getCircuitState();
        $state['failures']++;
        $state['last_failure'] = time();
        if ($state['failures'] >= self::CIRCUIT_FAILURE_THRESHOLD) {
            $state['open'] = true;
            Log::error('Quest circuit breaker opened', ['failures' => $state['failures']]);
        }
        $this->saveCircuitState($state);
    }

    private function resetCircuitBreaker(): void
    {
        $this->saveCircuitState(['open' => false, 'failures' => 0, 'last_failure' => 0]);
    }

    private function getCircuitState(): array
    {
        return Cache::get(self::CIRCUIT_BREAKER_KEY, ['open' => false, 'failures' => 0, 'last_failure' => 0]);
    }

    private function saveCircuitState(array $state): void
    {
        Cache::put(self::CIRCUIT_BREAKER_KEY, $state, now()->addHour());
    }

    // =========================================================================
    // PRIVATE — UTILITIES
    // =========================================================================

    private function assertUserOwnsQuestOrder(?string $questOrderId, ?string $referenceTestId): void
    {
        $userId = auth()->id();

        $owned = QuestOrder::where('user_id', $userId)
            ->where(function ($q) use ($questOrderId, $referenceTestId) {
                if ($questOrderId) {
                    $q->where('quest_order_id', $questOrderId);
                }
                if ($referenceTestId) {
                    $q->orWhere('reference_test_id', $referenceTestId);
                }
            })
            ->exists();

        if (!$owned && $questOrderId) {
            $owned = PortfolioTestApplication::where('user_id', $userId)
                ->where('quest_order_id', $questOrderId)
                ->exists();
        }

        abort_unless($owned, 403, 'You are not authorized to view this order.');
    }

    private function xmlEscape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1, 'UTF-8');
    }

    private function cleanXml(string $xml): string
    {
        return preg_replace('/\s+/', ' ', preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $xml));
    }

    private function buildFailureResult(string $errorId, string $detail, string $rawResponse): array
    {
        return [
            'method_id'           => null,
            'client_reference_id' => null,
            'reference_test_id'   => null,
            'quest_order_id'      => '0',
            'status'              => 'FAILURE',
            'display_url'         => null,
            'error'               => ['id' => $errorId, 'detail' => $detail],
            '_raw_response'       => substr($rawResponse, 0, 2000),
        ];
    }

    private function formatSiteLabel(CollectionSite $site): string
    {
        $parts = array_filter([$site->name, implode(', ', array_filter([$site->address_1, $site->city, $site->state, $site->zip_code]))]);
        return implode(' — ', $parts);
    }

    private function parseQuestDatetime(mixed $value): ?Carbon
    {
        $raw = trim((string) ($value ?? ''));
        if (empty($raw)) return null;
        try {
            return Carbon::parse($raw);
        } catch (\Throwable) {
            return null;
        }
    }

    private function xmlToArray(\SimpleXMLElement $xml): array
    {
        $result = [];
        foreach ($xml->children() as $key => $child) {
            $result[$key] = count($child->children()) > 0 ? $this->xmlToArray($child) : (string) $child;
        }
        return $result;
    }
}
