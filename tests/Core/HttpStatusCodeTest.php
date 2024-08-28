<?php


declare(strict_types=1);

use Phabrique\Core\HttpStatusCode;
use PHPUnit\Framework\TestCase;

final class HttpStatusCodeTest extends TestCase
{
    public function testEnumHasProperStatusTextValues()
    {
        $this->assertEquals("OK", HttpStatusCode::OK->getStatusText());
        $this->assertEquals("Created", HttpStatusCode::OK_CREATED->getStatusText());
        $this->assertEquals("No Content", HttpStatusCode::OK_NO_CONTENT->getStatusText());
        $this->assertEquals("Bad Request", HttpStatusCode::ERR_BAD_REQUEST->getStatusText());
        $this->assertEquals("Unauthorized", HttpStatusCode::ERR_UNAUTHORIZED->getStatusText());
        $this->assertEquals("Forbidden", HttpStatusCode::ERR_FORBIDDEN->getStatusText());
        $this->assertEquals("Not Found", HttpStatusCode::ERR_NOT_FOUND->getStatusText());
        $this->assertEquals("Method Not Allowed", HttpStatusCode::ERR_METHOD_NOT_ALLOWED->getStatusText());
        $this->assertEquals("Internal Server Error", HttpStatusCode::SERVER_ERROR->getStatusText());
    }
}
