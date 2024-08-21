<?php

namespace Phabrique\Core;
use Phabrique\Core\Request\Request;
use Exception;

class Router
{
    /**
     * @var Route[]
     */
    private array $routes = [];

    private function addRoute(string $method, string $path, RouteHandler $handler)
    {
        $matcher = RouteMatcher::compile($path);
        $route = new Route(
            $matcher,
            $handler,
            $method
        );

        // If a similar route already exists, throw
        foreach ($this->routes as $existing) {
            if ($existing->getMethod() == $method && $existing->getMatcher()->isEquivalentTo($matcher)) {
                throw new Exception("Route already exists");
            }
        }

        array_push($this->routes, $route);

        // Sort routes by priority descending
        usort($this->routes, function (Route $r1, Route $r2) {
            return -RouteMatcher::comparePriority($r1->getMatcher(), $r2->getMatcher());
        });
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
