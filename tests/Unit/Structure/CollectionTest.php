<?php

namespace PaymentApi\Test\Unit\Structure;

use PaymentApi\Structure\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * Should be able to match uses keys as regular expressions.
     */
    public function testMatchTo()
    {
        $collection = new Collection(
            [
                '.*?'      => 'a',
                '[0-9]{2}' => 'b',
                '[0-9]{3}' => 'c',
            ]
        );

        $this->assertEquals(
            ['a', 'b'],
            $collection->matchTo('55')->values()->all()
        );
    }

    /**
     * Should be able to exclude keys from the collection.
     */
    public function testExcept()
    {
        $collection = new Collection(
            [
                'a' => 5,
                'b' => 6,
                'c' => 7,
            ]
        );

        $this->assertEquals(
            [5, 7],
            $collection->except(['b'])->values()->all()
        );
    }

    /**
     * Should be able to keep only certain keys from the collection.
     */
    public function testKeep()
    {
        $collection = new Collection(
            [
                'a' => 5,
                'b' => 6,
                'c' => 7,
            ]
        );

        $this->assertEquals(
            [6],
            $collection->keep(['b'])->values()->all()
        );
    }

    /**
     * Should be able to prefix collection values.
     */
    public function testPrefix()
    {
        $collection = new Collection(['a', 'b', 'c']);
        $this->assertEquals(
            [':a', ':b', ':c'],
            $collection->prefix(':')->all()
        );
    }

    /**
     * Should be able to pluck a column from a collection.
     */
    public function testColumn()
    {
        $collection = new Collection(
            [
                [
                    'a' => 1,
                    'b' => 2,
                    'c' => 3,
                ],
                [
                    'a' => 10,
                    'b' => 20,
                    'c' => 30,
                ],
                [
                    'a' => 100,
                    'b' => 200,
                    'c' => 300,
                ],
            ]
        );

        $this->assertEquals(
            [2, 20, 200],
            $collection->column('b')->all()
        );
    }
}
