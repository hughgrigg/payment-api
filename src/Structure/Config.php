<?php

namespace PaymentApi\Structure;

class Config
{
    const APP_ROOT = __DIR__.'/../../';

    /** @var Collection */
    private $values;

    /**
     * @param string $path
     *
     * @return string
     */
    public static function pathTo(string $path): string
    {
        return self::APP_ROOT.trim($path, " \t\n\r\0\x0B/");
    }

    /**
     * @return string
     */
    public function pdoDsn(): string
    {
        return sprintf(
            "mysql:host={$this->dbHost()};dbname=payment_api;charset=utf8"
        );
    }

    /**
     * @return string
     */
    public function dbHost(): string
    {
        return $this->values()->get('db')->get('host');
    }

    /**
     * @return string
     */
    public function dbDatabase(): string
    {
        return $this->values()->get('db')->get('database');
    }

    /**
     * @return string
     */
    public function dbUsername(): string
    {
        return $this->values()->get('db')->get('username');
    }

    /**
     * @return string
     */
    public function dbPassword(): string
    {
        return $this->values()->get('db')->get('password');
    }

    /**
     * @return string
     */
    public function signingKey(): string
    {
        return (string) $this->values()->get('auth')->get('signing_key');
    }

    /**
     * @return bool
     */
    public function debugMode(): bool
    {
        return (bool) $this->values()->get('environment')->get('debug');
    }

    /**
     * @return Collection
     */
    private function values(): Collection
    {
        if ($this->values === null) {
            $this->values = new Collection(
                parse_ini_file(
                    self::pathTo('config/config.ini'),
                    true,
                    INI_SCANNER_TYPED
                )
            );
        }

        return $this->values;
    }
}
