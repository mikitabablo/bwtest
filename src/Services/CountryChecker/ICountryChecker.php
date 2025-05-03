<?php

namespace App\Services\CountryChecker;

interface ICountryChecker
{
    public function isEU(string $countryCode): bool;
}