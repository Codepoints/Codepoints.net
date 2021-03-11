<?php

namespace Codepoints\Api\Runner;

use Codepoints\Api\Runner;
use Codepoints\Api\Exception as ApiException;


class Name extends Runner {

    /**
     * return the name of the given code point
     *
     * The default content type is JSON. If the client sends an Accept header
     * without JSON suppoert (e.g., directly in the browser), the response
     * will be plain text.
     */
    public function handle(string $data) : string {
        $mime = 'application/json';
        if (isset($_SERVER['HTTP_ACCEPT']) &&
            strpos($_SERVER['HTTP_ACCEPT'], 'application/json') === false) {
            /* for non-JSON requests return plain text */
            $mime = 'text/plain';
        }
        header(sprintf('Content-Type: %s;charset=utf-8', $mime));

        if (! $data || strlen($data) > 6 || ctype_xdigit($data) === false) {
            throw new ApiException(__('No codepoint'), ApiException::BAD_REQUEST);
        }

        $cp = get_codepoint(hexdec($data), $this->env['db']);
        if (! $cp) {
            throw new ApiException(__('Not a codepoint'), ApiException::NOT_FOUND);
        }

        return $mime === 'text/plain'? $cp->name : json_encode($cp->name);
    }

}
