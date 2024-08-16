<?php

declare(strict_types=1);

namespace Phabrique\Core;

use Phabrique\Core\JSON\JSONSerializer;

class JSONResponse implements Response
{
    private JSONSerializer $serializer;
    private array $headers;

    function __construct(
        private mixed $content,
        private HttpStatusCode $statusCode = HttpStatusCode::OK,
        array $headers = [],
        private bool $pretty = False
    ) {

        $default_headers = [
            "Content-Type" => "application/json"
        ];
        $this->headers = array_merge($default_headers, $headers);
        $this->serializer = new JSONSerializer();
    }

    function getStatus(): HttpStatusCode
    {
        return $this->statusCode;
    }

    function getHeaders(): array
    {
        return $this->headers;
    }

    function getBody(): mixed
    {
        return $this->serializer->serialize($this->content);
    }
}
