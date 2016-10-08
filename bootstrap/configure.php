<?php

require_once __DIR__.'/../vendor/autoload.php';

use PaymentApi\Structure\App;
use PaymentApi\Structure\Config;

$config = new Config();

if ($config->debugMode()) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

$app = new App();

$app->bind(
    \PaymentApi\Http\Request::class,
    function () {
        return \PaymentApi\Http\Request::fromGlobals();
    }
);

$app->bind(
    PDO::class,
    function () use ($config) {
        return new PDO(
            $config->pdoDsn(),
            $config->dbUsername(),
            $config->dbPassword(),
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }
);
$app->bind(
    \PaymentApi\Persist\Persistence::class,
    function (App $app) {
        return new \PaymentApi\Persist\LazyPersistence(
            function () use ($app) {
                return new \PaymentApi\Persist\MySqlPersistence(
                    $app->make(PDO::class)
                );
            }
        );
    }
);
\PaymentApi\Domain\Entity\Entity::setPersistence(
    $app->make(\PaymentApi\Persist\Persistence::class)
);

$app->bind(
    \PaymentApi\Http\Router::class,
    function (App $app) {
        $route = new \PaymentApi\Http\Router($app->config());
        include Config::pathTo('bootstrap/routes.php');

        return $route;
    }
);

$app->bind(
    \PaymentApi\Http\Middleware\ProviderAuth::class,
    function (App $app) {
        return new \PaymentApi\Http\Middleware\ProviderAuth(
            $app->config()->signingKey()
        );
    }
);
$app->bind(
    \PaymentApi\Http\Middleware\AccountAuth::class,
    function (App $app) {
        return new \PaymentApi\Http\Middleware\AccountAuth(
            $app->make(
                \PaymentApi\Domain\Entity\Restaurant\RestaurantAccount::class
            )
        );
    }
);

$app->bind(
    \PaymentApi\Http\Controller\PaymentController::class,
    function (App $app) {
        return new \PaymentApi\Http\Controller\PaymentController(
            $app->make(\PaymentApi\Domain\Entity\Payment\Payment::class),
            $app->make(
                \PaymentApi\Domain\Entity\Restaurant\RestaurantLocation::class
            )
        );
    }
);

return $app;
