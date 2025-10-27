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

    private function createQuestOrder($orderXml)
    {
        $username = env('QUEST_USERNAME', 'cli_SkyrosUAT');
        $password = env('QUEST_PASSWORD', 'kfIVZEUj46uM');
        $url = config('app.env') === 'production' ? $this->prodUrl : $this->devUrl;

        // Log the original order XML for debugging
        // Log::info('Original Order XML: ' . $orderXml);

        // Build the SOAP request manually
        $soapRequest = $this->buildSoapRequest($username, $password, $orderXml);

        // Log the SOAP request for debugging
        Log::info('SOAP Request: ' . $soapRequest);

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $soapRequest,
            CURLOPT_HTTPHEADER => [
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: "http://wssim.labone.com/CreateOrder"',
                'Content-Length: ' . strlen($soapRequest)
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_CONNECTTIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_VERBOSE => true,
        ]);

        // Capture verbose output
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        // Get verbose output
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        fclose($verbose);

        curl_close($ch);

        // Log::info('HTTP Code: ' . $httpCode);
        // Log::info('cURL Error: ' . $error);
        // Log::info('Raw Response: ' . $response);

        if ($error) {
            Log::error('cURL Error: ' . $error);
            throw new \Exception('Failed to connect to Quest Diagnostics: ' . $error);
        }

        if ($httpCode !== 200) {
            Log::error('HTTP Error: ' . $httpCode);
            Log::error('Response Body: ' . $response);
            throw new \Exception('Quest Diagnostics returned HTTP ' . $httpCode);
        }

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
        // Log::info('Raw SOAP Response: ' . $response);

        try {
            // Extract the content between <CreateOrderResult> tags using regex
            preg_match('/<CreateOrderResult[^>]*>(.*?)<\/CreateOrderResult>/s', $response, $matches);

            if (isset($matches[1])) {
                $resultContent = $matches[1];
                // Log::info('Extracted Result Content: ' . $resultContent);

                // Decode HTML entities if the content is encoded
                if (strpos($resultContent, '&lt;') !== false || strpos($resultContent, '&gt;') !== false) {
                    $resultContent = html_entity_decode($resultContent, ENT_QUOTES | ENT_XML1, 'UTF-8');
                    // Log::info('Decoded Result Content: ' . $resultContent);
                }

                return $this->parseQuestResponse($resultContent);
            }

            // If regex extraction fails, try the XML parsing approach
            $response = html_entity_decode($response, ENT_QUOTES | ENT_XML1, 'UTF-8');
            $xml = simplexml_load_string($response);

            if ($xml) {
                $namespaces = $xml->getNamespaces(true);
                $body = $xml->children('soap', true)->Body;

                foreach ($namespaces as $prefix => $ns) {
                    $body = $body->children($ns);
                }

                if (isset($body->CreateOrderResponse->CreateOrderResult)) {
                    $resultContent = (string)$body->CreateOrderResponse->CreateOrderResult;
                    Log::info('Extracted Result Content via XML: ' . $resultContent);
                    return $this->parseQuestResponse($resultContent);
                }
            }

            throw new \Exception('Could not extract CreateOrderResult from SOAP response');
        } catch (\Exception $e) {
            Log::error('SOAP Response Parsing Error: ' . $e->getMessage());
            Log::error('Full Response: ' . $response);
            throw new \Exception('Invalid SOAP response format: ' . $e->getMessage());
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
                'dob' => !empty($formData['dob']) ? \Carbon\Carbon::createFromFormat('m/d/Y', $formData['dob']) : null,
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
                'end_datetime' => !empty($formData['end_datetime']) ? $formData['end_datetime'] : null,
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


    //public function getDocument(Request $request, $questOrderId, $docType)
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

    // Keep your existing parseGetDocumentResponse method
    private function parseGetDocumentResponse($responseXml)
    {
        $xml = simplexml_load_string($responseXml);
        $result = [
            'status' => (string)$xml->ResponseStatusId,
            'error_detail' => (string)$xml->ErrorDetail,
            'doc_type' => (string)$xml->DocType,
            'doc_format' => (string)$xml->DocFormat,
            'doc_stream' => (string)$xml->DocStream,
        ];
        return $result;
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

    // private function debugSoapResponse($soapResponse)
    // {
    //     // Check if it's a SOAP envelope
    //     if (strpos($soapResponse, '<s:Envelope') !== false || strpos($soapResponse, '<soap:Envelope') !== false) {
    //         Log::info('Detected SOAP envelope');

    //         // Look for encoded XML content
    //         if (preg_match('/&lt;\?xml.*?&lt;\/QuestMethodResponse&gt;/s', $soapResponse, $matches)) {
    //             Log::info('Found encoded XML content');
    //             $decoded = html_entity_decode($matches[0], ENT_QUOTES | ENT_XML1, 'UTF-8');
    //             Log::info('Decoded content: ' . $decoded);
    //         }

    //         // Try to extract content
    //         $content = $this->extractXmlFromSoap($soapResponse);
    //         if ($content) {
    //             Log::info('Extracted content: ' . $content);
    //         } else {
    //             Log::info('Could not extract content');
    //             Log::info('Raw response snippet: ' . substr($soapResponse, 0, 500));
    //         }
    //     } else {
    //         Log::info('Not a SOAP envelope');
    //         Log::info('Response: ' . $soapResponse);
    //     }

    //     Log::info('=== END DEBUG ===');
    // }




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
