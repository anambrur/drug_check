<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use SimpleXMLElement;
use Illuminate\Support\Facades\Log;

class QuestCollectionService
{
    protected $client;
    protected $username;
    protected $password;
    protected $endpoint;

    private $devUrl = 'https://qcs-uat.questdiagnostics.com/services/esservice.asmx';
    private $prodUrl = 'https://qcs.questdiagnostics.com/services/esservice.asmx';

    public function __construct()
    {
        $this->client = new Client([
            'verify' => false,
            'timeout' => 900, // 15 minutes
            'connect_timeout' => 60,
            'headers' => [
                'Content-Type' => 'text/xml; charset=utf-8',
            ]
        ]);

        $this->username = env('QUEST_USERNAME', 'cli_SkyrosUAT');
        $this->password = env('QUEST_PASSWORD', 'kfIVZEUj46uM');
        $this->endpoint = config('app.env') === 'production' ? $this->prodUrl : $this->devUrl;
    }

    /**
     * Retrieve all collection sites
     */
    public function getFullCollectionSiteDetails()
    {
        // Store original time limits
        $originalTimeLimit = ini_get('max_execution_time');
        $originalMemoryLimit = ini_get('memory_limit');

        try {
            // Increase time and memory limits
            set_time_limit(1800); // 30 minutes
            ini_set('max_execution_time', 1800);
            ini_set('memory_limit', '1024M');

            Log::info("Starting collection site retrieval with extended time limits");

            $soapRequest = $this->buildSoapRequest();
            $response = $this->makeSoapCall($soapRequest);

            return $response;
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
     * Build SOAP request with correct namespace from WSDL discovery
     */
    private function buildSoapRequest()
    {
        // Use the exact format from the documentation but with correct namespace
        return '<?xml version="1.0" encoding="utf-8"?>
            <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" 
                        xmlns:wss="http://wssim.labone.com/">
                <soap:Body>
                    <wss:FullRetrieveCollectionSiteDetails>
                        <wss:username>' . $this->username . '</wss:username>
                        <wss:password>' . $this->password . '</wss:password>
                    </wss:FullRetrieveCollectionSiteDetails>
                </soap:Body>
            </soap:Envelope>';
    }

    /**
     * Make SOAP call with correct SOAPAction from WSDL discovery
     */
    /**
     * Make SOAP call with correct SOAPAction from WSDL discovery
     */
    private function makeSoapCall($soapBody)
    {
        try {
            Log::info("Making collection site SOAP request to: " . $this->endpoint);

            $soapAction = 'http://wssim.labone.com/FullRetrieveCollectionSiteDetails';

            Log::debug("Using SOAPAction: " . $soapAction);

            $response = $this->client->post($this->endpoint, [
                'headers' => [
                    'Content-Type' => 'text/xml; charset=utf-8',
                    'SOAPAction' => $soapAction,
                    'Accept' => 'text/xml',
                ],
                'body' => $soapBody,
                'http_errors' => false,
                'timeout' => 600, // 10 minutes
                'connect_timeout' => 30,
                'read_timeout' => 600,
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = (string) $response->getBody();

            Log::info("Collection site API Response Status: " . $statusCode);
            Log::info("Response size: " . strlen($responseBody) . " bytes");

            if ($statusCode === 200) {
                return $this->parseSoapResponse($responseBody);
            } else {
                $errorMessage = $this->extractSoapErrorMessage($responseBody);
                throw new \Exception("HTTP {$statusCode} - " . ($errorMessage ?: 'Unknown error'));
            }
        } catch (GuzzleException $e) {
            $errorMsg = 'Collection site API Request Failed: ' . $e->getMessage();
            Log::error($errorMsg);

            if (strpos($e->getMessage(), 'timeout') !== false || strpos($e->getMessage(), 'timed out') !== false) {
                throw new \Exception('Quest API request timed out. The server may be slow or unresponsive. Please try again later or contact Quest support.');
            }

            throw new \Exception($errorMsg);
        }
    }

    /**
     * Extract error message from SOAP fault
     */
    private function extractSoapErrorMessage($responseBody)
    {
        try {
            // Try to parse as XML first
            if (preg_match('/<faultstring>(.*?)<\/faultstring>/is', $responseBody, $matches)) {
                return trim($matches[1]);
            }
            if (preg_match('/<faultstring[^>]*>(.*?)<\/faultstring>/is', $responseBody, $matches)) {
                return trim($matches[1]);
            }
        } catch (\Exception $e) {
            // If parsing fails, return raw response snippet
        }

        return substr($responseBody, 0, 100) . (strlen($responseBody) > 100 ? '...' : '');
    }

    /**
     * Parse SOAP response
     */
    private function parseSoapResponse($responseBody)
    {
        try {
            Log::debug("Starting to parse SOAP response");

            // Clean XML namespaces for easier parsing
            $cleanXml = preg_replace('/<soap:Envelope[^>]*>/', '', $responseBody);
            $cleanXml = preg_replace('/<\/soap:Envelope>/', '', $cleanXml);
            $cleanXml = preg_replace('/<soap:Body[^>]*>/', '', $cleanXml);
            $cleanXml = preg_replace('/<\/soap:Body>/', '', $cleanXml);

            // Replace namespace prefixes but keep the structure
            $cleanXml = preg_replace('/<([a-z]:|wss:)/', '<', $cleanXml);
            $cleanXml = preg_replace('/<\/([a-z]:|wss:)/', '</', $cleanXml);

            Log::debug("Cleaned XML: " . substr($cleanXml, 0, 200) . "...");

            $xml = new SimpleXMLElement($cleanXml);

            // Look for the Result element
            $result = null;
            foreach ($xml->children() as $child) {
                if (strpos($child->getName(), 'Result') !== false) {
                    $result = (string)$child;
                    break;
                }
            }

            if ($result) {
                Log::debug("Found result content: " . $result);

                // Check for error messages
                if (trim($result) === 'Identifier Not Found') {
                    throw new \Exception('Authentication failed: Identifier Not Found. Please check your credentials and ensure your account has access to the collection site API.');
                }

                if (trim($result) === 'Invalid Credentials') {
                    throw new \Exception('Authentication failed: Invalid Credentials. Please check your username and password.');
                }

                if (trim($result) === 'Access Denied') {
                    throw new \Exception('Authentication failed: Access Denied. Your account may not have permission to access this service.');
                }

                // Check if result contains XML
                if (strpos(trim($result), '<') === 0) {
                    $collectionSitesXml = new SimpleXMLElement($result);
                } else {
                    // If not XML, try parsing the original response
                    $collectionSitesXml = new SimpleXMLElement($cleanXml);
                }

                $sites = [];
                if (isset($collectionSitesXml->CollectionSiteDetail)) {
                    foreach ($collectionSitesXml->CollectionSiteDetail as $site) {
                        $sites[] = $this->parseSiteData($site);
                    }
                }

                return [
                    'sites' => $sites,
                    'total_changed' => isset($collectionSitesXml->NumberOfCollectionSitesChanged) ?
                        (int)$collectionSitesXml->NumberOfCollectionSitesChanged : count($sites)
                ];
            }

            throw new \Exception('Invalid response format - No Result element found');
        } catch (\Exception $e) {
            Log::error('Failed to parse collection site API response: ' . $e->getMessage());
            Log::error('Response that failed parsing: ' . substr($responseBody, 0, 500));
            throw new \Exception('Failed to parse API response: ' . $e->getMessage());
        }
    }

    /**
     * Parse individual site data
     */
    private function parseSiteData($site)
    {
        return [
            'site_code' => (string)($site->SiteCode ?? ''),
            'status' => (string)($site->Status ?? ''),
            'address' => [
                'name' => (string)($site->Address->Name ?? ''),
                'address1' => (string)($site->Address->Address1 ?? ''),
                'address2' => (string)($site->Address->Address2 ?? ''),
                'city' => (string)($site->Address->City ?? ''),
                'state' => (string)($site->Address->State ?? ''),
                'zip' => (string)($site->Address->Zip ?? ''),
                'county' => (string)($site->Address->County ?? ''),
            ],
            'primary_phone' => (string)($site->PrimaryPhoneNumber ?? ''),
            'secondary_phone' => (string)($site->SecondaryPhoneNumber ?? ''),
            'fax_number' => (string)($site->FaxNumber ?? ''),
            'active_date' => (string)($site->ActiveDate ?? ''),
            'inactive_date' => (string)($site->InactiveDate ?? ''),
            'latitude' => !empty((string)$site->Latitude) ? (float)$site->Latitude : null,
            'longitude' => !empty((string)$site->Longitude) ? (float)$site->Longitude : null,
            'is_active' => isset($site->IsActive) ? filter_var((string)$site->IsActive, FILTER_VALIDATE_BOOLEAN) : false,
            'time_zone' => (string)($site->TimeZone ?? ''),
            'site_type_id' => isset($site->CollectionSiteTypeId) ? (int)$site->CollectionSiteTypeId : 0,
            'scheduling' => isset($site->Scheduling) ? filter_var((string)$site->Scheduling, FILTER_VALIDATE_BOOLEAN) : false,
            'phlebotomy' => isset($site->Phlebotomy) ? filter_var((string)$site->Phlebotomy, FILTER_VALIDATE_BOOLEAN) : false,
            'nida_collections' => isset($site->NIDACollections) ? filter_var((string)$site->NIDACollections, FILTER_VALIDATE_BOOLEAN) : false,
            'sap_collections' => isset($site->SAPCollections) ? filter_var((string)$site->SAPCollections, FILTER_VALIDATE_BOOLEAN) : false,
            'observed_collection' => isset($site->ObservedCollection) ? filter_var((string)$site->ObservedCollection, FILTER_VALIDATE_BOOLEAN) : false,
            'breath_alcohol' => isset($site->BreathAlcohol) ? filter_var((string)$site->BreathAlcohol, FILTER_VALIDATE_BOOLEAN) : false,
            'e_breath_alcohol' => isset($site->eBreathAlcohol) ? filter_var((string)$site->eBreathAlcohol, FILTER_VALIDATE_BOOLEAN) : false,
            'pediatrics' => isset($site->Pediatrics) ? filter_var((string)$site->Pediatrics, FILTER_VALIDATE_BOOLEAN) : false,
            'hair_collections' => isset($site->HairCollections) ? filter_var((string)$site->HairCollections, FILTER_VALIDATE_BOOLEAN) : false,
            'open_to_public' => isset($site->OpenToPublic) ? filter_var((string)$site->OpenToPublic, FILTER_VALIDATE_BOOLEAN) : false,
            'hours_of_operation' => (string)($site->HoursOfOperation ?? ''),
            'drug_operation_hours' => (string)($site->DrugOperationHours ?? ''),
            'last_updated' => now()->toISOString()
        ];
    }

    /**
     * Test connection to Quest Collection Site API
     */
    public function testConnection()
    {
        try {
            $soapRequest = $this->buildSoapRequest();

            $response = $this->client->post($this->endpoint, [
                'headers' => [
                    'Content-Type' => 'text/xml; charset=utf-8',
                    'SOAPAction' => 'http://wssim.labone.com/FullRetrieveCollectionSiteDetails',
                    'Accept' => 'text/xml',
                ],
                'body' => $soapRequest,
                'http_errors' => false
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = (string) $response->getBody();

            if ($statusCode === 200) {
                return [
                    'success' => true,
                    'message' => 'Connection successful - HTTP 200 OK',
                    'response_preview' => substr($responseBody, 0, 200) . '...'
                ];
            } else {
                $errorMessage = $this->extractSoapErrorMessage($responseBody);
                return [
                    'success' => false,
                    'message' => "HTTP {$statusCode} - " . ($errorMessage ?: 'Unknown error'),
                    'response_preview' => substr($responseBody, 0, 200) . '...'
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ];
        }
    }
}
