<?php

namespace Phabrique\Core;

use Phabrique\Core\Request\Request;
use Phabrique\Core\Request\RequestMethod;

class Router
{
    private RadixNode $routesTrie;

    public function __construct()
    {
        $this->routesTrie = new RadixNode();
    }

    public function addRoute(RequestMethod $method, string $path, RouteHandler $handler)
    {
        $this->routesTrie->insert($path, $method, $handler);
    }

    public function get(string $path, RouteHandler $handler)
    {
        $this->addRoute(RequestMethod::Get, $path, $handler);
    }

    public function post(string $path, RouteHandler $handler)
    {
        $this->addRoute(RequestMethod::Post, $path, $handler);
    }

    public function static(string $path, string $rootDir)
    {
        if (!str_ends_with($path, '/*path')) {
            throw new InvalidPathException("The path to a static resource should end with '/*path'. Given '$path'");
        }
        $this->get($path, new StaticRouteHandler($rootDir));
    }

    public function direct(Request $request): Response
    {
        $route = $this->routesTrie->search($request->getPath());
        if (! $route->hasMatched()) {
            throw new HttpError(HttpStatusCode::ERR_NOT_FOUND, "The page you were looking for could not be found");
        }

        $method = $request->getMethod();
        $handler = $route->getRouteHandler($method);
        if ($handler === null) {
            $methodStr = $method->name;
            throw new HttpError(HttpStatusCode::ERR_METHOD_NOT_ALLOWED, "The resource you are trying to access does not support method '$methodStr'");
        }

        $request->setPathParameters($route->getPathParams());
        return $handler->handle($request); 
    }
}
