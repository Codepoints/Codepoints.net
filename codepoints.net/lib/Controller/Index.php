<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Controller\TraitPreload;
use Codepoints\Unicode\Codepoint;
use Codepoints\View;


class Index extends Controller {

    use TraitPreload;

    public function __invoke($match, Array $env) : string {
        $this->sendPreloadHeaders([
            sprintf('<%s>; rel=preload; as=image; fetchpriority=high; type=image/webp; media=(prefers-color-scheme: light)', static_url('src/images/front_light.webp')),
            sprintf('<%s>; rel=preload; as=image; fetchpriority=high; type=image/webp; media=(prefers-color-scheme: dark)', static_url('src/images/front_dark.webp')),
        ]);
        /** @var array */
        $data = $env['db']->getOne('SELECT cp, name, gc
            FROM codepoints
            WHERE cp = 0');
        $cp0 = Codepoint::getCached($data, $env['db']);
        /** @var array{c: string} */
        $data = $env['db']->getOne('SELECT COUNT(*) AS c FROM codepoints USE INDEX (PRIMARY)');
        $cp_count = $data['c'];

        $this->context += [
            'title' => __('Find all Unicode characters from Hieroglyphs to Dingbats'),
            'page_description' => __('Codepoints is a site dedicated to Unicode and all things related to codepoints, characters, glyphs and internationalization.'),
            'cp0' => $cp0,
            'cp_count' => $cp_count,
            'most_popular' => array_slice(\get_popular_codepoints($env['db']), 0, 20),
        ];
        return parent::__invoke($match, $env);
    }

}
