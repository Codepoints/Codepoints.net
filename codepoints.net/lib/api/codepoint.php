<?php

require_once __DIR__.'/../tools.php';

if (maybeCodepoint($data) === false) {
    $api->throwError(API_BAD_REQUEST, _('No codepoint given'));
}

$cp = Codepoint::getCP(hexdec($data), $api->_db);
try {
    $cp->getName();
} catch (Exception $e) {
    $api->throwError(API_NOT_FOUND, _('Not a codepoint'));
}


$properties = $cp->getProperties();

if (isset($_GET['property'])) {
    $mask = array_filter(explode(',', $_GET['property']));
    if (count($mask)) {
        $properties = array_intersect_key($properties, array_flip($mask));
    }
}

header('Link: <http://codepoints.net/U+'.$cp->getId('hex').'>; rel=alternate', false);
$next = $cp->getNext();
if ($next) {
    header('Link: <http://codepoints.net/api/v1/codepoint/'.$next->getId('hex').'>; rel=next', false);
}
$prev = $cp->getPrev();
if ($prev) {
    header('Link: <http://codepoints.net/api/v1/codepoint/'.$prev->getId('hex').'>; rel=prev', false);
}

return $properties;


#EOF
