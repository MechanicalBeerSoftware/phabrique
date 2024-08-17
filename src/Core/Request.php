<?php declare(strict_types=1);

namespace Phabrique\Core;

interface Request
{
    public function getQueryParameters(): array;
    public function getFormData(): array;
    public function getPath(): string;
    public function getMethod(): string;
}
