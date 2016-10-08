<?php

/** @var \PaymentApi\Structure\App $app */
$app = require __DIR__.'/../bootstrap/configure.php';

/** @var \PaymentApi\Http\Router $router */
$router = $app->make(\PaymentApi\Http\Router::class);

$response = $router->dispatch($app->make(\PaymentApi\Http\Request::class));

$response->send();
