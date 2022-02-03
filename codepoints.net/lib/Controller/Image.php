<?php

namespace Codepoints\Controller;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use Codepoints\Controller;

class Image extends Controller {

    /**
     * @param Array $match
     */
    public function __invoke($match, Array $env) : string {
        /* note, that users may see twice this value in the worst case. That
         * is, a cached entry shortly before going stale but an Expires header,
         * that starts fresh. */
        $cache_duration = DEBUG? 10 : 24*60*60;
        $root = hexdec($match[1]);
        header('Content-Type: image/svg+xml');
        header('Cache-Control: public, max-age='.$cache_duration);
        header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + $cache_duration));
        $cache = new FilesystemAdapter('codepts');
        return $cache->get('controller_image_'.dechex($root), function (ItemInterface $item) use($env, $root, $cache_duration) {
            $item->expiresAfter($cache_duration);
            $images = $env['db']->getAll('SELECT width, height, image
                FROM codepoint_image
                WHERE cp >= ? AND cp < ? + 256', $root, $root);
            return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg">'.
                '<style>:root svg{opacity:0;height:100%%;width:auto}:root svg:target{opacity:1}</style>'.
                '%s'.
                '</svg>', join('', array_map(function(Array $image) : string { return $image['image']; }, $images)));
        });
    }
}
