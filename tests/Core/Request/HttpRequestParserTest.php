<?php

declare(strict_types=1);

use Phabrique\Core\Request\HttpRequestParser;
use Phabrique\Core\Request\RequestMethod;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\After;

class HttpRequestParserTest extends TestCase
{

    #[After]
    public function resetGlobals()
    {
        $_GET = [];
        $_SERVER = [];
        $_POST = [];
    }

    public function testParsedRequestObjectContainsProperUriFromHttpRequest(): void
    {
        $_SERVER["REQUEST_URI"] = "/endpoint.php";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $requestParser = new HttpRequestParser();
        $request = $requestParser->parseIncomingRequest();

        $this->assertEquals("/endpoint.php", $request->getPath());
    }

    public function testParsedRequestContainsProperLowercaseHttpMethod()
    {
        $_SERVER["REQUEST_URI"] = "/endpoint.php";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $requestParser = new HttpRequestParser();
        $request = $requestParser->parseIncomingRequest();

        $this->assertEquals(RequestMethod::Get, $request->getMethod());
    }

    public function testParsedRequestContainsQueryParam()
    {
        $_SERVER["REQUEST_URI"] = "/endpoint.php";
        $_SERVER["REQUEST_METHOD"] = "GET";

        $_GET["param1"] = "value1";
        $_GET["param2"] = "value2";

        $requestParser = new HttpRequestParser();
        $request = $requestParser->parseIncomingRequest();

        $this->assertTrue(array_key_exists("param1", $request->getQueryParameters()));
        $this->assertEquals("value1", $request->getQueryParameters()["param1"]);
    }

    public function testParsedRequestContainsFormParam()
    {
        $_SERVER["REQUEST_URI"] = "/endpoint.php";
        $_SERVER["REQUEST_METHOD"] = "POST";
        $_POST["param1"] = "value1";

        $requestParser = new HttpRequestParser();
        $request = $requestParser->parseIncomingRequest();

        $this->assertTrue(array_key_exists("param1", $request->getBody()));
        $this->assertEquals("value1", $request->getBody()["param1"]);

        $this->assertFalse(array_key_exists("param1", $request->getQueryParameters()));
    }
}
