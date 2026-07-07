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

        if (!empty($data['end_datetime'])) {
            try {
                $eventInfo->addChild(
                    'EndDateTime',
                    Carbon::createFromFormat('Y-m-d\TH:i', $data['end_datetime'])->format('m/d/Y H:i:s')
                );
            } catch (\Throwable) {
                try {
                    $eventInfo->addChild('EndDateTime', Carbon::parse($data['end_datetime'])->format('m/d/Y H:i:s'));
                } catch (\Throwable) {
                    Log::warning('Quest: could not reformat end_datetime', ['value' => $data['end_datetime']]);
                }
            }
            if (!empty($data['end_datetime_timezone_id'])) {
                $eventInfo->addChild('EndDateTimeTimeZoneID', $data['end_datetime_timezone_id']);
            }
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

        $donorInfo->addChild('PrimaryPhone', $this->digitsOnly($data['primary_phone'] ?? ''));

        if (!empty($data['secondary_phone'])) {
            $donorInfo->addChild('SecondaryPhone', $this->digitsOnly($data['secondary_phone']));
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
            $xmlString = $this->wrapOrderCommentsInCdata($xmlString, $data['order_comments']);
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

    private function wrapOrderCommentsInCdata(string $xmlString, string $comments): string
    {
        $escaped = htmlspecialchars($comments, ENT_XML1, 'UTF-8');
        $pattern = '/<OrderComments>' . preg_quote($escaped, '/') . '<\/OrderComments>/';

        if (preg_match($pattern, $xmlString)) {
            return preg_replace(
                $pattern,
                '<OrderComments><![CDATA[' . $comments . ']]></OrderComments>',
                $xmlString
            );
        }

        return str_replace(
            '<Screenings>',
            '<OrderComments><![CDATA[' . $comments . ']]></OrderComments><Screenings>',
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
