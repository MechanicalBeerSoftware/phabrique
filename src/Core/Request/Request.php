<?php

declare(strict_types=1);

namespace Phabrique\Core\Request;

interface Request
{
    public function getQueryParameters(): array;
    public function getPathParameters(): array;
    public function setPathParameters(array $pathParameters): void;
    public function getPath(): string;
    public function getMethod(): RequestMethod;
    public function getHeaders(): array;
    public function getBody(): array|string;
}
