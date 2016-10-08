<?php

namespace PaymentApi\Structure;

/**
 * Application container
 */
class App
{
    /** @var Collection */
    private $bindings;

    /** @var Config */
    private $config;

    /**
     * @param string   $identifier
     * @param callable $make
     *
     * @throws \InvalidArgumentException
     */
    public function bind(string $identifier, callable $make)
    {
        $this->bindings()->put($identifier, $make);
    }

    /**
     * @param string $identifier
     *
     * @throws \InvalidArgumentException
     * @throws \OutOfRangeException
     */
    public function make(string $identifier)
    {
        if ($this->bindings()->has($identifier)) {
            $make = $this->bindings()->get($identifier);

            return $make($this);
        }

        return new $identifier();
    }

    /**
     * @return Config
     */
    public function config(): Config
    {
        if ($this->config === null) {
            $this->config = new Config();
        }

        return $this->config;
    }

    /**
     * @return Collection
     * @throws \InvalidArgumentException
     */
    private function bindings(): Collection
    {
        if ($this->bindings === null) {
            $this->bindings = new Collection();
        }

        return $this->bindings;
    }
}
