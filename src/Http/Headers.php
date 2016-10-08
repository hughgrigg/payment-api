<?php

namespace PaymentApi\Http;

use PaymentApi\Structure\Collection;

class Headers
{
    /** @var Collection */
    private $headers;

    /** @var array */
    private static $server;

    /**
     * @param Collection $headers
     */
    public function __construct(Collection $headers)
    {
        $this->headers = new Collection(
            array_change_key_case($headers->all(), CASE_LOWER)
        );
    }

    /**
     * @return Headers
     */
    public static function fromGlobal(): Headers
    {
        if (function_exists('getallheaders')) {
            return new self(new Collection(getallheaders()));
        }

        return new self(
            (new Collection(self::server()))
                ->whereKey(
                    function ($name) {
                        return strpos($name, 'HTTP_') === 0;
                    }
                )
                ->flip()
                ->map(
                    function ($name) {
                        return strtolower(
                            str_replace(
                                ' ',
                                '-',
                                str_replace('_', ' ', substr($name, 5))
                            )
                        );
                    }
                )
                ->flip()
        );
    }

    /**
     * Allow injecting this for testing.
     *
     * @param array $server
     */
    public static function setServer(array $server)
    {
        self::$server = $server;
    }

    /**
     * @param string $headerName
     *
     * @return string
     */
    public function get(string $headerName): string
    {
        return (string) $this->headers->get(strtolower($headerName));
    }

    /**
     * @param string $headerName
     *
     * @return bool
     */
    public function has(string $headerName): bool
    {
        return $this->headers->has(strtolower($headerName));
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->headers->all();
    }

    /**
     * @return array
     */
    private static function server(): array
    {
        if (self::$server === null) {
            self::$server = $_SERVER;
        }

        return self::$server;
    }
}
