<?php

namespace Tests\Services\CountryChecker;

use PHPUnit\Framework\TestCase;
use App\Services\CountryChecker\CountryChecker;
use Exception;

class CountryCheckerTest extends TestCase
{
    public function testIsEUWithEUCountry(): void
    {
        $checker = new CountryChecker();

        $this->assertTrue($checker->isEU('DE'));
        $this->assertTrue($checker->isEU('fr'));
        $this->assertTrue($checker->isEU('lt'));
    }

    public function testIsEUWithNonEUCountry(): void
    {
        $checker = new CountryChecker();

        $this->assertFalse($checker->isEU('US'));
        $this->assertFalse($checker->isEU('CN'));
    }

    public function testIsEUWithInvalidCodeLength(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid country code format. Must be an alpha-2 code with 2 characters');

        $checker = new CountryChecker();
        $checker->isEU('USA');
    }

    public function testIsEUWithEmptyCode(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid country code format. Must be an alpha-2 code with 2 characters');

        $checker = new CountryChecker();
        $checker->isEU('');
    }
}
