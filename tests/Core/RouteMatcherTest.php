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

    function testKnowIfEquivalentWithoutParams()
    {
        $m1 = RouteMatcher::compile("/items");
        $m2 = RouteMatcher::compile("/items");
        $this->assertTrue($m1->isEquivalentTo($m2));
        $this->assertTrue($m2->isEquivalentTo($m1));
    }

    function testKnowIfEquivalentWithParams()
    {
        $m1 = RouteMatcher::compile("/items/:id");
        $m2 = RouteMatcher::compile("/items/:itemId");
        $this->assertTrue($m1->isEquivalentTo($m2));
        $this->assertTrue($m2->isEquivalentTo($m1));
    }

    function testKnowIfNotEquivalentWithoutParams()
    {
        $m1 = RouteMatcher::compile("/peers");
        $m2 = RouteMatcher::compile("/apples");
        $this->assertFalse($m1->isEquivalentTo($m2));
        $this->assertFalse($m2->isEquivalentTo($m1));
    }

    function testKnowIfNotEquivalentWithParams()
    {
        $m1 = RouteMatcher::compile("/items/:id");
        $m2 = RouteMatcher::compile("/items/new");
        $this->assertFalse($m1->isEquivalentTo($m2));
        $this->assertFalse($m2->isEquivalentTo($m1));
    }

    static function routesPriorityProvider()
    {
        return [
            ["/", "/", "="],
            ["/items/first/price", "/items/:id/price", ">"],
            ["/items/:id/provider/last", "/items/first/provider/:id", "<"],
            ["/items/first/provider/:id", "/items/:id/provider/last", ">"],
        ];
    }

    #[DataProvider("routesPriorityProvider")]
    function testPriorityComparison(string $m1, string $m2, string $cmp)
    {
        $rm1 = RouteMatcher::compile($m1);
        $rm2 = RouteMatcher::compile($m2);
        switch ($cmp) {
            case "=":
                $this->assertTrue(RouteMatcher::comparePriority($rm1, $rm2) == 0);
                break;
            case ">":
                $this->assertTrue(RouteMatcher::comparePriority($rm1, $rm2) > 0);
                $this->assertTrue(RouteMatcher::comparePriority($rm2, $rm1) < 0);
                break;
            case "<":
                $this->assertTrue(RouteMatcher::comparePriority($rm1, $rm2) < 0);
                $this->assertTrue(RouteMatcher::comparePriority($rm2, $rm1) > 0);
        }
    }
}
