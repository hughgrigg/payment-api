<?php

namespace PaymentApi\Http;

use PaymentApi\Structure\Collection;

class Request
{
    /** @var Headers */
    private $headers;

    /** @var string */
    private $path;

    /** @var string */
    private $method;

    /** @var Collection */
    private $parameters;

    /** @var string */
    private $body;

    /** @var string */
    private $matchedRoute;

    /**
     * @return Request
     * @throws \InvalidArgumentException
     */
    public static function fromGlobals(): Request
    {
        return (new self())
            ->setHeaders(Headers::fromGlobal())
            ->setMethod($_SERVER['REQUEST_METHOD'])
            ->setPath(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
            ->setParameters(new Collection($_GET))
            ->setBody(file_get_contents('php://input'));
    }

    /**
     * @param Headers $headers
     *
     * @return Request
     */
    public function setHeaders(Headers $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function header(string $name): string
    {
        return $this->headers->get($name);
    }

    /**
     * @return Authorization
     * @throws \InvalidArgumentException
     */
    public function authorization(): Authorization
    {
        if ($this->headers->has('Authorization')) {
            return new Authorization($this->header('Authorization'));
        }

        if ($this->json()->has('authorization')) {
            return new Authorization($this->json()->get('authorization'));
        }

        return new Authorization(base64_encode(':'));
    }

    /**
     * @param string $method
     *
     * @return Request
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return string
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * @param string $path
     *
     * @return Request
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * @param Collection $parameters
     *
     * @return Request
     */
    public function setParameters(Collection $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @param string $body
     *
     * @return Request
     */
    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return string
     */
    public function body(): string
    {
        return (string) $this->body;
    }

    /**
     * @return Collection
     */
    public function json(): Collection
    {
        return new Collection((array) json_decode($this->body, true));
    }

    /**
     * @param string $matchedRoute
     *
     * @return Request
     */
    public function forMatchedRoute(string $matchedRoute): Request
    {
        return (clone $this)->setMatchedRoute($matchedRoute);
    }

    /**
     * @param string $matchedRoute
     *
     * @return Request
     */
    public function setMatchedRoute(string $matchedRoute): Request
    {
        $this->matchedRoute = $matchedRoute;

        return $this;
    }

    /**
     * @return Collection
     */
    public function matches(): Collection
    {
        if (empty($this->matchedRoute)) {
            return new Collection([]);
        }

        preg_match($this->matchedRoute, $this->path(), $matches);

        return new Collection((array) $matches);
    }
}
