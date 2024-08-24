<?php

namespace Phabrique\Core\Request;

class HttpRequestParser implements RequestParser
{
    public function parseIncomingRequest(): Request
    {
        $headers = $this->parseHeaders();

        return new ServerRequest(
            $this->parseQueryParams(),
            $this->parsePath(),
            $this->parseRequestMethod(),
            $this->parseBody($headers),
            $headers,
        );
    }

    private function parseHeaders(): array
    {
        $headers = [];
        if (function_exists("getallheaders")) {
            $headers = getallheaders();
        }
        return $headers;
    }

    private function parseQueryParams(): array
    {
        return $_GET;
    }

    private function parsePath(): string
    {
        return parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    }

    private function parseRequestMethod(): RequestMethod
    {
        return RequestMethod::fromString($_SERVER["REQUEST_METHOD"]);
    }

    private function parseBody(array $headers): string|array
    {
        return $_POST;
    }
}
