<?php

declare(strict_types=1);

use Phabrique\Core\Request\Request;
use Phabrique\Core\StaticRouteHandler;
use PHPUnit\Framework\TestCase;

final class StaticRouteHandlerTest extends TestCase
{
    public function testHandlerReturnsExistingFile()
    {
        $staticRouteHandler = new StaticRouteHandler(__DIR__);

        $requestMock = $this->createMock(Request::class);
        $requestMock->method("getPathParameters")->willReturn(["path" => basename(__FILE__)]);

        $this->expectNotToPerformAssertions();
        $staticRouteHandler->handle($requestMock);
    }

    public function testHandlerReturnsExistingFileForRootFolderPathEndingWithSlash()
    {
        $staticRouteHandler = new StaticRouteHandler(__DIR__ . '/');

        $requestMock = $this->createMock(Request::class);
        $requestMock->method("getPathParameters")->willReturn(["path" => basename(__FILE__)]);

        $this->expectNotToPerformAssertions();
        $staticRouteHandler->handle($requestMock);
    }

    public function testHandlerThrowsExceptionWithEscapingFromRootFolder()
    {
        $tempDir = __DIR__ . "/../../../tmpDir";
        if (!file_exists($tempDir)) {
            mkdir($tempDir);
        }

        $staticRouteHandler = new StaticRouteHandler(__DIR__);

        $requestMock = $this->createMock(Request::class);
        $requestMock->method("getPathParameters")->willReturn(["path" => $tempDir]);

        try {
            $staticRouteHandler->handle($requestMock);
            $this->fail("routerMock->direct should have failed");
        } catch (Exception $err) {
            $this->assertEquals("Invalid path provided", $err->getMessage());
        }
        rmdir($tempDir);
    }

    public function testHandlerThrowsExceptionWithNonExistingPath()
    {
        $path = "../../nonExistingFolder";
        try {
            $staticRouteHandler = new StaticRouteHandler($path);
        } catch (Exception $err) {
            $this->assertEquals("The specified root path doesn't exist. Path: '$path'", $err->getMessage());
        }
    }

    public function testHandlerThrowsExceptionIfRootPathNotDirectory()
    {
        $path = __FILE__;
        try {
            $staticRouteHandler = new StaticRouteHandler($path);
        } catch (Exception $err) {
            $this->assertEquals("The root path must be a directory, file given: '$path'", $err->getMessage());
        }
    }
}
