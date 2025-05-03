<?php

namespace App\Clients\BinList;

use App\DTOs\BinLookupResult;

interface IBinLookuper
{
    public function lookup(string $bin): ?BinLookupResult;
}