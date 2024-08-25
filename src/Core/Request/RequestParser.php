<?php

declare(strict_types=1);

namespace Phabrique\Core\Request;

interface RequestParser
{
    function parseIncomingRequest(): Request;
}
