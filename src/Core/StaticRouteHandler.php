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
        $fullRootPath = realpath($rootPath);

        if (!$fullRootPath) {
            throw new Exception("The specified root path doesn't exist. Path: '$rootPath'");
        }

        if (!is_dir($fullRootPath)) {
            throw new Exception("The root path must be a directory, file given: '$rootPath'");
        }

        $this->rootPath = $fullRootPath;

        if (!str_ends_with($this->rootPath, '/')) {
            $this->rootPath .= '/';
        }
    }

    public function handle(Request $request): Response
    {
        $relativePath = $this->rootPath . $request->getPathParameters()["path"];
        $absPath = realpath($relativePath);
        if (!$absPath || !strstr($absPath, $this->rootPath)) {
            throw new HttpError(HttpStatusCode::ERR_NOT_FOUND, "The resource you were looking for could not be found");
        }
        return new StaticResponse($absPath);
    }
}
