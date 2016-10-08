<?php

namespace PaymentApi\Test\Unit\Http;

use PaymentApi\Http\Headers;
use PHPUnit\Framework\TestCase;

/**
 * Test HTTP header extraction logic.
 */
class HeadersTest extends TestCase
{
    /**
     * Test getting headers from server.
     */
    public function testFromServer()
    {
        if (function_exists('getallheaders')) {
            return;
        }

        Headers::setServer(
            [
                'HTTP_ACCEPT_LANGUAGE' => 'en-GB',
                'HTTP_HOST'            => 'localhost',
                'HTTP_ACCEPT'          => 'text/html',
            ]
        );

        $headers = Headers::fromGlobal();

        $this->assertEquals(
            [
                'accept-language' => 'en-GB',
                'host'            => 'localhost',
                'accept'          => 'text/html',
            ],
            $headers->all()
        );
    }
}
