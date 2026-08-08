<?php

namespace App\Services\Quest;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class QuestXmlBuilder
{
    private const TIMEZONE_MAP = [
        1 => 'America/New_York',
        2 => 'America/Chicago',
        3 => 'America/Denver',
        4 => 'America/Los_Angeles',
        5 => 'Pacific/Honolulu',
        6 => 'America/Anchorage',
        7 => 'America/Puerto_Rico',
        8 => 'Pacific/Guam',
    ];

    /**
     * @return array{xml: string, client_reference_id: string}
     */
    public function buildOrderXml(array $data, ?string $clientReferenceId = null): array
    {
        $clientReferenceId = $this->normalizeClientReferenceId(
            $clientReferenceId ?? $data['client_reference_id'] ?? $this->generateClientReferenceId()
        );

        $unitCodes = $this->normalizeUnitCodes($data['unit_codes'] ?? []);
        if ($unitCodes === []) {
            throw new \InvalidArgumentException('At least one UnitCode is required for a Quest order.');
        }

        $dotTest = (string) ($data['dot_test'] ?? '');
        if (!in_array($dotTest, ['T', 'F'], true)) {
            throw new \InvalidArgumentException('DOTTest must be T or F.');
        }

        if ($dotTest === 'T' && empty($data['testing_authority'])) {
            throw new \InvalidArgumentException('TestingAuthority is required when DOTTest is T.');
        }

        if (empty($data['lab_account'])) {
            throw new \InvalidArgumentException('LabAccount is required for a Quest order.');
        }

        $parts = ['<Order>', '<EventInfo>'];

        if (!empty($data['collection_site_id'])) {
            $parts[] = $this->el('CollectionSiteID', substr((string) $data['collection_site_id'], 0, 6));
        }

        if (!empty($data['email'])) {
            $parts[] = '<EmailAuthorizationAddresses>';
            $parts[] = $this->el('EmailAddress', (string) $data['email']);
            $parts[] = '</EmailAuthorizationAddresses>';
        }

        $endDateTime = $this->resolveEndDateTime(
            $data['end_datetime'] ?? null,
            $data['end_datetime_timezone_id'] ?? null
        );

        if ($endDateTime !== null) {
            $parts[] = $this->el('EndDateTime', $endDateTime->format('m/d/Y H:i:s'));
            $parts[] = $this->el('EndDateTimeTimeZoneID', (string) $data['end_datetime_timezone_id']);
        }

        $parts[] = '</EventInfo>';
        $parts[] = '<DonorInfo>';
        $parts[] = $this->el('FirstName', $this->sanitizeAlpha((string) $data['first_name'], 20));

        if (!empty($data['middle_name'])) {
            $parts[] = $this->el('MiddleName', $this->sanitizeAlpha((string) $data['middle_name'], 20));
        }

        $parts[] = $this->el('LastName', $this->sanitizeAlpha((string) $data['last_name'], 25));
        $parts[] = $this->el('PrimaryID', substr((string) $data['primary_id'], 0, 25));

        if (!empty($data['primary_id_type'])) {
            $parts[] = $this->el('PrimaryIDType', substr((string) $data['primary_id_type'], 0, 5));
        }

        if (!empty($data['dob'])) {
            try {
                $parts[] = $this->el('DOB', Carbon::parse($data['dob'])->format('m/d/Y'));
            } catch (\Throwable) {
                Log::warning('Quest: could not reformat dob', ['value' => $data['dob']]);
            }
        }

        $primaryPhone = substr($this->digitsOnly($data['primary_phone'] ?? ''), 0, 10);
        if ($primaryPhone !== '') {
            $parts[] = $this->el('PrimaryPhone', $primaryPhone);
        }

        if (!empty($data['secondary_phone'])) {
            $parts[] = $this->el(
                'SecondaryPhone',
                substr($this->digitsOnly($data['secondary_phone']), 0, 12)
            );
        }

        if (!empty($data['zip_code'])) {
            $parts[] = '<PostalAddress>';
            $parts[] = $this->el('ZipCode', (string) $data['zip_code']);
            $parts[] = '</PostalAddress>';
        }

        $parts[] = '</DonorInfo>';
        $parts[] = '<ClientInfo>';

        if (!empty($data['contact_name'])) {
            $parts[] = $this->el('ContactName', substr((string) $data['contact_name'], 0, 45));
        }

        if (!empty($data['telephone_number'])) {
            $parts[] = $this->el(
                'TelephoneNumber',
                substr($this->digitsOnly($data['telephone_number']), 0, 10)
            );
        }

        $parts[] = $this->el('LabAccount', (string) $data['lab_account']);

        if (!empty($data['csl'])) {
            $parts[] = $this->el('CSL', substr((string) $data['csl'], 0, 20));
        }

        $parts[] = '</ClientInfo>';
        $parts[] = '<TestInfo>';
        $parts[] = $this->el('ClientReferenceID', $clientReferenceId);
        $parts[] = $this->el('DOTTest', $dotTest);

        if ($dotTest === 'T') {
            $parts[] = $this->el('TestingAuthority', (string) $data['testing_authority']);
        }

        if (!empty($data['reason_for_test_id'])) {
            $parts[] = $this->el('ReasonForTestID', (string) $data['reason_for_test_id']);
        }

        if (!empty($data['physical_reason_for_test_id'])) {
            $parts[] = $this->el('PhysicalReasonForTestID', (string) $data['physical_reason_for_test_id']);
        }

        if (!empty($data['observed_requested'])) {
            $parts[] = $this->el('ObservedRequested', (string) $data['observed_requested']);
        }

        if (!empty($data['split_specimen_requested'])) {
            $parts[] = $this->el('SplitSpecimenRequested', (string) $data['split_specimen_requested']);
        }

        if (!empty($data['order_comments'])) {
            $comments = trim(mb_substr((string) $data['order_comments'], 0, 250));
            if ($comments !== '') {
                // Spec describes OrderComments as CDATA-wrapped, but Quest re-wraps the whole
                // order XML in CDATA internally, so nested CDATA breaks deserialization.
                $parts[] = $this->el('OrderComments', $comments);
            }
        }

        $parts[] = '<Screenings><UnitCodes>';
        foreach ($unitCodes as $code) {
            $parts[] = $this->el('UnitCode', substr($code, 0, 15));
        }
        $parts[] = '</UnitCodes></Screenings>';
        $parts[] = '</TestInfo>';

        if (!empty($data['response_url'])) {
            $parts[] = '<ClientCustom>';
            $parts[] = $this->el('ResponseURL', (string) $data['response_url']);
            $parts[] = '</ClientCustom>';
        }

        $parts[] = '</Order>';

        return [
            'xml' => implode('', $parts),
            'client_reference_id' => $clientReferenceId,
        ];
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
            $this->el('QuestOrderID', $questOrderId),
            $this->el('ReferenceTestID', $referenceTestId),
            $this->el('DocType', $docType),
        ];

