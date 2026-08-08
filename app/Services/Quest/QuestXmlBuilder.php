<?php

namespace App\Services\Quest;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class QuestXmlBuilder
{
    public function buildOrderXml(array $data, ?string $clientReferenceId = null): string
    {
        $xml = new SimpleXMLElement('<Order/>');

        $eventInfo = $xml->addChild('EventInfo');

        if (!empty($data['collection_site_id'])) {
            $eventInfo->addChild('CollectionSiteID', substr($data['collection_site_id'], 0, 6));
        }

        if (!empty($data['email'])) {
            $eventInfo->addChild('EmailAuthorizationAddresses')
                ->addChild('EmailAddress', $data['email']);
        }

        $endDateTime = $this->resolveEndDateTime(
            $data['end_datetime'] ?? null,
            $data['end_datetime_timezone_id'] ?? null
        );

        if ($endDateTime !== null) {
            $eventInfo->addChild('EndDateTime', $endDateTime->format('m/d/Y H:i:s'));
            $eventInfo->addChild('EndDateTimeTimeZoneID', (string) $data['end_datetime_timezone_id']);
        }

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
            try {
                $donorInfo->addChild('DOB', Carbon::parse($data['dob'])->format('m/d/Y'));
            } catch (\Throwable) {
                Log::warning('Quest: could not reformat dob', ['value' => $data['dob']]);
            }
        }

        $donorInfo->addChild('PrimaryPhone', substr($this->digitsOnly($data['primary_phone'] ?? ''), 0, 10));

        if (!empty($data['secondary_phone'])) {
            $donorInfo->addChild('SecondaryPhone', substr($this->digitsOnly($data['secondary_phone']), 0, 12));
        }

        if (!empty($data['zip_code'])) {
            $donorInfo->addChild('PostalAddress')->addChild('ZipCode', $data['zip_code']);
        }

        $clientInfo = $xml->addChild('ClientInfo');

        if (!empty($data['contact_name'])) {
            $clientInfo->addChild('ContactName', substr($data['contact_name'], 0, 45));
        }

        if (!empty($data['telephone_number'])) {
            $clientInfo->addChild('TelephoneNumber', substr($this->digitsOnly($data['telephone_number']), 0, 10));
        }

        $clientInfo->addChild('LabAccount', $data['lab_account']);

        if (!empty($data['csl'])) {
            $clientInfo->addChild('CSL', $data['csl']);
        }

        $testInfo = $xml->addChild('TestInfo');
        $testInfo->addChild('ClientReferenceID', $this->normalizeClientReferenceId(
            $clientReferenceId ?? $data['client_reference_id'] ?? $this->generateClientReferenceId()
        ));
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

        $unitCodes = $this->normalizeUnitCodes($data['unit_codes'] ?? []);
        $screenings = $testInfo->addChild('Screenings');
        $unitCodesNode = $screenings->addChild('UnitCodes');
        foreach ($unitCodes as $code) {
            $unitCodesNode->addChild('UnitCode', $code);
        }

        if (!empty($data['response_url'])) {
            $xml->addChild('ClientCustom')->addChild('ResponseURL', $data['response_url']);
        }

        $xmlString = trim(preg_replace('/<\?xml[^?]*\?>/', '', $xml->asXML()));

        if (!empty($data['order_comments'])) {
            $xmlString = $this->insertOrderComments($xmlString, $data['order_comments']);
        }

        return $xmlString;
    }

    public function buildGetDocumentXml(
        string $questOrderId,
        string $referenceTestId,
        string $docType,
        ?string $specimenId = null,
        ?string $accountNumber = null
    ): string {
        $parts = [
            '<GetDocument>',
            '<QuestOrderID>' . $this->xmlEscape($questOrderId) . '</QuestOrderID>',
            '<ReferenceTestID>' . $this->xmlEscape($referenceTestId) . '</ReferenceTestID>',
            '<DocType>' . $this->xmlEscape($docType) . '</DocType>',
        ];

        if ($specimenId) {
            $parts[] = '<SpecimenID>' . $this->xmlEscape($specimenId) . '</SpecimenID>';
        }

        if ($accountNumber) {
            $parts[] = '<AccountNumber>' . $this->xmlEscape($accountNumber) . '</AccountNumber>';
        }

        $parts[] = '</GetDocument>';

        return implode('', $parts);
    }

    public function generateClientReferenceId(): string
    {
        return substr('ORDER_' . now()->format('Ymd_His') . '_' . random_int(1000, 9999), 0, 36);
    }

    /**
     * @return list<string>
     */
    public function normalizeUnitCodes(mixed $unitCodes): array
    {
        if (is_array($unitCodes)) {
            return array_values(array_filter(array_map(
                fn ($code) => trim((string) $code),
                $unitCodes
            )));
        }

        if (is_string($unitCodes)) {
            return array_values(array_filter(array_map('trim', explode(',', $unitCodes))));
        }

        return [];
    }

    private function normalizeClientReferenceId(string $value): string
    {
        return substr(trim($value), 0, 36);
    }

    /**
     * EndDateTimeTimeZoneID is required whenever EndDateTime is sent (spec 2.1.2), and an
     * already-expired order cannot be honoured. When either condition fails both elements
     * are omitted so Quest applies the account default expiry (spec 4.16).
     */
    private function resolveEndDateTime(?string $value, mixed $timezoneId): ?Carbon
    {
        if (empty($value)) {
            return null;
        }

        try {
            $parsed = Carbon::createFromFormat('Y-m-d\TH:i', $value);
        } catch (\Throwable) {
            try {
                $parsed = Carbon::parse($value);
            } catch (\Throwable) {
                Log::warning('Quest: could not reformat end_datetime', ['value' => $value]);

                return null;
            }
        }

        if (empty($timezoneId)) {
            Log::warning('Quest: end_datetime dropped, no timezone id supplied', ['value' => $value]);

            return null;
        }

        if ($parsed->isPast()) {
            Log::warning('Quest: end_datetime dropped, already in the past', ['value' => $value]);

            return null;
        }

        return $parsed;
    }

    /**
     * Capped at 250 characters per spec 4.27. The spec describes OrderComments as CDATA
     * wrapped, but Quest re-wraps the whole order XML in CDATA on its internal hop to
     * CreateIntegrationOrder, so a nested section makes their deserializer fail with
     * "Start element 'tem:xmlRequest' does not match end element 'OrderComments'".
     * Escaped text decodes to the same value without the nesting.
     */
    private function insertOrderComments(string $xmlString, string $comments): string
    {
        $comments = trim(mb_substr($comments, 0, 250));

        if ($comments === '') {
            return $xmlString;
        }

        return str_replace(
            '<Screenings>',
            '<OrderComments>' . $this->xmlEscape($comments) . '</OrderComments><Screenings>',
            $xmlString
        );
    }

    private function digitsOnly(?string $value): string
    {
        $value = $value ?? '';
        $result = preg_replace('/[^0-9]/', '', $value);

        return $result === null ? '' : $result;
    }

    private function xmlEscape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1, 'UTF-8');
    }
}
