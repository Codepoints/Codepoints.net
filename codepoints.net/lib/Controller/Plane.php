<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Unicode\Plane as UnicodePlane;


class Plane extends Controller {

    public function __invoke($match, array $env) : string {
        $data = $env['db']->getOne("
            SELECT name, first, last FROM planes
            WHERE replace(replace(lower(name), '_', ''), ' ', '') = ?
            LIMIT 1", str_replace([' ', '_'], '', strtolower($match[1])));
        if (! $data) {
            throw new Exception('no plane found');
        }
        $plane = new UnicodePlane($data, $env['db']);
        $this->context += [
            'title' => $plane->name,
            'page_description' => sprintf(
                __('The Unicode plane %s contains %s blocks and spans codepoints from U+%04X to U+%04X.'),
                $plane->name, count($plane->blocks), $plane->first, $plane->last),
            'plane' => $plane,
        ];
        return parent::__invoke($matches, $env);
    }

}
