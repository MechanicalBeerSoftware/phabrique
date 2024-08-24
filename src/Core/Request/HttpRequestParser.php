<?php

namespace Phabrique\Core\Request;

class HttpRequestParser implements RequestParser
{
    public function parseIncomingRequest(): Request
    {
        $method = $this->parseRequestMethod();
        $headers = $this->parseHeaders();
        $body = "";
        // GET doesn't have a body
        if ($method !== RequestMethod::Get) {
            $body = $this->parseBody($headers);
        }

        return new ServerRequest(
            $this->parseQueryParams(),
            $this->parsePath(),
            $method,
            $body,
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
        // Handle special case for html forms
        if (count($_POST) > 0) {
            return $_POST;
        }

        // TODO: inject via dep injection
        $bodyParserFactory = new BodyParserFactory();

        $bodyParser = $bodyParserFactory->getParserForContentType($headers["Content-Type"]);

        $body = file_get_contents('php://input');

        if (is_null($bodyParser)) {
            // return raw body
            return $body;
        }
        return $bodyParser->parse($body);
        return $_POST;
    }
}
