<?php

namespace Phabrique\Core;

enum HttpStatusCode: int {
    case OK = 200;
    case OK_CREATED = 201;
    case OK_NO_CONTENT = 204;
    case ERR_BAD_REQUEST = 400;
    case ERR_UNAUTHORIZED = 401;
    case ERR_FORBIDDEN = 403;
    case ERR_NOT_FOUND = 404;
    case ERR_METHOD_NOT_ALLOWED = 405;
    case SERVER_ERROR = 500;
}