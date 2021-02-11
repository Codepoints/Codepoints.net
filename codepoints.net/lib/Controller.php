<?php

namespace Codepoints;

use Codepoints\View;


/**
 * request/response controller
 */
class Controller {

    protected Array $context = [];

    /**
     * if called, automatically show a view with the same name
     */
    public function __invoke($match, Array $env) : string {
        $view = strtolower(preg_replace('/.*\\\\/', '', get_class($this)));
        return (new View($view))($this->context + [
            'match' => $match,
        ], $env);
    }

}
