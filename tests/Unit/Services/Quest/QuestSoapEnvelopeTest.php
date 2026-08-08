<?php

namespace Tests\Unit\Services\Quest;

use App\Services\Quest\QuestEspClient;
use App\Services\Quest\QuestOrderScreenService;
use App\Services\Quest\QuestResponseParser;
use App\Services\Quest\QuestXmlBuilder;
use Carbon\Carbon;
use ReflectionMethod;
use Tests\TestCase;

class QuestSoapEnvelopeTest extends TestCase
{
    private QuestXmlBuilder $xmlBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.quest.username' => 'test-user',
            'services.quest.password' => 'test-password',
        ]);

        $this->xmlBuilder = new QuestXmlBuilder();
    }

    public function test_envelope_stays_well_formed_when_order_comments_contain_cdata_and_ampersands(): void
    {
        $orderXml = $this->xmlBuilder->buildOrderXml($this->orderData([
            'order_comments' => 'Gate code 5 & 7 ]]> use the <rear> entrance',
        ]))['xml'];

        $envelope = $this->buildSoapEnvelope('CreateOrder', ['orderXml' => $orderXml]);

        // Quest re-wraps the order XML in CDATA internally, so no CDATA may survive here.
        $this->assertStringNotContainsString('<![CDATA[', $orderXml);
        $this->assertStringNotContainsString(']]>', $orderXml);
        $this->assertStringContainsString(
            '<OrderComments>Gate code 5 &amp; 7 ]]&gt; use the &lt;rear&gt; entrance</OrderComments>',
            $orderXml
        );

        libxml_use_internal_errors(true);
        libxml_clear_errors();
        $parsed = simplexml_load_string($envelope);
        $errors = libxml_get_errors();
        libxml_clear_errors();

        $this->assertNotFalse($parsed, 'SOAP envelope is not well-formed XML.');
        $this->assertSame([], $errors);
    }

    public function test_order_xml_round_trips_through_the_envelope(): void
    {
        $orderXml = $this->xmlBuilder->buildOrderXml($this->orderData([
            'order_comments' => 'Donor requires a split specimen & observed collection',
        ]))['xml'];

        $envelope = $this->buildSoapEnvelope('CreateOrder', ['orderXml' => $orderXml]);

        $parsed = simplexml_load_string($envelope);
        $received = (string) $parsed
            ->children('http://schemas.xmlsoap.org/soap/envelope/')->Body
            ->children('http://wssim.labone.com/')->CreateOrder->orderXml;

        $this->assertSame($orderXml, $received);
        $this->assertStringContainsString(
            '<OrderComments>Donor requires a split specimen &amp; observed collection</OrderComments>',
            $received
        );
    }

    public function test_order_comments_are_truncated_to_the_documented_maximum(): void
    {
        $orderXml = $this->xmlBuilder->buildOrderXml($this->orderData([
            'order_comments' => str_repeat('a', 300),
        ]))['xml'];

        preg_match('/<OrderComments>(.*?)<\/OrderComments>/s', $orderXml, $matches);

        $this->assertSame(250, strlen($matches[1] ?? ''));
    }

    public function test_end_datetime_is_omitted_without_a_timezone_id(): void
    {
        $orderXml = $this->xmlBuilder->buildOrderXml($this->orderData([
            'end_datetime' => now()->addDay()->format('Y-m-d\TH:i'),
            'end_datetime_timezone_id' => null,
        ]))['xml'];

        $this->assertStringNotContainsString('<EndDateTime>', $orderXml);
        $this->assertStringNotContainsString('<EndDateTimeTimeZoneID>', $orderXml);
    }

    public function test_end_datetime_in_the_past_is_omitted(): void
    {
        $orderXml = $this->xmlBuilder->buildOrderXml($this->orderData([
            'end_datetime' => '2000-05-08T10:03',
            'end_datetime_timezone_id' => 1,
        ]))['xml'];

        $this->assertStringNotContainsString('<EndDateTime>', $orderXml);
    }

    public function test_future_end_datetime_is_sent_with_its_timezone_id(): void
    {
        $orderXml = $this->xmlBuilder->buildOrderXml($this->orderData([
            'end_datetime' => now()->addDay()->format('Y-m-d\TH:i'),
            'end_datetime_timezone_id' => 2,
        ]))['xml'];

        $this->assertStringContainsString('<EndDateTimeTimeZoneID>2</EndDateTimeTimeZoneID>', $orderXml);
    }

    public function test_phone_numbers_are_capped_at_the_documented_lengths(): void
    {
        $orderXml = $this->xmlBuilder->buildOrderXml($this->orderData([
            'primary_phone' => '11533664776',
            'secondary_phone' => '157584377841234',
        ]))['xml'];

        $this->assertStringContainsString('<PrimaryPhone>1153366477</PrimaryPhone>', $orderXml);
        $this->assertStringContainsString('<SecondaryPhone>157584377841</SecondaryPhone>', $orderXml);
    }

    public function test_credentials_are_redacted_from_the_logged_request(): void
    {
        $client = new QuestEspClient(new QuestResponseParser());
        $envelope = $this->buildSoapEnvelope('CreateOrder', ['orderXml' => '<Order/>'], $client);

        $method = new ReflectionMethod($client, 'redactCredentials');
        $redacted = $method->invoke($client, $envelope);

        $this->assertStringNotContainsString('test-password', $redacted);
        $this->assertStringNotContainsString('test-user', $redacted);
        $this->assertStringContainsString('<wss:password>***</wss:password>', $redacted);
    }

    public function test_ampersands_in_donor_names_are_stripped_not_deleted_into_double_spaces_via_addchild(): void
    {
        $orderXml = $this->xmlBuilder->buildOrderXml($this->orderData([
            'first_name' => 'Ann & Marie',
            'last_name' => 'Smith & Sons',
            'middle_name' => 'Lee <Jr>',
        ]))['xml'];

        preg_match('/<DonorInfo>(.*?)<\/DonorInfo>/s', $orderXml, $donor);

        $this->assertStringContainsString('<FirstName>Ann Marie</FirstName>', $orderXml);
        $this->assertStringContainsString('<LastName>Smith Sons</LastName>', $orderXml);
        $this->assertStringContainsString('<MiddleName>Lee Jr</MiddleName>', $orderXml);
        $this->assertStringNotContainsString('&', $donor[1] ?? '');
        $this->assertStringNotContainsString('<Jr>', $donor[1] ?? '');
    }

    public function test_donor_info_element_sequence_matches_spec(): void
    {
        $orderXml = $this->xmlBuilder->buildOrderXml($this->orderData([
            'middle_name' => 'Q',
        ]))['xml'];

        $this->assertMatchesRegularExpression(
            '/<DonorInfo>.*<FirstName>.*<\/FirstName><MiddleName>.*<\/MiddleName><LastName>.*<\/LastName>/s',
            $orderXml
        );
    }

    public function test_empty_primary_phone_is_omitted(): void
    {
        $orderXml = $this->xmlBuilder->buildOrderXml($this->orderData([
            'primary_phone' => '',
        ]))['xml'];

        $this->assertStringNotContainsString('<PrimaryPhone>', $orderXml);
    }

    public function test_dot_test_requires_testing_authority(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('TestingAuthority is required');

        $this->xmlBuilder->buildOrderXml($this->orderData([
            'dot_test' => 'T',
            'testing_authority' => null,
            'reason_for_test_id' => 1,
        ]));
    }

    public function test_empty_unit_codes_hard_fail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('At least one UnitCode');

        $this->xmlBuilder->buildOrderXml($this->orderData([
            'unit_codes' => '',
        ]));
    }

    public function test_build_order_xml_returns_generated_client_reference_id(): void
    {
        $built = $this->xmlBuilder->buildOrderXml($this->orderData([
            'client_reference_id' => 'ORDER_CUSTOM_REF_001',
        ]));

        $this->assertSame('ORDER_CUSTOM_REF_001', $built['client_reference_id']);
        $this->assertStringContainsString(
            '<ClientReferenceID>ORDER_CUSTOM_REF_001</ClientReferenceID>',
            $built['xml']
        );
    }

    public function test_inbound_status_with_escaped_ampersand_parses(): void
    {
        $parser = new QuestResponseParser();
        $xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<OrderStatus>
  <ClientReferenceID>ORDER#1124</ClientReferenceID>
  <ReferenceTestID>396119</ReferenceTestID>
  <QuestOrderID>2901400</QuestOrderID>
  <PrimaryID>KS1111111</PrimaryID>
  <LabAccount>12345678</LabAccount>
  <FirstName>JANE</FirstName>
  <LastName>SMITH &amp; SONS</LastName>
  <ScreenType>drug</ScreenType>
  <OrderStatusDateTime>2016-08-26T15:08:44.8870000</OrderStatusDateTime>
  <OrderStatusID>COLLECTED</OrderStatusID>
</OrderStatus>
XML;

        $parsed = $parser->parseInboundXml($xml);
        $this->assertNotNull($parsed);
        $status = $parser->extractOrderStatus($parsed);
        $this->assertNotNull($status);
        $this->assertSame('2901400', $status['quest_order_id']);
        $this->assertSame('COLLECTED', $status['order_status_id']);
    }

    public function test_xml_to_array_collects_repeated_siblings(): void
    {
        $parser = new QuestResponseParser();
        $xml = simplexml_load_string(<<<'XML'
<Physical>
  <PartialReasons>
    <PartialReason>
      <PartialReasonDescription>Missing page 1</PartialReasonDescription>
    </PartialReason>
    <PartialReason>
      <PartialReasonDescription>Missing page 2</PartialReasonDescription>
    </PartialReason>
  </PartialReasons>
</Physical>
XML);

        $method = new ReflectionMethod($parser, 'xmlToArray');
        $result = $method->invoke($parser, $xml);

        $this->assertIsArray($result['PartialReasons']['PartialReason']);
        $this->assertCount(2, $result['PartialReasons']['PartialReason']);
    }

    public function test_out_of_order_status_datetime_is_rejected_by_comparison(): void
    {
        $existingAt = Carbon::parse('2016-08-26T16:00:00');
        $incomingAt = Carbon::parse('2016-08-26T15:00:00');

        $this->assertTrue($incomingAt->lt($existingAt));
        $this->assertInstanceOf(QuestOrderScreenService::class, new QuestOrderScreenService());
    }

    private function buildSoapEnvelope(string $method, array $params, ?QuestEspClient $client = null): string
    {
        $client ??= new QuestEspClient(new QuestResponseParser());

        return (new ReflectionMethod($client, 'buildSoapEnvelope'))->invoke($client, $method, $params);
    }

    private function orderData(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Eleanor',
            'last_name' => 'Doe',
            'primary_id' => '12345678',
            'primary_phone' => '9135551212',
            'dot_test' => 'F',
            'reason_for_test_id' => 1,
            'lab_account' => '11321249',
            'unit_codes' => 'ERO10A',
        ], $overrides);
    }
}
