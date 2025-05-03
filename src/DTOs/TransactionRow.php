<?php

namespace App\DTOs;

class TransactionRow
{
    public string $bin;
    public string $amount;
    public string $currency;

    public static function create(array $data): self
    {
        $row = new self();

        $row->bin = $data['bin'];
        $row->amount = (float) $data['amount'];
        $row->currency = $data['currency'];

        return $row;
    }

    public function getBin(): ?string
    {
        return $this->bin;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}