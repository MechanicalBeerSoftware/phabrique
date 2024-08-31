<?php

declare(strict_types=1);

use Phabrique\Core\AutoRouterFactory;
use Phabrique\Core\HttpStatusCode;
use Phabrique\Core\ServerResponse;
use Phabrique\Core\Attribute\Route;
use Phabrique\Core\Attribute\PathParam;
use Phabrique\Core\Attribute\QueryParam;
use Phabrique\Core\Request\RequestMethod;
use Phabrique\Core\Request\ServerRequest;
use PHPUnit\Framework\TestCase;

class AutoRouterFactoryTest extends TestCase
{
    function testCreateRouteHandlersFromAnnotatedFunctions()
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

        #[Route("/foobar/:id")]
        function foo(#[PathParam()] int $id, #[QueryParam("my-age")] int $age)
        {
            $sum = $age + $id;
            return new ServerResponse(HttpStatusCode::OK, [], "$sum");
        }

        $rf = new AutoRouterFactory();
        $router = $rf->buildRouter();
        
        $resp = $router->direct($request);
        $this->assertEquals(HttpStatusCode::OK, $resp->getStatus());
        $this->assertEquals("137", $resp->getBody());
    }
}
