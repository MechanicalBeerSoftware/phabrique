<?php

declare(strict_types=1);

namespace Phabrique\Core;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{

    public function testHandlerCalledWithExistingPathAndMethod(): void
    {
        /** @var Request&MockObject */
        $requestMock = $this->createMock(Request::class);
        $requestMock->method("getPath")->willReturn("/");
        $requestMock->method("getMethod")->willReturn("get");

        $router = new Router();

        /** @var RouteHandler&MockObject */
        $routeHandler = $this->createMock(RouteHandler::class);
        $routeHandler->expects($this->once())->method("handle");

        $router->get("/", $routeHandler);
        $router->direct($requestMock);
    }

    public function testHandlerCalledWithPostMethod(): void {
        /** @var Request&MockObject */
        $requestMock = $this->createMock(Request::class);
        $requestMock->method("getPath")->willReturn("/");
        $requestMock->method("getMethod")->willReturn("post");

        $router = new Router();

        /** @var RouteHandler&MockObject */
        $routeHandler = $this->createMock(RouteHandler::class);
        $routeHandler->expects($this->once())->method("handle");

        $router->post("/", $routeHandler);
        $router->direct($requestMock);

    }

    public function testErrorHandlerCalledWhenRequestPathNotFound()
    {
        /** @var Request&MockObject */
        $requestMock = $this->createMock(Request::class);
        $requestMock->method("getPath")->willReturn("/");

        $router = new Router();

        try {
            $router->direct($requestMock);
            $this->fail();
        } catch (HttpError $e) {
            $this->assertInstanceOf(HttpError::class, $e);
            $this->assertEquals(HttpStatusCode::ERR_NOT_FOUND, $e->getStatusCode());
        }
    }

    public function testErrorHandlerCalledWhenRoutingExistingPathWithNonExistingMethod()
    {
        /** @var Request&MockObject */
        $requestMock = $this->createMock(Request::class);
        $requestMock->method("getPath")->willReturn("/");
        $requestMock->method("getMethod")->willReturn("post");

        /** @var RouteHandler&MockObject */
        $routeHandlerMock = $this->createMock(RouteHandler::class);

        $router = new Router();
        $router->get("/", $routeHandlerMock);

        try {
            $router->direct($requestMock);
            $this->fail();
        } catch (HttpError $e) {
            $this->assertInstanceOf(HttpError::class, $e);
            $this->assertEquals(HttpStatusCode::ERR_METHOD_NOT_ALLOWED, $e->getStatusCode());
        }
    } 
}
