<?php

namespace PaymentApi\Test\Unit\Persist;

use PaymentApi\Persist\MySqlPersistence;
use PaymentApi\Structure\Collection;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Test fiddlier parts of MySqlPersistence functionality.
 */
class MySqlPersistenceTest extends TestCase
{
    /** @var MySqlPersistence */
    private $mySqlPersistence;

    /** @var PDO|PHPUnit_Framework_MockObject_MockObject */
    private $pdo;

    /**
     * Set up MySqlPersistence with dependency for each test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->pdo = $this->createMock(PDO::class);
        $this->mySqlPersistence = new MySqlPersistence($this->pdo);
    }

    /**
     * Should be able to insert a new row.
     */
    public function testStore()
    {
        // Given we have a destination table and some values;
        $destination = 'foo_table';
        $values = new Collection(
            [
                'x'   => 'y',
                'a'   => 'b',
                'foo' => 'bar',
            ]
        );

        // Then a statement should be prepared;
        $statement = $this->createMock(PDOStatement::class);
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with(
                "INSERT INTO `{$destination}` (`x`,`a`,`foo`) VALUES (:x,:a,:foo);"
            )
            ->willReturn($statement);

        // And the statement should be executed with the values bound to it;
        $statement->expects($this->once())
            ->method('execute')
            ->with(
                [
                    'x'   => 'y',
                    'a'   => 'b',
                    'foo' => 'bar',
                ]
            );
        $this->pdo->method('lastInsertId')->willReturn(1);

        // When we use the MySqlPersistence to store them.
        $this->mySqlPersistence->store($destination, $values);
    }

    public function testUpdate()
    {
        // Given we have a destination table and some values;
        $destination = 'foo_table';
        $id = 1;
        $values = new Collection(
            [
                'id' => $id,
                'x'  => 'y',
                'a'  => 'b',
            ]
        );

        // Then a statement should be prepared;
        $statement = $this->createMock(PDOStatement::class);
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with(
                "UPDATE `{$destination}` SET `x` = :x,`a` = :a WHERE `id` = {$id};"
            )
            ->willReturn($statement);

        // And the statement should be executed with the values bound to it;
        $statement->expects($this->once())
            ->method('execute')
            ->with(
                [
                    'x' => 'y',
                    'a' => 'b',
                ]
            );

        // When we use the MySqlPersistence to store them.
        $this->mySqlPersistence->update($destination, $id, $values);
    }
}
