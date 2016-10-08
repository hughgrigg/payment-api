<?php

namespace PaymentApi\Domain;

use JsonSerializable;
use NumberFormatter;

/**
 * Value object for money.
 */
class Money implements JsonSerializable
{
    /** @var int */
    private $amount;

    /**
     * @param int $amount
     */
    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function formatted(): string
    {
        return (new NumberFormatter(
            'en_GB', NumberFormatter::CURRENCY
        ))->format($this->amount / 100);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->formatted();
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
        return (string) $this;
    }
}
