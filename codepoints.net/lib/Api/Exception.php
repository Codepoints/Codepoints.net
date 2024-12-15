<?php

namespace Codepoints\Api;

use Exception as BaseException;

class Exception extends BaseException {

    public const int BAD_REQUEST = 400;
    public const int NOT_FOUND = 404;
    public const int METHOD_NOT_ALLOWED = 405;
    public const int PRECONDITION_FAILED = 412;
    public const int REQUEST_ENTITY_TOO_LARGE = 413;
    public const int REQUEST_URI_TOO_LONG = 414;
    public const int INTERNAL_SERVER_ERROR = 500;
    public const int NOT_IMPLEMENTED = 501;

    /**
     * @param string $message the error payload
     * @param int $code the HTTP error code 4XX/5XX
     */
    public function __construct(string $message, int $code = 500, BaseException $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}
