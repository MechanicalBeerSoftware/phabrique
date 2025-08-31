<?php declare(strict_types=1);

namespace Phabrique\Core;

interface Response {
    public function getStatus(): HttpStatusCode;
    public function getHeaders(): array;
    public function getBody(): mixed;
    /**
    * @return Cookie[] the cookies of the response
    */
    public function getCookies(): array;
    public function setCookie(Cookie $cookie): void;
    public function send(): void;
}
