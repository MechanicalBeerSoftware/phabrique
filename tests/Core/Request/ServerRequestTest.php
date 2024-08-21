<?php

declare(strict_types=1);

use Phabrique\Core\Request\ServerRequest;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\TestCase;

final class ServerRequestTest extends TestCase
{
    #[After]
    public function resetGlobals()
    {
        $_GET = [];
        $_SERVER = [];
        $_POST = [];
    }

    public function testRequestObjectContainsProperUriFromHttpRequest(): void
    {
        $_SERVER["REQUEST_URI"] = "/endpoint.php";
        $_SERVER["REQUEST_METHOD"] = "GET";
        $request = ServerRequest::parse();

        $this->assertEquals("/endpoint.php", $request->getPath());
    }

    public function testRequestContainsProperLowercaseHttpMethod()
    {
        $_SERVER["REQUEST_URI"] = "/endpoint.php";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $request = ServerRequest::parse();

        $this->assertEquals("get", $request->getMethod());
    }

    public function testRequestContainsQueryParam()
    {
        $_SERVER["REQUEST_URI"] = "/endpoint.php";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $_GET["param1"] = "value1";
        $_GET["param2"] = "value2";
        $request = ServerRequest::parse();

        $this->assertTrue(array_key_exists("param1", $request->getQueryParameters()));
        $this->assertEquals("value1", $request->getQueryParameters()["param1"]);
    }

    public function testRequestContainsFormParam()
    {
        $_SERVER["REQUEST_URI"] = "/endpoint.php";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $_POST["param1"] = "value1";
        $request = ServerRequest::parse();

        $this->assertTrue(array_key_exists("param1", $request->getFormData()));
        $this->assertEquals("value1", $request->getFormData()["param1"]);
        $this->assertFalse(array_key_exists("param2", $request->getQueryParameters()));
    }

    public function testRequestPathParamsAreEmptyOnCreation()
    {
        $_SERVER["REQUEST_URI"] = "/endpoint.php";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $_GET = ["param1" => "value1"];
        $request = ServerRequest::parse();

        $this->assertEmpty($request->getPathParameters());
    }

    public function testRequestPathParamsCanBeSet()
    {
        $_SERVER["REQUEST_URI"] = "/endpoint.php";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $_GET = ["param1" => "value1"];
        $request = ServerRequest::parse();

        $request->setPathParameters(["id" => 69]);
        $this->assertEquals(count($request->getPathParameters()), 1);
        $this->assertEquals(69, $request->getPathParameters()["id"]);
    }
}
