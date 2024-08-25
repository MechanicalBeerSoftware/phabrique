<?php

declare(strict_types=1);

namespace Phabrique\Core;

use Phabrique\Core\Request\RequestMethod;

class Route
{
    public function __construct(
        private readonly RouteMatcher $matcher,
        private readonly RouteHandler $handler,
        private readonly RequestMethod $method
    ) {}

    public function getMatcher(): RouteMatcher
    {
        return $this->matcher;
    }

    public function getHandler(): RouteHandler
    {
        return $this->handler;
    }

    public function getMethod(): RequestMethod
    {
        return $this->method;
    }
}
