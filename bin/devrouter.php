<?php

/**
 * this is a very primitive routing script, that allows to run _some_
 * features of codepoints.net without a full-fledged web server.  Be
 * prepared, that there will be bugs, if you do that.
 * Incantation:
 *     php -S localhost:8000 -t codepoints.net devrouter.php
 */

$req = preg_replace('/[^a-zA-Z0-9\\/._-]+$/', '', $_SERVER['REQUEST_URI']);
$req = preg_replace('/(\.\.|^\/index\.php)/', '', $req);
$_SERVER['REQUEST_URI'] = $req;
$_SERVER['PHP_SELF'] = '/index.php';
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

$filename = dirname(__DIR__).'/codepoints.net'.$req;
$static_extensions = ['png', 'jpg', 'jpeg', 'gif', 'css', 'js', 'svg', 'svgz',
                      'woff', 'ttf'];
if (in_array($ext, $static_extensions) && is_file($filename)) {
    header('Content-Type: '.$mimetype[$ext]);
    if ($ext === 'svgz') {
        header('Content-Encoding: gzip');
    }
    readfile($filename);
} else {
    require dirname(__DIR__).'/codepoints.net/index.php';
}
