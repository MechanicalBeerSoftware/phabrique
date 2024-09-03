<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use Phabrique\Core\Utils\StringUtils;

class StringUtilsTest extends TestCase
{
    static function commonPrefixProvider()
    {
        return [
            ["The quick brown fox", "The quick brown dog", "The quick brown "],
            ["The quick", "The quick brown fox", "The quick"],
            ["A string", "The string", ""],
            ["We're equal", "We're equal", "We're equal"],
        ];
    }

    #[DataProvider('commonPrefixProvider')]
    function testCommonPrefix($s, $s2, $expected)
    {
        $this->assertEquals($expected, StringUtils::commonPrefix($s, $s2));
    }

    function testIndent()
    {
        $src = 
"var a = 0;
var b = 5;
var c = a + b;";
        $expected = 
"  var a = 0;
  var b = 5;
  var c = a + b;";
        $this->assertEquals($expected, StringUtils::indent("  ", $src));
    }
}
