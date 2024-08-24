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


    public static function fromString(string $method): RequestMethod
    {
        return match (strtolower($method)) {
            "get" => RequestMethod::Get,
            "post" => RequestMethod::Post,
            "put" => RequestMethod::Put,
            "patch" => RequestMethod::Patch,
            "delete" => RequestMethod::Delete,
            "options" => RequestMethod::Options,
            default => throw new Exception("'$method' is not a valid request method")
        };
    }
}
