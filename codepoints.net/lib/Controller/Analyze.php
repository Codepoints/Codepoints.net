<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Unicode\Codepoint;
use Codepoints\View;
use Exception;


class Analyze extends Controller {

    public function __invoke($match, Array $env) : string {
        $title = __('Analyze');
        $page_description = __('Look under the hood of a string of text and find out what it is made of.');

        $cps = [];
        $q = (string)filter_input(INPUT_GET, 'q');

        if ($q && strlen($q) <= 256) {
            $cps = string_to_codepoints($q, $env['db']);
        }

        $this->context += [
            'title' => $title,
            'page_description' => $page_description,
            'q' => $q,
            'cps' => $cps,
        ];
        return (new View('analyze'))($this->context, $env);
    }

}
