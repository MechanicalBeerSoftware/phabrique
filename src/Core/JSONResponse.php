<?php

declare(strict_types=1);

namespace Phabrique\Core;

class JSONResponse implements Response
{
    function __construct(
        private mixed $content,
        private HttpStatusCode $statusCode = HttpStatusCode::OK,
        private array $headers = [],
        private bool $pretty = False
    ) {}

    function getStatus(): HttpStatusCode
    {
        return $this->statusCode;
    }

    function getHeaders(): array
    {
        $defaults = [
            "Content-Type" => "application/json"
        ];

        return array_merge($defaults, $this->headers);
    }

    function getBody(): mixed
    {
        // TODO: implement this
        if (is_array($this->content)) {
            if (array_is_list($this->content)) {
                return "[]";
            }
        }
        return "{}";
    }
}
