<?php

namespace PaymentApi\Test\Unit\Domain;

use PaymentApi\Domain\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    /**
     * @dataProvider amountFormatProvider
     *
     * @param int    $amount
     * @param string $expectedFormat
     */
    public function testFormat(int $amount, string $expectedFormat)
    {
        $money = new Money($amount);

        $this->assertEquals($expectedFormat, $money->formatted());
    }

    /**
     * @return array
     */
    public function amountFormatProvider(): array
    {
        return [
            [-100, '-£1.00'],
            [0, '£0.00'],
            [33, '£0.33'],
            [1000, '£10.00'],
            [2020, '£20.20'],
            [9999, '£99.99'],
        ];
    }
}
