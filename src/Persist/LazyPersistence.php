<?php

namespace PaymentApi\Persist;

use PaymentApi\Structure\Collection;

/**
 * Decorator for persistence that only instantiates the wrapped persistence
 * when needed.
 */
class LazyPersistence implements Persistence
{
    /** @var callable */
    private $factory;

    /** @var Persistence */
    private $persistence;

    /**
     * @param callable $factory
     */
    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Load a single item by field value.
     *
     * @param string     $source e.g. table
     * @param string     $field  e.g. column
     * @param string|int $value  value to find
     *
     * @return Collection of field values
     */
    public function find(
        string $source,
        string $field,
        $value
    ): Collection {
        return $this->persistence()->find($source, $field, $value);
    }

    /**
     * Load a collection of items by field value.
     *
     * @param string     $source
     * @param string     $field
     * @param string|int $value
     *
     * @return Collection
     */
    public function where(
        string $source,
        string $field,
        $value
    ): Collection {
        return $this->persistence()->where($source, $field, $value);
    }

    /**
     * Load a collection of items by field value.
     *
     * @param string     $source
     * @param string     $field
     * @param Collection $values
     *
     * @return Collection
     */
    public function whereIn(
        string $source,
        string $field,
        Collection $values
    ): Collection {
        return $this->persistence()->whereIn($source, $field, $values);
    }

    /**
     * @param string     $source
     * @param Collection $conditions
     *
     * @return Collection
     */
    public function whereConditions(
        string $source,
        Collection $conditions
    ): Collection {
        return $this->persistence()->whereConditions($source, $conditions);
    }

    /**
     * @param string     $destination
     * @param Collection $values
     *
     * @return int
     */
    public function store(
        string $destination,
        Collection $values
    ): int {
        return $this->persistence()->store($destination, $values);
    }

    /**
     * @param string     $destination
     * @param int        $id
     * @param Collection $values
     */
    public function update(
        string $destination,
        int $id,
        Collection $values
    ) {
        return $this->persistence()->update($destination, $id, $values);
    }

    /**
     * @return Persistence
     */
    private function persistence(): Persistence
    {
        if ($this->persistence === null) {
            $factory = $this->factory;
            $this->persistence = $factory();
        }

        return $this->persistence;
    }
}
