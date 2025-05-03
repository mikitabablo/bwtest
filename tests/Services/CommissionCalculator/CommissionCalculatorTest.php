<?php

namespace Tests\Services\CommissionCalculator;

use PHPUnit\Framework\TestCase;
use App\Services\CommissionCalculator\CommissionCalculator;
use App\Clients\BinList\IBinLookuper;
use App\Clients\ExchangeRates\IExchangeRatesLookuper;
use App\Services\CountryChecker\ICountryChecker;
use App\DTOs\TransactionRow;
use App\DTOs\BinLookupResult;
use App\DTOs\ExchangeRatesLookupResult;
use Exception;

class CommissionCalculatorTest extends TestCase
{
    public function testCalculateForEUCountry(): void
    {
        $binLookuper = $this->createMock(IBinLookuper::class);
        $exchangeRatesLookuper = $this->createMock(IExchangeRatesLookuper::class);
        $countryChecker = $this->createMock(ICountryChecker::class);

        $transaction = $this->createMock(TransactionRow::class);
        $transaction->method('getBin')->willReturn('45717360');
        $transaction->method('getAmount')->willReturn(100.0);
        $transaction->method('getCurrency')->willReturn('EUR');

        $binResult = $this->createMock(BinLookupResult::class);
        $binResult->method('getAlpha2')->willReturn('DE');

        $ratesResult = $this->createMock(ExchangeRatesLookupResult::class);
        $ratesResult->method('getRates')->willReturn(['EUR' => 1.0]);

        $binLookuper->method('lookup')->willReturn($binResult);
        $exchangeRatesLookuper->method('lookup')->willReturn($ratesResult);
        $countryChecker->method('isEU')->willReturn(true);

        $calculator = new CommissionCalculator($binLookuper, $exchangeRatesLookuper, $countryChecker);

        $result = $calculator->calculate($transaction);

        $this->assertEquals(1.00, $result);
    }

    public function testCalculateForNonEUCountry(): void
    {
        $binLookuper = $this->createMock(IBinLookuper::class);
        $exchangeRatesLookuper = $this->createMock(IExchangeRatesLookuper::class);
        $countryChecker = $this->createMock(ICountryChecker::class);

        $transaction = $this->createMock(TransactionRow::class);
        $transaction->method('getBin')->willReturn('516793');
        $transaction->method('getAmount')->willReturn(200.0);
        $transaction->method('getCurrency')->willReturn('USD');

        $binResult = $this->createMock(BinLookupResult::class);
        $binResult->method('getAlpha2')->willReturn('US');

        $ratesResult = $this->createMock(ExchangeRatesLookupResult::class);
        $ratesResult->method('getRates')->willReturn(['USD' => 1.0]);

        $binLookuper->method('lookup')->willReturn($binResult);
        $exchangeRatesLookuper->method('lookup')->willReturn($ratesResult);
        $countryChecker->method('isEU')->willReturn(false);

        $calculator = new CommissionCalculator($binLookuper, $exchangeRatesLookuper, $countryChecker);

        $result = $calculator->calculate($transaction);

        $this->assertEquals(4.00, $result);
    }

    public function testCalculateWithExchangeRate(): void
    {
        $binLookuper = $this->createMock(IBinLookuper::class);
        $exchangeRatesLookuper = $this->createMock(IExchangeRatesLookuper::class);
        $countryChecker = $this->createMock(ICountryChecker::class);

        $transaction = $this->createMock(TransactionRow::class);
        $transaction->method('getBin')->willReturn('123456');
        $transaction->method('getAmount')->willReturn(300.0);
        $transaction->method('getCurrency')->willReturn('JPY');

        $binResult = $this->createMock(BinLookupResult::class);
        $binResult->method('getAlpha2')->willReturn('FR');

        $ratesResult = $this->createMock(ExchangeRatesLookupResult::class);
        $ratesResult->method('getRates')->willReturn(['JPY' => 150.0]);

        $binLookuper->method('lookup')->willReturn($binResult);
        $exchangeRatesLookuper->method('lookup')->willReturn($ratesResult);
        $countryChecker->method('isEU')->willReturn(true);

        $calculator = new CommissionCalculator($binLookuper, $exchangeRatesLookuper, $countryChecker);

        $result = $calculator->calculate($transaction);

        $this->assertEquals(0.02, $result);
    }

    public function testCalculateWithBinLookupException(): void
    {
        $binLookuper = $this->createMock(IBinLookuper::class);
        $exchangeRatesLookuper = $this->createMock(IExchangeRatesLookuper::class);
        $countryChecker = $this->createMock(ICountryChecker::class);

        $transaction = $this->createMock(TransactionRow::class);
        $transaction->method('getBin')->willReturn('45717360');
        $transaction->method('getAmount')->willReturn(100.0);
        $transaction->method('getCurrency')->willReturn('EUR');

        $binLookuper->method('lookup')->willThrowException(new Exception("Bin lookup failed"));

        $calculator = new CommissionCalculator($binLookuper, $exchangeRatesLookuper, $countryChecker);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Bin lookup failed");

        $calculator->calculate($transaction);
    }

    public function testCalculateWithExchangeRateException(): void
    {
        $binLookuper = $this->createMock(IBinLookuper::class);
        $exchangeRatesLookuper = $this->createMock(IExchangeRatesLookuper::class);
        $countryChecker = $this->createMock(ICountryChecker::class);

        $transaction = $this->createMock(TransactionRow::class);
        $transaction->method('getBin')->willReturn('45717360');
        $transaction->method('getAmount')->willReturn(100.0);
        $transaction->method('getCurrency')->willReturn('EUR');

        $binResult = $this->createMock(BinLookupResult::class);
        $binResult->method('getAlpha2')->willReturn('DE');

        $binLookuper->method('lookup')->willReturn($binResult);
        $exchangeRatesLookuper->method('lookup')->willThrowException(new Exception("Exchange rates lookup failed"));

        $calculator = new CommissionCalculator($binLookuper, $exchangeRatesLookuper, $countryChecker);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Exchange rates lookup failed");

        $calculator->calculate($transaction);
    }

    public function testCalculateWithCountryCheckerException(): void
    {
        $binLookuper = $this->createMock(IBinLookuper::class);
        $exchangeRatesLookuper = $this->createMock(IExchangeRatesLookuper::class);
        $countryChecker = $this->createMock(ICountryChecker::class);

        $transaction = $this->createMock(TransactionRow::class);
        $transaction->method('getBin')->willReturn('45717360');
        $transaction->method('getAmount')->willReturn(100.0);
        $transaction->method('getCurrency')->willReturn('EUR');

        $binResult = $this->createMock(BinLookupResult::class);
        $binResult->method('getAlpha2')->willReturn('DE');

        $ratesResult = $this->createMock(ExchangeRatesLookupResult::class);
        $ratesResult->method('getRates')->willReturn(['EUR' => 1.0]);

        $binLookuper->method('lookup')->willReturn($binResult);
        $exchangeRatesLookuper->method('lookup')->willReturn($ratesResult);
        $countryChecker->method('isEU')->willThrowException(new Exception("Country check failed"));

        $calculator = new CommissionCalculator($binLookuper, $exchangeRatesLookuper, $countryChecker);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Country check failed");

        $calculator->calculate($transaction);
    }
}
