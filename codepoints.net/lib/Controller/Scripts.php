<?php

namespace Codepoints\Controller;

use Codepoints\Controller;


class Scripts extends Controller {

    /**
     * @param string $match
     */
    public function __invoke($match, Array $env) : string {
        $scripts = $env['db']->getAll('SELECT iso, name, abstract, src,
            (SELECT COUNT(*) FROM codepoint_script
            WHERE codepoint_script.sc = scripts.iso) count
            FROM scripts
            LEFT JOIN script_abstract ON (sc = iso AND lang = ?)', $env['lang']);

        $this->context += [
            'title' => __('Scripts'),
            'page_description' => __('Browse Codepoints by script. “Scripts” are the different '.
                'writing systems. They are presented geographically, so that '.
                'you can identify quickly interesting ones.'),
            'scripts' => $scripts,
        ];
        return parent::__invoke($match, $env);
    }

}
