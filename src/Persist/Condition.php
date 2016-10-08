<?php

namespace PaymentApi\Persist;

use PaymentApi\Structure\Collection;

class Condition
{
    /** @var string */
    private $field;

    /** @var string */
    private $operator;

    /** @var Collection */
    private $values;

    /**
     * @param string $field
     * @param string $operator
     * @param array  $values
     */
    public function __construct(string $field, string $operator, array $values)
    {
        $this->field = $field;
        $this->operator = $operator;
        $this->values = new Collection($values);
    }

    /**
     * @return string
     */
    public function field(): string
    {
        return $this->field;
    }

    public function operator(): string
    {
        return $this->operator;
    }

    /**
     * @return Collection
     */
    public function values(): Collection
    {
        return $this->values;
    }

    /**
     * @return string
     */
    public function statement(): string
    {
        if ($this->values->count() > 1 || $this->operator() === 'IN') {
            return sprintf(
                '%s %s (%s)',
                $this->field(),
                $this->operator(),
                $this->values()->map(
                    function () {
                        return '?';
                    }
                )->implode(',')
            );
        }

        return "{$this->field()} {$this->operator()} ?";
    }
}
