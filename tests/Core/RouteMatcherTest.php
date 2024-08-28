<?php

declare(strict_types=1);

use Phabrique\Core\RouteMatcher;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class RouteMatcherTest extends TestCase
{
    public function setUp(): void
    {
        # Turn on error reporting
        error_reporting(E_ALL);
    }

    public function testRouteMatcherWithDefaultRoute()
    {
        $m = RouteMatcher::compile("/");
        $this->assertTrue($m->matches("/"));
    }

    public function testRouteMatcherWithoutParamsMatchesCorrectPath()
    {
        $m = RouteMatcher::compile("/my/path");
        $this->assertTrue($m->matches("/my/path"));
    }

    public function testRouteMatcherWithoutParamsDoesntMatchIncorrectPath()
    {
        $m = RouteMatcher::compile("/my/path");
        $this->assertFalse($m->matches("/not/my/path"));
    }

    public function testSimpleRouteMatcherReturnsEmptyPathParams()
    {
        $m = RouteMatcher::compile("/my/path");
        $this->assertTrue($m->matches("/my/path"));
        $this->assertEmpty($m->extract());
    }

    public function testRouteMatcherParsesPathParams()
    {
        $m = RouteMatcher::compile("/items/:id");
        $m->matches("/items/69");
        $this->assertEquals(["id" => "69"], $m->extract());
    }

    public function testCannotExtractIfDidntMatch()
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

    public static function validTemplatesProvider()
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
    public function testValidTemplateCompiles($template)
    {
        $m = RouteMatcher::compile($template);
        $this->assertNotNull($m);
    }

    public function testInvalidTemplateThrows()
    {
        try {
            RouteMatcher::compile("invalid");
            $this->fail("matcher compilation was supposed to throw");
        } catch (Exception $e) {
            $this->assertEquals("Invalid template 'invalid'", $e->getMessage());
        }
    }

    public function testTemplateWithMultipleSameKeyThrows()
    {
        try {
            RouteMatcher::compile("/users/:id/friends/:id");
            $this->fail("matcher compilation was supposed to throw");
        } catch (Exception $e) {
            $this->assertEquals("Duplicate key 'id'", $e->getMessage());
        }
    }

    public function testInvalidPathThrows()
    {
        try {
            $m = RouteMatcher::compile("/items/:id/price");
            $m->matches("items 52");
            $this->fail('$m->matches() was supposed to throw');
        } catch (Exception $e) {
            $this->assertEquals("Invalid path 'items 52'", $e->getMessage());
        }
    }

    public function testKnowIfEquivalentWithoutParams()
    {
        $m1 = RouteMatcher::compile("/items");
        $m2 = RouteMatcher::compile("/items");
        $this->assertTrue($m1->isEquivalentTo($m2));
        $this->assertTrue($m2->isEquivalentTo($m1));
    }

    public function testKnowIfEquivalentWithParams()
    {
        $m1 = RouteMatcher::compile("/items/:id");
        $m2 = RouteMatcher::compile("/items/:itemId");
        $this->assertTrue($m1->isEquivalentTo($m2));
        $this->assertTrue($m2->isEquivalentTo($m1));
    }

    public function testKnowIfEquivalentWithWildcard()
    {
        $m1 = RouteMatcher::compile("/items/*path");
        $m2 = RouteMatcher::compile("/items/*anotherPath");
        $this->assertTrue($m1->isEquivalentTo($m2));
        $this->assertTrue($m2->isEquivalentTo($m1));
    }

    public function testKnowIfNotEquivalentWithoutParams()
    {
        $m1 = RouteMatcher::compile("/peers");
        $m2 = RouteMatcher::compile("/apples");
        $this->assertFalse($m1->isEquivalentTo($m2));
        $this->assertFalse($m2->isEquivalentTo($m1));
    }

    public function testKnowIfNotEquivalentWithParams()
    {
        $m1 = RouteMatcher::compile("/items/:id");
        $m2 = RouteMatcher::compile("/items/new");
        $this->assertFalse($m1->isEquivalentTo($m2));
        $this->assertFalse($m2->isEquivalentTo($m1));
    }

    public function testKnowIfNotEquivalentWithWildcard()
    {
        $m1 = RouteMatcher::compile("/items/*path");
        $m2 = RouteMatcher::compile("/");
        $this->assertFalse($m1->isEquivalentTo($m2));
        $this->assertFalse($m2->isEquivalentTo($m1));
    }

    public function testStaticRouteReturnsSimplePath()
    {
        $m = RouteMatcher::compile("/data/*path");
        $m->matches("/data/myimage.png");
        $this->assertEquals(["path" => "myimage.png"], $m->extract());
    }

    public function testWildCardRetrievesAllTheRemainingPath()
    {
        $m = RouteMatcher::compile("/data/*path");
        $m->matches("/data/user/img/myimage.png");
        $this->assertEquals(["path" => "user/img/myimage.png"], $m->extract());
    }

    public function testMatcherRetrievesPathParamsAndWildcard()
    {

        $m = RouteMatcher::compile("/user/:id/*path");
        $m->matches("/user/1/img/myimage.png");
        $this->assertEquals(["id" => "1", "path" => "img/myimage.png"], $m->extract());
    }

    public function testMatcherAcceptsTemplateWithWildcardOnly()
    {
        $m = RouteMatcher::compile("/*path");
        $m->matches("/foo/bar/baz");
        $this->assertEquals(["path" => "foo/bar/baz"], $m->extract());
    }

    public function testStaticRouteWithMultipleWildcardsIsInvalid()
    {
        $template = "/data/*foo/bar/*baz";
        try {
            $m = RouteMatcher::compile($template);
            $this->fail("RouteMatcher::compile was suppose to throw");
        } catch (Exception $err) {
            $this->assertEquals("Invalid template '$template'", $err->getMessage());
        }
    }

    public function testTemplateCannotContainDuplicateVariableBetweenPathParamAndWildcard()
    {
        $template = "/data/:path/*path";

        try {
            $m = RouteMatcher::compile($template);
            $this->fail("RouteMatcher::compile was suppose to throw");
        } catch (Exception $err) {
            $this->assertEquals("Duplicate key 'path'", $err->getMessage());
        }
    }

    public function testTemplateInvalidWhenWildcardIsNotTheLastElement()
    {
        $template = "/data/*foo/bar";

        try {
            $m = RouteMatcher::compile($template);
            $this->fail("RouteMatcher::compile was suppose to throw");
        } catch (Exception $err) {
            $this->assertEquals("Invalid template '$template'", $err->getMessage());
        }
    }

    public static function routesPriorityProvider()
    {
        return [
            ["/", "/", "="],
            ["/items/first/price", "/items/:id/price", ">"],
            ["/items/:id/provider/last", "/items/first/provider/:id", "<"],
            ["/items/first/provider/:id", "/items/:id/provider/last", ">"],
        ];
    }

    #[DataProvider("routesPriorityProvider")]
    public function testPriorityComparison(string $m1, string $m2, string $cmp)
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
