<?php

namespace App\Clients\ExchangeRates;

use App\DTOs\ExchangeRatesLookupResult;

interface IExchangeRatesLookuper
{
    public function lookup(): ?ExchangeRatesLookupResult;
}