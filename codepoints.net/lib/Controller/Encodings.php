<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Unicode\EncodingInfo;


class Encodings extends Controller {

    /**
     * @param string $match
     */
    public function __invoke($match, Array $env) : string {
        $encodings = array_map(/** @param array{"type": string} $enc */ function($enc) {
            $slug = str_replace('enc:', '', $enc['type']);
            return [
                'label' => EncodingInfo::getLabel($slug),
                'slug' => urlencode($slug),
            ];
        }, $env['db']->getAll('SELECT DISTINCT `type`
            FROM codepoint_alias
            WHERE `type` LIKE "enc:%"
            ORDER BY `type`'));

        $this->context += [
            'title' => __('Encodings'),
            'page_description' => __('Browse Codepoints by encoding.'),
            'encodings' => $encodings,
        ];
        return parent::__invoke($match, $env);
    }

}
