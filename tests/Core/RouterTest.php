<?php

declare(strict_types=1);

use Phabrique\Core\HttpError;
use Phabrique\Core\HttpStatusCode;
use Phabrique\Core\Request\Request;
use Phabrique\Core\Request\RequestMethod;
use Phabrique\Core\Response;
use Phabrique\Core\RouteHandler;
use Phabrique\Core\Router;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PHPUnit\Metadata\Api\Requirements;

final class RouterTest extends TestCase
{
    public function setUp(): void
    {
        # Turn on error reporting
        error_reporting(E_ALL);
    }

    public function testHandlerCalledWithExistingPathAndMethod(): void
    {
        /** @var Request&MockObject */
        $requestMock = $this->createMock(Request::class);
        $requestMock->method("getPath")->willReturn("/");
        $requestMock->method("getMethod")->willReturn(RequestMethod::Get);

        $router = new Router();

        /** @var RouteHandler&MockObject */
        $routeHandler = $this->createMock(RouteHandler::class);
        $routeHandler->expects($this->once())->method("handle");

        $router->get("/", $routeHandler);
        $router->direct($requestMock);
    }

    public function testStaticEndpointBoundToGetMethod(): void
    {
        $this->expectNotToPerformAssertions();

        /** @var Request&MockObject */
        $requestMock = $this->createMock(Request::class);
        $requestMock->method("getPath")->willReturn("/" . basename(__FILE__));
        $requestMock->method("getMethod")->willReturn(RequestMethod::Get);
        $requestMock->method("getPathParameters")->willReturn(["path" => basename(__FILE__)]);

        $router = new Router();

        $router->static("/*path", __DIR__);
        $router->direct($requestMock);
    }

    public function testHandlerCalledWithPostMethod(): void
    {
        /** @var Request&MockObject */
        $requestMock = $this->createMock(Request::class);
        $requestMock->method("getPath")->willReturn("/");
        $requestMock->method("getMethod")->willReturn(RequestMethod::Post);

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
        $requestMock->method("getMethod")->willReturn(RequestMethod::Post);

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

    public function testAppendPathParamsToRequest()
    {
        /** @var Request&MockObject */
        $requestMock = $this->createMock(Request::class);
        $requestMock->method("getPath")->willReturn("/items/69");
        $requestMock->method("getMethod")->willReturn(RequestMethod::Get);
        $requestMock->expects($this->once())->method("setPathParameters");

        $routeHandlerMock = $this->createMock(RouteHandler::class);

        $router = new Router();
        $router->get("/items/:id", $routeHandlerMock);

        $router->direct($requestMock);
    }

    public function testThrowOnDuplicateRouteWithSameMethod()
    {
        $router = new Router();
        $router->get("/items", $this->createMock(RouteHandler::class));

        try {
            $router->get("/items", $this->createMock(RouteHandler::class));
            $this->fail("should have thrown");
        } catch (Exception $e) {
            $this->assertEquals("Route already exists", $e->getMessage());
        }
    }

    public function testThrowOnDuplicateTemplateRouteWithSameMethod()
    {
        $router = new Router();
        $router->get("/items/:id", $this->createMock(RouteHandler::class));

        try {
            $router->get("/items/:foobar", $this->createMock(RouteHandler::class));
            $this->fail("should have thrown");
        } catch (Exception $e) {
            $this->assertEquals("Route already exists", $e->getMessage());
        }
    }

    public function testPrioritizeHigherPriorityRoutes()
    {
        $response = $this->createMock(Response::class);

        /** @var RouteHandler&MockObject */
        $handler = $this->createMock(RouteHandler::class);
        $handler->method("handle")->willReturn($response);
        $handler->expects($this->once())->method("handle");

        /** @var RouteHandler&MockObject */
        $badHandler = $this->createMock(RouteHandler::class);
        $badHandler->method("handle")->willReturn($response);
        $badHandler->expects($this->never())->method("handle");

        $router = new Router();
        $router->get("/items/first", $handler);
        $router->get("/items/:id", $badHandler);

        /** @var Request&MockObject */
        $request = $this->createMock(Request::class);
        $request->method("getPath")->willReturn("/items/first");
        $request->method("getMethod")->willReturn(RequestMethod::Get);

        $router->direct($request);
    }

    public function testWildcardRouteReturnsResponseForValidPath()
    {

        $response = $this->createMock(Response::class);

        /** @var RouteHandler&MockObject */
        $staticHandler = $this->createMock(RouteHandler::class);
        $staticHandler->expects($this->once())->method("handle")->willReturn($response);

        /** @var Request&MockObject */
        $request = $this->createMock(Request::class);
        $request->method("getPath")->willReturn("/data/img/test.png");
        $request->method("getMethod")->willReturn(RequestMethod::Get);

        $router = new Router();
        $router->get("/data/*path", $staticHandler);
        $router->direct($request);
    }

    public function testWildcardRouteHasLowerPriorityThanPathParams()
    {
        $response = $this->createMock(Response::class);

        /** @var RouteHandler&MockObject */
        $handler = $this->createMock(RouteHandler::class);
        $handler->method("handle")->willReturn($response);
        $handler->expects($this->once())->method("handle");

        /** @var RouteHandler&MockObject */
        $badHandler = $this->createMock(RouteHandler::class);
        $badHandler->method("handle")->willReturn($response);
        $badHandler->expects($this->never())->method("handle");

        $router = new Router();
        $router->get("/items/:id", $handler);
        $router->get("/items/*path", $badHandler);

        /** @var Request&MockObject */
        $request = $this->createMock(Request::class);
        $request->method("getPath")->willReturn("/items/1");
        $request->method("getMethod")->willReturn(RequestMethod::Get);

        $router->direct($request);
    }

    public function testWildcardRouteHasLowerPriorityThanSimpleRoute()
    {
        $response = $this->createMock(Response::class);

        /** @var RouteHandler&MockObject */
        $handler = $this->createMock(RouteHandler::class);
        $handler->method("handle")->willReturn($response);
        $handler->expects($this->once())->method("handle");

        /** @var RouteHandler&MockObject */
        $badHandler = $this->createMock(RouteHandler::class);
        $badHandler->method("handle")->willReturn($response);
        $badHandler->expects($this->never())->method("handle");

        $router = new Router();
        $router->get("/items/first", $handler);
        $router->get("/items/*path", $badHandler);

        /** @var Request&MockObject */
        $request = $this->createMock(Request::class);
        $request->method("getPath")->willReturn("/items/first");
        $request->method("getMethod")->willReturn(RequestMethod::Get);

        $router->direct($request);
    }
}
