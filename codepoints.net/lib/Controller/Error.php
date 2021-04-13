<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\View;


/**
 * Show a customized 500 error page
 *
 * This controller takes over, when any other controller throws an uncaught
 * exception. Therefore it should be as simple as possible.
 */
class Error extends Controller {

    public function __invoke($match, Array $env) : string {
        http_response_code(500);

        $title = __('Problem');
        $page_description = __('Unfortunately, there is a problem with this page.');

        $this->context += [
            'title' => $title,
            'page_description' => $page_description,
        ];
        return (new View('500'))($this->context, $env);
    }

}
