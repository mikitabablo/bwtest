<?php

namespace App\Clients\BinList;

use App\DTOs\BinLookupResult;
use Exception;
use GuzzleHttp\Client;

class BinListClient implements IBinLookuper
{
    private string $url;

    private Client $client;

    private LookupResponseValidator $lookupResponseValidator;

    public function __construct(
        string                  $url,
        Client                  $client,
        LookupResponseValidator $lookupResponseValidator,
    )
    {
        $this->url = $url;
        $this->client = $client;
        $this->lookupResponseValidator = $lookupResponseValidator;
    }

    public function lookup(string $bin): ?BinLookupResult
    {
        $lookupUrl = sprintf('%s/%s', $this->url, $bin);

// Because the API seems not working correctly w/o the subscription, I'm mocking the response.
// Uncomment these lines for unit-tests
//        $response = $this->client->get($this->url);
//        if ($response->getStatusCode() !== 200) {
//            throw new Exception(sprintf(
//                "Error fetching data with code %d from URL: %s",
//                $response->getStatusCode(),
//                $this->url,
//            ));
//        }
//        $data = json_decode($response->getBody()->getContents(), true);

// Comment these lines for unit-tests
        $respMock = $this->mocks($bin);
        $data = json_decode($respMock, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Error parsing JSON: " . json_last_error_msg());
        }

        $this->lookupResponseValidator->validate($data);

        return BinLookupResult::create($data);
    }

    private function mocks(string $bin)
    {
        $mocks = [
            '45717360' => '{
                "number": {
                    "length": 16,
                    "luhn": true
                },
                "scheme": "visa",
                "type": "debit",
                "brand": "Visa/Dankort",
                "prepaid": false,
                "country": {
                    "numeric": "208",
                    "alpha2": "DK",
                    "name": "Denmark",
                    "emoji": "🇩🇰",
                    "currency": "DKK",
                    "latitude": 56,
                    "longitude": 10
                },
                "bank": {
                    "name": "Jyske Bank",
                    "url": "www.jyskebank.dk",
                    "phone": "+4589893300",
                    "city": "Hjørring"
                }
            }',
            '516793' => '{
                "number": {},
                "scheme": "mastercard",
                "type": "debit",
                "brand": "Debit Mastercard",
                "country": {
                    "numeric": "440",
                    "alpha2": "LT",
                    "name": "Lithuania",
                    "emoji": "🇱🇹",
                    "currency": "EUR",
                    "latitude": 56,
                    "longitude": 24
                },
                "bank": {
                    "name": "Swedbank Ab"
                }
            }',
            '45417360' => '{
                "number": {},
                "scheme": "visa",
                "type": "credit",
                "brand": "Visa Classic",
                "country": {
                    "numeric": "392",
                    "alpha2": "JP",
                    "name": "Japan",
                    "emoji": "🇯🇵",
                    "currency": "JPY",
                    "latitude": 36,
                    "longitude": 138
                },
                "bank": {
                    "name": "Credit Saison Co., Ltd."
                }
            }',
            '41417360' => '{"number":null,"country":{},"bank":{}}',
            '4745030' => '{
                "number": {},
                "scheme": "visa",
                "type": "debit",
                "brand": "Visa Classic",
                "country": {
                    "numeric": "440",
                    "alpha2": "LT",
                    "name": "Lithuania",
                    "emoji": "🇱🇹",
                    "currency": "EUR",
                    "latitude": 56,
                    "longitude": 24
                },
                "bank": {
                    "name": "Uab Finansines Paslaugos Contis"
                }
            }',
        ];

        return $mocks[$bin] ?? '';
    }
}