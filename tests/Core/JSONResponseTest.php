<?php

declare(strict_types=1);

use Phabrique\Core\HttpStatusCode;
use Phabrique\Core\JSONResponse;
use PHPUnit\Framework\TestCase;

final class JSONResponseTest extends TestCase
{
    public function testResponseHasSaneDefaults()
    {
        $response = new JSONResponse([]);
        $this->assertEquals(HttpStatusCode::OK, $response->getStatus());
        $this->assertEquals(["Content-Type" => "application/json"], $response->getHeaders());
    }

    public function testResponseCanAppendHeaders() {
        $response = new JSONResponse([], headers: ["X-Foobar" => "baz"]);
        $this->assertEquals(
            ["Content-Type" => "application/json", "X-Foobar" => "baz"],
            $response->getHeaders()
        );
    }

    public function testResponseCanOverwriteHeaders() {
        $response = new JSONResponse([], headers: ["Content-Type" => "text/plain"]);
        $this->assertEquals(
            ["Content-Type" => "text/plain"],
            $response->getHeaders()
        );
    }

    public function testResponseCanOverwriteStatus() {
        $response = new JSONResponse(["message" => "you did wrong !"], statusCode: HttpStatusCode::ERR_BAD_REQUEST);
        $this->assertEquals(
            HttpStatusCode::ERR_BAD_REQUEST,
            $response->getStatus()
        ); 
    }
}
