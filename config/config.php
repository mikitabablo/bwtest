<?php

use Dotenv\Dotenv;

if (!isset($_ENV['APP_ENV'])) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

return [
    'exchange_rate_api' => [
        'url' => $_ENV['EXCHANGE_RATE_API_URL'],
        'access_key' => $_ENV['EXCHANGE_RATE_API_ACCESS_KEY'],
    ],
    'bin_list_api' => [
        'url' => $_ENV['BIN_LIST_API_URL'],
    ],
];
