<?php

namespace Phabrique\Core;

enum HttpStatusCode: int
{
    case OK = 200;
    case OK_CREATED = 201;
    case OK_NO_CONTENT = 204;
    case ERR_BAD_REQUEST = 400;
    case ERR_UNAUTHORIZED = 401;
    case ERR_FORBIDDEN = 403;
    case ERR_NOT_FOUND = 404;
    case ERR_METHOD_NOT_ALLOWED = 405;
    case ERR_UNPROCESSABLE_CONTENT = 422;
    case SERVER_ERROR = 500;

    public function getStatusText(): string
    {
        return match ($this) {
            HttpStatusCode::OK => "OK",
            HttpStatusCode::OK_CREATED => "Created",
            HttpStatusCode::OK_NO_CONTENT => "No Content",
            HttpStatusCode::ERR_BAD_REQUEST => "Bad Request",
            HttpStatusCode::ERR_UNAUTHORIZED => "Unauthorized",
            HttpStatusCode::ERR_FORBIDDEN => "Forbidden",
            HttpStatusCode::ERR_NOT_FOUND => "Not Found",
            HttpStatusCode::ERR_METHOD_NOT_ALLOWED => "Method Not Allowed",
            HttpStatusCode::ERR_UNPROCESSABLE_CONTENT => "Unprocessable Content",
            HttpStatusCode::SERVER_ERROR => "Internal Server Error",
        };
    }
}
