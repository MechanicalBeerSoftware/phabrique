<?php

declare(strict_types=1);

namespace Phabrique\Core;

use Exception;


const TEMPLATE_PATTERN = "/^(\/:?([a-zA-Z_-][0-9a-zA-Z_-]*)?)+$/";
const PATH_PATTERN = "/^(\/[0-9a-zA-Z_-]*)+$/"; // Same but without ':'

class RouteMatcher
{
    private ?array $lastMatch = null;

    public static function compile(string $template): RouteMatcher
    {
        if (!preg_match(TEMPLATE_PATTERN, $template)) {
            throw new Exception("Invalid template '$template'");
        }
        $parts = explode("/", substr($template, 1));
        if (count($parts) == 1 && strlen($parts[0]) == 0) {
            $parts = [];
        }

        // Assert no multiple keys
        foreach ($parts as $part) {
            if ($part[0] == ':') {
                if (array_count_values($parts)[$part] > 1) {
                    $key = substr($part, 1);
                    throw new Exception("Duplicate key '$key'");
                }
            }
        }

        return new RouteMatcher($parts);
    }

    private function __construct(private array $parts) {}

    public function matches(string $path): bool
    {
        $this->lastMatch == null;

        if (!preg_match(PATH_PATTERN, $path)) {
            $this->lastMatch = null;
            throw new Exception("Invalid path '$path'");
        }

        $pathParts = explode("/", substr($path, 1));
        if (count($pathParts) == 1 && strlen($pathParts[0]) == 0) {
            $pathParts = [];
        }

        if (count($pathParts) != count($this->parts)) {
            return false;
        }

        $lastMatch = [];
        for ($i = 0; $i < count($this->parts); $i++) {
            if ($this->parts[$i][0] == ':') {
                $key = substr($this->parts[$i], 1);
                $lastMatch[$key] = $pathParts[$i];
            } else {
                if ($pathParts[$i] != $this->parts[$i]) {
                    return false;
                }
            }
        }
        $this->lastMatch = $lastMatch;
        return true;
    }

    public function extract(): array
    {
        if (is_null($this->lastMatch)) {
            throw new Exception("Cannot extract path params from unmatched path");
        }
        return $this->lastMatch;
    }
}
