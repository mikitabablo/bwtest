<?php

namespace Tests\Clients\BinList;

use App\Clients\BinList\BinListClient;
use App\Clients\BinList\LookupResponseValidator;
use App\DTOs\BinLookupResult;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class BinListClientTest extends TestCase
{
    public function testLookupReturnsValidBinLookupResult(): void
    {
        $json = json_encode([
            "country" => [
                "alpha2" => "DK"
            ]
        ]);

        $client = $this->createMock(Client::class);
        $client->method('get')
            ->willReturn(new Response(200, [], $json));

        $binClient = new BinListClient("http://example.com", $client, new LookupResponseValidator());

        $result = $binClient->lookup("45717360");

        $this->assertInstanceOf(BinLookupResult::class, $result);
        $this->assertEquals("DK", $result->getAlpha2());
    }

    public function testLookupThrowsExceptionOnInvalidJson(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Error parsing JSON");

        $client = $this->createMock(Client::class);
        $client->method('get')
            ->willReturn(new Response(200, [], "{invalid_json"));

        $binClient = new BinListClient("http://example.com", $client, new LookupResponseValidator());
        $binClient->lookup("45717360");
    }

    public function testLookupThrowsExceptionOnHttpError(): void
    {
        $url = "http://example.com";
        $bin = "45717360";

        $client = $this->createMock(Client::class);
        $client->method('get')->willThrowException(
            new RequestException("Request failed", new Request(
                'GET',
                sprintf("%s/%s", $url, $bin))),
        );

        $binClient = new BinListClient($url, $client, new LookupResponseValidator());

        $this->expectException(RequestException::class);
        $binClient->lookup($bin);
    }
}
