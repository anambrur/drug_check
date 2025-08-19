<?php

namespace App\Http\Controllers\Frontend;

use SimpleXMLElement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class QuestDiagnosticsController extends Controller
{
    private $devUrl = 'https://ocs-uat.questdiagnostics.com/services/ESPService.asmx';
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
        $validator = Validator::make($request->all(), [
            'payment_intent_id' => 'required|string',
            'first_name' => 'required|string|max:20',
            'last_name' => 'required|string|max:25',
            'middle_name' => 'nullable|string|max:20',
            'email' => 'required_if:is_physical,true|email|max:254',
            'primary_phone' => 'required|string',
            'secondary_phone' => 'nullable|string',
            'primary_id' => 'required|string|max:25',
            'primary_id_type' => 'nullable|string|max:5',
            'dob' => 'nullable|date_format:m/d/Y',
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
            'lab_account' => 'required|string',
            'csl' => 'nullable|string|max:20',
            'contact_name' => 'required_if:is_ebat,true|nullable|string|max:45',
            'telephone_number' => 'required_if:is_ebat,true|nullable|string|max:10',
            'order_comments' => 'nullable|string|max:250',
            'response_url' => 'nullable|url|max:255',
        ]);

        // Any error checking
        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        try {
            // Test connection first
            $this->testQuestConnection();

            $orderXml = $this->buildOrderXml($validator->validated());
            $response = $this->createQuestOrder($orderXml);

            if ($response['status'] === 'SUCCESS') {
                // Store order details in database
                $questOrder = $this->storeQuestOrder($validator->validated(), $response);

                return redirect()->route('quest.order-success', [
                    'quest_order_id' => $response['quest_order_id'],
                    'reference_test_id' => $response['reference_test_id']
                ]);
            } else {
                return back()->withInput()->with(
                    'error',
                    'Failed to create Quest order: ' . $response['error']['detail']
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
            $eventInfo->addChild('EndDateTimeTimeZoneID', $data['end_datetime_timezone_id']);
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
        $clientInfo->addChild('ContactName', $data['contact_name']);
        if (!empty($data['telephone_number'])) {
            $clientInfo->addChild('TelephoneNumber', preg_replace('/[^0-9]/', '', $data['telephone_number']));
        }
        $clientInfo->addChild('LabAccount', $data['lab_account']);

        if (!empty($data['csl'])) {
            $clientInfo->addChild('CSL', $data['csl']);
        }

        // Test Info
        $testInfo = $xml->addChild('TestInfo');

        if (!empty($data['client_reference_id'])) {
            $testInfo->addChild('ClientReferenceID', $data['client_reference_id']);
        }

        $testInfo->addChild('DOTTest', $data['dot_test']);

        if ($data['dot_test'] === 'T') {
            $testInfo->addChild('TestingAuthority', $data['testing_authority']);
        }

        $testInfo->addChild('ReasonForTestID', $data['reason_for_test_id']);

        if (!empty($data['physical_reason_for_test_id'])) {
            $testInfo->addChild('PhysicalReasonForTestID', $data['physical_reason_for_test_id']);
        }

        if (!empty($data['observed_requested'])) {
            $testInfo->addChild('ObservedRequested', $data['observed_requested']);
        }

        if (!empty($data['split_specimen_requested'])) {
            $testInfo->addChild('SplitSpecimenRequested', $data['split_specimen_requested']);
        }

        if (!empty($data['cso'])) {
            $csos = $testInfo->addChild('CSOs');
            foreach ($data['cso'] as $cso) {
                $csoNode = $csos->addChild('CSO');
                $csoNode->addChild('CSONumber', $cso['number']);
                $csoNode->addChild('CSOPrompt', $cso['prompt']);
                $csoNode->addChild('CSOText', $cso['text']);
            }
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
        $username = env('QUEST_USERNAME');
        $password = env('QUEST_PASSWORD');
        $url = config('app.env') === 'production' ? $this->prodUrl : $this->devUrl;

        // Disable SSL verification for local development
        $contextOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $context = stream_context_create($contextOptions);

        try {
            $client = new \SoapClient($url . '?WSDL', [
                'trace' => 1,
                'exceptions' => true,
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'connection_timeout' => 30
            ]);

            $response = $client->CreateOrder([
                'username' => $username,
                'password' => $password,
                'orderXml' => $orderXml
            ]);

            return $this->parseQuestResponse($response->CreateOrderResult);
        } catch (\SoapFault $e) {
            Log::error('Quest SOAP Error: ' . $e->getMessage());
            throw new \Exception('Failed to connect to Quest Diagnostics: ' . $e->getMessage());
        }
    }

    private function testQuestConnection()
    {
        $url = config('app.env') === 'production' ? $this->prodUrl : $this->devUrl;
        $testUrl = $url . '?WSDL';

        // Test with cURL first
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $testUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_NOBODY => true, // HEAD request only
            CURLOPT_FOLLOWLOCATION => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception("Cannot connect to Quest Diagnostics. HTTP Code: $httpCode. Error: $error");
        }

        return true;
    }

    private function parseQuestResponse($responseXml)
    {
        $xml = simplexml_load_string($responseXml);

        $result = [
            'method_id' => (string)$xml->MethodID,
            'client_reference_id' => (string)$xml->ClientReferenceID,
            'reference_test_id' => (string)$xml->ReferenceTestID,
            'quest_order_id' => (string)$xml->QuestOrderID,
            'status' => (string)$xml->ResponseStatusID,
            'display_url' => (string)$xml->DisplayURL,
            'error' => null
        ];

        if ($result['status'] === 'FAILURE') {
            $result['error'] = [
                'id' => (string)$xml->Error->ErrorID,
                'detail' => (string)$xml->Error->ErrorDetail
            ];
        }

        return $result;
    }

    // private function storeQuestOrder($data, $response)
    // {
    //     // Implement your database storage logic here
    //     // Example:
    //     return \App\Models\QuestOrder::create([
    //         'user_id' => auth()->id(),
    //         'quest_order_id' => $response['quest_order_id'],
    //         'reference_test_id' => $response['reference_test_id'],
    //         'client_reference_id' => $data['client_reference_id'] ?? null,
    //         'first_name' => $data['first_name'],
    //         'last_name' => $data['last_name'],
    //         'dob' => !empty($data['dob']) ? \Carbon\Carbon::createFromFormat('m/d/Y', $data['dob']) : null,
    //         'status' => 'created',
    //         'request_xml' => $this->buildOrderXml($data),
    //         'response_xml' => $responseXml,
    //         'test_type' => implode(',', $data['unit_codes'])
    //     ]);
    // }

    public function orderSuccess($questOrderId, $referenceTestId)
    {
        return view('quest.order-success', [
            'questOrderId' => $questOrderId,
            'referenceTestId' => $referenceTestId
        ]);
    }
}
