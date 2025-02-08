<?php

declare(strict_types=1);

namespace Phabrique\Core\Request;

class BodyParserFactory
{
    function getParserForContentType(HttpContentType $contentType): ?BodyParser
    {
        return match ($contentType->getName()) {
            "application/json" => new JSONBodyParser(),
            "application/x-www-form-urlencoded" => new FormBodyParser(),
            "multipart/form-data" => new MultipartFormBodyParser(),
            default => null,
        };
    }
}
