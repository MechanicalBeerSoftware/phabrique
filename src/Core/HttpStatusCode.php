<?php

namespace Phabrique\Core;

enum HttpStatusCode: int
{
    case INFO_CONTINUE = 100;
    case INFO_SWITCHING_PROTOCOLS = 101;
    case INFO_PROCESSING = 102;
    case INFO_EARLY_HINTS = 103;
    case OK = 200;
    case OK_CREATED = 201;
    case OK_ACCEPTED = 202;
    case OK_NON_AUTHORITATIVE_INFORMATION = 203;
    case OK_NO_CONTENT = 204;
    case OK_RESET_CONTENT = 205;
    case OK_PARTIAL_CONTENT = 206;
    case OK_MULTI_STATUS = 207;
    case OK_ALREADY_REPORTED = 208;
    case OK_IM_USED = 226;
    case REDIRECT_MULTIPLE_CHOICES = 300;
    case REDIRECT_MOVED_PERMANENTLY = 301;
    case REDIRECT_FOUND = 302;
    case REDIRECT_SEE_OTHER = 303;
    case REDIRECT_NOT_MODIFIED = 304;
    case REDIRECT_NOT_PROXY = 305;
    case REDIRECT_UNUSED = 306;
    case REDIRECT_TEMPORARY_REDIRECT = 307;
    case REDIRECT_PERMANENT_REDIRECT = 308;
    case ERR_BAD_REQUEST = 400;
    case ERR_UNAUTHORIZED = 401;
    case ERR_PAYMENT_REQUIRED = 402;
    case ERR_FORBIDDEN = 403;
    case ERR_NOT_FOUND = 404;
    case ERR_METHOD_NOT_ALLOWED = 405;
    case ERR_NOT_ACCEPTABLE = 406;
    case ERR_PROXY_AUTHENTICATION_REQUIRED = 407;
    case ERR_REQUEST_TIMEOUT = 408;
    case ERR_CONFLICT = 409;
    case ERR_GONE = 410;
    case ERR_LENGTH_REQUIRED = 411;
    case ERR_PRECONDITION_FAILED = 412;
    case ERR_PAYLOAD_TOO_LARGE = 413;
    case ERR_URI_TOO_LONG = 414;
    case ERR_UNSUPPORTED_MEDIA_TYPE = 415;
    case ERR_RANGE_NOT_SATISFIABLE = 416;
    case ERR_EXPECTATION_FAILED = 417;
    case ERR_I_AM_A_TEAPOT = 218;
    case ERR_MISDIRECTED_REQUEST = 421;
    case ERR_UNPROCESSABLE_CONTENT = 422;
    case ERR_LOCKED = 423;
    case ERR_FAILED_DEPENDENCY = 424;
    case ERR_TOO_EARLY = 425;
    case ERR_UPGRADE_REQUIRED = 426;
    case ERR_PRECONDITION_REQUIRED = 428;
    case ERR_TOO_MANY_REQUESTS = 429;
    case ERR_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    case ERR_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    case SERR_INTERNAL_SERVER_ERROR = 500;
    case SERR_NOT_IMPLEMENTED = 501;
    case SERR_BAD_GATEWAY = 502;
    case SERR_SERVICE_UNAVAILABLE = 503;
    case SERR_GATEWAY_TIMEOUT = 504;
    case SERR_HTTP_VERSION_NOT_SUPPORTED = 505;
    case SERR_VARIANT_ALSO_NEGOTIATES = 506;
    case SERR_INSUFFICIENT_STORAGE = 507;
    case SERR_LOOP_DETECTED = 508;
    case SERR_NOT_EXTENDED = 510;
    case SERR_NETWORK_AUTHENTICATION_REQUIRED = 511;


