<?php

namespace App\DTOs;

class TransactionProcessingResult
{
    public ?string $transactionRow = null;
    public ?float $comission = null;
    public ?string $error = null;
}