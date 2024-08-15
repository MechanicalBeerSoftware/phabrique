<?php

namespace Phabrique\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, RouteHandler $handler)
    {
        $this->routes[$path]["get"] = $handler;
    }

    public function post(string $path, RouteHandler $handler)
    {
        $this->routes[$path]["post"] = $handler;
    }

    public function direct(Request $request): Response
    {
        $path = $request->getPath();
        if (!array_key_exists($path, $this->routes)) {
            throw new HttpError(HttpStatusCode::ERR_NOT_FOUND, "Not Found", "The page you were looking for could not be found");
        }

        $route = $this->routes[$path];
        if (!array_key_exists($request->getMethod(), $route)) {
            throw new HttpError(HttpStatusCode::ERR_METHOD_NOT_ALLOWED, "Method not allowed", "The given method could not be applied to the object you were looking for");
        }

        return $route[$request->getMethod()]->handle($request);
    }
}
