<?php

namespace Phabrique\Core;
use Phabrique\Core\Request\Request;

interface RouteHandler {
    public function handle(Request $request): Response;
}
