<?php

namespace Phabrique\Core;

interface ErrorHandler {
    public function handle(Request $request, HttpError $exception): Response;
}
