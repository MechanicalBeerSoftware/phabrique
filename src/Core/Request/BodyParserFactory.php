<?php

declare(strict_types=1);

namespace Phabrique\Core\Request;

class BodyParserFactory
{
    function getParserForContentType(string $contentType): BodyParser|null
    {
        return match ($contentType) {
            "application/json" => new JSONBodyParser(),
            "application/x-www-form-urlencoded" => new FormDataBodyParser(),
            "multipart/form-data" => new MultipartFormBodyParser(),
            default => null,
        };
    }
}
