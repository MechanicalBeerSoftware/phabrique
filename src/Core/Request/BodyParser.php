<?php

declare(strict_types=1);

namespace Phabrique\Core\Request;

interface BodyParser
{
    function parse(string $body): array;
}
