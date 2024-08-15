<?php

declare(strict_types=1);

namespace Phabrique\Core;

class Application
{
    private Router $router;

    public function __construct(RouterFactory $routerFactory, private ErrorHandler $errorHandler)
    {
        $this->router = $routerFactory->buildRouter();
    }

    public function handleRequest(Request $request): void
    {
        try {
            $response = $this->router->direct($request);
        } catch (HttpError $err) {
            $response = $this->errorHandler->handle($request, $err);
        } catch (Exception | Error $err) {
            error_log($err->getMessage());
            $serverError = new HttpError(HttpStatusCode::SERVER_ERROR, "Internal Server Error", "Something went wrong while processing your request");
            $response = $this->errorHandler->handle($request, $serverError);
        }

        http_response_code($response->getStatus()->value);
        echo $response->getBody();
    }
}