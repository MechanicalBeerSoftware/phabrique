<?php

declare(strict_types=1);

use Phabrique\Core\Request\RequestMethod;
use Phabrique\Core\Request\ServerRequest;
use PHPUnit\Framework\TestCase;

final class ServerRequestTest extends TestCase
{
    public function testRequestPathParamsAreEmptyOnCreation()
    {
        $request = new ServerRequest(
            [],
            "/foo",
            RequestMethod::Get,
            [],
            [],
        );

        $this->assertEmpty($request->getPathParameters());
    }

    public function testRequestPathParamsCanBeSet()
    {
        $request = new ServerRequest(
            [],
            "/foo",
            RequestMethod::Get,
            [],
            [],
        );

        $request->setPathParameters(["id" => 69]);
        $this->assertEquals(count($request->getPathParameters()), 1);
        $this->assertEquals(69, $request->getPathParameters()["id"]);
    }
}
