<?php

namespace App\Clients\ExchangeRates;

use App\DTOs\ExchangeRatesLookupResult;
use Exception;
use GuzzleHttp\Client;

class ExchangeRatesApiClient implements IExchangeRatesLookuper
{
    private string $url;

    private Client $client;
    private string $accessKey;

    private ?ExchangeRatesLookupResult $rates = null;

    public function __construct(
        string $url,
        string $accessKey,
        Client $client,
    )
    {
        $this->url = $url;
        $this->accessKey = $accessKey;
        $this->client = $client;
    }

    /**
     * @throws Exception
     */
    public function lookup(): ?ExchangeRatesLookupResult
    {
        if ($this->rates !== null) {
            return $this->rates;
        }

        $url = sprintf('%s?access_key=%s', $this->url, $this->accessKey);
        $response = $this->client->get($url);
        if ($response->getStatusCode() !== 200) {
            throw new Exception(sprintf(
              "Error fetching data with code %d from URL: %s",
                $response->getStatusCode(),
                $this->url,
            ));
        }
        $data = json_decode($response->getBody()->getContents(), true);

// Use mocks if you don't have an access key
//        $mockData = $this->mocks();
//        $data = json_decode($mockData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception(
                sprintf("Error parsing JSON: %s", json_last_error_msg())
            );
        }
        if ($data === null) {
            throw new Exception("Error parsing JSON");
        }

        // caching rates
        $this->rates = ExchangeRatesLookupResult::create($data);

        return $this->rates;
    }

    private function mocks(): string
    {
        return '{
          "success": true,
          "timestamp": 1746218655,
          "base": "EUR",
          "date": "2025-05-02",
          "rates": {
            "AED": 4.149372,
            "AFN": 80.207907,
            "ALL": 97.94802,
            "AMD": 439.910301,
            "ANG": 2.036016,
            "AOA": 1035.919699,
            "ARS": 1324.824072,
            "AUD": 1.752359,
            "AWG": 2.033429,
            "AZN": 1.924946,
            "BAM": 1.950306,
            "BBD": 2.281474,
            "BDT": 137.283308,
            "BGN": 1.954323,
            "BHD": 0.425699,
            "BIF": 3311.100786,
            "BMD": 1.129683,
            "BND": 1.466183,
            "BOB": 7.808145,
            "BRL": 6.387571,
            "BSD": 1.129917,
            "BTC": 0.00001163563,
            "BTN": 95.494706,
            "BWP": 15.384801,
            "BYN": 3.697917,
            "BYR": 22141.786218,
            "BZD": 2.269707,
            "CAD": 1.560285,
            "CDF": 3243.320216,
            "CHF": 0.933994,
            "CLF": 0.027824,
            "CLP": 1067.731603,
            "CNY": 8.214607,
            "CNH": 8.144319,
            "COP": 4803.976831,
            "CRC": 571.395582,
            "CUC": 1.129683,
            "CUP": 29.936599,
            "CVE": 109.955284,
            "CZK": 24.907296,
            "DJF": 200.767702,
            "DKK": 7.464121,
            "DOP": 66.364244,
            "DZD": 150.127005,
            "EGP": 57.35367,
            "ERN": 16.945245,
            "ETB": 148.158364,
            "EUR": 1,
            "FJD": 2.547892,
            "FKP": 0.851098,
            "GBP": 0.85164,
            "GEL": 3.095773,
            "GGP": 0.851098,
            "GHS": 16.497727,
            "GIP": 0.851098,
            "GMD": 80.776683,
            "GNF": 9787.431294,
            "GTQ": 8.702564,
            "GYD": 237.096357,
            "HKD": 8.755139,
            "HNL": 29.150157,
            "HRK": 7.534086,
            "HTG": 147.475903,
            "HUF": 404.551206,
            "IDR": 18602.432988,
            "ILS": 4.069338,
            "IMP": 0.851098,
            "INR": 95.592135,
            "IQD": 1480.243633,
            "IRR": 47573.778397,
            "ISK": 146.136225,
            "JEP": 0.851098,
            "JMD": 179.225168,
            "JOD": 0.801176,
            "JPY": 163.746986,
            "KES": 146.011958,
            "KGS": 98.79121,
            "KHR": 4527.287857,
            "KMF": 490.851537,
            "KPW": 1016.713193,
            "KRW": 1580.28005,
            "KWD": 0.346406,
            "KYD": 0.941656,
            "KZT": 583.75089,
            "LAK": 24434.390827,
            "LBP": 101243.718399,
            "LKR": 338.356935,
            "LRD": 225.997424,
            "LSL": 20.800494,
            "LTL": 3.335661,
            "LVL": 0.683334,
            "LYD": 6.169622,
            "MAD": 10.47748,
            "MDL": 19.435599,
            "MGA": 5083.573758,
            "MKD": 61.51046,
            "MMK": 2371.682154,
            "MNT": 4036.614545,
            "MOP": 9.021409,
            "MRU": 45.006626,
            "MUR": 51.208957,
            "MVR": 17.408842,
            "MWK": 1959.315793,
            "MXN": 22.126451,
            "MYR": 4.814148,
            "MZN": 72.300099,
            "NAD": 20.800494,
            "NGN": 1811.684302,
            "NIO": 41.516275,
            "NOK": 11.7591,
            "NPR": 152.791331,
            "NZD": 1.899505,
            "OMR": 0.434923,
            "PAB": 1.129917,
            "PEN": 4.142668,
            "PGK": 4.584536,
            "PHP": 62.882716,
            "PKR": 317.51935,
            "PLN": 4.275342,
            "PYG": 9040.535111,
            "QAR": 4.123385,
            "RON": 4.978743,
            "RSD": 117.167372,
            "RUB": 93.480847,
            "RWF": 1594.835988,
            "SAR": 4.236555,
            "SBD": 9.42204,
            "SCR": 16.060327,
            "SDG": 678.378776,
            "SEK": 10.908276,
            "SGD": 1.4659,
            "SHP": 0.887753,
            "SLE": 25.745894,
            "SLL": 23688.868701,
            "SOS": 645.7924,
            "SRD": 41.600618,
            "STD": 23382.156664,
            "SVC": 9.88715,
            "SYP": 14687.984966,
            "SZL": 20.791619,
            "THB": 37.405502,
            "TJS": 11.694962,
            "TMT": 3.95389,
            "TND": 3.371543,
            "TOP": 2.645835,
            "TRY": 43.576324,
            "TTD": 7.662485,
            "TWD": 34.661276,
            "TZS": 3045.153123,
            "UAH": 47.17773,
            "UGX": 4139.377069,
            "USD": 1.129683,
            "UYU": 47.41644,
            "UZS": 14595.504353,
            "VES": 97.986408,
            "VND": 29377.405642,
            "VUV": 136.777371,
            "WST": 3.130212,
            "XAF": 654.1203,
            "XAG": 0.03528,
            "XAU": 0.000349,
            "XCD": 3.053025,
            "XDR": 0.816775,
            "XOF": 650.136611,
            "XPF": 119.331742,
            "YER": 276.377349,
            "ZAR": 20.794035,
            "ZMK": 10168.506404,
            "ZMW": 31.361939,
            "ZWL": 363.757456
          }
        }';
    }
}
