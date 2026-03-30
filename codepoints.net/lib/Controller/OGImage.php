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
        header('Content-Type: image/png');
        /* in the error case we send a 1x1 black pixel PNG w/o caching headers */

        /** @var Array{cp: int, gc: string, name: string}|false */
        $cp_data = $env['db']->getOne('SELECT cp, gc, name
            FROM codepoints
            WHERE cp = ?', hexdec($match[1]));
        if (! $cp_data) {
            http_response_code(404);
            return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQAAAAA3bvkkAAAACklEQVR4AWNgAAAAAgABc3UBGAAAAABJRU5ErkJggg==');
        }
        $cp = Codepoint::getCached($cp_data, $env['db']);

        /** @var Array{width: int, height: int, image: string}|false */
        $dbimage = $env['db']->getOne('SELECT width, height, image
            FROM codepoint_image
            WHERE cp = ?', get_printable_codepoint($cp->id, $cp->gc));
        if (! $dbimage) {
            http_response_code(404);
            return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQAAAAA3bvkkAAAACklEQVR4AWNgAAAAAgABc3UBGAAAAABJRU5ErkJggg==');
        }
        $svg = (string)preg_replace(
            '/viewBox="[^"]+"/',
            sprintf('xmlns="http://www.w3.org/2000/svg" viewBox="-50 0 %s %s"', $dbimage['width'] + 100, $dbimage['height'] + 100),
            $dbimage['image'],
            1);
        $svg = (string)preg_replace_callback(
            '/<svg id="([^"]+(hk|jp|kr|sc|tc))" viewBox="([^"]+)"/',
            function($matches) use ($dbimage) {
                $xy = match($matches[2]) {
                    'sc' => [                  0.0,                    0.0],
                    'tc' => [                  0.0, $dbimage['height'] / 2],
                    'jp' => [$dbimage['width'] / 2,                    0.0],
                    'hk' => [$dbimage['width'] / 2, $dbimage['height'] / 2],
                    'kr' => [$dbimage['width'] / 2, $dbimage['height'] / 2],
                    default => [               0.0,                    0.0],
                };
                return sprintf('<text x="%s" y="%s" text-anchor="end" font-size="90" fill-opacity=".3333">%s</text><svg id="%s" width="%s" height="%s" transform="translate(%s, %s)" viewBox="%s"',
                    (float)$xy[0] + 500.0,
                    (float)$xy[1] + 100.0,
                    strtoupper($matches[2]),
                    $matches[1],
                    $dbimage['width'] / 2,
                    $dbimage['height'] / 2,
                    $xy[0],
                    $xy[1],
                    $matches[3]);
            },
            $svg);
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

        /* send an expiry of 1 year. This mirrors the Apache config for the
         * cached files. */
        $cache_duration = 365*24*60*60;
        header('Cache-Control: public, max-age='.$cache_duration);
        header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + $cache_duration));
        return $img->getImageBlob();
    }
}
