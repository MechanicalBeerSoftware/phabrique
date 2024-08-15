<?php

namespace Phabrique\Core;

interface RouteHandler {
    public function handle(Request $request): Response;
}
