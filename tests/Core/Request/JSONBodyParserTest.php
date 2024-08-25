<?php

declare(strict_types=1);

use Phabrique\Core\Request\BodyParserException;
use Phabrique\Core\Request\JSONBodyParser;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class JSONBodyParserTest extends TestCase
{
    function testParseSimpleJSON()
    {
        $body = '{"username": "toto", "password": "1234"}';
        $bodyParser = new JSONBodyParser();
        $expectedData = ["username" => "toto", "password" => "1234"];
        $this->assertEquals($expectedData, $bodyParser->parse($body));
    }

    function testParseComplexJSON()
    {
        $body = '{"items": [{"id": 1, "label": "tomato"}, {"id": 2, "label": "onion"}], "fromCache": false, "cacheId": null}';
        $bodyParser = new JSONBodyParser();
        $expectedData = [
            "items" => [
                ["id" => 1, "label" => "tomato"],
                ["id" => 2, "label" => "onion"]
            ],
            "fromCache" => false,
            "cacheId" => null,
        ];
        $this->assertEquals($expectedData, $bodyParser->parse($body));
    }

    function testParseRootJSONArray()
    {
        $body = '[1, 2, 3, 4]';
        $bodyParser = new JSONBodyParser();
        $expectedData = [1, 2, 3, 4];
        $this->assertEquals($expectedData, $bodyParser->parse($body));
    }

    function testRaiseErrorWithInvalidJSON()
    {
        $body = 'Am dumb';
        $bodyParser = new JSONBodyParser();
        try {
            $bodyParser->parse($body);
        } catch (BodyParserException $e) {
            assertEquals("body parser error", $e->getMessage());
        }
    }
}
