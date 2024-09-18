<?php


declare(strict_types=1);

use Phabrique\Core\HttpStatusCode;
use PHPUnit\Framework\TestCase;

final class HttpStatusCodeTest extends TestCase
{
    public function testEnumHasProperStatusTextValues()
    {
        $this->assertEquals("Continue", HttpStatusCode::INFO_CONTINUE->getStatusText());
        $this->assertEquals("Switching Protocols", HttpStatusCode::INFO_SWITCHING_PROTOCOLS->getStatusText());
        $this->assertEquals("Processing", HttpStatusCode::INFO_PROCESSING->getStatusText());
        $this->assertEquals("Early Hints", HttpStatusCode::INFO_EARLY_HINTS->getStatusText());
        $this->assertEquals("OK", HttpStatusCode::OK->getStatusText());
        $this->assertEquals("Created", HttpStatusCode::OK_CREATED->getStatusText());
        $this->assertEquals("Accepted", HttpStatusCode::OK_ACCEPTED->getStatusText());
        $this->assertEquals("Non Authoritative Information", HttpStatusCode::OK_NON_AUTHORITATIVE_INFORMATION->getStatusText());
        $this->assertEquals("No Content", HttpStatusCode::OK_NO_CONTENT->getStatusText());
        $this->assertEquals("Reset Content", HttpStatusCode::OK_RESET_CONTENT->getStatusText());
        $this->assertEquals("Partial Content", HttpStatusCode::OK_PARTIAL_CONTENT->getStatusText());
        $this->assertEquals("Multi Status", HttpStatusCode::OK_MULTI_STATUS->getStatusText());
        $this->assertEquals("Already Reported", HttpStatusCode::OK_ALREADY_REPORTED->getStatusText());
        $this->assertEquals("Im Used", HttpStatusCode::OK_IM_USED->getStatusText());
        $this->assertEquals("Multiple Choices", HttpStatusCode::REDIRECT_MULTIPLE_CHOICES->getStatusText());
        $this->assertEquals("Moved Permanently", HttpStatusCode::REDIRECT_MOVED_PERMANENTLY->getStatusText());
        $this->assertEquals("Found", HttpStatusCode::REDIRECT_FOUND->getStatusText());
        $this->assertEquals("See Other", HttpStatusCode::REDIRECT_SEE_OTHER->getStatusText());
        $this->assertEquals("Not Modified", HttpStatusCode::REDIRECT_NOT_MODIFIED->getStatusText());
        $this->assertEquals("Not Proxy", HttpStatusCode::REDIRECT_NOT_PROXY->getStatusText());
        $this->assertEquals("Unused", HttpStatusCode::REDIRECT_UNUSED->getStatusText());
        $this->assertEquals("Temporary Redirect", HttpStatusCode::REDIRECT_TEMPORARY_REDIRECT->getStatusText());
        $this->assertEquals("Permanent Redirect", HttpStatusCode::REDIRECT_PERMANENT_REDIRECT->getStatusText());
        $this->assertEquals("Bad Request", HttpStatusCode::ERR_BAD_REQUEST->getStatusText());
        $this->assertEquals("Unauthorized", HttpStatusCode::ERR_UNAUTHORIZED->getStatusText());
        $this->assertEquals("Payment Required", HttpStatusCode::ERR_PAYMENT_REQUIRED->getStatusText());
        $this->assertEquals("Forbidden", HttpStatusCode::ERR_FORBIDDEN->getStatusText());
        $this->assertEquals("Not Found", HttpStatusCode::ERR_NOT_FOUND->getStatusText());
        $this->assertEquals("Method Not Allowed", HttpStatusCode::ERR_METHOD_NOT_ALLOWED->getStatusText());
        $this->assertEquals("Not Acceptable", HttpStatusCode::ERR_NOT_ACCEPTABLE->getStatusText());
        $this->assertEquals("Proxy Authentication Required", HttpStatusCode::ERR_PROXY_AUTHENTICATION_REQUIRED->getStatusText());
        $this->assertEquals("Request Timeout", HttpStatusCode::ERR_REQUEST_TIMEOUT->getStatusText());
        $this->assertEquals("Conflict", HttpStatusCode::ERR_CONFLICT->getStatusText());
        $this->assertEquals("Gone", HttpStatusCode::ERR_GONE->getStatusText());
        $this->assertEquals("Length Required", HttpStatusCode::ERR_LENGTH_REQUIRED->getStatusText());
        $this->assertEquals("Precondition Failed", HttpStatusCode::ERR_PRECONDITION_FAILED->getStatusText());
        $this->assertEquals("Payload Too Large", HttpStatusCode::ERR_PAYLOAD_TOO_LARGE->getStatusText());
        $this->assertEquals("Uri Too Long", HttpStatusCode::ERR_URI_TOO_LONG->getStatusText());
        $this->assertEquals("Unsupported Media Type", HttpStatusCode::ERR_UNSUPPORTED_MEDIA_TYPE->getStatusText());
        $this->assertEquals("Range Not Satisfiable", HttpStatusCode::ERR_RANGE_NOT_SATISFIABLE->getStatusText());
        $this->assertEquals("Expectation Failed", HttpStatusCode::ERR_EXPECTATION_FAILED->getStatusText());
        $this->assertEquals("I Am A Teapot", HttpStatusCode::ERR_I_AM_A_TEAPOT->getStatusText());
        $this->assertEquals("Misdirected Request", HttpStatusCode::ERR_MISDIRECTED_REQUEST->getStatusText());
        $this->assertEquals("Unprocessable Content", HttpStatusCode::ERR_UNPROCESSABLE_CONTENT->getStatusText());
        $this->assertEquals("Locked", HttpStatusCode::ERR_LOCKED->getStatusText());
        $this->assertEquals("Failed Dependency", HttpStatusCode::ERR_FAILED_DEPENDENCY->getStatusText());
        $this->assertEquals("Too Early", HttpStatusCode::ERR_TOO_EARLY->getStatusText());
        $this->assertEquals("Upgrade Required", HttpStatusCode::ERR_UPGRADE_REQUIRED->getStatusText());
        $this->assertEquals("Precondition Required", HttpStatusCode::ERR_PRECONDITION_REQUIRED->getStatusText());
        $this->assertEquals("Too Many Requests", HttpStatusCode::ERR_TOO_MANY_REQUESTS->getStatusText());
        $this->assertEquals("Request Header Fields Too Large", HttpStatusCode::ERR_REQUEST_HEADER_FIELDS_TOO_LARGE->getStatusText());
        $this->assertEquals("Unavailable For Legal Reasons", HttpStatusCode::ERR_UNAVAILABLE_FOR_LEGAL_REASONS->getStatusText());
        $this->assertEquals("Internal Server Error", HttpStatusCode::SERR_INTERNAL_SERVER_ERROR->getStatusText());
        $this->assertEquals("Not Implemented", HttpStatusCode::SERR_NOT_IMPLEMENTED->getStatusText());
        $this->assertEquals("Bad Gateway", HttpStatusCode::SERR_BAD_GATEWAY->getStatusText());
        $this->assertEquals("Service Unavailable", HttpStatusCode::SERR_SERVICE_UNAVAILABLE->getStatusText());
        $this->assertEquals("Gateway Timeout", HttpStatusCode::SERR_GATEWAY_TIMEOUT->getStatusText());
        $this->assertEquals("Http Version Not Supported", HttpStatusCode::SERR_HTTP_VERSION_NOT_SUPPORTED->getStatusText());
        $this->assertEquals("Variant Also Negotiates", HttpStatusCode::SERR_VARIANT_ALSO_NEGOTIATES->getStatusText());
        $this->assertEquals("Insufficient Storage", HttpStatusCode::SERR_INSUFFICIENT_STORAGE->getStatusText());
        $this->assertEquals("Loop Detected", HttpStatusCode::SERR_LOOP_DETECTED->getStatusText());
        $this->assertEquals("Not Extended", HttpStatusCode::SERR_NOT_EXTENDED->getStatusText());
        $this->assertEquals("Network Authentication Required", HttpStatusCode::SERR_NETWORK_AUTHENTICATION_REQUIRED->getStatusText());
    }
}
