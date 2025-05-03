<?php

namespace App\Services\CommissionCalculator;

use App\DTOs\TransactionRow;

interface ICommissionCalculator
{
    public function calculate(TransactionRow $transaction): float;
}