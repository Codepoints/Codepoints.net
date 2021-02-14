<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Unicode\Plane;


class Planes extends Controller {

    /**
     * @param string $match
     */
    public function __invoke($match, Array $env) : string {
        $this->context += [
            'title' => __('Unicode Planes'),
            'page_description' => __('Unicode defines 17 planes, in which all the codepoints are separated.'),
            'planes' => Plane::getAll($env['db']),
        ];
        return parent::__invoke($match, $env);
    }

}
