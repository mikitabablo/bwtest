<?php

namespace App\DTOs;

class ExchangeRatesLookupResult
{
    private array $rates;

    public function __construct(array $rates)
    {
        $this->rates = $rates;
    }
    public static function create(array $data): self
    {
        return new self($data['rates']);
    }

    public function getRates(): array
    {
        return $this->rates;
    }
}
