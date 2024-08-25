<?php

declare(strict_types=1);

namespace Phabrique\Core\Request;

use Exception;

enum RequestMethod
{
    case Get;
    case Post;
    case Put;
    case Patch;
    case Delete;
    case Options;
    case Connect;
    case Head;
    case Trace;


    public static function fromString(string $method): RequestMethod
    {
        return match (strtolower($method)) {
            "get" => RequestMethod::Get,
            "post" => RequestMethod::Post,
            "put" => RequestMethod::Put,
            "patch" => RequestMethod::Patch,
            "delete" => RequestMethod::Delete,
            "options" => RequestMethod::Options,
            "connect" => RequestMethod::Connect,
            "trace" => RequestMethod::Trace,
            "head" => RequestMethod::Head,
            default => throw new Exception("'$method' is not a valid request method")
        };
    }
}
