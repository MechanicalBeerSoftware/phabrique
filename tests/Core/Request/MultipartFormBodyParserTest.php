<?php

declare(strict_types=1);

use Phabrique\Core\Request\MultipartFormBodyParser;
use PHPUnit\Framework\TestCase;

class MultipartFormBodyParserTest extends TestCase
{

    public function testParseSimpleFormData()
    {
        $body = "-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"firstname\"\r\n\r\nJohn\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"lastname\"\r\n\r\nDoe\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"publication_date\"\r\n\r\n2010-10-10T10:10\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"visible\"\r\n\r\non\r\n-----------------------------6807086855428376552168079959--\r\n";

        $parser = new MultipartFormBodyParser();

        $expectedResult = [
            "firstname" => "John",
            "lastname" => "Doe",
            "publication_date" => "2010-10-10T10:10",
            "visible" => "on"
        ];

        $this->assertEquals($expectedResult, $parser->parse($body));
    }

    public function testParseFormWithEmptyField()
    {
        $body = "-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"firstname\"\r\n\r\n\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"lastname\"\r\n\r\nDoe\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"publication_date\"\r\n\r\n2010-10-10T10:10\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"visible\"\r\n\r\non\r\n-----------------------------6807086855428376552168079959--\r\n";

        $parser = new MultipartFormBodyParser();

        $expectedResult = [
            "firstname" => "",
            "lastname" => "Doe",
            "publication_date" => "2010-10-10T10:10",
            "visible" => "on"
        ];

        $this->assertEquals($expectedResult, $parser->parse($body));
    }

    public function testParseFormWithArray()
    {
        $body = "-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"tag[]\"\r\n\r\ncar\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"tag[]\"\r\n\r\nred\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"tag[]\"\r\n\r\nvehicle\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"tag[]\"\r\n\r\nbreak\r\n-----------------------------6807086855428376552168079959--\r\n";

        $parser = new MultipartFormBodyParser();

        $expectedResult = [
            "tag" => [
                "car",
                "red",
                "vehicle",
                "break"
            ]
        ];

        $this->assertEquals($expectedResult, $parser->parse($body));
    }


    public function testParseFormWithSimpleObject()
    {
        $body = "-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"user[lastname]\"\r\n\r\nDoe\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"user[firstname]\"\r\n\r\nJohn\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"user[email]\"\r\n\r\nj.doe@example.com\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"user[age]\"\r\n\r\n28\r\n-----------------------------6807086855428376552168079959--\r\n";

        $parser = new MultipartFormBodyParser();

        $expectedResult = [
            "user" => [
                "lastname" => "Doe",
                "firstname" => "John",
                "age" => 28,
                "email" => "j.doe@example.com"
            ]
        ];

        $this->assertEquals($expectedResult, $parser->parse($body));
    }

    public function testParseFormWithNestedObject()
    {

        $body = "-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"user[lastname]\"\r\n\r\nDoe\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"user[firstname]\"\r\n\r\nJohn\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"user[address][street]\"\r\n\r\nExample street 18\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"user[address][city]\"\r\n\r\nExample city\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"user[address][country]\"\r\n\r\nExample country\r\n-----------------------------6807086855428376552168079959--\r\n";

        $parser = new MultipartFormBodyParser();

        $expectedResult = [
            "user" => [
                "lastname" => "Doe",
                "firstname" => "John",
                "address" => [
                    "street" => "Example street 18",
                    "city" => "Example city",
                    "country" => "Example country"
                ]
            ]
        ];

        $this->assertEquals($expectedResult, $parser->parse($body));
    }

    public function testParseFormWithDuplicateKeys()
    {

        $body = "-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"firstname\"\r\n\r\nJohn\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"firstname\"\r\n\r\nMarc\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"publication_date\"\r\n\r\n2010-10-10T10:10\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"visible\"\r\n\r\non\r\n-----------------------------6807086855428376552168079959--\r\n";

        $parser = new MultipartFormBodyParser();

        $expectedResult = [
            "firstname" => "Marc",
            "publication_date" => "2010-10-10T10:10",
            "visible" => "on"
        ];

        $this->assertEquals($expectedResult, $parser->parse($body));
    }

    public function testParseFormWithSingleTextFile()
    {

        $body = "-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"firstname\"\r\n\r\nJohn\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"lastname\"\r\n\r\nDoe\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"file\"; filename=\"notes.txt\"\r\nContent-Type: text/plain\r\n\r\nThis is the content of my file\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"visible\"\r\n\r\non\r\n-----------------------------6807086855428376552168079959--\r\n";

        $parser = new MultipartFormBodyParser();

        $expectedResult = [
            "firstname" => "John",
            "lastname" => "Doe",
            "file" => [
                "filename" => "notes.txt",
                "content" => "This is the content of my file",
                "type" => "text/plain"
            ],
            "visible" => "on"
        ];

        $this->assertEquals($expectedResult, $parser->parse($body));
    }

    public function testParseFormWithSingleUnkownTypeFile()
    {

        $body = "-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"firstname\"\r\n\r\nJohn\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"lastname\"\r\n\r\nDoe\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"file\"; filename=\"notes.txt\"\r\n\r\nThis is the content of my file\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"visible\"\r\n\r\non\r\n-----------------------------6807086855428376552168079959--\r\n";

        $parser = new MultipartFormBodyParser();

        $expectedResult = [
            "firstname" => "John",
            "lastname" => "Doe",
            "file" => [
                "filename" => "notes.txt",
                "content" => "This is the content of my file",
            ],
            "visible" => "on"
        ];

        $this->assertEquals($expectedResult, $parser->parse($body));
    }

    public function testParseFormWithMutlipleFiles()
    {

        $body = "-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"firstname\"\r\n\r\nJohn\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"lastname\"\r\n\r\nDoe\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"file\"; filename=\"notes.txt\"\r\nContent-Type: text/plain\r\n\r\nThis is the content of my file\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"file\"; filename=\"data.json\"\r\nContent-Type: application/json\r\n\r\n{ \"username\": \"johndoe\", \"email\": \"j.doe@example.com\"}\r\n-----------------------------6807086855428376552168079959\r\nContent-Disposition: form-data; name=\"visible\"\r\n\r\non\r\n-----------------------------6807086855428376552168079959--\r\n";

        $parser = new MultipartFormBodyParser();

        $expectedResult = [
            "firstname" => "John",
            "lastname" => "Doe",
            "file" => [
                [
                    "filename" => "notes.txt",
                    "content" => "This is the content of my file",
                    "type" => "text/plain"
                ],
                [
                    "filename" => "data.json",
                    "content" => '{ "username": "johndoe", "email": "j.doe@example.com"}',
                    "type" => "application/json"
                ]
            ],
            "visible" => "on"
        ];

        $this->assertEquals($expectedResult, $parser->parse($body));
    }
}
