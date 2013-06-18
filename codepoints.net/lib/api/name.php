<?php

require_once __DIR__.'/../tools.php';

$api->_mime = 'text/plain';

if (maybeCodepoint($data) === false) {
    $api->throwError(API_BAD_REQUEST, _('No codepoint.'));
}

$cp = Codepoint::getCP(hexdec($data), $api->_db);
return $cp->getName();

#EOF
