<?php

declare(strict_types=1);

namespace Phabrique\Core;

class ServerResponse implements Response
{

    /* @var Cookie[] $cookies */
    private array $cookies = [];

    public function __construct(private readonly HttpStatusCode $status, private readonly array $headers, private readonly mixed $body) {}

    public function getStatus(): HttpStatusCode
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): mixed
    {
        return $this->body;
    }

    public function getCookies(): array
    {
        return $this->cookies;
    }

    public function setCookie(Cookie $cookie): void {
        $this->cookies[] = $cookie;
    }

    public function send(): void
    {
        echo $this->body;
    }

}
