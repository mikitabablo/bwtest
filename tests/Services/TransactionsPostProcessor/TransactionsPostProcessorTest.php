<?php

namespace Tests\Unit\Services;

use App\DTOs\TransactionProcessingResult;
use App\DTOs\TransactionRow;
use App\Services\CommissionCalculator\ICommissionCalculator;
use App\Services\FileReader\IFileReader;
use App\Services\TransactionsPostProcessor\TransactionsPostProcessor;
use PHPUnit\Framework\TestCase;

class TransactionsPostProcessorTest extends TestCase
{
    public function testProcessesValidTransaction(): void
    {
        $transactionJson = json_encode([
            'bin' => '45717360',
            'amount' => 100.00,
            'currency' => 'EUR'
        ]);

        $mockReader = $this->createMock(IFileReader::class);
        $mockReader->method('readLine')
            ->willReturnOnConsecutiveCalls(
                $transactionJson,
                null
            );

        $mockCalculator = $this->createMock(ICommissionCalculator::class);
        $mockCalculator->expects($this->once())
            ->method('calculate')
            ->with($this->callback(function (TransactionRow $row) {
                return $row->getBin() === '45717360' &&
                    $row->getAmount() === 100.00 &&
                    $row->getCurrency() === 'EUR';
            }))
            ->willReturn(1.00);

        $processor = new TransactionsPostProcessor($mockReader, $mockCalculator);
        $results = $processor->process();

        $this->assertCount(1, $results);
        $this->assertNull($results[0]->error);
        $this->assertSame(1.00, $results[0]->comission);
        $this->assertSame($transactionJson, $results[0]->transactionRow);
    }

    public function testReturnsErrorOnMalformedJson(): void
    {
        $invalidJson = '{"bin": "123456", "amount": 50}'; // no currency

        $mockReader = $this->createMock(IFileReader::class);
        $mockReader->method('readLine')
            ->willReturnOnConsecutiveCalls(
                $invalidJson,
                null
            );

        $mockCalculator = $this->createMock(ICommissionCalculator::class);
        $mockCalculator->expects($this->never())->method('calculate');

        $processor = new TransactionsPostProcessor($mockReader, $mockCalculator);
        $results = $processor->process();

        $this->assertCount(1, $results);
        $this->assertSame('Incorrect transaction JSON', $results[0]->error);
        $this->assertSame($invalidJson, $results[0]->transactionRow);
    }

    public function testHandlesCalculatorException(): void
    {
        $validJson = json_encode([
            'bin' => '123456',
            'amount' => 75.50,
            'currency' => 'USD'
        ]);

        $mockReader = $this->createMock(IFileReader::class);
        $mockReader->method('readLine')
            ->willReturnOnConsecutiveCalls(
                $validJson,
                null
            );

        $mockCalculator = $this->createMock(ICommissionCalculator::class);
        $mockCalculator->method('calculate')
            ->willThrowException(new \RuntimeException('Calculation failed'));

        $processor = new TransactionsPostProcessor($mockReader, $mockCalculator);
        $results = $processor->process();

        $this->assertCount(1, $results);
        $this->assertSame('Calculation failed', $results[0]->error);
        $this->assertSame($validJson, $results[0]->transactionRow);
    }

    public function testReturnsEmptyArrayWhenNoTransactions(): void
    {
        $mockReader = $this->createMock(IFileReader::class);
        $mockReader->method('readLine')->willReturn(null);

        $mockCalculator = $this->createMock(ICommissionCalculator::class);

        $processor = new TransactionsPostProcessor($mockReader, $mockCalculator);
        $results = $processor->process();

        $this->assertSame([], $results);
    }
}
