<?php

namespace PaymentApi\Http\Middleware;

use PaymentApi\Domain\Entity\Restaurant\RestaurantAccount;
use PaymentApi\Http\Request;
use PaymentApi\Http\Response;
use PaymentApi\Structure\Collection;

class AccountAuth implements Middleware
{
    /** @var RestaurantAccount */
    private $account;

    /**
     * @param RestaurantAccount $account
     */
    public function __construct(RestaurantAccount $account)
    {
        $this->account = $account;
    }

    /**
     * @param Request  $request
     * @param callable $next
     *
     * @return Response
     * @throws \OutOfRangeException
     * @throws \InvalidArgumentException
     */
    public function handle(Request $request, callable $next): Response
    {
        $authorisation = $request->authorization();
        /** @var RestaurantAccount $account */
        $account = $this->account->load((int) $authorisation->username());

        // Must be the account being requested.

        // Must be correct password.
        if (!password_verify(
            $authorisation->password(),
            $account->passwordHash()
        )
        ) {
            return new Response(
                401,
                new Collection(['errors' => ['Not authenticated.']])
            );
        }

        return $next($request);
    }
}
