<?php

declare(strict_types=1);

use Phabrique\Core\HttpStatusCode;
use Phabrique\Core\RadixNode;
use Phabrique\Core\Request\Request;
use Phabrique\Core\Request\RequestMethod;
use Phabrique\Core\Response;
use Phabrique\Core\RouteHandler;
use Phabrique\Core\ServerResponse;
use PHPUnit\Framework\TestCase;

class RadixNodeTest extends TestCase
{
    function testCanInsertNestedPath()
    {
        $fooHandler = new class implements RouteHandler {
            function handle(Request $request): Response
            {
                return new ServerResponse(HttpStatusCode::OK, [], "");
            }
        };

        $root = new RadixNode();
        $root->insert("/foo", RequestMethod::Get, $fooHandler);
        $root->insert("/foo/bar", RequestMethod::Get, $fooHandler);
        $graph = $root->graph();
        $expectedGraph =
            "<root>
  foo
    bar";
        $this->assertEquals($expectedGraph, $graph);
    }

    function testCanInsertSuperPath()
    {
        $fooHandler = new class implements RouteHandler {
            function handle(Request $request): Response
            {
                return new ServerResponse(HttpStatusCode::OK, [], "");
            }
        };

        $root = new RadixNode();
        $root->insert("/foo/bar", RequestMethod::Get, $fooHandler);
        $root->insert("/foo", RequestMethod::Get, $fooHandler);
        $graph = $root->graph();
        $expectedGraph =
            "<root>
  foo
    bar";
        $this->assertEquals($expectedGraph, $graph);
    }

    function testCanInsertWithCommonPrefix()
    {
        $fooHandler = new class implements RouteHandler {
            function handle(Request $request): Response
            {
                return new ServerResponse(HttpStatusCode::OK, [], "");
            }
        };

        $root = new RadixNode();
        $root->insert("/foo/bar", RequestMethod::Get, $fooHandler);
        $root->insert("/foo/baz", RequestMethod::Get, $fooHandler);
        $root->insert("/foo/:id", RequestMethod::Get, $fooHandler);
        $root->insert("/foo/*path", RequestMethod::Get, $fooHandler);
        $graph = $root->graph();
        $expectedGraph =
            "<root>
  foo
    baz
    bar
    :id
    *path";
        $this->assertEquals($expectedGraph, $graph);
    }

    function testCanMatchSimplePath()
    {
        $fooHandler = new class implements RouteHandler {
            function handle(Request $request): Response
            {
                return new ServerResponse(HttpStatusCode::OK, [], "");
            }
        };

        $root = new RadixNode();
        $root->insert("/foo/bar/baz", RequestMethod::Get, $fooHandler);
        $match = $root->search("/foo/bar/baz");
        $this->assertTrue($match->hasMatched());
        $this->assertEquals([], $match->getPathParams());
        $this->assertEquals($fooHandler, $match->getRouteHandler(RequestMethod::Get));
    }

    function testCanMatchPlaceholderPath()
    {
        $fooHandler = new class implements RouteHandler {
            function handle(Request $request): Response
            {
                return new ServerResponse(HttpStatusCode::OK, [], "");
            }
        };

        $root = new RadixNode();
        $root->insert("/foo/:id/bar", RequestMethod::Get, $fooHandler);
        $match = $root->search("/foo/123/bar");
        $this->assertTrue($match->hasMatched());
        $this->assertEquals(["id" => "123"], $match->getPathParams());
        $this->assertEquals($fooHandler, $match->getRouteHandler(RequestMethod::Get));
    }

    function testCanMatchMultiPlaceholderPath()
    {
        $fooHandler = new class implements RouteHandler {
            function handle(Request $request): Response
            {
                return new ServerResponse(HttpStatusCode::OK, [], "");
            }
        };

        $root = new RadixNode();
        $root->insert("/foo/:bar/:baz", RequestMethod::Get, $fooHandler);
        $match = $root->search("/foo/123/456");
        $this->assertTrue($match->hasMatched());
        $this->assertEquals(["bar" => "123", "baz" => "456"], $match->getPathParams());
        $this->assertEquals($fooHandler, $match->getRouteHandler(RequestMethod::Get));
    }

    function testCanMatchWildcardPath()
    {
        $fooHandler = new class implements RouteHandler {
            function handle(Request $request): Response
            {
                return new ServerResponse(HttpStatusCode::OK, [], "");
            }
        };

        $root = new RadixNode();
        $root->insert("/foo/*path", RequestMethod::Get, $fooHandler);
        $match = $root->search("/foo/img/thumbnail.png");
        $this->assertTrue($match->hasMatched());
        $this->assertEquals(["path" => "img/thumbnail.png"], $match->getPathParams());
        $this->assertEquals($fooHandler, $match->getRouteHandler(RequestMethod::Get));
    }

    function testCanUseMultipleRequestMethods()
    {
        $fooHandler = new class implements RouteHandler {
            function handle(Request $request): Response
            {
                return new ServerResponse(HttpStatusCode::OK, [], "");
            }
        };

        $barHandler = new class implements RouteHandler {
            function handle(Request $request): Response
            {
                return new ServerResponse(HttpStatusCode::OK, [], "");
            }
        };

        $root = new RadixNode();
        $root->insert("/foo/bar/baz", RequestMethod::Get, $fooHandler);
        $root->insert("/foo/bar/baz", RequestMethod::Post, $barHandler);

        $match = $root->search("/foo/bar/baz");
        $this->assertTrue($match->hasMatched());
        $this->assertEquals([], $match->getPathParams());
        $this->assertEquals($fooHandler, $match->getRouteHandler(RequestMethod::Get));
        $this->assertEquals($barHandler, $match->getRouteHandler(RequestMethod::Post));
    }

    function testDoesntMatchUnspecifiedParentPath()
    {
        $fooHandler = new class implements RouteHandler {
            function handle(Request $request): Response
            {
                return new ServerResponse(HttpStatusCode::OK, [], "");
            }
        };

        $root = new RadixNode();
        $root->insert("/foo/bar/baz", RequestMethod::Get, $fooHandler);
        $this->assertFalse($root->search("/foo/bar")->hasMatched());
        $this->assertFalse($root->search("/foo")->hasMatched());
        $this->assertFalse($root->search("/")->hasMatched());
    }

    function testPlaceHolderAndWildcardsHaveCorrectPriority()
    {
        $fooHandler = new class implements RouteHandler {
            function handle(Request $request): Response
            {
                return new ServerResponse(HttpStatusCode::OK, [], "");
            }
        };
        
        $barHandler = new class implements RouteHandler {
            function handle(Request $request): Response
            {
                return new ServerResponse(HttpStatusCode::OK, [], "");
            }
        };
        
        $bazHandler = new class implements RouteHandler {
            function handle(Request $request): Response
            {
                return new ServerResponse(HttpStatusCode::OK, [], "");
            }
        };

        $root = new RadixNode();
        $root->insert("/foo/*path", RequestMethod::Get, $fooHandler);
        $root->insert("/foo/:id", RequestMethod::Get, $barHandler);
        $root->insert("/foo/all", RequestMethod::Get, $bazHandler);

        $this->assertEquals($bazHandler, $root->search("/foo/all")->getRouteHandler(RequestMethod::Get));
        $this->assertEquals($barHandler, $root->search("/foo/123")->getRouteHandler(RequestMethod::Get));
        $this->assertEquals($fooHandler, $root->search("/foo/bar/baz")->getRouteHandler(RequestMethod::Get));
    }
}
