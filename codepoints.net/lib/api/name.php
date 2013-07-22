<?php

require_once __DIR__.'/../tools.php';

if (isset($_SERVER['HTTP_ACCEPT']) &&
    strpos($_SERVER['HTTP_ACCEPT'], 'application/json') === false &&
    ! array_key_exists('callback', $_GET)) {
    /* for non-JSON requests return plain text */
    $api->_mime = 'text/plain';
}

if (maybeCodepoint($data) === false) {
    $api->throwError(API_BAD_REQUEST, _('No codepoint'));
}

try {
    $cp = Codepoint::getCP(hexdec($data), $api->_db);
    $name = $cp->getName();
} catch (Exception $e) {
    $api->throwError(API_NOT_FOUND, _('Not a codepoint'));
}

return $name;

#EOF
