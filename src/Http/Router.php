<?php

namespace PaymentApi\Http;

use InvalidArgumentException;
use PaymentApi\Structure\Collection;
use PaymentApi\Structure\Config;
use Throwable;

class Router
{
    /** @var Config */
    private $config;

    /** @var Collection */
    private $methodHandlers;

    /**
     * Router constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws InvalidArgumentException
     * @throws \PaymentApi\Http\HttpException
     */
    public function dispatch(Request $request): Response
    {
        try {
            return $this->respondTo($request);
        } catch (Throwable $e) {
            $message = $this->config->debugMode() ? $e->getMessage() : '500';

            return new Response(500, new Collection(['errors' => [$message]]));
        }
    }

    /**
     * @param string   $match
     * @param string   $method
     * @param callable $handle
     *
     * @throws InvalidArgumentException
     */
    public function addHandler(string $method, string $match, callable $handle)
    {
        $this->methodHandlers($method)->put($match, $handle);
    }

    /**
     * @param string   $match
     * @param callable $handle
     *
     * @throws \InvalidArgumentException
     */
    public function get(string $match, callable $handle)
    {
        $this->addHandler('GET', $match, $handle);
    }

    /**
     * @param string   $match
     * @param callable $handle
     *
     * @throws \InvalidArgumentException
     */
    public function post(string $match, callable $handle)
    {
        $this->addHandler('POST', $match, $handle);
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \PaymentApi\Http\HttpException
     */
    private function respondTo(Request $request): Response
    {
        $handlers = $this->methodHandlers($request->method())->matchTo(
            $request->path()
        );

        if ($handlers->isEmpty()) {
            throw new HttpException(
                "Path {$request->path()} not found.",
                404
            );
        }

        $request->setMatchedRoute("%{$handlers->keys()->last()}%");

        $pipeline = $handlers->reduce(
            function (callable $current, callable $next) use ($request) {
                return function () use ($current, $next, $request) {
                    return $current($request, $next);
                };
            },
            $handlers->shift()
        );

        return $pipeline($request);
    }

    /**
     * @param string $method
     *
     * @return callable[]|Collection
     */
    private function methodHandlers(string $method): Collection
    {
        if (empty($this->methodHandlers[$method])) {
            $this->methodHandlers[$method] = new Collection();
        }

        return $this->methodHandlers[$method];
    }
}
