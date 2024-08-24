<?php

declare(strict_types=1);

namespace Phabrique\Core\Request;

class JSONBodyParser implements BodyParser
{

    function parse(string $body): array
    {
        $data = json_decode($body, associative: true);
        if (is_null($data)) {
            throw new BodyParserException("body parser error");
        }
        return $data;
    }
}
