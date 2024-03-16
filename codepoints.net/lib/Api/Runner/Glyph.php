<?php

namespace Codepoints\Api\Runner;

use Codepoints\Api\Runner;
use Codepoints\Api\Exception as ApiException;


class Glyph extends Runner {

    /**
     * return an example glyph for the requested code point
     */
    public function handle(string $data) : string {
        if (! $data || strlen($data) > 6 || ctype_xdigit($data) === false) {
            throw new ApiException(__('No codepoint'), ApiException::BAD_REQUEST);
        }

        $cp = get_codepoint(hexdec($data), $this->env['db']);
        if (! $cp) {
            throw new ApiException(__('Not a codepoint'), ApiException::NOT_FOUND);
        }

        $img = $this->env['db']->getOne('
            SELECT image
            FROM codepoint_image
            WHERE cp = ?
            LIMIT 1', $cp->id);

        if ($img) {
            /* extend caching to 1 week */
            header('Cache-Control: public, max-age=604800');
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 604800) . ' GMT');
            header('Content-Type: image/svg+xml');
            if (strpos($img['image'], 'xmlns="http://www.w3.org/2000/svg"') === false) {
                $img['image'] = preg_replace('/^<svg /', '<svg xmlns="http://www.w3.org/2000/svg" ', $img['image']);
            }
            return $img['image'];
        } else {
            throw new ApiException(__('No image found'), ApiException::NOT_FOUND);
        }
    }

}
