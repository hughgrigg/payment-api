<?php

namespace PaymentApi\Persist;

use PaymentApi\Structure\Collection;

interface Persistence
{
    /**
     * Load a single item by field value.
     *
     * @param string     $source e.g. table
     * @param string     $field  e.g. column
     * @param string|int $value  value to find
     *
     * @return Collection of field values
     */
    public function find(string $source, string $field, $value): Collection;

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
    ): Collection;

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
    ): Collection;

    /**
     * @param string     $source
     * @param Collection $conditions
     *
     * @return Collection|Condition[]
     */
    public function whereConditions(
        string $source,
        Collection $conditions
    ): Collection;

    /**
     * @param string     $destination
     * @param Collection $values
     *
     * @return int
     */
    public function store(string $destination, Collection $values): int;

    /**
     * @param string     $destination
     * @param int        $id
     * @param Collection $values
     */
    public function update(string $destination, int $id, Collection $values);
}
