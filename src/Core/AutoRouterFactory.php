<?php

declare(strict_types=1);

namespace Phabrique\Core;

use Phabrique\Core\Attribute\Controller;
use Phabrique\Core\Attribute\PathParam;
use Phabrique\Core\Attribute\QueryParam;
use Phabrique\Core\Request\Request;
use Phabrique\Core\Attribute\Route;
use Phabrique\Core\Request\RequestMethod;
use ReflectionClass;
use ReflectionMethod;

class AutoRouterFactory implements RouterFactory
{
    public function buildRouter(): Router
    {
        $router = new Router();
        $definedClasses = get_declared_classes();
        foreach ($definedClasses as $className) {
            $this->processClass($router, $className);
        }
        return $router;
    }

    private function processClass(Router $router, string $className)
    {
        $controllerRef = new ReflectionClass($className);
        $classAttributes = $controllerRef->getAttributes(Controller::class);

        if (empty($classAttributes)) {
            return;
        }

        $classMethods = $controllerRef->getMethods();
        foreach ($classMethods as $fn) {
            $this->processFunction($router, $controllerRef, $fn);
        }
    }

    private function processFunction(Router $router, ReflectionClass $controllerRef, ReflectionMethod $fnRef)
    {
        $routeAttributes = $fnRef->getAttributes(Route::class);
        $controllerName = $controllerRef->getName();
        $controllerAttributes = $controllerRef->getAttributes(Controller::class);
        foreach ($controllerAttributes as $controllerAttribute) {
            $controllerDescription = $controllerAttribute->newInstance();
            foreach ($routeAttributes as $routeAttribute) {
                $routeDescription = $routeAttribute->newInstance();
                $router->addRoute(
                    $routeDescription->method,
                    $controllerDescription->prefix . $routeDescription->path,
                    $this->buildRouteHandler(new $controllerName(), $fnRef)
                );
            }
        }
    }

    private function buildRouteHandler(object $controllerInstance, ReflectionMethod $fnRef)
    {
        return new class($controllerInstance, $fnRef) implements RouteHandler {
            public function __construct(private readonly object $controllerInstance, private readonly ReflectionMethod $fnRef) {}

            public function handle(Request $request): Response
            {
                $callParams = [];

                // Auto-binding logic here
                $paramRefs = $this->fnRef->getParameters();
                $errors = [];
                foreach ($paramRefs as $paramRef) {
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
                        if (!is_null($queryParam->name)) {
                            $name = $queryParam->name;
                        }

                        $requestQueryParams = $request->getQueryParameters();

                        if (!$paramRef->allowsNull() && !array_key_exists($name, $requestQueryParams)) {
                            $errors[$name] = "Missing query parameter '$name'";
                        }

                        $callParams[$paramRef->getName()] = $request->getQueryParameters()[$name] ?? null;
                    }
                }

                if (empty($errors)) {
                    return call_user_func_array([$this->controllerInstance, $this->fnRef->getName()], $callParams);
                }

                if (count($errors) === 1) {
                    throw new HttpError(HttpStatusCode::ERR_BAD_REQUEST, array_values($errors)[0]);
                }

                throw new HttpError(HttpStatusCode::ERR_BAD_REQUEST, "Several required query parameters are missing");
            }
        };
    }
}
