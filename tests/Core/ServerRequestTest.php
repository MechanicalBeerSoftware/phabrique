<?php

declare(strict_types=1);

use Phabrique\Core\ServerRequest;
use PHPUnit\Framework\TestCase;

final class ServerRequestTest extends TestCase
{
    public function testRequestObjectContainsProperUriFromHttpRequest(): void
    {
        $_SERVER["REQUEST_URI"] = "/endpoint.php";
        $_SERVER["REQUEST_METHOD"] = "GET";
        $request = ServerRequest::parse();

        $this->assertEquals("/endpoint.php", $request->getPath());
    }

    public function testRequestContainsProperLowercaseHttpMethod() {
        $_SERVER["REQUEST_URI"] = "/endpoint.php";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $request = ServerRequest::parse();

        $this->assertEquals("get", $request->getMethod());
    }

    public function testRequestContainsQueryParam()
    {
        $_SERVER["REQUEST_URI"] = "/endpoint.php";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $_GET = ["param1" => "value1", "param2" => "value2"];
        $request = ServerRequest::parse();

        $this->assertTrue(array_key_exists("param1", $request->getQueryParameters()));
        $this->assertEquals("value1", $request->getQueryParameters()["param1"]);
    }

    public function testRequestDoesntContainUnknownQueryParam() {
        $_SERVER["REQUEST_URI"] = "/endpoint.php";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $_GET = ["param1" => "value1"];
        $request = ServerRequest::parse();

        $this->assertFalse(array_key_exists("param2", $request->getQueryParameters()));

    }
}
