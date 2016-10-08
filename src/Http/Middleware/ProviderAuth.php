<?php

namespace PaymentApi\Http\Middleware;

use PaymentApi\Http\Request;
use PaymentApi\Http\Response;
use PaymentApi\Structure\Collection;

class ProviderAuth implements Middleware
{
    /** @var string */
    private $signingKey;

    /**
     * @param string $signingKey
     */
    public function __construct(string $signingKey)
    {
        $this->signingKey = $signingKey;
    }

    /**
     * @param Request  $request
     * @param callable $next
     *
     * @return Response
     */
    public function handle(Request $request, callable $next): Response
    {
        $authenticated = hash_hmac(
                'sha256',
                $request->body(),
                $this->signingKey
            ) === $request->header('X-Signature');

        if (!$authenticated) {
            return new Response(
                401,
                new Collection(['errors' => ['Not authenticated.']])
            );
        }

        return $next($request);
    }
}
