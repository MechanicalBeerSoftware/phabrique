<?php

namespace Phabrique\Core;

class ServerRequest implements Request {

    public static function parse() : Request { 
        return new ServerRequest(
            $_GET,
            parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH),
            strtolower($_SERVER["REQUEST_METHOD"])
        );
    }

    private function __construct(private readonly array $query_params, private readonly string $path, private readonly string $method) {

    }

    public function getQueryParameters(): array
    {
        return $this->query_params;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
