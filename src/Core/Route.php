<?php

namespace Phabrique\Core;

class Route {
    
    public function __construct(private readonly string $path) {
    }

    public function get_path() {
        return $this->path;
    }
}