        if ($specimenId) {
            $parts[] = $this->el('SpecimenID', $specimenId);
        }

        if ($accountNumber) {
            $parts[] = $this->el('AccountNumber', $accountNumber);
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

        if (empty($timezoneId)) {
            Log::warning('Quest: end_datetime dropped, no timezone id supplied', ['value' => $value]);

            return null;
        }

        $timezoneName = self::TIMEZONE_MAP[(int) $timezoneId] ?? null;
        if ($timezoneName === null) {
            Log::warning('Quest: end_datetime dropped, invalid timezone id', ['timezone_id' => $timezoneId]);

            return null;
        }

        try {
            $parsed = Carbon::createFromFormat('Y-m-d\TH:i', $value, $timezoneName);
        } catch (\Throwable) {
            try {
                $parsed = Carbon::parse($value, $timezoneName);
            } catch (\Throwable) {
                Log::warning('Quest: could not reformat end_datetime', ['value' => $value]);

                return null;
            }
        }

        if ($parsed->isPast()) {
            Log::warning('Quest: end_datetime dropped, already in the past', ['value' => $value]);

            return null;
        }

        return $parsed;
    }

    /**
     * Spec §4: special characters (&, >, <) must not appear in non-CDATA fields.
     * Names are alpha-oriented; we strip the forbidden characters deliberately.
     */
    private function sanitizeAlpha(string $value, int $maxLength): string
    {
        $value = preg_replace('/[&<>]/', '', $value) ?? '';
        $value = preg_replace('/\s+/', ' ', trim($value)) ?? '';

        return mb_substr($value, 0, $maxLength);
    }

    private function digitsOnly(?string $value): string
    {
        $value = $value ?? '';
        $result = preg_replace('/[^0-9]/', '', $value);

        return $result === null ? '' : $result;
    }

    private function el(string $name, string $value): string
    {
        return '<' . $name . '>' . $this->xmlEscape($value) . '</' . $name . '>';
    }

    private function xmlEscape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1, 'UTF-8');
    }
}
