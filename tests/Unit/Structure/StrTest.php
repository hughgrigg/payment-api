<?php

namespace PaymentApi\Test\Unit\Structure;

use PaymentApi\Structure\Str;
use PHPUnit\Framework\TestCase;

class StrTest extends TestCase
{
    /**
     * @dataProvider snakeCaseExpectationProvider
     *
     * @param string $input
     * @param string $expected
     */
    public function testSnakeCase(string $input, string $expected)
    {
        $str = new Str($input);
        $this->assertEquals($expected, $str->snakeCase());
    }

    /**
     * @return array
     */
    public function snakeCaseExpectationProvider(): array
    {
        return [
            ['foo', 'foo'],
            ['Foo', 'foo'],
            ['FooBar', 'foo_bar'],
            ['FooBarThisThat', 'foo_bar_this_that'],
            ['ABC', 'a_b_c'],
        ];
    }

    /**
     * @dataProvider pluralExpectationProvider
     *
     * @param string $input
     * @param string $expected
     */
    public function testPlural(string $input, string $expected)
    {
        $str = new Str($input);
        $this->assertEquals($expected, $str->plural());
    }

    /**
     * @return array
     */
    public function pluralExpectationProvider(): array
    {
        return [
            ['location', 'locations'],
            ['country', 'countries'],
        ];
    }
}
