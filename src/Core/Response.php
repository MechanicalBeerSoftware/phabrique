<?php declare(strict_types=1);

namespace Phabrique\Core;

interface Response {
    public function getStatus(): HttpStatusCode;
    public function getHeaders(): array;
    public function getBody(): mixed;
}
