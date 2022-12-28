<?php

namespace Codepoints\Api\Runner;

use Codepoints\Api\Runner;
use Codepoints\Api\Exception as ApiException;


class Oembed extends Runner {

    public function handle(string $data) : string {
        if (! isset($_GET['url']) || ! $_GET['url'] || is_array($_GET['url'])) {
            header('Content-Type: application/json; charset=utf-8');
            return json_encode([
                'description' => __('oEmbed API endpoint for URLs matching “codepoints.net”'),
                'oembed_url' => 'https://codepoints.net/api/v1/oembed{?url}{?format*}{?maxwidth*}{?maxheight*}',
                'url' => 'https://codepoints.net/*',
                'format' => ['xml', 'json'],
                'maxwidth' => 'integer',
                'maxheight' => 'integer',
            ]);
        }

        $format = 'json';
        if (isset($_GET['format'])) {
            if ($_GET['format'] === 'xml') {
                $format = 'xml';
            } elseif ($_GET['format'] !== 'json') {
                throw new ApiException(__('Unknown format parameter'), ApiException::NOT_IMPLEMENTED);
            }
        }

        $maxwidth = 640;
        if (isset($_GET['maxwidth']) && is_string($_GET['maxwidth']) && ctype_digit($_GET['maxwidth'])) {
            $maxwidth = max(26, intval($_GET['maxwidth']));
        }

        $maxheight = 640;
        if (isset($_GET['maxheight']) && is_string($_GET['maxheight']) && ctype_digit($_GET['maxheight'])) {
            $maxheight = max(40, intval($_GET['maxheight']));
        }

        $url = $_GET['url'];
        if (! preg_match('/^https?:\/\/(www\.)?codepoints\.[a-z]+\//', $url)) {
            throw new ApiException(__('Invalid URL'), ApiException::NOT_FOUND);
        }

        $path = preg_replace('/^https?:\/\/(www\.)?codepoints\.[a-z]+\//', '', $url);
        if (preg_match('/^[Uu](?:\\+| |%20)([A-Fa-f0-9]{1,6})$/', $path, $matches)) {
            $dec = hexdec($matches[1]);
        } elseif (mb_strlen($path, 'UTF-8') === 1) {
            $dec = unpack('N', mb_convert_encoding($path, 'UCS-4BE', 'UTF-8'))[1];
        } else {
            throw new ApiException(__('URL path must be single character (UTF-8 encoded) or match /U+[A-F0-9]{4,6}/.'), ApiException::NOT_FOUND);
        }

        $cp = get_codepoint($dec, $this->env['db']);
        if (! $cp) {
            throw new ApiException(__('Not a valid codepoint URL'), ApiException::NOT_FOUND);
        }

        header(sprintf('Link: <https://codepoints.net/U+%04X>; rel=alternate', $cp->id), false);

        $data = [
            'type' => 'rich',
            'version' => '1.0',
            'title' => sprintf('U+%04X %s', $cp->id, $cp->name),
            'author_url' => 'https://codepoints.net/',
            'provider_name' => 'Codepoints.net',
            'provider_url' => 'https://codepoints.net/',
            'cache_age' => 60*60*24*7/*s*/,
            'thumbnail_url' => sprintf('https://codepoints.net/api/v1/glyph/%04X', $cp->id),
            'html' => sprintf(
                '<iframe src="https://codepoints.net/U+%04X?embed" style="width:%dpx;height:%dpx;border:1px solid #444"></iframe>',
                q($cp->id), $maxwidth, $maxheight),
            'width' => $maxwidth,
            'height' => $maxheight,
        ];

        if ($format === 'xml') {
            header('Content-Type: text/xml; charset=utf-8');
            $xml = '<?xml version="1.0" encoding="utf-8" standalone="yes"?'.'><oembed>';
            foreach ($data as $element => $value) {
                $xml .= "<$element>".q((string)$value)."</$element>";
            }
            return $xml.'</oembed>';
        } else {
            header('Content-Type: application/json; charset=utf-8');
            return json_encode($data);
        }
    }

}
