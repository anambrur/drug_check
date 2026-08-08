<?php

namespace Tests\Unit\Services\Quest;

use App\Services\Quest\QuestEspClient;
use App\Services\Quest\QuestResponseParser;
use App\Services\Quest\QuestXmlBuilder;
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
        ]));

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
        ]));

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
        ]));

        preg_match('/<OrderComments>(.*?)<\/OrderComments>/s', $orderXml, $matches);

        $this->assertSame(250, strlen($matches[1] ?? ''));
    }

    public function test_end_datetime_is_omitted_without_a_timezone_id(): void
    {
        $orderXml = $this->xmlBuilder->buildOrderXml($this->orderData([
            'end_datetime' => now()->addDay()->format('Y-m-d\TH:i'),
            'end_datetime_timezone_id' => null,
        ]));

        $this->assertStringNotContainsString('<EndDateTime>', $orderXml);
        $this->assertStringNotContainsString('<EndDateTimeTimeZoneID>', $orderXml);
    }

    public function test_end_datetime_in_the_past_is_omitted(): void
    {
        $orderXml = $this->xmlBuilder->buildOrderXml($this->orderData([
            'end_datetime' => '2000-05-08T10:03',
            'end_datetime_timezone_id' => 1,
        ]));

        $this->assertStringNotContainsString('<EndDateTime>', $orderXml);
    }

    public function test_future_end_datetime_is_sent_with_its_timezone_id(): void
    {
        $orderXml = $this->xmlBuilder->buildOrderXml($this->orderData([
            'end_datetime' => now()->addDay()->format('Y-m-d\TH:i'),
            'end_datetime_timezone_id' => 2,
        ]));

        $this->assertStringContainsString('<EndDateTimeTimeZoneID>2</EndDateTimeTimeZoneID>', $orderXml);
    }

    public function test_phone_numbers_are_capped_at_the_documented_lengths(): void
    {
        $orderXml = $this->xmlBuilder->buildOrderXml($this->orderData([
            'primary_phone' => '11533664776',
            'secondary_phone' => '157584377841234',
        ]));

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
