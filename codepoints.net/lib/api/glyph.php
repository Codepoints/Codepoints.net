<?php

require_once __DIR__.'/../tools.php';

$api->_mime = 'image/png';

if (maybeCodepoint($data) === false) {
    $api->throwError(API_BAD_REQUEST,
        file_get_contents(dirname(dirname(__DIR__)).'/'.Codepoint::$defaultImage));
}

$sql = $api->_db->prepare('SELECT image
    FROM codepoint_image
    WHERE cp = ?
    LIMIT 0,1');
$sql->execute(array(hexdec($data)));
$img = $sql->fetchColumn();

if ($img) {
    if (! CP_DEBUG) {
        /* extend caching to 1 week */
        header('Cache-Control: public, mag-age=604800');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 604800) . ' GMT');
    }
    return base64_decode($img);
} else {
    $api->throwError(API_NOT_FOUND,
    file_get_contents(dirname(dirname(__DIR__)).'/'.Codepoint::$defaultImage));
}


#EOF
