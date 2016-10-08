<?php

namespace PaymentApi\Test\Functional;

use PaymentApi\Command\Migrate;
use PaymentApi\Command\Reset;
use PaymentApi\Command\Seed;
use PaymentApi\Http\Headers;
use PaymentApi\Http\Request;
use PaymentApi\Http\Response;
use PaymentApi\Http\Router;
use PaymentApi\Structure\App;
use PaymentApi\Structure\Collection;
use PaymentApi\Structure\Config;
use PHPUnit\Framework\TestCase;

abstract class FunctionalTest extends TestCase
{
    /** @var App */
    protected $app;

    /**
     * Set up app for testing.
     */
    public function setUp()
    {
        parent::setUp();

        $this->app = require Config::pathTo('bootstrap/configure.php');

        (new Reset($this->app, '/dev/null'))->run();
        (new Migrate($this->app, '/dev/null'))->run();
        (new Seed($this->app, '/dev/null'))->run();
    }

    /**
     * @param string  $path
     * @param Headers $headers
     *
     * @return Response
     */
    protected function get(string $path, Headers $headers): Response
    {
        return $this->sendRequest(
            (new Request())
                ->setMethod('GET')
                ->setPath($path)
                ->setHeaders($headers)
        );
    }

    /**
     * @param string  $path
     * @param Headers $headers
     * @param string  $body
     *
     * @return Response
     */
    protected function post(
        string $path,
        Headers $headers,
        string $body
    ): Response {
        return $this->sendRequest(
            (new Request())
                ->setMethod('POST')
                ->setPath($path)
                ->setHeaders($headers)
                ->setBody($body)
        );
    }

    /**
     * @param array $headers
     *
     * @return Headers
     */
    protected function headers(array $headers): Headers
    {
        return new Headers(new Collection($headers));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    private function sendRequest(Request $request): Response
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        return $router->dispatch($request);
    }
}
