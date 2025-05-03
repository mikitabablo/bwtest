<?php

namespace App\Clients\BinList;

use Exception;

class LookupResponseValidator
{
    public function validate(array $data): void
    {
        if (
            !isset($data['country']) ||
            !isset($data['country']['alpha2'])
        ) {
            throw new Exception('Invalid response.');
        }
    }
}