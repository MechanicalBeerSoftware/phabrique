<?php

declare(strict_types=1);

use Phabrique\Core\Request\FormBodyParser;
use PHPUnit\Framework\TestCase;

class FormBodyParserTest extends TestCase
{
    public function testParseSimpleFormData()
    {
        $body = "firstname=John&lastname=Doe&age=28";
        $parser = new FormBodyParser();
        $expectedData = ["firstname" => "John", "lastname" => "Doe", "age" => 28];

        $this->assertEquals($expectedData, $parser->parse($body));
    }

    public function testParseComplexFormData()
    {
        $body = "firstname=John&lastname=Doe&address[street]=Example street 16&address[zipcode]=1000&address[country]=France";
        $parser = new FormBodyParser();

        $expectedData = [
            "firstname" => "John",
            "lastname" => "Doe",
            "address" => [
                "street" => "Example street 16",
                "zipcode" => 1000,
                "country" => "France"
            ]
        ];

        $this->assertEquals($expectedData, $parser->parse($body));
    }

    public function testParseFormDataWithEmptyField()
    {
        $body = "firstname=&address[street]=Example street 16&address[zipcode]=1000&address[country]=France";
        $parser = new FormBodyParser();

        $expectedData = [
            "firstname" => "",
            "address" => [
                "street" => "Example street 16",
                "zipcode" => 1000,
                "country" => "France"
            ]
        ];

        $this->assertEquals($expectedData, $parser->parse($body));
    }

    public function testParseSingleArray()
    {
        $body = "tags[]=car&tags[]=red";
        $parser = new FormBodyParser();

        $expectedData = [
            "tags" => [
                "car",
                "red"
            ],
        ];

        $this->assertEquals($expectedData, $parser->parse($body));
    }

    public function testParseSingleString()
    {

        $body = "Some text";
        $parser = new FormBodyParser();

        $expectedData = [
            "Some_text" => ""
        ];

        $this->assertEquals($expectedData, $parser->parse($body));
    }

    public function testParseNestedObject()
    {

        $body = "user[lastname]=Doe&user[firstname]=John&user[address][street]=Example street&user[address][country]=France&user[age]=28";
        $parser = new FormBodyParser();

        $expectedData = [
            "user" => [
                "firstname" => "John",
                "lastname" => "Doe",
                "age" => 28,
                "address" => [
                    "street" => "Example street",
                    "country" => "France"
                ]
            ],
        ];

        $this->assertEquals($expectedData, $parser->parse($body));
    }
}
