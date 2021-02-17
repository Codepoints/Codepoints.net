<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Unicode\Codepoint;


class Index extends Controller {

    public function __invoke($match, Array $env) : string {
        $data = $env['db']->getOne('SELECT cp, name, gc
            FROM codepoints
            WHERE cp = 0');
        $cp0 = new Codepoint($data, $env['db']);
        $data = $env['db']->getOne('SELECT COUNT(*) AS c FROM codepoints USE INDEX (PRIMARY)');
        $cp_count = $data['c'];

        $this->context += [
            'title' => __('Find all Unicode characters from Hieroglyphs to Dingbats'),
            'page_description' => __('Codepoints is a site dedicated to Unicode and all things related to codepoints, characters, glyphs and internationalization.'),
            'cp0' => $cp0,
            'cp_count' => $cp_count,
        ];
        return parent::__invoke($match, $env);
    }

}
