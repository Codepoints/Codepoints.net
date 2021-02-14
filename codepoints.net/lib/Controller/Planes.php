<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Unicode\Plane;

require_once __DIR__.'/../view_functions.php';


class Planes extends Controller {

    public function __invoke(string $matches, Array $env) : string {
        $this->context += [
            'title' => __('Unicode Planes'),
            'page_description' => __('Unicode defines 17 planes, in which all the codepoints are separated.'),
            'planes' => Plane::getAll($env['db']),
        ];
        return parent::__invoke($matches, $env);
    }

}
