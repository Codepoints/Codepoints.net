<?php

/**
 * this is a very primitive routing script, that allows to run _some_
 * features of codepoints.net without a full-fledged web server.  Be
 * prepared, that there will be bugs, if you do that.
 * Incantation:
 *     php -S localhost:8000 -t codepoints.net devrouter.php
 */

$req = preg_replace('/![a-zA-Z0-9]+$/', '', $_SERVER["REQUEST_URI"]);
$req = preg_replace('/^\/index\.php/', '', $req);
$_SERVER['REQUEST_URI'] = $req;
$ext = pathinfo($req, PATHINFO_EXTENSION);

$mimetype = [
    'png' => 'image/png',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'gif' => 'image/gif',
    'css' => 'text/css',
    'js' => 'application/javascript',
    'svg' => 'image/svg+xml',
    'svgz' => 'image/svg+xml',
    'woff' => 'application/octet-stream',
    'ttf' => 'application/octet-stream',
];

if (in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'css', 'js', 'svg', 'svgz', 'woff', 'ttf'])) {
    $filename = dirname(__FILE__).'/codepoints.net'.$req;
    if (is_file($filename)) {
        header('Content-Type: '.$mimetype[$ext]);
        if ($ext === 'svgz') {
            header('Content-Encoding: gzip');
        }
        readfile($filename);
    } else {
        return false; // Liefere die angefragte Ressource direkt aus
    }
} else {
    require dirname(__FILE__).'/codepoints.net/index.php';
}
