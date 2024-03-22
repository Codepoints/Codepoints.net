<?php

namespace Codepoints\Controller;


trait TraitPreload {

    private function sendPreloadHeaders(Array $additional=[]) : void {
        $headers = array_merge([
            sprintf('<%s>; rel=preload; as=style; fetchpriority=high', static_url('src/css/main.css')),
            sprintf('<%s>; rel=preload; as=font; fetchpriority=high; crossorigin=anonymous', static_url('src/fonts/Literata.woff2')),
            sprintf('<%s>; rel=preload; as=font; fetchpriority=high; crossorigin=anonymous', static_url('src/fonts/Literata-Italic.woff2')),
            sprintf('<%s>; rel=preload; as=style; fetchpriority=low', static_url('src/css/print.css')),
            /* TODO preloading SVGs for <use> is not yet supported:
             * https://github.com/whatwg/html/issues/8870 */
            # sprintf('<%s>; rel=preload; as=image', static_url('src/images/icons.svg')),
            # sprintf('<%s>; rel=preload; as=image', static_url('src/images/unicode-logo-framed.svg')),
            sprintf('<%s>; rel=modulepreload; as=script', static_url('src/js/main.js')),
        ], $additional);
        header('Link:'.join(',', $headers), false);
        header('Link: <https://stats.codepoints.net>; rel=preconnect', false);
    }

}
