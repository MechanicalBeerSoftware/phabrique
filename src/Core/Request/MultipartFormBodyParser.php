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
                $fieldNameMatch = [];
                preg_match("/name=\"([^\"]+)\"/", $headers["Content-Disposition"], $fieldNameMatch);
                $fileMatch = [];
                $hasFilename = preg_match("/filename=\"([^\"]+)\"/", $headers["Content-Disposition"], $fileMatch);

                $fieldName = $fieldNameMatch[1];

                if ($hasFilename === 1) {
                    $parsedField = [];
                    $parsedField["content"] = $partContent;
                    $parsedField["filename"] = $fileMatch[1];

                    if (array_key_exists("Content-Type", $headers)) {
                        $parsedField["type"] = $headers["Content-Type"];
                    }

                    if (array_key_exists($fieldName, $parsedBody)) {
                        $existingFile = $parsedBody[$fieldName];
                        if (is_array($existingFile)) {
                            $parsedBody[$fieldName] = [$existingFile, $parsedField];
                        } else {
                            array_push($existingFile, $parsedField);
                            $parsedBody[$fieldName] = $existingFile;
                        }
                    } else {
                        $parsedBody[$fieldName] = $parsedField;
                    }
                } else {
                    if (str_contains($fieldName, "[") && str_contains($fieldName, "]")) {
                        $parsedField = [];
                        parse_str("$fieldName=$partContent", $parsedField);
                        $parsedBody = array_merge_recursive($parsedBody, $parsedField);
                    } else {
                        $parsedBody[$fieldName] = $partContent;
                    }
                }
            }
        }

        return $parsedBody;
    }
}
