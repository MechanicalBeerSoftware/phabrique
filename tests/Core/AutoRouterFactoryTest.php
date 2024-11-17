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

    #[Route("/foobar/baz")]
    public function foobarbaz(#[QueryParam()] ?int $age)
    {
        if (!is_null($age)) {
            return new ServerResponse(HttpStatusCode::OK, [], "Your age is: $age");
        }
        return new ServerResponse(HttpStatusCode::OK, [], "Your age is undefined");
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

    public function testDirectRequestWithMissingQueryParameter()
    {
        // Note: this kind of behaviour is extremely inconvenient to test
        // because php lacks the ability to declare local functions. So
        // if I decide to create my own route function, it will be available
        // within all other tests.

        $request = new ServerRequest(
            [],
            "/foobar/123",
            RequestMethod::Get,
            "",
            []
        );


        $rf = new AutoRouterFactory();
        $router = $rf->buildRouter();

        try {
            $resp = $router->direct($request);
            $this->fail("You shouldn't be here");
        } catch (HttpError $err) {
            $this->assertEquals(HttpStatusCode::ERR_BAD_REQUEST, $err->getStatusCode());
            $this->assertEquals("Missing query parameter my-age", $err->getMessage());
        }
    }

    public function testCreateRouteHandlersForMethodsWithUnspecifiedOptionalQueryParameters()
    {
        $request = new ServerRequest(
            [],
            "/foobar/baz",
            RequestMethod::Get,
            "",
            []
        );

        $rf = new AutoRouterFactory();
        $router = $rf->buildRouter();

        $resp = $router->direct($request);
        $this->assertEquals(HttpStatusCode::OK, $resp->getStatus());
        $this->assertEquals("Your age is undefined", $resp->getBody());
    }

    public function testCreateRouteHandlersForMethodsWithOptionalQueryParameter()
    {
        $request = new ServerRequest(
            ["age" => 25],
            "/foobar/baz",
            RequestMethod::Get,
            "",
            []
        );

        $rf = new AutoRouterFactory();
        $router = $rf->buildRouter();

        $resp = $router->direct($request);
        $this->assertEquals(HttpStatusCode::OK, $resp->getStatus());
        $this->assertEquals("Your age is: 25", $resp->getBody());
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
