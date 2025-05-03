<?php

namespace App\Services\CountryChecker;

use Exception;

class CountryChecker implements ICountryChecker
{
    private const array EU_COUNTRY_CODES = [
        'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR',
        'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PO', 'PT', 'RO',
        'SE', 'SI', 'SK',
    ];

    public function isEU(string $countryCode): bool
    {
        if (strlen($countryCode) !== 2) {
            throw new Exception('Invalid country code format. Must be an alpha-2 code with 2 characters');
        }

        if (in_array(
            strtoupper($countryCode),
            self::EU_COUNTRY_CODES,
        )) {
            return true;
        }

        return false;
    }
}
