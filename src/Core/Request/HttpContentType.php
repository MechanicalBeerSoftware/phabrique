<?php

declare(strict_types=1);

namespace Phabrique\Core\Request;

class HttpContentType
{

    private string $name;
    private array $parameters;

    public function __construct(string $rawContentType)
    {
        $contentTypeElements =  explode(';', $rawContentType);

        $this->name = trim($contentTypeElements[0]);
        $this->parameters = $this->parseParameters(array_slice($contentTypeElements, 1));
    }

    public function getName(): string {
        return $this->name;
    }

    public function getParameters(): array {
        return $this->parameters;
    }

    private function parseParameters(array $rawParameters): array {
        $parameters = [];
        foreach($rawParameters as $rawParam) {
            $eltSplit = explode('=', trim($rawParam));
            $parameters[$eltSplit[0]] = $eltSplit[1];
        }
        return $parameters;
    }
}

