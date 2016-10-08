<?php

namespace PaymentApi\Domain\Entity;

use Carbon\Carbon;
use JsonSerializable;
use PaymentApi\Persist\Persistence;
use PaymentApi\Structure\Collection;
use PaymentApi\Structure\Str;
use ReflectionClass;

abstract class Entity implements JsonSerializable
{
    /** @var Collection */
    protected $values;

    /** @var Persistence */
    private static $persistence;

    /**
     * @param Collection|array $values
     */
    public function __construct($values = null)
    {
        $values = $values ?? [];
        if (!($values instanceof Collection)) {
            $values = new Collection((array) $values);
        }
        $this->values = $values;
    }

    /**
     * Determine the table name from the class name.
     *
     * @return string
     */
    public static function tableName(): string
    {
        return (string) (new Str(
            (new ReflectionClass(static::class))->getShortName()
        ))->snakeCase()->plural();
    }

    /**
     * @param int $id
     *
     * @return Entity|self
     */
    public static function load(int $id): self
    {
        return new static(
            self::$persistence->find(self::tableName(), 'id', $id)
        );
    }

    /**
     * @param string $field
     * @param        $value
     *
     * @return Entity[]|Collection
     */
    public static function loadWhere(string $field, $value): Collection
    {
        return self::$persistence->where(self::tableName(), $field, $value)
            ->map(
                function (array $values) {
                    return new static($values);
                }
            );
    }

    /**
     * @param string     $field
     * @param Collection $values
     *
     * @return Entity[]|Collection
     */
    public static function loadWhereIn(
        string $field,
        Collection $values
    ): Collection {
        return self::$persistence->whereIn(self::tableName(), $field, $values)
            ->map(
                function (array $values) {
                    return new static($values);
                }
            );
    }

    /**
     * @param Collection $conditions
     *
     * @return Collection
     */
    public static function loadWhereConditions(
        Collection $conditions
    ): Collection {
        return self::$persistence->whereConditions(
            self::tableName(),
            $conditions
        )->map(
            function (array $values) {
                return new static($values);
            }
        );
    }

    /**
     * @param Collection|array|null $values
     *
     * @return Entity
     */
    public static function create($values = null): self
    {
        return (new static($values))->save();
    }

    /**
     * @return Entity
     */
    public function save(): self
    {
        if ($this->exists()) {
            self::$persistence->update(
                self::tableName(),
                $this->id(),
                $this->values
            );

            return $this;
        }

        $this->values->put(
            'id',
            self::$persistence->store(self::tableName(), $this->values)
        );

        return $this;
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return $this->values->has('id');
    }

    /**
     * @return int
     */
    public function id(): int
    {
        return (int) $this->values->get('id');
    }

    /**
     * @return Carbon
     */
    public function createdAt(): Carbon
    {
        return new Carbon($this->values->get('created_at'));
    }

    /**
     * @return Carbon
     */
    public function updatedAt(): Carbon
    {
        return new Carbon($this->values->get('updated_at'));
    }

    /**
     * @return Carbon
     */
    public function deletedAt(): Carbon
    {
        return new Carbon($this->values->get('deleted_at'));
    }

    /**
     * @return array|null|Collection
     */
    public function jsonSerialize()
    {
        return $this->values;
    }

    /**
     * @param Persistence $persistence
     */
    public static function setPersistence(Persistence $persistence)
    {
        self::$persistence = $persistence;
    }

    /**
     * @param Entity $owner
     * @param string $foreignKey
     *
     * @return Entity
     */
    protected function belongsTo(Entity $owner, string $foreignKey): Entity
    {
        return $owner->load($this->values->get($foreignKey));
    }

    /**
     * @param Entity $owned
     * @param string $foreignKey
     *
     * @return Collection of owned entities
     */
    protected function hasMany(Entity $owned, string $foreignKey): Collection
    {
        return $owned->loadWhere($foreignKey, $this->values->get('id'));
    }
}
