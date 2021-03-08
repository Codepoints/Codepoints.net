<?php

namespace Codepoints\Controller;

use Codepoints\Controller;

class Image extends Controller {

    /**
     * @param Array $match
     */
    public function __invoke($match, Array $env) : string {
        $root = hexdec($match[1]);
        header('Content-Type: image/svg+xml');
        // header('Cache-Control: public, max-age=3600');
        // header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60)));
        $images = $env['db']->getAll('SELECT width, height, image
            FROM codepoint_image
            WHERE cp >= ? AND cp < ? + 256', $root, $root);
        return sprintf(
        '<svg xmlns="http://www.w3.org/2000/svg">'.
            '<style>:root svg{opacity:0;height:100%%;width:auto}:root svg:target{opacity:1}</style>'.
            '%s'.
        '</svg>', join('', array_map(function(Array $item) : string { return $item['image']; }, $images)));
    }
}
