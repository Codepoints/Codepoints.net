<?php

namespace Codepoints\Controller;

use Codepoints\Controller;

class Image extends Controller {

    /**
     * @param Array $match
     */
    public function __invoke($match, Array $env) : string {
        $cache_duration = DEBUG? 10 : 24*60*60;
        $root = hexdec($match[1]);
        $canonical = str_pad(strtoupper(dechex($root)), 4, '0', STR_PAD_LEFT);
        header('Content-Type: image/svg+xml');
        header('Cache-Control: public, max-age='.$cache_duration);
        header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + $cache_duration));
        $images = $env['db']->getAll('SELECT width, height, image
            FROM codepoint_image
            WHERE cp >= ? AND cp < ? + 256', $root, $root);
        $svg = sprintf(
        '<svg xmlns="http://www.w3.org/2000/svg">'.
            '<style>:root svg{opacity:0;height:100%%;width:auto}:root svg:target{opacity:1}</style>'.
            '%s'.
            '</svg>', join('', array_map(function(Array $image) : string { return $image['image']; }, $images)));
        /* let the web server take over by storing already created images in
         * their place in the docroot */
        file_put_contents(
            dirname(dirname(__DIR__)).'/image/'.$canonical.'.svg',
            $svg);
        return $svg;
    }
}
