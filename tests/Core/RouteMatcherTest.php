<?php

declare(strict_types=1);

use Phabrique\Core\RouteMatcher;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class RouteMatcherTest extends TestCase
{

    function setUp(): void
    {
        # Turn on error reporting
        error_reporting(E_ALL);
    }

    function testRouteMatcherWithoutParamsMatchesCorrectPath()
    {
        $m = RouteMatcher::compile("/my/path");
        $this->assertTrue($m->matches("/my/path"));
    }

    function testRouteMatcherWithoutParamsDoesntMatchIncorrectPath()
    {
        $m = RouteMatcher::compile("/my/path");
        $this->assertFalse($m->matches("/not/my/path"));
    }

    function testSimpleRouteMatcherReturnsEmptyPathParams()
    {
        $m = RouteMatcher::compile("/my/path");
        $this->assertTrue($m->matches("/my/path"));
        $this->assertEmpty($m->extract());
    }

    function testRouteMatcherParsesPathParams()
    {
        $m = RouteMatcher::compile("/items/:id");
        $m->matches("/items/69");
        $this->assertEquals(["id" => "69"], $m->extract());
    }

    function testCannotExtractIfDidntMatch()
    {
        $m = RouteMatcher::compile("/items/:id");
        $m->matches("/other-items/69");

        try {
            $m->extract();
            $this->fail('$m->extract() was supposed to throw');
        } catch (Exception $e) {
            $this->assertEquals("Cannot extract path params from unmatched path", $e->getMessage());
        }
    }

    static function validTemplatesProvider()
    {
        return [
            ["/"],
            ["/items"],
            ["/items/new"],
            ["/items/:id"],
            ["/items/:id/price"],
        ];
    }

    #[DataProvider("validTemplatesProvider")]
    function testValidTemplateCompiles($template)
    {
        $m = RouteMatcher::compile($template);
        $this->assertNotNull($m);
    }

    function testInvalidTemplateThrows()
    {
        try {
            RouteMatcher::compile("invalid");
            $this->fail("matcher compilation was supposed to throw");
        } catch (Exception $e) {
            $this->assertEquals("Invalid template 'invalid'", $e->getMessage());
        }
    }

    function testTemplateWithMultipleSameKeyThrows()
    {
        try {
            RouteMatcher::compile("/users/:id/friends/:id");
            $this->fail("matcher compilation was supposed to throw");
        } catch (Exception $e) {
            $this->assertEquals("Duplicate key 'id'", $e->getMessage());
        }
    }

    function testInvalidPathThrows()
    {
        try {
            $m = RouteMatcher::compile("/items/:id/price");
            $m->matches("items 52");
            $this->fail('$m->matches() was supposed to throw');
        } catch (Exception $e) {
            $this->assertEquals("Invalid path 'items 52'", $e->getMessage());
        }
    }
}
