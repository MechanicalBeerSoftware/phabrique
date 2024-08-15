<?php

declare(strict_types=1);

namespace Phabrique\Core;

use Exception;

class HttpError extends Exception
{

    public function __construct(private HttpStatusCode $statusCode, private string $statusText, string $message)
    {
        parent::__construct($message);
    }

    public function getStatusCode(): HttpStatusCode
    {
        return $this->statusCode;
    }

    public function getStatusText()
    {
        return $this->statusText;
    }
}
