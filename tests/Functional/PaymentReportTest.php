<?php

namespace PaymentApi\Test\Functional;

use PaymentApi\Structure\Collection;

class PaymentReportTest extends FunctionalTest
{
    use SendsPayments;

    /**
     * Should be able to get payments for a restaurant chain.
     */
    public function testChainPaymentsReport()
    {
        // Given there are 3 payments for a chain;
        $body = json_encode($this->bodyContent());
        $signingKey = 'foobar';
        $this->signingKeyIs($signingKey, $this->app);
        for ($i = 0; $i < 3; $i++) {
            $response = $this->post(
                '/restaurant-chains/123/locations/456/payments',
                $this->headers(
                    [
                        'X-Signature' => hash_hmac(
                            'sha256',
                            $body,
                            $signingKey
                        ),
                    ]
                ),
                $body
            );
            $this->assertEquals(201, $response->status());
        }

        // When a staff user requests payments for that chain;
        $response = $this->get(
            '/restaurant-chains/123/payments',
            $this->headers(
                [
                    'Authorization' => 'Basic '.base64_encode('789:secret'),
                ]
            )
        );

        // Then the payments should be present;
        $this->assertEquals(200, $response->status());
        $this->assertJson($response->body());

        $content = new Collection(json_decode($response->body(), true));
        $this->assertGreaterThanOrEqual(3, $content->count());
    }

    /**
     * Should be able to get payments for a restaurant location.
     */
    public function testLocationPaymentsReport()
    {
        // Given there are 3 payments for a location;
        $body = json_encode($this->bodyContent());
        $signingKey = 'foobar';
        $this->signingKeyIs($signingKey, $this->app);
        for ($i = 0; $i < 3; $i++) {
            $this->post(
                '/restaurant-chains/123/locations/456/payments',
                $this->headers(
                    [
                        'X-Signature' => hash_hmac(
                            'sha256',
                            $body,
                            $signingKey
                        ),
                    ]
                ),
                $body
            );
        }

        // When a staff user requests payments for that chain;
        $response = $this->get(
            '/restaurant-chains/123/locations/456/payments',
            $this->headers(
                [
                    'Authorization' => 'Basic '.base64_encode('789:secret'),
                ]
            )
        );

        // Then the payments should be present;
        $this->assertEquals(200, $response->status());
        $this->assertJson($response->body());

        $content = new Collection(json_decode($response->body(), true));
        $this->assertGreaterThanOrEqual(3, $content->count());
    }

    /**
     * Bad authorization should get a 401 response.
     */
    public function testBadAuthorization()
    {
        // If a request is made for a payment report with bad authorization;
        $response = $this->get(
            '/restaurant-chains/123/locations/456/payments',
            $this->headers(
                [
                    'Authorization' => 'Basic '.base64_encode('789:bad auth'),
                ]
            )
        );

        // Then it should be rejected with a 401 response.
        $this->assertEquals(401, $response->status());
    }
}
