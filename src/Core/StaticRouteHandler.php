<?php

declare(strict_types=1);

namespace Phabrique\Core;

use Exception;
use Phabrique\Core\Request\Request;

class StaticRouteHandler implements RouteHandler
{
    private string $rootPath;

    public function __construct(string $rootPath)
    {
        if (!file_exists($rootPath)) {
            throw new Exception("The specified root path doesn't exist. Path: '$rootPath'");
        }

        if (!is_dir($rootPath)) {
            throw new Exception("The root path must be a directory, file given: '$rootPath'");
        }

        $this->rootPath = $rootPath;

        if (!str_ends_with($this->rootPath, '/')) {
            $this->rootPath .= '/';
        }
    }

    public function handle(Request $request): Response
    {
        $absPath = realpath($this->rootPath . $request->getPathParameters()["path"]);
        if (!$absPath || !strstr($absPath, $this->rootPath)) {
            throw new HttpError(HttpStatusCode::ERR_BAD_REQUEST, "Bad Request", "Invalid path provided");
        }
        return new StaticResponse($absPath);
    }
}
