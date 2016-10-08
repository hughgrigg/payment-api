<?php

namespace PaymentApi\Http\Middleware;

use PaymentApi\Http\Request;
use PaymentApi\Http\Response;

interface Middleware
{
    /**
     * @param Request  $request
     * @param callable $next
     *
     * @return Response
     */
    public function handle(Request $request, callable $next): Response;
}
