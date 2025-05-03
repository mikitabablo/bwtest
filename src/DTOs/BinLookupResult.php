<?php

namespace App\DTOs;

class BinLookupResult
{
    // we would need only this field for now, so I would leave only it :)
    private string $alpha2;

    public function __construct(
        string $alpha2,
    )
    {
        $this->alpha2 = $alpha2;
    }

    public static function create(array $data): self
    {
        return new self(alpha2: $data['country']['alpha2']);
    }

    public function getAlpha2(): string
    {
        return $this->alpha2;
    }
}