<?php

namespace Phabrique\Core;

class Router
{
    /**
     * @var Route[]
     */
    private array $routes = [];

    private function addRoute(string $method, string $path, RouteHandler $handler)
    {
        $route = new Route(
            RouteMatcher::compile($path),
            $handler,
            $method
        );
        array_push($this->routes, $route);
        // TODO: enforce no similar registered
        // TODO: sort
    }

    public function get(string $path, RouteHandler $handler)
    {
        $this->addRoute("get", $path, $handler);
    }

    public function post(string $path, RouteHandler $handler)
    {
        $this->addRoute("post", $path, $handler);
    }

    public function direct(Request $request): Response
    {
        $pathFound = false;
        foreach ($this->routes as $route) {
            $m = $route->getMatcher();
            if (! $m->matches($request->getPath())) {
                continue;
            }
            $pathFound = true;

            if ($route->getMethod() != $request->getMethod()) {
                continue;
            }

            $request->setPathParameters($m->extract());
            return $route->getHandler()->handle($request);
        }

        if ($pathFound) {
            $method = $request->getMethod();
            throw new HttpError(HttpStatusCode::ERR_METHOD_NOT_ALLOWED, "Method not allowed", "The resource you are trying to access does not support method '$method'");
        } else {
            throw new HttpError(HttpStatusCode::ERR_NOT_FOUND, "Not Found", "The page you were looking for could not be found");
        }
    }
}
