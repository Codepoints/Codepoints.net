<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\View;


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
