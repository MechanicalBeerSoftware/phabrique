<?php

declare(strict_types=1);

namespace Phabrique\Core\Request;

class FormBodyParser implements BodyParser
{
    public function parse(string $body): array
    {
        $output = [];
        parse_str($body, $output);
        return $output;
    }
}
