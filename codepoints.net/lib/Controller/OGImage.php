<?php

namespace Codepoints\Controller;

use Imagick;
use ImagickDraw;
use ImagickPixel;
use Codepoints\Controller;

final class OGImage extends Controller {

    /**
     * @param Array $match
     */
    #[\Override]
    public function __invoke($match, Array $env) : string {
        /* send an expiry of 1 year. This mirrors the Apache config for the
         * cached files. */
        $cache_duration = 365*24*60*60;
        $root = hexdec($match[1]);
        $info = $env['db']->getOne('SELECT gc, name
            FROM codepoints
            WHERE cp = ?', $root);
        $canonical = str_pad(strtoupper(dechex($root)), 4, '0', STR_PAD_LEFT);
        header('Content-Type: image/png');
        header('Cache-Control: public, max-age='.$cache_duration);
        header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + $cache_duration));
        $dbimage = $env['db']->getOne('SELECT width, height, image
            FROM codepoint_image
            WHERE cp = ?', get_printable_codepoint($root, $info['gc']));
        if (! $dbimage) {
            http_response_code(404);
            return '';
        }
        $svg = sprintf(
        '<svg xmlns="http://www.w3.org/2000/svg" width="%s" height="%s" viewBox="0 0 1000 1000">'.
            '<style>:root svg{opacity:0;height:100%%;width:auto}:root svg:target{opacity:1}</style>'.
            '%s'.
            '</svg>', $dbimage['width'], $dbimage['height'], $dbimage['image']);

        $img = new Imagick();
        $img->setSize(1200, 630);
        $img->readImage('static/images/og-image.png');

        $svgimg = new Imagick();
        $svgimg->setBackgroundColor('transparent');
        $svgimg->readImageBlob($svg);
        $svgimg->setImageFormat('png24');
        $svgimg->resizeImage(250, 250, imagick::FILTER_LANCZOS, 1);
        $img->compositeImage($svgimg, Imagick::COMPOSITE_DEFAULT, 100 + (250 - $svgimg->getImageWidth()) / 2, 100);

        $name = case_cp_name($info['name']);
        $draw = new ImagickDraw();
        $draw->setFillColor('#1c1917');
        $draw->setFont(dirname(dirname(__DIR__)).static_url('src/fonts/Literata.woff2'));

        $draw->setFontSize(100);
        $img->annotateImage($draw, 400, 350, 0, sprintf('U+%04X', $root));

        $draw->setFontSize(strlen($name) < 34? 60 : 40);
        $img->annotateImage($draw, 75, 500, 0, $name);

        $draw->setFillColor('rgb(95, 35, 40)');
        $draw->setFontSize(30);
        $img->annotateImage($draw, 75, 570, 0, sprintf('Read more on codepoints.net/U+%04X', $root));

        /* let the web server take over by storing already created images in
         * their place in the docroot */
        $img->writeImage(dirname(dirname(__DIR__)).'/image/og-'.$canonical.'.png');
        return $img->getImageBlob();
    }
}
