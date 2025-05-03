<?php

namespace App\Services\TransactionsPostProcessor;

use App\DTOs\TransactionProcessingResult;
use App\DTOs\TransactionRow;
use App\Services\CommissionCalculator\ICommissionCalculator;
use App\Services\FileReader\IFileReader;
use Throwable;

class TransactionsPostProcessor
{
    private IFileReader $fileReader;
    private ICommissionCalculator $commissionCalculator;

    public function __construct(
        IFileReader           $fileReader,
        ICommissionCalculator $CommissionCalculator,
    )
    {
        $this->commissionCalculator = $CommissionCalculator;
        $this->fileReader = $fileReader;
    }

    /**
     * @return TransactionProcessingResult[]
     */
    public function process(): array
    {
        $results = [];
        while ($row = $this->fileReader->readLine()) {
            $result = new TransactionProcessingResult();
            $result->transactionRow = $row;

            $rowDecoded = json_decode($row, true);
            if (
                !isset($rowDecoded['bin']) ||
                !isset($rowDecoded['amount']) ||
                !isset($rowDecoded['currency'])
            ) {
                $result->error = 'Incorrect transaction JSON';
                $results[] = $result;
                continue;
            }

            $transaction = TransactionRow::create($rowDecoded);
            try {
                $result->comission = $this->commissionCalculator->calculate($transaction);
            } catch (Throwable $exception) {
                $result->error = $exception->getMessage();
                $results[] = $result;
                continue;
            }

            $results[] = $result;
        }

        return $results;
    }
}
