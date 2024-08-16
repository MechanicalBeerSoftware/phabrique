<?php

declare(strict_types=1);

use Phabrique\Core\JSON\JSONException;
use Phabrique\Core\JSON\JSONField;
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

final class Baz
{
    function __construct(
        #[JsonField(fieldName: "name")] private string $userName,
        #[JsonField(ignore: true)] private string $password,
    ) {}
}

final class Ter
{
    function __construct(
        #[JsonField(fieldName: "name")] #[JsonField(ignore: true)] private string $userName
    ) {}
}

final class EmptyClass {}


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

    public function testSerializeAnnotatedObject()
    {
        $obj = new Baz("pol", "1234");
        $serializer = new JSONSerializer();
        $json = $serializer->serialize($obj);
        $this->assertEquals('{"name":"pol"}', $json);
    }

    public function testRejectMultipleJsonFieldAnotation()
    {
        try {
            $obj = new Ter("dylan");
            $serializer = new JSONSerializer();
            $serializer->serialize($obj);
            $this->fail("Should have thrown JSONException");
        } catch (JSONException $e) {
            $this->assertEquals("Cannot make use of multiple '" . JSONField::class . "' attributes.", $e->getMessage());
        }
    }

    public function testSerializeEmptyArray() {
        $arr = [];
        $serializer = new JSONSerializer();
        $json = $serializer->serialize($arr);
        $this->assertEquals("[]", $json); 
    }

    /*
    public function testSerializeEmptyAssoc() {
        // FIXME: unable to discriminate empty Assoc from empty Array
        // IDEA: forceAssoc attribute in JSONField annotation
        $arr = [];
        $serializer = new JSONSerializer();
        $json = $serializer->serialize($arr);
        $this->assertEquals("{}", $json); 
    }
    */

    public function testSerializeEmptyObject() {
        $obj = new EmptyClass();
        $serializer = new JSONSerializer();
        $json = $serializer->serialize($obj);
        $this->assertEquals("{}", $json); 
    }
}
