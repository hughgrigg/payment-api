<?php

namespace PaymentApi\Http;

class Authorization
{
    /** @var string */
    private $raw;

    /**
     * @param string $raw
     */
    public function __construct(string $raw)
    {
        $this->raw = $raw;
    }

    /**
     * @return string
     */
    public function username(): string
    {
        return $this->basicParts()[0];
    }

    /**
     * @return string
     */
    public function password(): string
    {
        return $this->basicParts()[1];
    }

    /**
     * @return array
     */
    public function basicParts(): array
    {
        return explode(
            ':',
            base64_decode(str_replace('Basic ', '', $this->raw))
        );
    }
}
