<?php

namespace Phabrique\Core;
use Phabrique\Core\Request\Request;

interface ErrorHandler {
    public function handle(Request $request, HttpError $exception): Response;
}
