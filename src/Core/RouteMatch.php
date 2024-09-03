<?php

declare(strict_types=1);

namespace Phabrique\Core;

use Phabrique\Core\Request\RequestMethod;

class RouteMatch
{

    /**
     * @param bool $hasMatched
     * @param array $pathParams
     * @param array<RequestMethod, RouteHandler> $routeHandlers
     */
    public function __construct(private bool $hasMatched, private array $pathParams, private array $routeHandlers) {}

    public function hasMatched(): bool
    {
        return $this->hasMatched;
    }

    public function getRouteHandler(RequestMethod $method): ?RouteHandler
    {
        if (!array_key_exists($method->name, $this->routeHandlers)) {
            return null;
        }
        return $this->routeHandlers[$method->name];
    }

    public function getPathParams(): array
    {
        return $this->pathParams;
    }

    public function addPathParam(string $label, string $value)
    {
        $this->pathParams[$label] = $value;
    }
}
