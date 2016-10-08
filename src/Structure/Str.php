<?php

namespace PaymentApi\Structure;

class Str
{
    /** @var string */
    private $raw;

    /**
     * Str constructor.
     *
     * @param string $raw
     */
    public function __construct(string $raw)
    {
        $this->raw = $raw;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->raw;
    }

    /**
     * @return Str
     */
    public function snakeCase(): Str
    {
        return new self(
            strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $this->raw))
        );
    }

    /**
     * @return Str
     */
    public function plural(): Str
    {
        if (mb_substr($this->raw, -1) === 'y') {
            return new self(mb_substr($this->raw, 0, -1).'ies');
        }

        return new self("{$this->raw}s");
    }
}
