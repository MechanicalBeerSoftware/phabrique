<?php

declare(strict_types=1);

use Phabrique\Core\Attribute\Controller;
use Phabrique\Core\AutoRouterFactory;
use Phabrique\Core\HttpStatusCode;
use Phabrique\Core\ServerResponse;
use Phabrique\Core\Attribute\Route;
use Phabrique\Core\Attribute\PathParam;
use Phabrique\Core\Attribute\QueryParam;
use Phabrique\Core\HttpError;
use Phabrique\Core\Request\RequestMethod;
use Phabrique\Core\Request\ServerRequest;
use PHPUnit\Framework\TestCase;

#[Controller()]
class AutoRouterFactoryTestExampleClass
{
    #[Route("/foobar/:id")]
    public function foo(#[PathParam()] int $id, #[QueryParam("my-age")] int $age)
    {
        $sum = $age + $id;
        return new ServerResponse(HttpStatusCode::OK, [], "$sum");
    }
}

#[Controller("/prefix")]
class AutoRouterFactoryTestExamplePrefixedController
{
    #[Route("/foobar/:id")]
    public function foo(#[PathParam()] int $id, #[QueryParam("my-age")] int $age)
    {
        $sum = $age + $id;
        return new ServerResponse(HttpStatusCode::OK, [], "$sum");
    }
}

class AutoRouterFactoryTestExampleNonControllerClass
{
    #[Route("/barfoo/:id")]
    public function bar(#[PathParam()] int $id, #[QueryParam("my-age")] int $age)
    {
        $sum = $age + $id;
        return new ServerResponse(HttpStatusCode::OK, [], "$sum");
    }
}

class AutoRouterFactoryTest extends TestCase
{
    public function testCreateRouteHandlersFromAnnotatedMethodOfController()
    {
        // Note: this kind of behaviour is extremely inconvenient to test
        // because php lacks the ability to declare local functions. So
        // if I decide to create my own route function, it will be available
        // within all other tests.

        $request = new ServerRequest(
            ["my-age" => "14"],
            "/foobar/123",
            RequestMethod::Get,
            "",
            []
        );


        $rf = new AutoRouterFactory();
        $router = $rf->buildRouter();

        $resp = $router->direct($request);
        $this->assertEquals(HttpStatusCode::OK, $resp->getStatus());
        $this->assertEquals("137", $resp->getBody());
    }

    public function testCreateRouteHandlersWithPrefixFromController()
    {
        $request = new ServerRequest(
            ["my-age" => "14"],
            "/prefix/foobar/123",
            RequestMethod::Get,
            "",
            []
        );

        $rf = new AutoRouterFactory();
        $router = $rf->buildRouter();

        $resp = $router->direct($request);
        $this->assertEquals(HttpStatusCode::OK, $resp->getStatus());
        $this->assertEquals("137", $resp->getBody());
    }

    public function testCreateRouteHandlersOnlyFindsControllerClassesMethods()
    {
        $request = new ServerRequest(
            ["my-age" => "14"],
            "/barfoo/123",
            RequestMethod::Get,
            "",
            []
        );


        $rf = new AutoRouterFactory();
        $router = $rf->buildRouter();

        try {

            $router->direct($request);
            $this->fail("Endpoint should not have been reached");
        } catch (HttpError $err) {
            $this->assertEquals(HttpStatusCode::ERR_NOT_FOUND, $err->getStatusCode());
        }
    }
}
