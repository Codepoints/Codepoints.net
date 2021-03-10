<?php

namespace Codepoints\Api;

use Exception as BaseException;

class Exception extends BaseException {

    public const BAD_REQUEST = 400;
    public const NOT_FOUND = 404;
    public const METHOD_NOT_ALLOWED = 405;
    public const PRECONDITION_FAILED = 412;
    public const REQUEST_ENTITY_TOO_LARGE = 413;
    public const REQUEST_URI_TOO_LONG = 414;
    public const INTERNAL_SERVER_ERROR = 500;
    public const NOT_IMPLEMENTED = 501;

    /**
     * @param string $message the error payload
     * @param int $code the HTTP error code 4XX/5XX
     */
    public function __construct(string $message, int $code = 500, BaseException $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}
