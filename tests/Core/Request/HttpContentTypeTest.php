<?php

declare(strict_types=1);

use Phabrique\Core\Request\HttpContentType;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\After;

class HttpContentTypeTest extends TestCase
{
    public function testSimpleContentTypeHasProperAttributes(): void {
        $rawContentType = 'application/json';

        $contentType = new HttpContentType($rawContentType);

        $this->assertEquals('application/json', $contentType->getName());
    }

    public function testComplexContentTypeHasProperAttributes(): void {
        $rawContentType = 'multipart/form-data; charset=UTF-8; boundary=----------------------1234567890';

        $contentType = new HttpContentType($rawContentType);
        $contentTypeParameters = $contentType->getParameters();

        $this->assertEquals('multipart/form-data', $contentType->getName());
        $this->assertEquals(2, count($contentTypeParameters));

        $this->assertEquals('----------------------1234567890', $contentTypeParameters['boundary']);
        $this->assertEquals('UTF-8', $contentTypeParameters['charset']);
    }
}
