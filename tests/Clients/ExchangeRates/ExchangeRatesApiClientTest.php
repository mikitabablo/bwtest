<?php

namespace Tests\Clients\ExchangeRates;

use App\Clients\ExchangeRates\ExchangeRatesApiClient;
use App\DTOs\ExchangeRatesLookupResult;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ExchangeRatesApiClientTest extends TestCase
{
    public function testLookupReturnsExchangeRatesResult(): void
    {
        $json = json_encode([
            "success" => true,
            "base" => "EUR",
            "rates" => [
                "USD" => 1.13,
                "PLN" => 4.27
            ]
        ]);

        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->once()) // гарантирует, что запрос выполнится только один раз
            ->method('get')
            ->willReturn(new Response(200, [], $json));

        $client = new ExchangeRatesApiClient("http://api.exchangeratesapi.io/latest", "dummy_key", $mockClient);

        $result = $client->lookup();
        $this->assertInstanceOf(ExchangeRatesLookupResult::class, $result);
        $this->assertEquals(1.13, $result->getRates()['USD']);
        $this->assertEquals(4.27, $result->getRates()['PLN']);

        $cachedResult = $client->lookup();
        $this->assertSame($result, $cachedResult);
    }

    public function testLookupThrowsExceptionOnInvalidJson(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Error parsing JSON");

        $mockClient = $this->createMock(Client::class);
        $mockClient->method('get')
            ->willReturn(new Response(200, [], '{invalid_json'));

        $client = new ExchangeRatesApiClient("http://api.exchangeratesapi.io/latest", "dummy_key", $mockClient);
        $client->lookup();
    }

    public function testLookupThrowsExceptionOnNon200Response(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Error fetching data");

        $mockClient = $this->createMock(Client::class);
        $mockClient->method('get')
            ->willReturn(new Response(500, [], ''));

        $client = new ExchangeRatesApiClient("http://api.exchangeratesapi.io/latest", "dummy_key", $mockClient);
        $client->lookup();
    }
}

// TODO FIX ALL OF THESE
