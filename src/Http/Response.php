<?php

namespace PaymentApi\Http;

use JsonSerializable;
use PaymentApi\Structure\Collection;

class Response implements JsonSerializable
{
    /** @var int */
    private $status;

    /** @var Collection */
    private $body;

    /**
     * Response constructor.
     *
     * @param int        $status
     * @param Collection $body
     */
    public function __construct(int $status, Collection $body)
    {
        $this->status = $status;
        $this->body = $body;
    }

    /**
     * @return int
     */
    public function status(): int
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function body(): string
    {
        return json_encode($this->body);
    }

    /**
     * Send this response to the client.
     */
    public function send()
    {
        http_response_code($this->status);
        header('Content-Type: application/json');
        print json_encode($this, JSON_PRETTY_PRINT);
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
        return $this->body;
    }
}
