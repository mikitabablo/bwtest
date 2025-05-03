<?php

declare(strict_types=1);

use App\Clients\BinList\BinListClient;
use App\Clients\BinList\LookupResponseValidator;
use App\Clients\ExchangeRates\ExchangeRatesApiClient;
use App\Services\CommissionCalculator\CommissionCalculator;
use App\Services\CountryChecker\CountryChecker;
use App\Services\FileReader\FileReader;
use App\Services\TransactionsPostProcessor\TransactionsPostProcessor;
use GuzzleHttp\Client;

require __DIR__ . '/vendor/autoload.php';
$config = require __DIR__ . '/config/config.php';


/** Input processing */
if (!isset($argv[1])) {
    echo 'Error: no file provided';
    exit(1);
}

$inputFilePath = $argv[1];

try {
    // Dependencies initialization
    $fileReader = new FileReader($inputFilePath);
    $httpClient = new Client(['timeout' => 30]);
    $binlistClient = new BinListClient(
        $config['bin_list_api']['url'],
        $httpClient,
        new LookupResponseValidator(),
    );
    $exchangeRatesLookuper = new ExchangeRatesApiClient(
        $config['exchange_rate_api']['url'],
        $config['exchange_rate_api']['access_key'],
        $httpClient,
    );

    $commissionCalculator = new CommissionCalculator(
        $binlistClient,
        $exchangeRatesLookuper,
        new CountryChecker(),
    );
    $transactionsPostProcessor = new TransactionsPostProcessor(
        $fileReader,
        $commissionCalculator,
    );

    // Running the command
    $processingResults = $transactionsPostProcessor->process();
    foreach ($processingResults as $index => $result) {
        $transactionRowEol = str_ends_with($result->transactionRow, PHP_EOL) ? '' : PHP_EOL;
        echo sprintf('### %d', $index) . PHP_EOL;
        echo sprintf('Transaction row: %s', $result->transactionRow) . $transactionRowEol;
        echo sprintf('Commission: %s', $result->comission) . PHP_EOL;
        echo sprintf('Error: %s', $result->error) . PHP_EOL . PHP_EOL;
    }
} catch (Throwable $e) {
    echo $e->getMessage();
    exit(1);
}
