<?php

namespace PaymentApi\Persist;

use PaymentApi\Structure\Collection;
use PDO;

class MySqlPersistence implements Persistence
{
    /** @var PDO */
    private $pdo;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Load a single item by field value.
     *
     * @param string     $source e.g. table
     * @param string     $field  e.g. column
     * @param string|int $value  value to find
     *
     * @return Collection of field values
     * @throws \InvalidArgumentException
     */
    public function find(string $source, string $field, $value): Collection
    {
        $statement = $this->pdo->prepare(
            "SELECT * FROM {$source} WHERE {$field} = :value LIMIT 1;"
        );
        $statement->execute(['value' => $value]);

        return new Collection((array) $statement->fetch());
    }

    /**
     * Load a collection of items by field value.
     *
     * @param string     $source
     * @param string     $field
     * @param string|int $value
     *
     * @return Collection of rows
     * @throws \InvalidArgumentException
     */
    public function where(
        string $source,
        string $field,
        $value
    ): Collection {
        $statement = $this->pdo->prepare(
            "SELECT * FROM {$source} WHERE {$field} = :value;"
        );
        $statement->execute(['value' => $value]);

        return new Collection($statement->fetchAll(PDO::FETCH_ASSOC) ?? []);
    }

    /**
     * Load a collection of items by presence in a set of field values.
     *
     * @param string     $source
     * @param string     $field
     * @param Collection $values
     *
     * @return Collection of rows
     * @throws \InvalidArgumentException
     */
    public function whereIn(
        string $source,
        string $field,
        Collection $values
    ): Collection {
        $statement = $this->pdo->prepare(
            sprintf(
                "SELECT * FROM {$source} WHERE {$field} IN (%s);",
                $values->map(
                    function () {
                        return '?';
                    }
                )->implode(',')
            )
        );
        $statement->execute($values->values()->all());

        return new Collection($statement->fetchAll(PDO::FETCH_ASSOC) ?? []);
    }

    /**
     * @param string     $source
     * @param Collection $conditions
     *
     * @return Collection|Condition[]
     */
    public function whereConditions(
        string $source,
        Collection $conditions
    ): Collection {
        $statement = $this->pdo->prepare(
            sprintf(
                "SELECT * FROM {$source} WHERE %s;",
                $conditions->map(
                    function (Condition $condition) {
                        return $condition->statement();
                    }
                )->implode(' AND ')
            )
        );
        $statement->execute(
            $conditions->reduce(
                function (array $bindings, Condition $condition) {
                    return array_merge($bindings, $condition->values()->all());
                },
                []
            )
        );

        return new Collection($statement->fetchAll(PDO::FETCH_ASSOC) ?? []);
    }

    /**
     * @param string     $destination
     * @param Collection $values
     *
     * @return int
     */
    public function store(string $destination, Collection $values): int
    {
        $statement = $this->pdo->prepare(
            sprintf(
                'INSERT INTO `%s` (`%s`) VALUES (%s);',
                $destination,
                $values->keys()->implode('`,`'),
                $values->keys()->prefix(':')->implode(',')
            )
        );
        $statement->execute($values->all());

        return $this->pdo->lastInsertId();
    }

    /**
     * @param string     $destination
     * @param int        $id
     * @param Collection $values
     */
    public function update(string $destination, int $id, Collection $values)
    {
        $values = $values->except(['id', 'updated_at']);

        $statement = $this->pdo->prepare(
            sprintf(
                'UPDATE `%s` SET %s WHERE `id` = %s;',
                $destination,
                $values->keys()->map(
                    function (string $key) {
                        return "`{$key}` = :{$key}";
                    }
                )->implode(','),
                $id
            )
        );
        $statement->execute($values->all());
    }
}
