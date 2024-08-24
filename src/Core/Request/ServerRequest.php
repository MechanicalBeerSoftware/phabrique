<?php

declare(strict_types=1);

namespace Phabrique\Core\Request;

class ServerRequest implements Request
{
    private $pathParameters = [];

    public function __construct(
        private readonly array $query_params,
        private readonly string $path,
        private readonly RequestMethod $method,
        private readonly string|array $body,
        private readonly array $headers,
    ) {}


    public function getQueryParameters(): array
    {
        return $this->query_params;
    }

    public function getPathParameters(): array
    {
        return $this->pathParameters;
    }

    public function setPathParameters(array $pathParameters): void
    {
        $this->pathParameters = $pathParameters;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMethod(): RequestMethod
    {
        return $this->method;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): array|string
    {
        return $this->body;
    }
}
