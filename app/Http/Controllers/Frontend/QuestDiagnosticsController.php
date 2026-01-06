<?php

namespace App\Http\Controllers\Frontend;

use Stripe\Stripe;
use SimpleXMLElement;
use Stripe\PaymentIntent;
use Illuminate\Http\Request;
use App\Models\Admin\Employee;
use App\Models\Admin\Portfolio;
use App\Models\Admin\QuestOrder;
use App\Models\Admin\ClientProfile;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Admin\CollectionSite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QuestDiagnosticsController extends Controller
{
    private $devUrl = 'https://qcs-uat.questdiagnostics.com/services/ESPService.asmx';
    private $prodUrl = 'https://ocs.questdiagnostics.com/services/ESPService.asmx';

    public function showOrderForm(Request $request)
    {
        // Retrieve payment data from session or database
        $paymentData = $request->session()->get('payment_data');
        $portfolio = $paymentData['portfolio'];

        if (!$paymentData) {
            return redirect()->back()->with('error', 'Payment data not found. Please complete payment first.');
        }

        return view(
            'quest.order-form',
            array_merge(getFrontendData(), [
                'paymentData' => $paymentData,
                'portfolio' => $paymentData['portfolio']
            ])
        );
    }

    public function submitOrder(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'payment_intent_id' => 'required|string',
            'first_name' => 'required|string|max:20',
            'last_name' => 'required|string|max:25',
            'middle_name' => 'nullable|string|max:20',
            'email' => 'required|email|max:254',
            'primary_phone' => 'required|string',
            'secondary_phone' => 'nullable|string',
            'primary_id' => 'required|string|max:25',
            'primary_id_type' => 'nullable|string|max:5',
            'dob' => 'nullable|date_format:Y-m-d',
            'zip_code' => 'nullable|string|max:10',
            'dot_test' => 'required|in:T,F',
            'testing_authority' => 'required_if:dot_test,T|nullable|in:FMCSA,PHMSA,FAA,FTA,FRA,USCG',
            'reason_for_test_id' => 'required_if:is_physical,false|integer',
            'physical_reason_for_test_id' => 'required_if:is_physical,true|nullable|string|max:3',
            'collection_site_id' => 'nullable|string|max:6',
            'end_datetime' => 'nullable|date_format:Y-m-d\TH:i',
            'end_datetime_timezone_id' => 'nullable|integer',
            'observed_requested' => 'nullable|in:Y,N',
            'split_specimen_requested' => 'nullable|in:Y,N',
            'unit_codes' => 'required|array',
            'unit_codes.*' => 'string|max:15',
            'lab_account' => 'required|string|max:20',
            'csl' => 'nullable|string|max:20',
            'contact_name' => 'required_if:is_ebat,true|nullable|string|max:45',
            'telephone_number' => 'required_if:is_ebat,true|nullable|string|max:10',
            'order_comments' => 'nullable|string|max:250',
            'response_url' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        try {
            $orderXml = $this->buildOrderXml($validator->validated());
            $response = $this->createQuestOrder($orderXml);

            if ($response['status'] === 'SUCCESS') {
                // Store order details in database
                $questOrder = $this->storeQuestOrder($validator->validated(), $response, $orderXml);

                // Use the Job class instead of the closure
                // SendQPassportEmail::dispatch(
                //     $response['quest_order_id'],
                //     $validator->validated()
                // )->delay(now()->addSeconds(30));

                return redirect()->route('quest.order-success',  [
                    'quest_order_id' => $response['quest_order_id'],
                    'reference_test_id' => $response['reference_test_id']
                ]);
            } else {
                return back()->withInput()->with(
                    'error',
                    'Failed to create Quest order: ' . ($response['error']['detail'] ?? 'Unknown error')
                );
            }
        } catch (\Exception $e) {
            Log::error('Quest order submission failed: ' . $e->getMessage());
            return back()->withInput()->with(
                'error',
                'An error occurred while submitting to Quest Diagnostics. Please try again.'
            );
        }
    }


    private function buildOrderXml(array $data)
    {
        $xml = new SimpleXMLElement('<Order></Order>');

        // Event Info
        $eventInfo = $xml->addChild('EventInfo');
        if (!empty($data['collection_site_id'])) {
            $eventInfo->addChild('CollectionSiteID', $data['collection_site_id']);
        }

        if (!empty($data['email'])) {
            $emailAddresses = $eventInfo->addChild('EmailAuthorizationAddresses');
            $emailAddresses->addChild('EmailAddress', $data['email']);
        }

        if (!empty($data['end_datetime'])) {
            $eventInfo->addChild('EndDateTime', $data['end_datetime']);
            if (!empty($data['end_datetime_timezone_id'])) {
                $eventInfo->addChild('EndDateTimeTimeZoneID', $data['end_datetime_timezone_id']);
            }
        }

        // Donor Info
        $donorInfo = $xml->addChild('DonorInfo');
        $donorInfo->addChild('FirstName', $data['first_name']);
        $donorInfo->addChild('LastName', $data['last_name']);

        if (!empty($data['middle_name'])) {
            $donorInfo->addChild('MiddleName', $data['middle_name']);
        }

        $donorInfo->addChild('PrimaryID', $data['primary_id']);

        if (!empty($data['primary_id_type'])) {
            $donorInfo->addChild('PrimaryIDType', $data['primary_id_type']);
        }

        if (!empty($data['dob'])) {
            $donorInfo->addChild('DOB', $data['dob']);
        }

        $donorInfo->addChild('PrimaryPhone', preg_replace('/[^0-9]/', '', $data['primary_phone']));

        if (!empty($data['secondary_phone'])) {
            $donorInfo->addChild('SecondaryPhone', preg_replace('/[^0-9]/', '', $data['secondary_phone']));
        }

        if (!empty($data['zip_code'])) {
            $postalAddress = $donorInfo->addChild('PostalAddress');
            $postalAddress->addChild('ZipCode', $data['zip_code']);
        }

        // Client Info - Use environment configuration instead of form data
        $clientInfo = $xml->addChild('ClientInfo');
        if (!empty($data['contact_name'])) {
            $clientInfo->addChild('ContactName', $data['contact_name']);
        }
        if (!empty($data['telephone_number'])) {
            $clientInfo->addChild('TelephoneNumber', preg_replace('/[^0-9]/', '', $data['telephone_number']));
        }

        // Use LabAccount from form data instead of environment
        $clientInfo->addChild('LabAccount', $data['lab_account']);

        if (!empty($data['csl'])) {
            $clientInfo->addChild('CSL', $data['csl']);
        }

        // Test Info
        $testInfo = $xml->addChild('TestInfo');

        // Generate a client reference ID
        $clientReferenceId = 'ORDER_' . time() . '_' . rand(1000, 9999);
        $testInfo->addChild('ClientReferenceID', $clientReferenceId);

        $testInfo->addChild('DOTTest', $data['dot_test']);

        if ($data['dot_test'] === 'T' && !empty($data['testing_authority'])) {
            $testInfo->addChild('TestingAuthority', $data['testing_authority']);
        }

        if (!empty($data['reason_for_test_id'])) {
            $testInfo->addChild('ReasonForTestID', $data['reason_for_test_id']);
        }

        if (!empty($data['physical_reason_for_test_id'])) {
            $testInfo->addChild('PhysicalReasonForTestID', $data['physical_reason_for_test_id']);
        }

        if (!empty($data['observed_requested'])) {
            $testInfo->addChild('ObservedRequested', $data['observed_requested']);
        }

        if (!empty($data['split_specimen_requested'])) {
            $testInfo->addChild('SplitSpecimenRequested', $data['split_specimen_requested']);
        }

        if (!empty($data['order_comments'])) {
            $testInfo->addChild('OrderComments', $data['order_comments']);
        }

        $screenings = $testInfo->addChild('Screenings');
        $unitCodes = $screenings->addChild('UnitCodes');
        foreach ($data['unit_codes'] as $unitCode) {
            $unitCodes->addChild('UnitCode', $unitCode);
        }

        if (!empty($data['response_url'])) {
            $clientCustom = $xml->addChild('ClientCustom');
            $clientCustom->addChild('ResponseURL', $data['response_url']);
        }

        return $xml->asXML();
    }

    // private function createQuestOrder($orderXml)
    // {
    //     $username = env('QUEST_USERNAME', 'cli_SkyrosUAT');
    //     $password = env('QUEST_PASSWORD', 'kfIVZEUj46uM');
    //     $url = config('app.env') === 'production' ? $this->prodUrl : $this->devUrl;

    //     // Log the original order XML for debugging
    //     // Log::info('Original Order XML: ' . $orderXml);

    //     // Build the SOAP request manually
    //     $soapRequest = $this->buildSoapRequest($username, $password, $orderXml);

    //     // Log the SOAP request for debugging
    //     Log::info('SOAP Request: ' . $soapRequest);

    //     $ch = curl_init();

    //     curl_setopt_array($ch, [
    //         CURLOPT_URL => $url,
    //         CURLOPT_POST => true,
    //         CURLOPT_POSTFIELDS => $soapRequest,
    //         CURLOPT_HTTPHEADER => [
    //             'Content-Type: text/xml; charset=utf-8',
    //             'SOAPAction: "http://wssim.labone.com/CreateOrder"',
    //             'Content-Length: ' . strlen($soapRequest)
    //         ],
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_TIMEOUT => 30,
    //         CURLOPT_CONNECTTIMEOUT => 10,
    //         CURLOPT_SSL_VERIFYPEER => false,
    //         CURLOPT_SSL_VERIFYHOST => false,
    //         CURLOPT_VERBOSE => true,
    //     ]);

    //     // Capture verbose output
    //     $verbose = fopen('php://temp', 'w+');
    //     curl_setopt($ch, CURLOPT_STDERR, $verbose);

    //     $response = curl_exec($ch);
    //     $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //     $error = curl_error($ch);

    //     // Get verbose output
    //     rewind($verbose);
    //     $verboseLog = stream_get_contents($verbose);
    //     fclose($verbose);

    //     curl_close($ch);

    //     // Log::info('HTTP Code: ' . $httpCode);
    //     // Log::info('cURL Error: ' . $error);
    //     // Log::info('Raw Response: ' . $response);

    //     if ($error) {
    //         Log::error('cURL Error: ' . $error);
    //         throw new \Exception('Failed to connect to Quest Diagnostics: ' . $error);
    //     }

    //     if ($httpCode !== 200) {
    //         Log::error('HTTP Error: ' . $httpCode);
    //         Log::error('Response Body: ' . $response);
    //         throw new \Exception('Quest Diagnostics returned HTTP ' . $httpCode);
    //     }

    //     return $this->parseSoapResponse($response);
    // }



    private function createQuestOrder($orderXml)
    {
        $username = env('QUEST_USERNAME', 'cli_SkyrosUAT');
        $password = env('QUEST_PASSWORD', 'kfIVZEUj46uM');
        $url = config('app.env') === 'production' ? $this->prodUrl : $this->devUrl;


        // Build the SOAP request manually
        $soapRequest = $this->buildSoapRequest($username, $password, $orderXml);

        Log::info('SOAP Request being sent to: ' . $url);

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $soapRequest,
            CURLOPT_HTTPHEADER => [
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: "http://wssim.labone.com/CreateOrder"',
                'Content-Length: ' . strlen($soapRequest),
                'Connection: Keep-Alive',
                'Keep-Alive: timeout=30, max=10'
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60, // Increased to 60 seconds total timeout
            CURLOPT_CONNECTTIMEOUT => 15, // Increased to 15 seconds connection timeout
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_VERBOSE => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_ENCODING => '', // Enable compression if supported
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        ]);

        // Capture verbose output
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        // Get cURL info for debugging
        $curlInfo = curl_getinfo($ch);
        Log::info('cURL Info: ', [
            'total_time' => $curlInfo['total_time'],
            'connect_time' => $curlInfo['connect_time'],
            'namelookup_time' => $curlInfo['namelookup_time'],
            'pretransfer_time' => $curlInfo['pretransfer_time'],
            'starttransfer_time' => $curlInfo['starttransfer_time'],
        ]);

        // Get verbose output
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        fclose($verbose);

        curl_close($ch);

        if ($error) {
            Log::error('cURL Error Details: ' . $error);
            Log::error('cURL Verbose Log: ' . $verboseLog);

            // Check if this is a timeout or connection issue
            if (strpos($error, 'timed out') !== false) {
                throw new \Exception('Quest Diagnostics API timeout. Please try again in a moment.');
            } elseif (strpos($error, 'Could not resolve host') !== false) {
                throw new \Exception('Cannot connect to Quest Diagnostics server. Please check your internet connection.');
            } else {
                throw new \Exception('Failed to connect to Quest Diagnostics: ' . $error);
            }
        }

        if ($httpCode !== 200) {
            Log::error('HTTP Error: ' . $httpCode);
            Log::error('Response Body: ' . substr($response, 0, 1000));
            throw new \Exception('Quest Diagnostics returned HTTP ' . $httpCode);
        }

        Log::info('Quest API Response received, length: ' . strlen($response));
        return $this->parseSoapResponse($response);
    }

    private function buildSoapRequest($username, $password, $orderXml)
    {
        $orderXml = preg_replace('/<\?xml.*?\?>/', '', $orderXml);
        $orderXml = trim($orderXml);

        return '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" 
                    xmlns:wss="http://wssim.labone.com/">
        <soap:Body>
            <wss:CreateOrder>
            <wss:username>' . $this->escapeXml($username) . '</wss:username>
            <wss:password>' . $this->escapeXml($password) . '</wss:password>
            <wss:orderXml><![CDATA[' . $orderXml . ']]></wss:orderXml>
            </wss:CreateOrder>
        </soap:Body>
        </soap:Envelope>';
    }

    // Add this helper method to properly escape XML content
    private function escapeXml($string)
    {
        return htmlspecialchars($string, ENT_XML1, 'UTF-8');
    }


    private function parseSoapResponse($response)
    {
        // Log response size for debugging
        $responseSize = strlen($response);
        Log::info('SOAP Response received, size: ' . $responseSize . ' bytes');

        // Circuit breaker tracking
        static $failureCount = 0;
        static $lastFailureTime = 0;
        static $circuitOpen = false;

        // Check circuit breaker
        if ($circuitOpen) {
            // If circuit was opened less than 60 seconds ago, fail fast
            if ((time() - $lastFailureTime) < 60) {
                Log::warning('Circuit breaker is open - failing fast');
                return [
                    'method_id' => 'CREATEORDER',
                    'client_reference_id' => null,
                    'reference_test_id' => null,
                    'quest_order_id' => '0',
                    'status' => 'FAILURE',
                    'display_url' => null,
                    'error' => [
                        'id' => 'CIRCUIT_BREAKER',
                        'detail' => 'Service temporarily unavailable due to multiple failures'
                    ],
                    '_raw_response' => substr($response, 0, 1000)
                ];
            } else {
                // Circuit has been open for more than 60 seconds, try again
                $circuitOpen = false;
                $failureCount = 0;
            }
        }

        // Limit response size to prevent DoS/memory issues
        $maxResponseSize = 5 * 1024 * 1024; // 5MB
        if ($responseSize > $maxResponseSize) {
            Log::error('Response exceeds maximum size: ' . $responseSize . ' bytes');
            $this->incrementFailureCount($failureCount, $lastFailureTime, $circuitOpen);
            return [
                'method_id' => 'CREATEORDER',
                'client_reference_id' => null,
                'reference_test_id' => null,
                'quest_order_id' => '0',
                'status' => 'FAILURE',
                'display_url' => null,
                'error' => [
                    'id' => 'RESPONSE_TOO_LARGE',
                    'detail' => 'Response size (' . round($responseSize / 1024, 2) . 'KB) exceeds limit'
                ],
                '_raw_response' => substr($response, 0, 1000)
            ];
        }

        // Clean the response before parsing
        $response = $this->cleanResponse($response);

        // Track parsing time
        $startTime = microtime(true);

        try {
            // Method 1: Try fast SimpleXML parsing first
            $result = $this->parseWithSimpleXML($response, $startTime);
            if ($result !== false) {
                // Success - reset failure count
                $failureCount = 0;
                return $result;
            }

            // Method 2: Try DOMDocument parsing
            $result = $this->parseWithDOMDocument($response, $startTime);
            if ($result !== false) {
                // Success - reset failure count
                $failureCount = 0;
                return $result;
            }

            // Method 3: Try optimized regex parsing as last resort
            $result = $this->parseWithOptimizedRegex($response, $startTime);
            if ($result !== false) {
                // Success - reset failure count
                $failureCount = 0;
                return $result;
            }

            // All parsing methods failed
            throw new \Exception('All parsing methods failed to extract CreateOrderResult');
        } catch (\Exception $e) {
            $this->incrementFailureCount($failureCount, $lastFailureTime, $circuitOpen);

            $parsingTime = round(microtime(true) - $startTime, 3);
            Log::error('SOAP Response Parsing Failed after ' . $parsingTime . 's: ' . $e->getMessage());
            Log::debug('Response snippet (first 2000 chars): ' . substr($response, 0, 2000));

            // Check if this looks like a specific error response
            if (
                strpos($response, 'Invalid order') !== false ||
                strpos($response, 'Invalid credentials') !== false ||
                strpos($response, 'Access denied') !== false
            ) {
                return [
                    'method_id' => 'CREATEORDER',
                    'client_reference_id' => null,
                    'reference_test_id' => null,
                    'quest_order_id' => '0',
                    'status' => 'FAILURE',
                    'display_url' => null,
                    'error' => [
                        'id' => '400',
                        'detail' => 'Invalid order or credentials. Please check your Quest Diagnostics account configuration.'
                    ],
                    '_raw_response' => substr($response, 0, 2000)
                ];
            }

            return [
                'method_id' => 'CREATEORDER',
                'client_reference_id' => null,
                'reference_test_id' => null,
                'quest_order_id' => '0',
                'status' => 'FAILURE',
                'display_url' => null,
                'error' => [
                    'id' => 'PARSE_ERROR',
                    'detail' => 'Failed to parse Quest Diagnostics response. Please try again.'
                ],
                '_raw_response' => substr($response, 0, 2000)
            ];
        }
    }

    /**
     * Clean the response XML before parsing
     */
    private function cleanResponse($response)
    {
        // Remove any null bytes and control characters (except tab, newline, carriage return)
        $response = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $response);

        // Replace multiple spaces/newlines with single ones to reduce size
        $response = preg_replace('/\s+/', ' ', $response);

        // Decode HTML entities if present
        if (strpos($response, '&lt;') !== false || strpos($response, '&gt;') !== false) {
            $response = html_entity_decode($response, ENT_QUOTES | ENT_XML1, 'UTF-8');
        }

        return $response;
    }

    /**
     * Parse using SimpleXML (fastest method)
     */
    private function parseWithSimpleXML($response, $startTime)
    {
        set_time_limit(5); // Limit this method to 5 seconds

        try {
            // Add XML declaration if missing (helps SimpleXML)
            if (strpos($response, '<?xml') === false) {
                $response = '<?xml version="1.0" encoding="UTF-8"?>' . $response;
            }

            // Suppress warnings for invalid XML
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($response);
            $errors = libxml_get_errors();
            libxml_clear_errors();

            if (!$xml || !empty($errors)) {
                return false;
            }

            // Check if this is a SOAP envelope
            $namespaces = $xml->getNamespaces(true);

            // Try common SOAP namespaces
            foreach (['soap', 's', 'SOAP-ENV'] as $nsPrefix) {
                if (isset($namespaces[$nsPrefix]) || isset($namespaces[''])) {
                    $ns = $namespaces[$nsPrefix] ?? $namespaces[''];
                    $body = $xml->children($ns)->Body ?? null;

                    if ($body) {
                        // Look for CreateOrderResponse in any namespace
                        foreach ($body->children() as $child) {
                            if (strpos($child->getName(), 'CreateOrderResponse') !== false) {
                                $resultElement = $child->children();
                                $resultContent = (string)$resultElement;

                                $parsingTime = round(microtime(true) - $startTime, 3);
                                Log::info('Parsed via SimpleXML in ' . $parsingTime . 's');

                                return $this->parseQuestResponse($resultContent);
                            }
                        }
                    }
                }
            }

            // Direct check for CreateOrderResult (non-SOAP response)
            if (isset($xml->CreateOrderResult)) {
                $resultContent = (string)$xml->CreateOrderResult;

                $parsingTime = round(microtime(true) - $startTime, 3);
                Log::info('Parsed direct SimpleXML in ' . $parsingTime . 's');

                return $this->parseQuestResponse($resultContent);
            }

            return false;
        } catch (\Exception $e) {
            Log::debug('SimpleXML parsing failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Parse using DOMDocument (more robust)
     */
    private function parseWithDOMDocument($response, $startTime)
    {
        set_time_limit(7); // Limit this method to 7 seconds

        try {
            $dom = new \DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = false;

            // Suppress XML parsing errors
            $oldErrorReporting = libxml_use_internal_errors(true);

            // Load XML with options
            $loadSuccess = $dom->loadXML($response, LIBXML_NOWARNING | LIBXML_NOERROR | LIBXML_PARSEHUGE);

            if (!$loadSuccess) {
                libxml_use_internal_errors($oldErrorReporting);
                return false;
            }

            libxml_use_internal_errors($oldErrorReporting);

            // Create XPath instance
            $xpath = new \DOMXPath($dom);

            // Register common namespaces
            $namespaces = [
                'soap' => 'http://schemas.xmlsoap.org/soap/envelope/',
                's' => 'http://schemas.xmlsoap.org/soap/envelope/',
                'SOAP-ENV' => 'http://schemas.xmlsoap.org/soap/envelope/',
                'wss' => 'http://wssim.labone.com/'
            ];

            foreach ($namespaces as $prefix => $uri) {
                $xpath->registerNamespace($prefix, $uri);
            }

            // Try multiple XPath queries in order of likelihood
            $queries = [
                '//soap:Body//CreateOrderResult',
                '//s:Body//CreateOrderResult',
                '//SOAP-ENV:Body//CreateOrderResult',
                '//CreateOrderResult',
                '//*[contains(local-name(), "CreateOrderResult")]',
                '//*[local-name()="CreateOrderResult"]',
                '//Body//*[local-name()="CreateOrderResult"]'
            ];

            foreach ($queries as $query) {
                $nodes = $xpath->query($query);

                if ($nodes && $nodes->length > 0) {
                    $resultContent = $nodes->item(0)->nodeValue;

                    // Check if content needs further decoding
                    if (strpos($resultContent, '&lt;') !== false) {
                        $resultContent = html_entity_decode($resultContent, ENT_QUOTES | ENT_XML1, 'UTF-8');
                    }

                    $parsingTime = round(microtime(true) - $startTime, 3);
                    Log::info('Parsed via DOMDocument XPath in ' . $parsingTime . 's (query: ' . $query . ')');

                    return $this->parseQuestResponse($resultContent);
                }
            }

            return false;
        } catch (\Exception $e) {
            Log::debug('DOMDocument parsing failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Parse using optimized regex (last resort)
     */
    private function parseWithOptimizedRegex($response, $startTime)
    {
        set_time_limit(3); // Limit this method to 3 seconds

        try {
            // Optimized regex patterns - ordered by efficiency
            $patterns = [
                // Pattern 1: Direct CDATA with minimal backtracking
                '/<CreateOrderResult[^>]*>\s*<!\[CDATA\[([^]]*)\]\]>\s*<\/CreateOrderResult>/is',

                // Pattern 2: Direct content, non-greedy with lookahead
                '/<CreateOrderResult[^>]*>\s*(.*?)\s*<\/CreateOrderResult>/is',

                // Pattern 3: With any namespace prefix
                '/<(\w+:)?CreateOrderResult[^>]*>\s*(.*?)\s*<\/\1?CreateOrderResult>/is',

                // Pattern 4: Inside SOAP Body
                '/<soap:Body>.*?<CreateOrderResult[^>]*>\s*(.*?)\s*<\/CreateOrderResult>.*?<\/soap:Body>/is',

                // Pattern 5: Case insensitive
                '/<createorderresult[^>]*>\s*(.*?)\s*<\/createorderresult>/is',
            ];

            foreach ($patterns as $patternIndex => $pattern) {
                // Use preg_match with offset to prevent excessive backtracking
                if (preg_match($pattern, $response, $matches, PREG_OFFSET_CAPTURE)) {
                    $resultContent = $matches[count($matches) - 1][0]; // Get last match group

                    // Clean up the result
                    $resultContent = trim($resultContent);

                    // Decode HTML entities if needed
                    if (strpos($resultContent, '&lt;') !== false) {
                        $resultContent = html_entity_decode($resultContent, ENT_QUOTES | ENT_XML1, 'UTF-8');
                    }

                    $parsingTime = round(microtime(true) - $startTime, 3);
                    Log::info('Parsed via regex pattern ' . ($patternIndex + 1) . ' in ' . $parsingTime . 's');

                    return $this->parseQuestResponse($resultContent);
                }
            }

            return false;
        } catch (\Exception $e) {
            Log::debug('Regex parsing failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Increment failure count and manage circuit breaker
     */
    private function incrementFailureCount(&$failureCount, &$lastFailureTime, &$circuitOpen)
    {
        $failureCount++;
        $lastFailureTime = time();

        // Open circuit if we have 3 failures in 30 seconds
        if ($failureCount >= 3 && (time() - $lastFailureTime) < 30) {
            $circuitOpen = true;
            Log::error('Circuit breaker opened due to ' . $failureCount . ' failures in 30 seconds');
        }

        // Reset failure count after 2 minutes of no activity
        if ((time() - $lastFailureTime) > 120) {
            $failureCount = 0;
            $circuitOpen = false;
            Log::info('Circuit breaker reset - failure count cleared');
        }
    }



    private function parseQuestResponse($responseXml, $returnRawOnFailure = false)
    {
        // $this->debugSoapResponse($responseXml);

        // Store the original raw response
        $originalResponse = $responseXml;

        // Check if this is a SOAP envelope response
        if (strpos($responseXml, '<s:Envelope') !== false || strpos($responseXml, '<soap:Envelope') !== false) {
            // Extract the XML content from the SOAP response
            $xmlContent = $this->extractXmlFromSoap($responseXml);

            if ($xmlContent) {
                $responseXml = $xmlContent;
            } else {
                // If extraction fails and we want raw response on failure
                if ($returnRawOnFailure) {
                    return [
                        'status' => 'FAILURE',
                        'error' => [
                            'id' => 'SOAP_EXTRACTION_FAILED',
                            'detail' => 'Failed to extract XML from SOAP response',
                            'raw_response' => $originalResponse
                        ],
                        '_raw_response' => $originalResponse
                    ];
                }

                // Handle the error response directly
                if (strpos($responseXml, 'Invalid order') !== false) {
                    return [
                        'method_id' => 'GETORDERDETAIL',
                        'client_reference_id' => null,
                        'reference_test_id' => null,
                        'quest_order_id' => '0',
                        'status' => 'FAILURE',
                        'display_url' => null,
                        'error' => [
                            'id' => '400',
                            'detail' => 'Invalid order. The order may not exist or may not be accessible with your credentials.',
                            'raw_response' => $originalResponse
                        ],
                        '_raw_response' => $originalResponse
                    ];
                }

                throw new \Exception('Failed to extract XML from SOAP response. Raw: ' . substr($originalResponse, 0, 500));
            }
        }

        // Decode HTML entities if present
        $responseXml = html_entity_decode($responseXml);

        // Remove any XML declaration if present
        $responseXml = preg_replace('/<\?xml.*?\?>/', '', $responseXml);
        $responseXml = trim($responseXml);

        Log::info('Cleaned Quest Response: ' . $responseXml);

        $xml = simplexml_load_string($responseXml);

        if (!$xml) {
            Log::error('Failed to parse XML: ' . $responseXml);

            if ($returnRawOnFailure) {
                return [
                    'status' => 'FAILURE',
                    'error' => [
                        'id' => 'XML_PARSE_ERROR',
                        'detail' => 'Failed to parse XML response',
                        'raw_response' => $originalResponse
                    ],
                    '_raw_response' => $originalResponse
                ];
            }

            throw new \Exception('Invalid XML response from Quest Diagnostics. Raw: ' . substr($originalResponse, 0, 500));
        }

        $result = [
            'method_id' => (string)$xml->MethodID,
            'client_reference_id' => (string)$xml->ClientReferenceID,
            'reference_test_id' => (string)$xml->ReferenceTestID,
            'quest_order_id' => (string)$xml->QuestOrderID,
            'status' => (string)$xml->ResponseStatusID,
            'display_url' => (string)$xml->DisplayURL,
            'error' => null,
            '_raw_response' => $originalResponse // Always include raw response
        ];

        if ($result['status'] === 'FAILURE') {
            // Handle multiple errors
            $errors = [];
            if (isset($xml->Errors) && isset($xml->Errors->Error)) {
                foreach ($xml->Errors->Error as $error) {
                    $errors[] = [
                        'id' => (string)$error->ErrorID,
                        'detail' => (string)$error->ErrorDetail
                    ];
                }
            }

            $result['error'] = !empty($errors) ? $errors[0] : [
                'id' => 'Unknown',
                'detail' => 'Unknown error',
                'raw_response' => $originalResponse
            ];
        }

        return $result;
    }

    private function extractXmlFromSoap($soapResponse)
    {
        try {
            // First, try to parse as regular SOAP response
            $soapXml = simplexml_load_string($soapResponse);
            if ($soapXml) {
                // Register the SOAP namespace
                $soapXml->registerXPathNamespace('s', 'http://schemas.xmlsoap.org/soap/envelope/');

                // Find the response element
                $response = $soapXml->xpath('//s:Body/*');

                if (!empty($response)) {
                    // Get the result content
                    $resultElement = $response[0];
                    $methodName = $resultElement->getName() . 'Result';

                    if (isset($resultElement->$methodName)) {
                        $content = (string)$resultElement->$methodName;

                        // Check if the content contains HTML-encoded XML
                        if (strpos($content, '&lt;?xml') !== false || strpos($content, '&lt;QuestMethodResponse') !== false) {
                            // Decode HTML entities
                            $content = html_entity_decode($content, ENT_QUOTES | ENT_XML1, 'UTF-8');
                        }

                        return $content;
                    }
                }
            }

            // If the above fails, try regex extraction for nested encoded XML
            preg_match('/<GetOrderDetailsResult[^>]*>(.*?)<\/GetOrderDetailsResult>/s', $soapResponse, $matches);

            if (isset($matches[1])) {
                $content = $matches[1];

                // Decode HTML entities if the content contains encoded XML
                if (strpos($content, '&lt;?xml') !== false || strpos($content, '&lt;QuestMethodResponse') !== false) {
                    $content = html_entity_decode($content, ENT_QUOTES | ENT_XML1, 'UTF-8');
                }

                return $content;
            }

            // Try other possible method names
            preg_match('/<(CreateOrder|UpdateOrder|CancelOrder|GetOrderDetails)Result[^>]*>(.*?)<\/\1Result>/s', $soapResponse, $matches);

            if (isset($matches[2])) {
                $content = $matches[2];

                // Decode HTML entities if the content contains encoded XML
                if (strpos($content, '&lt;?xml') !== false || strpos($content, '&lt;QuestMethodResponse') !== false) {
                    $content = html_entity_decode($content, ENT_QUOTES | ENT_XML1, 'UTF-8');
                }

                return $content;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error extracting XML from SOAP: ' . $e->getMessage());
            return null;
        }
    }

    // Store the order in the database
    private function storeQuestOrder($formData, $apiResponse, $orderXml)
    {
        try {
            $questOrder = QuestOrder::create([
                'user_id' => auth()->id(), // If the user is logged in
                'payment_intent_id' => $formData['payment_intent_id'],
                // Quest Identifiers
                'quest_order_id' => $apiResponse['quest_order_id'] ?? null,
                'reference_test_id' => $apiResponse['reference_test_id'] ?? null,
                'client_reference_id' => $apiResponse['client_reference_id'] ?? null,
                // Donor Info
                'first_name' => $formData['first_name'],
                'last_name' => $formData['last_name'],
                'middle_name' => $formData['middle_name'] ?? null,
                'primary_id' => $formData['primary_id'],
                'primary_id_type' => $formData['primary_id_type'] ?? null,
                'dob' => !empty($formData['dob']) ? \Carbon\Carbon::createFromFormat('Y-m-d', $formData['dob']) : null,
                'primary_phone' => $formData['primary_phone'],
                'secondary_phone' => $formData['secondary_phone'] ?? null,
                'email' => $formData['email'] ?? null,
                'zip_code' => $formData['zip_code'] ?? null,
                // Test Info
                'portfolio_name' => $formData['portfolio']->title ?? null, // Assuming you pass the portfolio object
                'unit_codes' => json_encode($formData['unit_codes']),
                'dot_test' => $formData['dot_test'],
                'testing_authority' => $formData['testing_authority'] ?? null,
                'reason_for_test_id' => $formData['reason_for_test_id'] ?? null,
                'physical_reason_for_test_id' => $formData['physical_reason_for_test_id'] ?? null,
                'collection_site_id' => $formData['collection_site_id'] ?? null,
                'observed_requested' => $formData['observed_requested'] ?? 'N',
                'split_specimen_requested' => $formData['split_specimen_requested'] ?? 'N',
                'order_comments' => $formData['order_comments'] ?? null,
                // Client Info
                'lab_account' => env('QUEST_LAB_ACCOUNT'),
                'csl' => $formData['csl'] ?? null,
                'contact_name' => $formData['contact_name'] ?? null,
                'telephone_number' => $formData['telephone_number'] ?? null,
                // Timing
                'end_datetime' => !empty($formData['end_datetime']) ? \Carbon\Carbon::parse($formData['end_datetime']) : null,
                'end_datetime_timezone_id' => $formData['end_datetime_timezone_id'] ?? null,
                // API Logging
                'request_xml' => $orderXml,
                'create_response_xml' => $apiResponse['_raw_response'] ?? null, // You might want to store the raw string
                'create_response_status' => $apiResponse['status'],
                'create_error' => $apiResponse['error'] ? json_encode($apiResponse['error']) : null,
            ]);

            Log::info('Quest Order Stored in Database', ['id' => $questOrder->id, 'quest_order_id' => $questOrder->quest_order_id]);
            return $questOrder;
        } catch (\Exception $e) {
            Log::error('Failed to store Quest order in database:', [
                'error' => $e->getMessage(),
                'quest_response' => $apiResponse
            ]);
            // Even if DB storage fails, don't break the user flow. Just log it.
            return null;
        }
    }

    public function orderSuccess($questOrderId, $referenceTestId)
    {
        return view(
            'quest.order-success',
            array_merge(getFrontendData(),  [
                'questOrderId' => $questOrderId,
                'referenceTestId' => $referenceTestId
            ])
        );
    }

    public function testEnvVars()
    {
        return response()->json([
            'QUEST_USERNAME' => env('QUEST_USERNAME', 'NOT_FOUND'),
            'QUEST_PASSWORD_set' => !empty(env('QUEST_PASSWORD')),
            'QUEST_PASSWORD_length' => strlen(env('QUEST_PASSWORD', '')),
            'APP_ENV' => config('app.env'),
            'all_env_vars' => [
                'QUEST_USERNAME' => substr(env('QUEST_USERNAME', ''), 0, 3) . '...',
                'QUEST_PASSWORD' => str_repeat('*', strlen(env('QUEST_PASSWORD', ''))),
            ]
        ]);
    }



    public function getDocument(Request $request, $questOrderId, $docType)
    {
        $username = env('QUEST_USERNAME');
        $password = env('QUEST_PASSWORD');
        $url = config('app.env') === 'production' ? $this->prodUrl : $this->devUrl;

        // Validate the document type
        $validDocTypes = ['QPassport', 'LabReport', 'MROLetter', 'Copy1', 'Copy2', 'ATF', 'AlcoholReport', 'OHS'];

        if (!in_array($docType, $validDocTypes)) {
            return back()->with('error', 'Invalid document type requested.');
        }

        // Build the DocXML string dynamically
        $docXml = <<<XML
            <GetDocument>
                <QuestOrderID>{$questOrderId}</QuestOrderID>
                <DocType>{$docType}</DocType>
            </GetDocument>
            XML;

        try {
            $client = new \SoapClient($url . '?WSDL', [
                'trace' => 1,
                'exceptions' => true,
                'stream_context' => stream_context_create(['ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]])
            ]);

            // Call the GetDocument method
            $response = $client->GetDocument([
                'username' => $username,
                'password' => $password,
                'DocXml' => $docXml
            ]);

            $result = $this->parseGetDocumentResponse($response->GetDocumentResult);

            if ($result['status'] === 'Success') {
                // $result['doc_stream'] contains the Base64-encoded file
                $fileContent = base64_decode($result['doc_stream']);

                // Determine file extension based on DocFormat
                $fileExtension = strtolower($result['doc_format']) === 'pdf' ? 'pdf' : 'tiff';

                // Create a descriptive filename
                $filename = "{$docType}-{$questOrderId}.{$fileExtension}";

                // Return the file as a download to the browser
                return response()->make($fileContent, 200, [
                    'Content-Type' => $result['doc_format'] === 'PDF' ? 'application/pdf' : 'image/tiff',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]);
            } else {
                Log::error('GetDocument Failed: ', ['error' => $result['error_detail']]);
                return back()->with('error', 'Failed to retrieve document: ' . $result['error_detail']);
            }
        } catch (\SoapFault $e) {
            Log::error('Quest GetDocument SOAP Error: ' . $e->getMessage());
            Log::error('SOAP Request: ' . $client->__getLastRequest());
            Log::error('SOAP Response: ' . $client->__getLastResponse());

            return back()->with('error', 'Failed to retrieve document. Please try again later.');
        }
    }


    private function parseGetDocumentResponse($responseXml)
    {
        try {
            Log::info('Parsing GetDocument Response:', ['xml' => substr($responseXml, 0, 500)]);

            // Clean the response first
            $responseXml = trim($responseXml);

            // Check if response is empty
            if (empty($responseXml)) {
                return [
                    'status' => 'Error',
                    'error_detail' => 'Empty response from Quest',
                    'doc_type' => '',
                    'doc_format' => '',
                    'doc_stream' => ''
                ];
            }

            // Try to parse as XML
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($responseXml);

            if ($xml === false) {
                $errors = libxml_get_errors();
                libxml_clear_errors();

                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->message;
                }

                Log::error('XML Parse Errors:', $errorMessages);

                return [
                    'status' => 'Error',
                    'error_detail' => 'Invalid XML response: ' . implode(', ', $errorMessages),
                    'doc_type' => '',
                    'doc_format' => '',
                    'doc_stream' => ''
                ];
            }

            // Check for error response
            if (isset($xml->ResponseStatusId) && (string)$xml->ResponseStatusId !== 'Success') {
                return [
                    'status' => (string)($xml->ResponseStatusId ?? 'Error'),
                    'error_detail' => (string)($xml->ErrorDetail ?? 'Unknown error'),
                    'doc_type' => (string)($xml->DocType ?? ''),
                    'doc_format' => (string)($xml->DocFormat ?? ''),
                    'doc_stream' => (string)($xml->DocStream ?? '')
                ];
            }

            // Success response
            return [
                'status' => (string)($xml->ResponseStatusId ?? 'Success'),
                'error_detail' => (string)($xml->ErrorDetail ?? ''),
                'doc_type' => (string)($xml->DocType ?? ''),
                'doc_format' => (string)($xml->DocFormat ?? ''),
                'doc_stream' => (string)($xml->DocStream ?? '')
            ];
        } catch (\Exception $e) {
            Log::error('ParseGetDocumentResponse Error: ' . $e->getMessage());
            return [
                'status' => 'Error',
                'error_detail' => 'Parse error: ' . $e->getMessage(),
                'doc_type' => '',
                'doc_format' => '',
                'doc_stream' => ''
            ];
        }
    }


    // Add this method to your QuestDiagnosticsController
    public function getOrderDetails(Request $request, $questOrderId = null, $referenceTestId = null)
    {
        $username = env('QUEST_USERNAME');
        $password = env('QUEST_PASSWORD');
        $url = config('app.env') === 'production' ? $this->prodUrl : $this->devUrl;

        // If no parameters provided, check request input
        $questOrderId = $questOrderId ?: $request->input('quest_order_id');
        $referenceTestId = $referenceTestId ?: $request->input('reference_test_id');

        // Validate that at least one identifier is provided
        if (empty($questOrderId) && empty($referenceTestId)) {
            return back()->with('error', 'Quest Order ID or Reference Test ID is required.');
        }

        try {
            $client = new \SoapClient($url . '?WSDL', [
                'trace' => 1,
                'exceptions' => true,
                'stream_context' => stream_context_create(['ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]])
            ]);

            // Build the parameters according to the example
            $params = [
                'username' => $username,
                'password' => $password,
                'referenceTestId' => $referenceTestId,
                'questOrderId' => $questOrderId,
                'SpecimenID' => '', // Optional but should be included as empty
                'AccountNumber' => '' // Optional but should be included as empty
            ];

            // Remove empty parameters to avoid sending null values
            $params = array_filter($params, function ($value) {
                return $value !== null && $value !== '';
            });

            // Call the GetOrderDetails method with proper parameters
            $response = $client->GetOrderDetails($params);

            // Get the raw SOAP response for debugging
            $rawResponse = $client->__getLastResponse();
            Log::info('Raw SOAP Response: ' . $rawResponse);

            $result = $this->parseQuestResponse($response->GetOrderDetailsResult, true);

            if ($result['status'] === 'SUCCESS') {
                // Store the order details in session for display
                session()->flash('order_details', [
                    'display_url' => $result['display_url'],
                    'quest_order_id' => $result['quest_order_id'],
                    'reference_test_id' => $result['reference_test_id'],
                    'client_reference_id' => $result['client_reference_id']
                ]);

                return redirect()->route('quest.order-details.show');
            } else {
                // Store the raw response for debugging
                $rawResponseData = [
                    'raw_request' => $client->__getLastRequest(),
                    'raw_response' => $rawResponse,
                    'parsed_error' => $result['error'] ?? null
                ];

                Log::error('GetOrderDetails Failed: ', $rawResponseData);

                return back()->with([
                    'error' => 'Failed to retrieve order details: ' . ($result['error']['detail'] ?? 'Unknown error'),
                    'raw_response' => $rawResponse,
                    'raw_request' => $client->__getLastRequest()
                ]);
            }
        } catch (\SoapFault $e) {
            $rawRequest = isset($client) ? $client->__getLastRequest() : 'Client not initialized';
            $rawResponse = isset($client) ? $client->__getLastResponse() : 'Client not initialized';

            Log::error('Quest GetOrderDetails SOAP Error: ' . $e->getMessage());
            Log::error('SOAP Request: ' . $rawRequest);
            Log::error('SOAP Response: ' . $rawResponse);

            return back()->with([
                'error' => 'Failed to retrieve order details. Please try again later.',
                'soap_error' => $e->getMessage(),
                'raw_request' => $rawRequest,
                'raw_response' => $rawResponse
            ]);
        }
    }

    public function showOrderDetails()
    {
        $orderDetails = session('order_details');

        if (!$orderDetails) {
            return redirect()->route('quest.order-form')->with('error', 'No order details found.');
        }

        return view(
            'quest.order-details',
            array_merge(getFrontendData(), [
                'orderDetails' => $orderDetails
            ])
        );
    }

    public function getOrderDetailsForm()
    {
        return view(
            'quest.order-details-form',
            array_merge(getFrontendData(), [])
        );
    }





    // admin dot test start
    public function dotTest($portfolioId = null)
    {
        $portfolio = Portfolio::findOrFail($portfolioId);
        $authUser = Auth::user();
        $role = $authUser->roles()->first();

        $clientProfile = ClientProfile::where('user_id', $authUser->id)->first();

        if ($role->name == 'company') {
            $employees = Employee::with('clientProfile')->where('status', 'active')->where('client_profile_id', $clientProfile->id)->get();
        } elseif ($role->name == 'super-admin') {
            $employees = Employee::with('clientProfile')->where('status', 'active')->get();
        } else {
            $employees = [];
        }

        return view('admin.dot-test.index', compact('portfolio', 'authUser', 'employees'));
    }
    public function processPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'portfolio_id' => 'required|exists:portfolios,id',
            'price' => 'required|numeric',
            'employee_id' => 'required|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $portfolio = Portfolio::find($request->portfolio_id);
            $amount = $request->price; // Already in cents from frontend

            Stripe::setApiKey(env('STRIPE_SECRET'));

            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'usd',
                'metadata' => [
                    'portfolio_id' => $portfolio->id,
                    'test_name' => $portfolio->title,
                    'employee_id' => $request->employee_id,
                ],
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            // Store minimal payment session data
            session([
                'dot_test_payment_data' => [
                    'payment_intent_id' => $paymentIntent->id,
                    'portfolio_id' => $portfolio->id,
                    'amount' => $amount / 100,
                    'test_name' => $portfolio->title,
                    'quest_unit_code' => $portfolio->quest_unit_code,
                    'employee_id' => $request->employee_id,
                ]
            ]);

            return response()->json([
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Payment processing failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed. Please try again.'
            ], 500);
        }
    }

    public function showDotOrderForm(Request $request, $paymentIntent)
    {
        // Retrieve payment data from session
        $paymentData = session('dot_test_payment_data');

        if (!$paymentData || $paymentData['payment_intent_id'] !== $paymentIntent) {
            toastr()->error('Invalid or expired payment session. Please complete payment first.', 'Error');
            return redirect()->route('dot-test.index', ['portfolioId' => $paymentData['portfolio_id'] ?? null]);
        }
        // Get employee data from session or database
        $employee = null;
        if (isset($paymentData['employee_id'])) {
            $employee = Employee::with('clientProfile')->find($paymentData['employee_id']);
        }

        // Get portfolio info
        $portfolio = Portfolio::find($paymentData['portfolio_id']);

        $collectionSites = CollectionSite::orderBy('name')->get();

        return view('admin.dot-test.dot-test-order-form', compact('employee', 'paymentData', 'portfolio'));
    }

    // Add this new method for AJAX search
    public function searchCollectionSites(Request $request)
    {
        try {
            $searchTerm = $request->get('q', '');

            // Validate search term
            if (strlen($searchTerm) < 2) {
                return response()->json([]);
            }

            $sites = CollectionSite::when($searchTerm, function ($query) use ($searchTerm) {
                $searchTerm = '%' . $searchTerm . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', $searchTerm)
                        ->orWhere('address_1', 'LIKE', $searchTerm)
                        ->orWhere('address_2', 'LIKE', $searchTerm)
                        ->orWhere('city', 'LIKE', $searchTerm)
                        ->orWhere('zip_code', 'LIKE', $searchTerm)
                        ->orWhere('state', 'LIKE', $searchTerm);
                });
            })
                ->orderBy('name')
                ->limit(30) // Reduced limit for better performance
                ->get(['id', 'collection_site_code', 'name', 'address_1', 'address_2', 'city', 'state', 'zip_code']);


            $formattedSites = $sites->map(function ($site) {
                return [
                    'id' => $site->id,
                    'collection_site_code' => $site->collection_site_code,
                    'text' => $this->formatSiteDisplay($site)
                ];
            });

            return response()->json($formattedSites);
        } catch (\Exception $e) {
            Log::error('Collection site search error: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    private function formatSiteDisplay($site)
    {
        $parts = [];

        if ($site->name) {
            $parts[] = $site->name;
        }

        $addressParts = [];
        if ($site->address_1) {
            $addressParts[] = $site->address_1;
        }
        if ($site->city) {
            $addressParts[] = $site->city;
        }
        if ($site->state) {
            $addressParts[] = $site->state;
        }
        if ($site->zip_code) {
            $addressParts[] = $site->zip_code;
        }

        if (!empty($addressParts)) {
            $parts[] = implode(', ', $addressParts);
        }

        return implode(' - ', $parts);
    }
}
