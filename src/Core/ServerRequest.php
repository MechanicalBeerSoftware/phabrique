<?php

namespace Phabrique\Core;

class ServerRequest implements Request {

    private $pathParameters = [];
    
    private function __construct(private readonly array $query_params, private readonly string $path, private readonly string $method, private readonly array $formData) {}

    public static function parse() : Request { 
        return new ServerRequest(
            $_GET,
            parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH),
            strtolower($_SERVER["REQUEST_METHOD"]),
            $_POST
        );
    }

    public function getFormData(): array
    {
        return $this->formData;
    }

    public function getQueryParameters(): array
    {
        return $this->query_params;
    }

    public function getPathParameters(): array
    {
        return $this->pathParameters;
    }

    public function setPathParameters(array $pathParameters): void
    {
        $this->pathParameters = $pathParameters;
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
