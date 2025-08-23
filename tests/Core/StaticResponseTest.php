<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use Phabrique\Core\HttpError;
use Phabrique\Core\HttpStatusCode;
use Phabrique\Core\StaticResponse;
use PHPUnit\Framework\TestCase;

final class StaticResponseTest extends TestCase
{
    public function testResponseHasProperBody()
    {
        file_put_contents("foo.txt", "bar");

        $response = new StaticResponse("./foo.txt");

        $this->assertEquals("text/plain", $response->getHeaders()["Content-Type"]);
        $this->assertEquals("bar", $response->getBody());

        unlink("foo.txt");
    }

    static function textfileProvider() {
        return [
            "CSS file" => ["foo.css", "text/css"],
            "XML file" => ["foo.xml", "application/xml"],
            "HTML file" => ["foo.html", "text/html"],
            "JS file" => ["foo.js", "text/javascript"],
            "JSON file" => ["foo.json", "application/json"]
        ];
    }

    #[DataProvider('textfileProvider')]
    public function testResponseHasProperHeadersForTextFiles(string $filename, string $contentType)
    {
        file_put_contents($filename, "bar");

        $headers = [
            "Content-Type" => $contentType
        ];

        $response = new StaticResponse($filename);

        $this->assertEquals($headers, $response->getHeaders());

        unlink($filename);
    }

    public function testResponseHasProperOverridenHeaders()
    {
        file_put_contents("foo.png", "bar");

        $headers = [
            "Content-Type" => "image/png"
        ];

        $response = new StaticResponse("./foo.png", headers: $headers);

        $this->assertEquals($headers, $response->getHeaders());

        unlink("foo.png");
    }


    public function testResponseHasAllHeaders()
    {
        $headers = [
            "Transfer-Encoding" => "chunked"
        ];

        $mergedHeaders = [
            "Content-Type" => "text/plain",
            "Transfer-Encoding" => "chunked"
        ];

        file_put_contents("foo.txt", "bar");

        $response = new StaticResponse("./foo.txt", headers: $headers);

        $this->assertEquals($mergedHeaders, $response->getHeaders());

        unlink("foo.txt");
    }

    public function testResponseThrowsExceptionWithInvalidFile()
    {
        try {
            $response = new StaticResponse("./foo.txt");
        } catch (HttpError $err) {
            $this->assertEquals(HttpStatusCode::ERR_NOT_FOUND, $err->getStatusCode());
            $this->assertEquals("No such file", $err->getMessage());
        }
    }

    public function testThrowsExceptionWhenPathIsDirectory()
    {
        if (!file_exists("tmp/")) {
            mkdir("tmp/");
        }

        try {
            $response = new StaticResponse("tmp/");
        } catch (HttpError $err) {
            $this->assertEquals(HttpStatusCode::ERR_BAD_REQUEST, $err->getStatusCode());
            $this->assertEquals("Requested file is a directory", $err->getMessage());
        }

        rmdir("tmp/");
    }
}
