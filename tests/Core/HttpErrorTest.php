<?php

declare(strict_types=1);

use Phabrique\Core\HttpError;
use Phabrique\Core\HttpStatusCode;
use PHPUnit\Framework\TestCase;

final class HttpErrorTest extends TestCase {

    public function testErrorHasProperStatusCodeWhenThrown() {
        try {
            throw new HttpError(HttpStatusCode::ERR_BAD_REQUEST, "Bad Request", "Unable to process your request");
        } catch(HttpError $err) {
            $this->assertEquals(HttpStatusCode::ERR_BAD_REQUEST, $err->getStatusCode());
        }
    }

    public function testHasProperStatusTextWhenThrown() {
        try {
            throw new HttpError(HttpStatusCode::ERR_BAD_REQUEST, "Bad Request", "Unable to process your request");
        } catch(HttpError $err) {
            $this->assertEquals("Bad Request", $err->getStatusText());
        }
    }


    public function testHasProperMessageWhenThrown() {
        try {
            throw new HttpError(HttpStatusCode::ERR_BAD_REQUEST, "Bad Request", "Unable to process your request");
        } catch(HttpError $err) {
            $this->assertEquals("Unable to process your request", $err->getMessage());
        }
    }
}
