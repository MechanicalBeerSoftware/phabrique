<?php

declare(strict_types=1);

namespace Phabrique\Core;

use Phabrique\Core\Attribute\PathParam;
use Phabrique\Core\Attribute\QueryParam;
use Phabrique\Core\Request\Request;
use Phabrique\Core\Attribute\Route;
use ReflectionFunction;

class AutoRouterFactory implements RouterFactory
{
    public function buildRouter(): Router
    {
        $router = new Router();
        $userspaceFunctions = get_defined_functions()["user"];
        foreach ($userspaceFunctions as $fn) {
            $this->processFunction($router, $fn);
        }
        return $router;
    }

    private function processFunction(Router $router, callable $fn)
    {
        $fnRef = new ReflectionFunction($fn);
        $routeAttributes = $fnRef->getAttributes(Route::class);
        foreach ($routeAttributes as $routeAttribute) {
            $routeDescription = $routeAttribute->newInstance();
            $router->addRoute(
                $routeDescription->method,
                $routeDescription->path,
                $this->buildRouteHandler($fn, $fnRef)
            );
        }
    }

    private function buildRouteHandler(callable $fn, ReflectionFunction $fnRef)
    {   
        return new class($fn, $fnRef) implements RouteHandler {
            public function __construct(private readonly mixed $fn, private readonly ReflectionFunction $fnRef) {}

            public function handle(Request $request): Response
            {
                $callParams = [];

                // Auto-binding logic here
                $paramRefs = $this->fnRef->getParameters();
                foreach($paramRefs as $paramRef)
                {
                    if ($paramRef->getType() == Request::class) {
                        $callParams[$paramRef->getName()] = $request;
                        continue;
                    }

                    $pathAttributes = $paramRef->getAttributes(PathParam::class);
                    if (count($pathAttributes) > 0) {
                        $pathParam = $pathAttributes[0]->newInstance();
                        $name = $paramRef->getName();
                        if (! is_null($pathParam->name)) {
                            $name = $pathParam->name;
                        }
                        $callParams[$paramRef->getName()] = $request->getPathParameters()[$name];
                    }

                    $queryAttributes = $paramRef->getAttributes(QueryParam::class);
                    if (count($queryAttributes) > 0) {
                        $queryParam = $queryAttributes[0]->newInstance();
                        $name = $paramRef->getName();
                        if (! is_null($queryParam->name)) {
                            $name = $queryParam->name;
                        }
                        $callParams[$paramRef->getName()] = $request->getQueryParameters()[$name];
                    }

                }

                return call_user_func_array($this->fn, $callParams);
            }
        };
    }
}
