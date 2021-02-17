<?php

namespace Codepoints\Router;

use Exception;

class Redirect extends Exception {

    /**
     * @param string $message the location to redirect to
     * @param int $code the HTTP redirect code 3XX
     */
    public function __construct(string $message, int $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}
