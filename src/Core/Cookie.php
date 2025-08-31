<?php

declare(strict_types=1);

namespace Phabrique\Core;

class Cookie {

    public function __construct(private string $name, private string $value = '', private int $expiry = 0, private string $path = "/", private string $domain = "", private bool $secure = false, private bool $httpOnly = false)
    {
    }

    public function set(): bool {
        return setcookie(
            name: $this->name,
            value: $this->value,
            expires_or_options: $this->expiry,
            path: $this->path,
            domain: $this->domain,
            secure: $this->secure,
            httponly: $this->httpOnly
        );
    }
}
