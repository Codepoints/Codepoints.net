<?php

require_once __DIR__.'/../tools.php';
$origin = get_origin();

if (! isset($_GET['url'])) {
    $host = $origin.'api/v1';
    return array(
        "description" => _("oEmbed API endpoint for URLs matching “codepoints.net”"),
        "oembed_url" => "$host/oembed{?url}{?format*}{?maxwidth*}{?maxheight*}",
        "url" => $origin.'*',
        "format" => ['xml', 'json'],
        "maxwidth" => 'integer',
        "maxheight" => 'integer',
    );
}

$format = 'json';
if (isset($_GET['format'])) {
    if ($_GET['format'] === 'xml') {
        $format = 'xml';
    } elseif ($_GET['format'] !== 'json') {
        $api->throwError(501, _('Unknown format parameter'));
    }
}

$maxwidth = 640;
if (isset($_GET['maxwidth']) && ctype_digit($_GET['maxwidth'])) {
    $maxwidth = max(26, intval($_GET['maxwidth']));
}

$maxheight = 640;
if (isset($_GET['maxheight']) && ctype_digit($_GET['maxheight'])) {
    $maxheight = max(40, intval($_GET['maxheight']));
}

$url = $_GET['url'];
if (substr($url, 0, strlen($origin)) !== $origin) {
    $api->throwError(API_NOT_FOUND, _('Invalid URL'));
}

$path = substr($url, strlen($origin));
if (preg_match('/^[Uu](?:\\+| |%20)([A-Fa-f0-9]{1,6})$/', $path, $matches)) {
    $dec = hexdec($matches[1]);
} elseif (mb_strlen($path, 'UTF-8') === 1) {
    $dec = unpack('N', mb_convert_encoding($path, 'UCS-4BE', 'UTF-8'))[1];
} else {
    $api->throwError(API_NOT_FOUND, _('URL path must be single character (UTF-8 encoded) or match /U+[A-F0-9]{4,6}/.'));
}

$cp = Codepoint::getCP($dec, $api->_db);
try {
    $cp->getName();
} catch (Exception $e) {
    $api->throwError(API_NOT_FOUND, _('Not a valid codepoint URL'));
}

header('Link: <'.$origin.'U+'.$cp->getId('hex').'>; rel=alternate', false);

$data = [
    'type' => 'rich',
    'version' => '1.0',
    'title' => 'U+' . $cp->getId('hex'). ' ' . $cp->getName(),
    'author_url' => $origin,
    'provider_name' => 'Codepoints.net',
    'provider_url' => $origin,
    'cache_age' => 60*60*24*7/*s*/,
    'thumbnail_url' => $origin.'api/v1/glyph/'.$cp->getId('hex'),
    'html' => '<iframe src="'.$origin.'U+'.q($cp->getId('hex')).'?embed" style="width: '.$maxwidth.'px; height: '.$maxheight.'px; border: 1px solid #444;"></iframe>',
    'width' => $maxwidth,
    'height' => $maxheight,
];

if ($format === 'xml') {
    $api->_mime = 'text/xml';
    $xml = '<?xml version="1.0" encoding="utf-8" standalone="yes"?'.'><oembed>';
    foreach ($data as $element => $value) {
        $xml .= "<$element>".q($value)."</$element>";
    }
    return $xml.'</oembed>';
} else {
    return $data;
}
