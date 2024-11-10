<?php

declare(strict_types=1);

namespace Phabrique\Core\Request;

class MultipartFormBodyParser implements BodyParser
{
    public function parse(string $body): array
    {
        $boundary = substr($body, 0, strpos($body, "\r\n"));
        $parts = explode($boundary, $body);
        array_pop($parts); // Removing trailing "--" from array
        $parsedBody = [];

        foreach ($parts as $part) {
            if (trim($part) === "") {
                continue;
            }

            $headerContent = explode("\r\n\r\n", trim($part));

            $partContent = $headerContent[1] ?? "";

            $headers = [];

            foreach (explode("\r\n", $headerContent[0]) as $header) {
                [$headerName, $headerValue] = explode(": ", $header);
                $headers[$headerName] = $headerValue;
            }

            if (array_key_exists("Content-Disposition", $headers)) {
                $nameMatch = [];
                preg_match("/name=\"([^\"]+)\"/", $headers["Content-Disposition"], $nameMatch);
                $fileMatch = [];
                $hasFilename = preg_match("/filename=\"([^\"]+)\"/", $headers["Content-Disposition"], $fileMatch);

                $name = $nameMatch[1];

                if ($hasFilename === 1) {
                    $parsedField = [];
                    $parsedField["content"] = $partContent;
                    $parsedField["filename"] = $fileMatch[1];

                    if (array_key_exists("Content-Type", $headers)) {
                        $parsedField["type"] = $headers["Content-Type"];
                    }

                    if (array_key_exists($name, $parsedBody)) {
                        $existingFile = $parsedBody[$name];
                        if (is_array($existingFile)) {
                            $parsedBody[$name] = [$existingFile, $parsedField];
                        } else {
                            array_push($existingFile, $parsedField);
                            $parsedBody[$name] = $existingFile;
                        }
                    } else {
                        $parsedBody[$name] = $parsedField;
                    }
                } else {
                    if (str_contains($name, "[") && str_contains($name, "]")) {
                        $parsedField = [];
                        parse_str("$name=$partContent", $parsedField);
                        $parsedBody = array_merge_recursive($parsedBody, $parsedField);
                    } else {
                        $parsedBody[$name] = $partContent;
                    }
                }
            }
        }


        return $parsedBody;
    }
}
