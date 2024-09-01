<?php

declare(strict_types=1);

use Phabrique\Core\RadixNode;
use PHPUnit\Framework\TestCase;

class RadixNodeTest extends TestCase
{
    function testCanInsertNestedPath()
    {
        $root = new RadixNode();
        $root->insert("/foo", null);
        $root->insert("/foo/bar", null);
        $graph = $root->graph();
        $expectedGraph =
            "<root>
  foo
    bar";
        $this->assertEquals($expectedGraph, $graph);
    }

    function testCanInsertSuperPath()
    {
        $root = new RadixNode();
        $root->insert("/foo/bar", null);
        $root->insert("/foo", null);
        $graph = $root->graph();
        $expectedGraph =
            "<root>
  foo
    bar";
        $this->assertEquals($expectedGraph, $graph);
    }

    function testCanInsertWithCommonPrefix()
    {
        $root = new RadixNode();
        $root->insert("/foo/bar", null);
        $root->insert("/foo/baz", null);
        $graph = $root->graph();
        $expectedGraph =
            "<root>
  foo
    bar
    baz";
        $this->assertEquals($expectedGraph, $graph);
    }

    function testCanOverwriteExisting()
    {
        $root = new RadixNode();
        $root->insert("/foo/bar", 123);
        $root->insert("/foo/bar", 456);
        $this->assertEquals(456, $root->search("/foo/bar")->getValue());
    }

    function testCanAddValueToRoot()
    {
        $root = new RadixNode();
        $root->insert("/", 123);
        $this->assertEquals(123, $root->getValue());
    }

    function testCanFindNodeIfPresent()
    {
        $root = new RadixNode();
        $inserted = $root->insert("/foo/bar/baz", 123);
        $found = $root->search("/foo/bar/baz");
        $this->assertEquals($inserted, $found);
    }
}
