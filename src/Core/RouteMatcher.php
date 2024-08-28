<?php

declare(strict_types=1);

namespace Phabrique\Core;

use Exception;

const TEMPLATE_PATTERN = "/^(\/:?([a-zA-Z_][0-9a-zA-Z_]*)?)*(\/\*([a-zA-Z_][a-zA-Z0-9_]+)){0,1}$/";
const PATH_PATTERN = "/^(\/[0-9a-zA-Z_.-]*)+$/";

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
        $duplicates = [];
        foreach ($parts as $part) {
            if ($part[0] == ':' || $part[0] == '*') {
                $key = substr($part, 1);
                array_push($duplicates, $key);
                if (array_count_values($duplicates)[$key] > 1) {
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

        $lastPartIndex = count($this->parts) - 1;

        if (($lastPartIndex > -1 && !str_contains($this->parts[$lastPartIndex][0], '*')) && count($pathParts) != count($this->parts)) {
            return false;
        }

        $lastMatch = [];
        for ($i = 0; $i < count($this->parts); $i++) {
            if ($this->parts[$i][0] == ':') {
                $key = substr($this->parts[$i], 1);
                $lastMatch[$key] = $pathParts[$i];
            } elseif ($this->parts[$i][0] == '*') {
                $key = substr($this->parts[$i], 1);
                $lastMatch[$key] = implode("/", array_slice($pathParts, $i));
                break;
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

    public function isEquivalentTo(RouteMatcher $other): bool
    {
        if (count($other->parts) != count($this->parts)) {
            return false;
        }
        for ($i = 0; $i < count($this->parts); $i++) {
            if ($this->parts[$i][0] == ':') {
                if ($other->parts[$i][0] != ':') {
                    return false;
                }
            } elseif ($this->parts[$i][0] == '*') {
                if ($other->parts[$i][0] != '*') {
                    return false;
                }
            } elseif ($this->parts[$i] != $other->parts[$i]) {
                return false;
            }
        }
        return true;
    }

    public static function comparePriority(RouteMatcher $a, RouteMatcher $b): int
    {
        return strcmp(join("/", $a->parts), join("/", $b->parts));
    }
}
