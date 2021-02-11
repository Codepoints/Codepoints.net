<?php

use \Codepoints\Router;
use \Codepoints\URLMatcher;
use \Codepoints\Controller\Index;
use \Codepoints\Controller\Plane;
use \Codepoints\Controller\Planes;
use \Codepoints\Controller\Random;
use \Codepoints\Controller\Codepoint;


Router::add('', new Index());

Router::add('planes', new Planes());

Router::add(new URLMatcher('plane/([a-zA-Z0-9()_-]+)$'), new Plane());

Router::add('random', new Random());

Router::add(new URLMatcher('U\\+([0-9A-F]{4,6})$'), new Codepoint());


Router::add(new URLMatcher('image/([0-9A-F]{2,4}00).svg$'), function(Array $match, Array $env) : string {
    $root = hexdec($match[1]);
    header('Content-Type: image/svg+xml');
    // header('Cache-Control: public, max-age=3600');
    // header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60)));
    $images = $env['db']->getAll('SELECT width, height, image
        FROM codepoint_image
        WHERE cp >= ? AND cp < ? + 256', $root, $root);
    return sprintf(
    '<svg xmlns="http://www.w3.org/2000/svg">'.
        '<style>:root svg{display:none;height:100%%;width:auto}:root svg:target{display:inline}</style>'.
        '%s'.
    '</svg>', join('', array_map(function(Array $item) : string { return $item['image']; }, $images)));
});
