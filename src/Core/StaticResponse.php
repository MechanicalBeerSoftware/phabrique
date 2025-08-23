<?php

declare(strict_types=1);

namespace Phabrique\Core;

use finfo;

class StaticResponse implements Response
{
    private array $headers = [];
    private string $body;

    public function __construct(private string $resourcePath, private HttpStatusCode $statusCode = HttpStatusCode::OK, array $headers = [])
    {
        if (!file_exists($resourcePath)) {
            throw new HttpError(HttpStatusCode::ERR_NOT_FOUND, "No such file");
        }

        if (is_dir($resourcePath)) {
            throw new HttpError(HttpStatusCode::ERR_BAD_REQUEST, "Requested file is a directory");
        }

        $this->headers = [
            "Content-Type" => $this->getMimeType($resourcePath),
        ];

        $this->headers = array_merge($this->headers, $headers);

        $this->body = file_get_contents($resourcePath);
        if (!$this->body) {
            throw new HttpError(HttpStatusCode::SERR_INTERNAL_SERVER_ERROR, "An error occured when reading the file");
        }
    }

    public function getBody(): mixed
    {
        return $this->body;
    }

    public function getStatus(): HttpStatusCode
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    function getMimeType(string $filePath): string
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($filePath);

        // Fallback mapping for common web files
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $map = $this->generateMimeTypesMap();

        return $map[$ext] ?? $mime;
    }

    function generateMimeTypesMap(): array | bool
    {
        $mimeFilePath = __DIR__ . "/../../data/mime.types";
        $s = [];
        foreach (explode("\n", file_get_contents($mimeFilePath)) as $x) {
            if (isset($x[0]) && $x[0] !== '#' && preg_match_all('#([^\s]+)#', $x, $out) && isset($out[1]) && ($c = count($out[1])) > 1) {
                for ($i = 1; $i < $c; $i++) {
                    $s[$out[1][$i]] = $out[1][0];
                }
            }
        }
        return $s;
    }

}
