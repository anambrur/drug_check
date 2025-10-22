<?php

namespace App\Http\Controllers\Frontend;

use Carbon\Carbon;
use Stripe\Stripe;
use SimpleXMLElement;
use Stripe\PaymentIntent;
use Illuminate\Http\Request;
use App\Models\Admin\Employee;
use App\Models\Admin\Portfolio;
use App\Jobs\SendQPassportEmail;
use App\Models\Admin\QuestOrder;
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
        $paymentData = $request->session()->get('payment_data');

        if (!$paymentData) {
            toastr()->error('Payment data not found. Please complete payment first.', 'Error');
            return redirect()->back();
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
            'csl' => 'nullable|string|max:20',
            'contact_name' => 'required_if:is_ebat,true|nullable|string|max:45',
            'telephone_number' => 'required_if:is_ebat,true|nullable|string|max:10',
            'order_comments' => 'nullable|string|max:250',
            'response_url' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'Validation Error');
            return back();
        }

        try {
            $orderXml = $this->buildOrderXml($validator->validated());
            $response = $this->createQuestOrder($orderXml);

            if ($response['status'] === 'SUCCESS') {
                $questOrder = $this->storeQuestOrder($validator->validated(), $response, $orderXml);

                // Queue email job
                // SendQPassportEmail::dispatch(
                //     $response['quest_order_id'],
                //     $validator->validated()
                // )->delay(now()->addSeconds(30));

                // toastr()->success('Order created successfully! QPassport will be emailed shortly.', 'Success');
                return redirect()->route('quest.order-success',  [
                    'quest_order_id' => $response['quest_order_id'],
                    'reference_test_id' => $response['reference_test_id']
                ]);
            } else {
                $errorMessage = $response['error']['detail'] ?? 'Unknown error';
                toastr()->error('Failed to create Quest order: ' . $errorMessage, 'Error');
                return back()->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Quest order submission failed: ' . $e->getMessage());
            toastr()->error('An error occurred while submitting to Quest Diagnostics. Please try again.', 'Error');
            return back()->withInput();
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

        // Client Info
        $clientInfo = $xml->addChild('ClientInfo');
        if (!empty($data['contact_name'])) {
            $clientInfo->addChild('ContactName', $data['contact_name']);
        }
        if (!empty($data['telephone_number'])) {
            $clientInfo->addChild('TelephoneNumber', preg_replace('/[^0-9]/', '', $data['telephone_number']));
        }

        $clientInfo->addChild('LabAccount', env('QUEST_LAB_ACCOUNT'));

        if (!empty($data['csl'])) {
            $clientInfo->addChild('CSL', $data['csl']);
        }

        // Test Info
        $testInfo = $xml->addChild('TestInfo');
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
        set_time_limit(120); // 2 minutes
        $username = env('QUEST_USERNAME', 'cli_SkyrosUAT');
        $password = env('QUEST_PASSWORD', 'kfIVZEUj46uM');
        $url = config('app.env') === 'production' ? $this->prodUrl : $this->devUrl;

        $contextOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ],
            'http' => [
                'timeout' => 60, // 60 second timeout
                'connection_timeout' => 30
            ]
        ];

        $context = stream_context_create($contextOptions);

        $maxRetries = 3;
        $retryCount = 0;


        // try {
        //     $client = new \SoapClient($url . '?WSDL', [
        //         'trace' => 1,
        //         'exceptions' => true,
        //         'stream_context' => $context,
        //         'cache_wsdl' => WSDL_CACHE_NONE,
        //         'connection_timeout' => 30
        //     ]);

        //     $response = $client->CreateOrder([
        //         'username' => $username,
        //         'password' => $password,
        //         'orderXml' => $orderXml
        //     ]);

        //     return $this->parseQuestResponse($response->CreateOrderResult);
        // } catch (\SoapFault $e) {
        //     Log::error('Quest SOAP Error: ' . $e->getMessage());
        //     throw new \Exception('Failed to connect to Quest Diagnostics: ' . $e->getMessage());
        // }

        while ($retryCount < $maxRetries) {
            try {
                $client = new \SoapClient($url . '?WSDL', [
                    'trace' => 1,
                    'exceptions' => true,
                    'stream_context' => $context,
                    'cache_wsdl' => WSDL_CACHE_NONE,
                    'connection_timeout' => 30,
                    'response_timeout' => 60 // Add response timeout
                ]);

                $response = $client->CreateOrder([
                    'username' => $username,
                    'password' => $password,
                    'orderXml' => $orderXml
                ]);

                return $this->parseQuestResponse($response->CreateOrderResult);
            } catch (\SoapFault $e) {
                $retryCount++;
                Log::warning("Quest SOAP Attempt {$retryCount} failed: " . $e->getMessage());

                if ($retryCount >= $maxRetries) {
                    Log::error('Quest SOAP Error after ' . $maxRetries . ' attempts: ' . $e->getMessage());
                    throw new \Exception('Failed to connect to Quest Diagnostics after ' . $maxRetries . ' attempts: ' . $e->getMessage());
                }

                // Wait before retrying (exponential backoff)
                sleep(pow(2, $retryCount));
            }
        }
    }

    private function parseQuestResponse($responseXml, $returnRawOnFailure = false)
    {
        $originalResponse = $responseXml;

        if (strpos($responseXml, '<s:Envelope') !== false || strpos($responseXml, '<soap:Envelope') !== false) {
            $xmlContent = $this->extractXmlFromSoap($responseXml);
            if ($xmlContent) {
                $responseXml = $xmlContent;
            } else {
                if ($returnRawOnFailure) {
                    return [
                        'status' => 'FAILURE',
                        'error' => [
                            'id' => 'SOAP_EXTRACTION_FAILED',
                            'detail' => 'Failed to extract XML from SOAP response'
                        ],
                        '_raw_response' => $originalResponse
                    ];
                }

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
                            'detail' => 'Invalid order. The order may not exist or may not be accessible with your credentials.'
                        ]
                    ];
                }

                throw new \Exception('Failed to extract XML from SOAP response');
            }
        }

        $responseXml = html_entity_decode($responseXml);
        $responseXml = preg_replace('/<\?xml.*?\?>/', '', $responseXml);
        $responseXml = trim($responseXml);

        $xml = simplexml_load_string($responseXml);

        if (!$xml) {
            if ($returnRawOnFailure) {
                return [
                    'status' => 'FAILURE',
                    'error' => [
                        'id' => 'XML_PARSE_ERROR',
                        'detail' => 'Failed to parse XML response'
                    ]
                ];
            }
            throw new \Exception('Invalid XML response from Quest Diagnostics');
        }

        $result = [
            'method_id' => (string)$xml->MethodID,
            'client_reference_id' => (string)$xml->ClientReferenceID,
            'reference_test_id' => (string)$xml->ReferenceTestID,
            'quest_order_id' => (string)$xml->QuestOrderID,
            'status' => (string)$xml->ResponseStatusID,
            'display_url' => (string)$xml->DisplayURL,
            'error' => null,
            '_raw_response' => $originalResponse
        ];

        if ($result['status'] === 'FAILURE') {
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
                'detail' => 'Unknown error'
            ];
        }

        return $result;
    }

    private function extractXmlFromSoap($soapResponse)
    {
        try {
            $soapXml = simplexml_load_string($soapResponse);
            if ($soapXml) {
                $soapXml->registerXPathNamespace('s', 'http://schemas.xmlsoap.org/soap/envelope/');
                $response = $soapXml->xpath('//s:Body/*');

                if (!empty($response)) {
                    $resultElement = $response[0];
                    $methodName = $resultElement->getName() . 'Result';

                    if (isset($resultElement->$methodName)) {
                        $content = (string)$resultElement->$methodName;
                        if (strpos($content, '&lt;?xml') !== false || strpos($content, '&lt;QuestMethodResponse') !== false) {
                            $content = html_entity_decode($content, ENT_QUOTES | ENT_XML1, 'UTF-8');
                        }
                        return $content;
                    }
                }
            }

            preg_match('/<GetOrderDetailsResult[^>]*>(.*?)<\/GetOrderDetailsResult>/s', $soapResponse, $matches);
            if (isset($matches[1])) {
                $content = $matches[1];
                if (strpos($content, '&lt;?xml') !== false || strpos($content, '&lt;QuestMethodResponse') !== false) {
                    $content = html_entity_decode($content, ENT_QUOTES | ENT_XML1, 'UTF-8');
                }
                return $content;
            }

            preg_match('/<(CreateOrder|UpdateOrder|CancelOrder|GetOrderDetails)Result[^>]*>(.*?)<\/\1Result>/s', $soapResponse, $matches);
            if (isset($matches[2])) {
                $content = $matches[2];
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

    private function storeQuestOrder($formData, $apiResponse, $orderXml)
    {
        try {
            $questOrder = QuestOrder::create([
                'user_id' => auth()->id(),
                'payment_intent_id' => $formData['payment_intent_id'],
                'quest_order_id' => $apiResponse['quest_order_id'] ?? null,
                'reference_test_id' => $apiResponse['reference_test_id'] ?? null,
                'client_reference_id' => $apiResponse['client_reference_id'] ?? null,
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
                'portfolio_name' => $formData['portfolio']->title ?? null,
                'unit_codes' => json_encode($formData['unit_codes']),
                'dot_test' => $formData['dot_test'],
                'testing_authority' => $formData['testing_authority'] ?? null,
                'reason_for_test_id' => $formData['reason_for_test_id'] ?? null,
                'physical_reason_for_test_id' => $formData['physical_reason_for_test_id'] ?? null,
                'collection_site_id' => $formData['collection_site_id'] ?? null,
                'observed_requested' => $formData['observed_requested'] ?? 'N',
                'split_specimen_requested' => $formData['split_specimen_requested'] ?? 'N',
                'order_comments' => $formData['order_comments'] ?? null,
                'lab_account' => env('QUEST_LAB_ACCOUNT'),
                'csl' => $formData['csl'] ?? null,
                'contact_name' => $formData['contact_name'] ?? null,
                'telephone_number' => $formData['telephone_number'] ?? null,
                'end_datetime' => !empty($formData['end_datetime']) ? $formData['end_datetime'] : null,
                'end_datetime_timezone_id' => $formData['end_datetime_timezone_id'] ?? null,
                'request_xml' => $orderXml,
                'create_response_xml' => $apiResponse['_raw_response'] ?? null,
                'create_response_status' => $apiResponse['status'],
                'create_error' => $apiResponse['error'] ? json_encode($apiResponse['error']) : null,
            ]);

            return $questOrder;
        } catch (\Exception $e) {
            Log::error('Failed to store Quest order in database: ' . $e->getMessage());
            return null;
        }
    }

    public function orderSuccess($questOrderId, $referenceTestId)
    {
        toastr()->success('Your order has been placed successfully!', 'Success');
        return view(
            'quest.order-success',
            array_merge(getFrontendData(),  [
                'questOrderId' => $questOrderId,
                'referenceTestId' => $referenceTestId
            ])
        );
    }

    public function getDocument(Request $request, $questOrderId, $docType)
    {
        $username = env('QUEST_USERNAME');
        $password = env('QUEST_PASSWORD');
        $url = config('app.env') === 'production' ? $this->prodUrl : $this->devUrl;

        $validDocTypes = ['QPassport', 'LabReport', 'MROLetter', 'Copy1', 'Copy2', 'ATF', 'AlcoholReport', 'OHS'];

        if (!in_array($docType, $validDocTypes)) {
            toastr()->error('Invalid document type requested.', 'Error');
            return back();
        }

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

            $response = $client->GetDocument([
                'username' => $username,
                'password' => $password,
                'DocXml' => $docXml
            ]);

            $result = $this->parseGetDocumentResponse($response->GetDocumentResult);

            if ($result['status'] === 'Success') {
                $fileContent = base64_decode($result['doc_stream']);
                $fileExtension = strtolower($result['doc_format']) === 'pdf' ? 'pdf' : 'tiff';
                $filename = "{$docType}-{$questOrderId}.{$fileExtension}";

                return response()->make($fileContent, 200, [
                    'Content-Type' => $result['doc_format'] === 'PDF' ? 'application/pdf' : 'image/tiff',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]);
            } else {
                toastr()->error('Failed to retrieve document: ' . $result['error_detail'], 'Error');
                return back();
            }
        } catch (\SoapFault $e) {
            toastr()->error('Failed to retrieve document. Please try again later.', 'Error');
            return back();
        }
    }

    private function parseGetDocumentResponse($responseXml)
    {
        $xml = simplexml_load_string($responseXml);
        return [
            'status' => (string)$xml->ResponseStatusId,
            'error_detail' => (string)$xml->ErrorDetail,
            'doc_type' => (string)$xml->DocType,
            'doc_format' => (string)$xml->DocFormat,
            'doc_stream' => (string)$xml->DocStream,
        ];
    }

    public function getOrderDetails(Request $request, $questOrderId = null, $referenceTestId = null)
    {
        $username = env('QUEST_USERNAME');
        $password = env('QUEST_PASSWORD');
        $url = config('app.env') === 'production' ? $this->prodUrl : $this->devUrl;

        $questOrderId = $questOrderId ?: $request->input('quest_order_id');
        $referenceTestId = $referenceTestId ?: $request->input('reference_test_id');

        if (empty($questOrderId) && empty($referenceTestId)) {
            toastr()->error('Quest Order ID or Reference Test ID is required.', 'Error');
            return back();
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

            $params = [
                'username' => $username,
                'password' => $password,
                'referenceTestId' => $referenceTestId,
                'questOrderId' => $questOrderId,
                'SpecimenID' => '',
                'AccountNumber' => ''
            ];

            $params = array_filter($params, function ($value) {
                return $value !== null && $value !== '';
            });

            $response = $client->GetOrderDetails($params);
            $result = $this->parseQuestResponse($response->GetOrderDetailsResult, true);

            if ($result['status'] === 'SUCCESS') {
                session()->flash('order_details', [
                    'display_url' => $result['display_url'],
                    'quest_order_id' => $result['quest_order_id'],
                    'reference_test_id' => $result['reference_test_id'],
                    'client_reference_id' => $result['client_reference_id']
                ]);

                toastr()->success('Order details retrieved successfully.', 'Success');
                return redirect()->route('quest.order-details.show');
            } else {
                $errorMessage = $result['error']['detail'] ?? 'Unknown error';
                toastr()->error('Failed to retrieve order details: ' . $errorMessage, 'Error');
                return back();
            }
        } catch (\SoapFault $e) {
            toastr()->error('Failed to retrieve order details. Please try again later.', 'Error');
            return back();
        }
    }

    public function showOrderDetails()
    {
        $orderDetails = session('order_details');

        if (!$orderDetails) {
            toastr()->error('No order details found.', 'Error');
            return redirect()->route('quest.order-form');
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

        return view('admin.dot-test.index', compact('portfolio', 'authUser'));
    }
    public function processPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'portfolio_id' => 'required|exists:portfolios,id',
            'price' => 'required|numeric',
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

        $authUser = Auth::user();
        $employee = Employee::with('clientProfile')->where('user_id', $authUser->id)->first();


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