    public function getStatusText(): string
    {
        return match ($this) {
            HttpStatusCode::INFO_CONTINUE => "Continue",
            HttpStatusCode::INFO_SWITCHING_PROTOCOLS => "Switching Protocols",
            HttpStatusCode::INFO_PROCESSING => "Processing",
            HttpStatusCode::INFO_EARLY_HINTS => "Early Hints",
            HttpStatusCode::OK => "OK",
            HttpStatusCode::OK_CREATED => "Created",
            HttpStatusCode::OK_ACCEPTED => "Accepted",
            HttpStatusCode::OK_NON_AUTHORITATIVE_INFORMATION => "Non Authoritative Information",
            HttpStatusCode::OK_NO_CONTENT => "No Content",
            HttpStatusCode::OK_RESET_CONTENT => "Reset Content",
            HttpStatusCode::OK_PARTIAL_CONTENT => "Partial Content",
            HttpStatusCode::OK_MULTI_STATUS => "Multi Status",
            HttpStatusCode::OK_ALREADY_REPORTED => "Already Reported",
            HttpStatusCode::OK_IM_USED => "Im Used",
            HttpStatusCode::REDIRECT_MULTIPLE_CHOICES => "Multiple Choices",
            HttpStatusCode::REDIRECT_MOVED_PERMANENTLY => "Moved Permanently",
            HttpStatusCode::REDIRECT_FOUND => "Found",
            HttpStatusCode::REDIRECT_SEE_OTHER => "See Other",
            HttpStatusCode::REDIRECT_NOT_MODIFIED => "Not Modified",
            HttpStatusCode::REDIRECT_NOT_PROXY => "Not Proxy",
            HttpStatusCode::REDIRECT_UNUSED => "Unused",
            HttpStatusCode::REDIRECT_TEMPORARY_REDIRECT => "Temporary Redirect",
            HttpStatusCode::REDIRECT_PERMANENT_REDIRECT => "Permanent Redirect",
            HttpStatusCode::ERR_BAD_REQUEST => "Bad Request",
            HttpStatusCode::ERR_UNAUTHORIZED => "Unauthorized",
            HttpStatusCode::ERR_PAYMENT_REQUIRED => "Payment Required",
            HttpStatusCode::ERR_FORBIDDEN => "Forbidden",
            HttpStatusCode::ERR_NOT_FOUND => "Not Found",
            HttpStatusCode::ERR_METHOD_NOT_ALLOWED => "Method Not Allowed",
            HttpStatusCode::ERR_NOT_ACCEPTABLE => "Not Acceptable",
            HttpStatusCode::ERR_PROXY_AUTHENTICATION_REQUIRED => "Proxy Authentication Required",
            HttpStatusCode::ERR_REQUEST_TIMEOUT => "Request Timeout",
            HttpStatusCode::ERR_CONFLICT => "Conflict",
            HttpStatusCode::ERR_GONE => "Gone",
            HttpStatusCode::ERR_LENGTH_REQUIRED => "Length Required",
            HttpStatusCode::ERR_PRECONDITION_FAILED => "Precondition Failed",
            HttpStatusCode::ERR_PAYLOAD_TOO_LARGE => "Payload Too Large",
            HttpStatusCode::ERR_URI_TOO_LONG => "Uri Too Long",
            HttpStatusCode::ERR_UNSUPPORTED_MEDIA_TYPE => "Unsupported Media Type",
            HttpStatusCode::ERR_RANGE_NOT_SATISFIABLE => "Range Not Satisfiable",
            HttpStatusCode::ERR_EXPECTATION_FAILED => "Expectation Failed",
            HttpStatusCode::ERR_I_AM_A_TEAPOT => "I Am A Teapot",
            HttpStatusCode::ERR_MISDIRECTED_REQUEST => "Misdirected Request",
            HttpStatusCode::ERR_UNPROCESSABLE_CONTENT => "Unprocessable Content",
            HttpStatusCode::ERR_LOCKED => "Locked",
            HttpStatusCode::ERR_FAILED_DEPENDENCY => "Failed Dependency",
            HttpStatusCode::ERR_TOO_EARLY => "Too Early",
            HttpStatusCode::ERR_UPGRADE_REQUIRED => "Upgrade Required",
            HttpStatusCode::ERR_PRECONDITION_REQUIRED => "Precondition Required",
            HttpStatusCode::ERR_TOO_MANY_REQUESTS => "Too Many Requests",
            HttpStatusCode::ERR_REQUEST_HEADER_FIELDS_TOO_LARGE => "Request Header Fields Too Large",
            HttpStatusCode::ERR_UNAVAILABLE_FOR_LEGAL_REASONS => "Unavailable For Legal Reasons",
            HttpStatusCode::SERR_INTERNAL_SERVER_ERROR => "Internal Server Error",
            HttpStatusCode::SERR_NOT_IMPLEMENTED => "Not Implemented",
            HttpStatusCode::SERR_BAD_GATEWAY => "Bad Gateway",
            HttpStatusCode::SERR_SERVICE_UNAVAILABLE => "Service Unavailable",
            HttpStatusCode::SERR_GATEWAY_TIMEOUT => "Gateway Timeout",
            HttpStatusCode::SERR_HTTP_VERSION_NOT_SUPPORTED => "Http Version Not Supported",
            HttpStatusCode::SERR_VARIANT_ALSO_NEGOTIATES => "Variant Also Negotiates",
            HttpStatusCode::SERR_INSUFFICIENT_STORAGE => "Insufficient Storage",
            HttpStatusCode::SERR_LOOP_DETECTED => "Loop Detected",
            HttpStatusCode::SERR_NOT_EXTENDED => "Not Extended",
            HttpStatusCode::SERR_NETWORK_AUTHENTICATION_REQUIRED => "Network Authentication Required",
        };
    }
}
