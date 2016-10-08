<?php

namespace PaymentApi\Structure;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

class Collection implements Countable, IteratorAggregate, JsonSerializable
{
    /** @var array */
    private $items = [];

    /**
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @param mixed $item
     *
     * @return Collection
     */
    public function push($item): self
    {
        return $this->put(count($this->items), $item);
    }

    /**
     * @param string|int $key
     * @param mixed      $item
     *
     * @return Collection
     */
    public function put($key, $item): self
    {
        $this->items[$key] = $item;

        return $this;
    }

    /**
     * @param string|int $key
     *
     * @return mixed|Collection|null
     */
    public function get($key)
    {
        $value = $this->items[$key] ?? null;

        if (is_array($value)) {
            return new self($value);
        }

        return $value;
    }

    /**
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->items);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @param string $subject
     *
     * @return Collection
     */
    public function matchTo(string $subject): Collection
    {
        return $this->whereKey(
            function ($pattern) use ($subject) {
                return preg_match("%{$pattern}%", $subject);
            }
        );
    }

    /**
     * @param string|int $key
     *
     * @return bool
     */
    public function has($key): bool
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->count() < 1;
    }

    /**
     * @param callable $map
     *
     * @return Collection
     */
    public function map(callable $map): Collection
    {
        return new self(array_map($map, $this->items));
    }

    /**
     * @param callable $mapWith
     *
     * @return Collection
     */
    public function mapWith(callable $mapWith): Collection
    {
        $items = $this->items;
        array_walk($items, $mapWith);

        return new self($items);
    }

    /**
     * @return Collection
     */
    public function flip(): Collection
    {
        return new self(array_flip($this->items));
    }

    /**
     * @param array $unwantedKeys
     *
     * @return Collection
     */
    public function except(array $unwantedKeys): Collection
    {
        return $this->whereKey(
            function ($key) use ($unwantedKeys) {
                return !in_array($key, $unwantedKeys, true);
            }
        );
    }

    /**
     * @param array $wantedKeys
     *
     * @return Collection
     */
    public function keep(array $wantedKeys): Collection
    {
        return $this->whereKey(
            function ($key) use ($wantedKeys) {
                return in_array($key, $wantedKeys, true);
            }
        );
    }

    /**
     * @param callable $reduce
     * @param mixed    $initial
     *
     * @return mixed
     */
    public function reduce(callable $reduce, $initial)
    {
        return array_reduce($this->items, $reduce, $initial);
    }

    /**
     * @param callable $filter
     *
     * @return Collection
     */
    public function where(callable $filter): Collection
    {
        return new self(
            array_filter($this->items, $filter, ARRAY_FILTER_USE_BOTH)
        );
    }

    /**
     * @param callable $filter
     *
     * @return Collection
     */
    public function whereKey(callable $filter): Collection
    {
        return $this->where(
            function ($item, $key) use ($filter) {
                return $filter($key);
            }
        );
    }

    /**
     * @param callable|null $condition
     *
     * @return mixed|null
     */
    public function first(callable $condition = null)
    {
        if (!$condition) {
            return reset($this->items);
        }

        foreach ($this->items as $key => $item) {
            if ($condition($item, $key)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param callable|null $condition
     *
     * @return mixed|null
     */
    public function last(callable $condition = null)
    {
        return $this->reverse()->first($condition);
    }

    /**
     * @return Collection
     */
    public function reverse(): Collection
    {
        return new self(array_reverse($this->items));
    }

    /**
     * @param callable $action
     *
     * @return Collection
     */
    public function each(callable $action): Collection
    {
        foreach ($this->items as $key => $value) {
            $action($this->get($key), $key);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function keys(): Collection
    {
        return new self(array_keys($this->items));
    }

    /**
     * @return Collection
     */
    public function values(): Collection
    {
        return new self(array_values($this->items));
    }

    /**
     * @param string $delimiter
     *
     * @return string
     */
    public function implode(string $delimiter): string
    {
        return implode($delimiter, $this->items);
    }

    /**
     * @param string $prefix
     *
     * @return Collection
     */
    public function prefix(string $prefix): Collection
    {
        return $this->map(
            function ($item) use ($prefix) {
                return "{$prefix}{$item}";
            }
        );
    }

    /**
     * @param string $columnName
     *
     * @return Collection
     */
    public function column(string $columnName): Collection
    {
        return $this->map(
            function ($item) use ($columnName) {
                if ($item instanceof Collection) {
                    return $item->get($columnName);
                }

                if (is_object($item)) {
                    return $item->{$columnName};
                }

                if (is_array($item)) {
                    return $item[$columnName];
                }

                return null;
            }
        );
    }

    /**
     * Count elements of an object
     *
     * @link  http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     *        </p>
     *        <p>
     *        The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Retrieve an external iterator
     *
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing
     *                     <b>Iterator</b> or
     *        <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *        which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->items;
    }
}
