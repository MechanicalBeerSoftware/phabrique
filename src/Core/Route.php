<?php

declare(strict_types=1);

namespace Phabrique\Core;

class Route
{
    public function __construct(
        private readonly RouteMatcher $matcher,
        private readonly RouteHandler $handler,
        private readonly string $method
    ) {}

    public function getMatcher(): RouteMatcher
    {
        return $this->matcher;
    }

    public function getHandler(): RouteHandler
    {
        return $this->handler;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
