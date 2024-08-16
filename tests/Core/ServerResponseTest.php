<?php

declare(strict_types=1);

use Phabrique\Core\HttpStatusCode;
use Phabrique\Core\ServerResponse;
use PHPUnit\Framework\TestCase;

final class ServerResponseTest extends TestCase
{

    public function testResponseHasProperStatusCode()
    {
        $response = new ServerResponse(HttpStatusCode::OK, [], "");

        $this->assertEquals(HttpStatusCode::OK, $response->getStatus());
    }

    public function testResponseHasEmptyHeaders()
    {
        $response = new ServerResponse(HttpStatusCode::OK, [], "");

        $this->assertEmpty($response->getHeaders());
    }

    public function testResponseHasHeaders()
    {
        $headers = ["Content-Type" => "application/json"];
        $response = new ServerResponse(HttpStatusCode::OK, $headers, "");

        $this->assertNotEmpty($response->getHeaders());
        $this->assertEquals($headers, $response->getHeaders());
    }

    public function testResponseHasBody() {
        $body = "<h1>Test Case</h1>";
        $response = new ServerResponse(HttpStatusCode::OK, [], $body);

        $this->assertEquals($body, $response->getBody());
    }
}
