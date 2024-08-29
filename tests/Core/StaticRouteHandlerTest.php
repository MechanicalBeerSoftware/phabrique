<?php

declare(strict_types=1);

use Phabrique\Core\HttpError;
use Phabrique\Core\HttpStatusCode;
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

    public function testHandlerReturnsExistingFileWhenRootPathHigherInHierarchy()
    {
        $fileDir = __DIR__ . "/../../";
        file_put_contents($fileDir . "foo.txt", "bar");

        $staticRouteHandler = new StaticRouteHandler($fileDir);

        $requestMock = $this->createMock(Request::class);
        $requestMock->method("getPathParameters")->willReturn(["path" => "foo.txt"]);

        $this->expectNotToPerformAssertions();
        $staticRouteHandler->handle($requestMock);

        unlink($fileDir . "foo.txt");
    }

    public function testHandlerThrowsExceptionWithEscapingFromRootFolder()
    {
        $tempDir = "../../../tmpDir";
        $fullPath = __DIR__ . '/' . $tempDir;
        if (!file_exists($fullPath)) {
            mkdir($fullPath);
        }

        $staticRouteHandler = new StaticRouteHandler(__DIR__);

        $requestMock = $this->createMock(Request::class);
        $requestMock->method("getPathParameters")->willReturn(["path" => $tempDir]);

        try {
            $staticRouteHandler->handle($requestMock);
            $this->fail("routerMock->direct should have failed");
        } catch (HttpError $err) {
            $this->assertEquals(HttpStatusCode::ERR_NOT_FOUND, $err->getStatusCode());
            $this->assertEquals("The resource you were looking for could not be found", $err->getMessage());
        }
        rmdir($fullPath);
    }

    public function testHandlerThrowsExceptionWithNonExistingPath()
    {
        $path = "../../nonExistingFolder";
        try {
            $staticRouteHandler = new StaticRouteHandler($path);
            $this->fail("Should have thrown exception for non existing path");
        } catch (Exception $err) {
            $this->assertEquals("The specified root path doesn't exist. Path: '$path'", $err->getMessage());
        }
    }


    public function testHandlerThrowsExceptionIfRootPathNotDirectory()
    {
        $path = __FILE__;
        try {
            $staticRouteHandler = new StaticRouteHandler($path);
            $this->fail("Should have thrown exception when root path not directory");
        } catch (Exception $err) {
            $this->assertEquals("The root path must be a directory, file given: '$path'", $err->getMessage());
        }
    }
}
