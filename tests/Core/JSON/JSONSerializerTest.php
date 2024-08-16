<?php

declare(strict_types=1);

use Phabrique\Core\JSON\JSONSerializer;
use PHPUnit\Framework\TestCase;

final class Foo
{
    function __construct(private string|null $a, private int $b) {}
}

final class Bar
{
    function __construct(private Foo $foo) {}
}


final class JSONSerializerTest extends TestCase
{
    public function testSerializeArray(): void
    {
        $arr = [1, 2.258, 3, 4];
        $serializer = new JSONSerializer();
        $json = $serializer->serialize($arr);
        $this->assertEquals("[1,2.258,3,4]", $json);
    }

    public function testSerializingNestedArray(): void
    {
        $arr = [
            ["1", "0", "0"],
            ["0", "cos t", "-sin t"],
            ["0", "sin t", "cos t"]
        ];
        $serializer = new JSONSerializer();
        $json = $serializer->serialize($arr);
        $this->assertEquals('[["1","0","0"],["0","cos t","-sin t"],["0","sin t","cos t"]]', $json);
    }

    public function testSerializeAssoc(): void
    {
        $assoc = [
            "foo" => "Issou",
            "bar" => 123,
            "baz" => true
        ];
        $serializer = new JSONSerializer();
        $json = $serializer->serialize($assoc);
        $this->assertEquals('{"foo":"Issou","bar":123,"baz":true}', $json);
    }

    public function testSerializeNestedAssoc(): void
    {
        $assoc = [
            "foo" => [1, 2, 3],
            "bar" => ["foo" => "hello", "bar" => 5]
        ];
        $serializer = new JSONSerializer();
        $json = $serializer->serialize($assoc);
        $this->assertEquals('{"foo":[1,2,3],"bar":{"foo":"hello","bar":5}}', $json);
    }

    public function testSerializeObject(): void
    {
        $fooObj = new Foo("Hello", 69);
        $serializer = new JSONSerializer();
        $json = $serializer->serialize($fooObj);
        $this->assertEquals('{"a":"Hello","b":69}', $json);
    }

    public function testSerializeNestedObject()
    {
        $barObj = new Bar(new Foo("Issou", 69));
        $serializer = new JSONSerializer();
        $json = $serializer->serialize($barObj);
        $this->assertEquals('{"foo":{"a":"Issou","b":69}}', $json);
    }

    public function testSerializeArrayOfObjects()
    {
        $objs = [new Foo("Hello", 420), new Foo("world", 69)];
        $serializer = new JSONSerializer();
        $json = $serializer->serialize($objs);
        $this->assertEquals('[{"a":"Hello","b":420},{"a":"world","b":69}]', $json);
    }

    public function testSerializeObjectWithNull()
    {
        $obj = new Foo(null, 69);
        $serializer = new JSONSerializer();
        $json = $serializer->serialize($obj);
        $this->assertEquals('{"a":null,"b":69}', $json);
    }

    public function testSerializeObjectWithNaN()
    {
        $definitelyNan = NAN;
        $serializer = new JSONSerializer();
        $json = $serializer->serialize($definitelyNan);
        $this->assertEquals('null', $json);
    }
}
