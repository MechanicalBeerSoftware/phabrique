<?php

declare(strict_types=1);

namespace Phabrique\Core\Request;

class MultipartFormBodyParser implements BodyParser
{
    public function parse(string $body): array
    {
        return [];
    }
}
