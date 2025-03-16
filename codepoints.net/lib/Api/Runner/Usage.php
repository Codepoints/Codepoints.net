<?php

namespace Codepoints\Api\Runner;

use Codepoints\Api\Runner;
use Codepoints\View;


class Usage extends Runner {

    public function handle(string $data) : string {
        if (isset($_SERVER['HTTP_ACCEPT']) &&
            strpos($_SERVER['HTTP_ACCEPT'], 'text/html') !== false) {
            return file_get_contents(dirname(dirname(dirname(__DIR__))).'/views/api.html');
        }

        $host = 'https://codepoints.net/api/v1';

        header('Content-Type: application/json; charset=utf-8');
        return (string)json_encode([
            'description' => __('Welcome to codepoint.net’s Unicode API. Most requests are JSON-based. The API supports for those requests JSONP via the `callback` GET parameter. The `glyph` and `property` methods return PNG images. When problems appear, the API response conforms to <https://tools.ietf.org/html/draft-ietf-appsawg-http-problem-02>. Documentation is published at <https://github.com/Codepoints/codepoints.net/wiki/API>.'),
            'codepoint_url' => "$host/codepoint/{codepoint}{?property*}{?callback}",
            'block_url' => "$host/block/{block}{?callback}",
            'plane_url' => "$host/plane/{plane}{?callback}",
            'glyph_url' => "$host/glyph/{codepoint}",
            'name_url' => "$host/name/{codepoint}{?callback}",
            'transform_url' => "$host/transform/{action}/{data}{?callback}",
            'filter_url' => "$host/filter/{data}{?property*}{?callback}",
            'property_url' => "$host/property/{property}",
            'script_url' => "$host/script/{iso}{?callback}",
            'search_url' => "$host/search{?property}{&page}{&per_page}{&callback}",
            'patterns' => [
                'codepoint' => '(10)?[A-Fa-f0-9]{4}|0[A-Fa-f0-9]{5}',
                'data' => '.{1,1024}',
                'iso' => '[A-Z][a-z]{3}',
                'page' => '[1-9][0-9]*',
            ],
        ]);
    }

}
