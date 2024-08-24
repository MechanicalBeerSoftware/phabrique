<?php

declare(strict_types=1);

use Phabrique\Core\Request\RequestMethod;
use PHPUnit\Framework\TestCase;

class RequestMethodTest extends TestCase
{

    function testParseFromString()
    {
        $this->assertEquals(RequestMethod::Get, RequestMethod::fromString("get"));
        $this->assertEquals(RequestMethod::Post, RequestMethod::fromString("post"));
        $this->assertEquals(RequestMethod::Put, RequestMethod::fromString("put"));
        $this->assertEquals(RequestMethod::Patch, RequestMethod::fromString("patch"));
        $this->assertEquals(RequestMethod::Options, RequestMethod::fromString("options"));
        $this->assertEquals(RequestMethod::Delete, RequestMethod::fromString("delete"));
    }

    function testParseFromStringIgnoreCase()
    {
        $this->assertEquals(RequestMethod::Get, RequestMethod::fromString("Get"));
        $this->assertEquals(RequestMethod::Post, RequestMethod::fromString("POST"));
        $this->assertEquals(RequestMethod::Delete, RequestMethod::fromString("deLETe"));
        $this->assertEquals(RequestMethod::Patch, RequestMethod::fromString("pAtCh"));
    }

    function testParseRejectOnIncorrectInput()
    {
        try {
            RequestMethod::fromString("foo");
            $this->fail();
        } catch (Throwable $e) {
            $this->assertEquals("'foo' is not a valid request method", $e->getMessage());
        }
    }
}
