<?php

namespace PaymentApi\Test\Functional;

use PaymentApi\Http\Middleware\ProviderAuth;
use PaymentApi\Structure\App;

trait SendsPayments
{
    /**
     * @return array
     */
    private function bodyContent(): array
    {
        return [
            'provider'     => 123,
            'total_amount' => 2020,
            'items'        => [
                [
                    'description' => 'A starter',
                    'amount'      => 350,
                ],
                [
                    'description' => 'A main course',
                    'amount'      => 1200,
                ],
                [
                    'description' => 'A drink',
                    'amount'      => 550,
                ],
                [
                    'description' => 'Special discount',
                    'amount'      => -100,
                ],
                [
                    'description' => 'Gratuity',
                    'amount'      => 20,
                ],
            ],
            'table_no'     => 5,
            'served_by'    => [
                'name' => 'Joe Bloggs',
            ],
            'user'         => 456,
            'device'       => [
                'operating_system' => 'Android 7.1',
                'model'            => 'Some Phone 5',
            ],
            'method'       => [
                'organisation'  => 'visa',
                'last_4_digits' => '4242',
                'fraud_risk'    => 'low',
            ],
        ];
    }

    /**
     * @param string $signingKey
     * @param App    $app
     */
    private function signingKeyIs(string $signingKey, App $app)
    {
        $app->bind(
            ProviderAuth::class,
            function () use ($signingKey) {
                return new ProviderAuth($signingKey);
            }
        );
    }
}
