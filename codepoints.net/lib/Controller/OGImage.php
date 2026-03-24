<?php

namespace Codepoints\Controller;

use Imagick;
use ImagickDraw;
use ImagickPixel;
use Codepoints\Controller;
use Codepoints\Unicode\Codepoint;
use Codepoints\Unicode\CodepointInfo\SENSITIVITY_LEVEL;

final class OGImage extends Controller {

    /**
     * @param Array $match
     */
    #[\Override]
    public function __invoke($match, Array $env) : string {
        $cp = Codepoint::getCached($env['db']->getOne('SELECT cp, gc, name
            FROM codepoints
            WHERE cp = ?', hexdec($match[1])), $env['db']);

        /* send an expiry of 1 year. This mirrors the Apache config for the
         * cached files. */
        $cache_duration = 365*24*60*60;
        header('Content-Type: image/png');
        header('Cache-Control: public, max-age='.$cache_duration);
        header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + $cache_duration));

        /** @var ?Array{width: int, height: int, image: string} */
        $dbimage = $env['db']->getOne('SELECT width, height, image
            FROM codepoint_image
            WHERE cp = ?', get_printable_codepoint($cp->id, $cp->gc));
        if (! $dbimage) {
            http_response_code(404);
            return '';
        }
        $svg = (string)preg_replace(
            '/viewBox="[^"]+"/',
            sprintf('viewBox="-50 0 %s %s"', $dbimage['width'] + 100, $dbimage['height'] + 100),
            $dbimage['image']);
        $ratio = ($dbimage['width'] + 100) / ($dbimage['height'] + 100);

        $img = new Imagick();
        $img->setSize(1200, 630);
        $img->readImage('static/images/og-image.png');

        $svgimg = new Imagick();
        $svgimg->setBackgroundColor('transparent');
        $svgimg->readImageBlob($svg);
        $svgimg->setImageFormat('png24');
        if ($cp->sensitivity->value !== SENSITIVITY_LEVEL::NORMAL->value) {
            $blur = match($cp->sensitivity->value) {
                SENSITIVITY_LEVEL::RAISED->value => 50,
                SENSITIVITY_LEVEL::HIGH->value => 80,
                SENSITIVITY_LEVEL::MAX->value => 100,
                default => 0,
            };
            $svgimg->blurImage($blur, $blur);
        }
        /** @psalm-suppress InvalidOperand */
        $svgimg->resizeImage((int)ceil(min(400.0, 400.0 * $ratio)), (int)ceil(min(400.0, 400.0 / $ratio)), imagick::FILTER_LANCZOS, 1);
        $img->compositeImage($svgimg, Imagick::COMPOSITE_DEFAULT, 50 + (int)((400 - $svgimg->getImageWidth()) / 2), 50);

        $name = case_cp_name($cp->name);
        $draw = new ImagickDraw();
        $draw->setFillColor('#1c1917');
        $draw->setFont(dirname(dirname(__DIR__)).static_url('src/fonts/Literata.woff2'));

        $draw->setFontSize(100);
        $img->annotateImage($draw, 500, 300, 0, sprintf('U+%04X', $cp->id));

        $draw->setFontSize(strlen($name) < 34? 60 : 40);
        $img->annotateImage($draw, 50, 530, 0, $name);

        $draw->setFillColor('rgb(95, 35, 40)');
        $draw->setFontSize(30);
        $img->annotateImage($draw, 50, 590, 0, sprintf('Read more on codepoints.net/U+%04X', $cp->id));

        /* let the web server take over by storing already created images in
         * their place in the docroot */
        $canonical = str_pad(strtoupper(dechex($cp->id)), 4, '0', STR_PAD_LEFT);
        $img->writeImage(dirname(dirname(__DIR__)).'/image/og-'.$canonical.'.png');
        return $img->getImageBlob();
    }
}
