<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Phabrique\Core\Application;
use Phabrique\Core\ErrorHandler;
use Phabrique\Core\HttpError;
use Phabrique\Core\Request\Request;
use Phabrique\Core\Request\RequestMethod;
use Phabrique\Core\Request\ServerRequest;
use Phabrique\Core\Response;
use Phabrique\Core\RouteHandler;
use Phabrique\Core\Router;
use Phabrique\Core\RouterFactory;
use Phabrique\Core\ServerResponse;

class DefaultErrorHandler implements ErrorHandler {
    public function handle(Request $request, HttpError $exception): Response {
        return new ServerResponse($exception->getStatusCode(), [], "");
    }
}

class ApplicationTest extends TestCase {

    function testMiddlewareCalledWithRequest(): void {
        $mockMiddlewareHandler = $this->createMock(RouteHandler::class);
        $mockMiddlewareHandler
            ->expects($this->once())
            ->method('handle');

        $app = new Application(new class implements RouterFactory {
            function buildRouter(): Router {
                return new Router();
            }
        }, new DefaultErrorHandler());

        $request = new ServerRequest(
            query_params: [],
            path: "/test",
            method: RequestMethod::Get,
            body: "",
            headers: []
        );

        $app->withMiddleware(middleware: function (Request $request, callable $next) use ($mockMiddlewareHandler): Response {
            $mockMiddlewareHandler->handle($request);
            return $next($request);
        })->handleRequest($request);
    }

}
