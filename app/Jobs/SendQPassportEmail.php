<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\QPassportNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendQPassportEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $questOrderId;
    public $formData;
    public $tries = 3; // Number of retries
    public $timeout = 120; // 2 minutes timeout

    public function __construct($questOrderId, $formData)
    {
        $this->questOrderId = $questOrderId;
        $this->formData = $formData;
    }

    public function handle()
    {
        set_time_limit(120); // 2 minutes max

        try {
            $username = env('QUEST_USERNAME');
            $password = env('QUEST_PASSWORD');
            $url = config('app.env') === 'production'
                ? 'https://ocs.questdiagnostics.com/services/ESPService.asmx'
                : 'https://qcs-uat.questdiagnostics.com/services/ESPService.asmx';

            // More aggressive timeout settings
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
                'http' => [
                    'timeout' => 45, // 45 seconds for HTTP
                    'connection_timeout' => 30 // 30 seconds for connection
                ],
                'socket' => [
                    'bindto' => '0:0', // Force fresh connection
                ]
            ]);

            $client = new \SoapClient($url . '?WSDL', [
                'trace' => 1,
                'exceptions' => true,
                'stream_context' => $context,
                'connection_timeout' => 30,
                'keep_alive' => false, // Don't keep connection alive
                'cache_wsdl' => WSDL_CACHE_NONE, // Don't cache WSDL
            ]);

            // Set a timeout for the SOAP call itself
            ini_set('default_socket_timeout', 45);

            $docXml = <<<XML
                <GetDocument>
                    <QuestOrderID>{$this->questOrderId}</QuestOrderID>
                    <DocType>QPassport</DocType>
                </GetDocument>
                XML;

            Log::info('Retrieving QPassport for order: ' . $this->questOrderId);
            log::info('form data: ' . json_encode($this->formData));

            $response = $client->GetDocument([
                'username' => $username,
                'password' => $password,
                'DocXml' => $docXml
            ]);

            $result = $this->parseGetDocumentResponse($response->GetDocumentResult);

            if ($result['status'] === 'Success') {
                $fileContent = base64_decode($result['doc_stream']);
                $fileExtension = strtolower($result['doc_format']) === 'pdf' ? 'pdf' : 'tiff';

                Mail::to($this->formData['email'])->send(new QPassportNotification(
                    $this->formData['first_name'],
                    $this->formData['last_name'],
                    $this->questOrderId,
                    $fileContent,
                    $fileExtension
                ));
                Log::info('QPassport email sent for order: ' . $this->questOrderId);
            } else {
                Log::warning('QPassport not available for order: ' . $this->questOrderId);
            }
        } catch (\SoapFault $e) {
            Log::error('SOAP Error retrieving QPassport: ' . $e->getMessage());
            $this->release(60); // Retry after 60 seconds
        } catch (\Exception $e) {
            Log::error('Error sending QPassport email: ' . $e->getMessage());
        }
    }

    private function parseGetDocumentResponse($responseXml)
    {
        try {
            $xml = simplexml_load_string($responseXml);
            return [
                'status' => (string)$xml->ResponseStatusId,
                'error_detail' => (string)$xml->ErrorDetail,
                'doc_type' => (string)$xml->DocType,
                'doc_format' => (string)$xml->DocFormat,
                'doc_stream' => (string)$xml->DocStream,
            ];
        } catch (\Exception $e) {
            Log::error('Error parsing document response: ' . $e->getMessage());
            return ['status' => 'Error'];
        }
    }
}
