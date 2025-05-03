<?php

namespace App\Services\CommissionCalculator;


use App\Clients\BinList\IBinLookuper;
use App\Clients\ExchangeRates\IExchangeRatesLookuper;
use App\DTOs\TransactionRow;
use App\Services\CountryChecker\ICountryChecker;

class CommissionCalculator implements ICommissionCalculator
{
    private const float COMMISSION_EU = 0.01;
    private const float COMMISSION_NON_EU = 0.02;

    private IBinLookuper $binLookuper;
    private IExchangeRatesLookuper $exchangeRatesLookuper;
    private ICountryChecker $countryChecker;

    public function __construct(
        IBinLookuper           $binLookuper,
        IExchangeRatesLookuper $exchangeRatesLookuper,
        ICountryChecker        $countryChecker,
    )
    {
        $this->binLookuper = $binLookuper;
        $this->exchangeRatesLookuper = $exchangeRatesLookuper;
        $this->countryChecker = $countryChecker;
    }

    public function calculate(TransactionRow $transaction): float
    {
        $binLookupResult = $this->binLookuper->lookup($transaction->getBin());

        $isEU = $this->countryChecker->isEU($binLookupResult->getAlpha2());
        $commission = $isEU ? self::COMMISSION_EU : self::COMMISSION_NON_EU;

        $rates = $this->exchangeRatesLookuper->lookup();
        $exchangeRate = $rates->getRates()[$transaction->getCurrency()];

        // Amount with commission
        return $this->toCents($transaction->getAmount() / $exchangeRate * $commission);
    }

    private function toCents(float $amount): float
    {
        return round($amount, 2);
    }
}