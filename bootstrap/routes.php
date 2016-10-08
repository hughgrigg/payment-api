<?php

/** @var \PaymentApi\Http\Router $route */

use PaymentApi\Http\Controller\PaymentController;
use PaymentApi\Http\Middleware\AccountAuth;
use PaymentApi\Http\Middleware\ProviderAuth;
use PaymentApi\Http\Request;
use PaymentApi\Http\Response;
use PaymentApi\Structure\Collection;

// Index action
$route->get(
    '^/$',
    function (): Response {
        return new Response(200, new Collection(['Payment API Index']));
    }
);

// Auth middleware on POSTing to any payment endpoint.
$route->post(
    'payments',
    function (Request $request, callable $next) use ($app): Response {
        /** @var ProviderAuth $auth */
        $auth = $app->make(ProviderAuth::class);

        return $auth->handle($request, $next);
    }
);
// Controller action for POSTing new payments.
$route->post(
    '^/restaurant-chains/([0-9]+)/locations/([0-9]+)/payments$',
    function (Request $request) use ($app): Response {
        /** @var PaymentController $controller */
        $controller = $app->make(PaymentController::class);

        return $controller->postPayment($request);
    }
);

// Auth middleware on GET payments report actions.
$route->get(
    'payments',
    function (Request $request, callable $next) use ($app): Response {
        /** @var AccountAuth $auth */
        $auth = $app->make(AccountAuth::class);

        return $auth->handle($request, $next);
    }
);
// Controller action for GET single location payments report.
$route->get(
    '^/restaurant-chains/([0-9]+)/locations/([0-9]+)/payments',
    function (Request $request) use ($app): Response {
        /** @var PaymentController $controller */
        $controller = $app->make(PaymentController::class);

        return $controller->reportLocationPayments($request);
    }
);
// Controller action for GET all locations payments report.
$route->get(
    '^/restaurant-chains/([0-9]+)/payments$',
    function (Request $request) use ($app): Response {
        /** @var PaymentController $controller */
        $controller = $app->make(PaymentController::class);

        return $controller->reportChainPayments($request);
    }
);
