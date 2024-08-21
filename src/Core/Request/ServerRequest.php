<?php

namespace Phabrique\Core\Request;

class ServerRequest implements Request
{

    private $pathParameters = [];

    private function __construct(
        private readonly array $query_params,
        private readonly string $path,
        private readonly string $method,
        private readonly array $formData,
        private readonly array $headers,
    ) {}

    public static function parse(): Request
    {
        $headers = [];
        if (function_exists("getallheaders")) {
            $headers = getallheaders();
        }
        return new ServerRequest(
            $_GET,
            parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH),
            strtolower($_SERVER["REQUEST_METHOD"]),
            $_POST,
            $headers,
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

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
