<?php declare(strict_types=1);

namespace Phabrique\Core\Request;

interface Request
{
    public function getQueryParameters(): array;
    public function getFormData(): array;
    public function getPathParameters(): array;
    public function setPathParameters(array $pathParameters): void;
    public function getPath(): string;
    public function getMethod(): string;
    public function getHeaders(): array;
}
