<?php

namespace PaymentApi\Test\Functional;

use PaymentApi\Structure\Collection;

class ProviderPaymentTest extends FunctionalTest
{
    use SendsPayments;

    /**
     * A payment provider should be able to make a signed request to post a new
     * payment.
     */
    public function testPostPayment()
    {
        // Given the signing key is "foobar";
        $signingKey = 'foobar';
        $this->signingKeyIs($signingKey, $this->app);

        // When a provider posts a payment;
        $body = json_encode($this->bodyContent());

        $response = $this->post(
            '/restaurant-chains/123/locations/456/payments',
            $this->headers(
                [
                    'X-Signature' => hash_hmac('sha256', $body, $signingKey),
                ]
            ),
            $body
        );

        // Then they should get a response describing the creation of the
        // payment.
        $this->assertEquals(201, $response->status());
        $this->assertJson($response->body());

        $content = new Collection(json_decode($response->body(), true));
        $payment = $content->get('payment');
        $this->assertNotEmpty($payment);
        $this->assertEquals(123, $payment->get('payment_provider_id'));
        $this->assertEquals(456, $payment->get('restaurant_location_id'));
        $this->assertEquals('£20.20', $payment->get('amount'));
    }

    /**
     * An invalid signature should be rejected with 401.
     */
    public function testBadSignature()
    {
        // When a payment post request is made with an invalid signature;
        $response = $this->post(
            '/restaurant-chains/123/locations/456/payments',
            $this->headers(
                [
                    'X-Signature' => 'bad signature',
                ]
            ),
            'foobar body'
        );

        // It should be rejected with a 401 response.
        $this->assertEquals(401, $response->status());
        $this->assertJson($response->body());

        $content = new Collection(json_decode($response->body(), true));
        $this->assertEquals(
            'Not authenticated.',
            $content->get('errors')->first()
        );
    }

    /**
     * An incorrect total should get a 400 response.
     */
    public function testBadTotal()
    {
        // When a post payment request is made with a mismatched total;
        $signingKey = 'foobar';
        $this->signingKeyIs($signingKey, $this->app);

        $content = $this->bodyContent();
        $content['total_amount'] += 1000;
        $body = json_encode($content);

        $response = $this->post(
            '/restaurant-chains/123/locations/456/payments',
            $this->headers(
                [
                    'X-Signature' => hash_hmac('sha256', $body, $signingKey),
                ]
            ),
            $body
        );

        // It should be rejected with a 400 response.
        $this->assertEquals(400, $response->status());
        $this->assertContains('does not match', $response->body());
    }
}
