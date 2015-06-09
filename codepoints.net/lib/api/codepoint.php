<?php

require_once __DIR__.'/../tools.php';

if (! $data) {
    $host = get_origin().'api/v1';
    return array(
        "description" => _("show detailed information about a single codepoint. You can specify fields of interest with the “property” parameter: codepoint/1234?property=age,uc,lc"),
        "codepoint_url" => "$host/codepoint/{codepoint}{?property*}",
        "codepoint" => "(10)?[A-Fa-f0-9]{4}|0[A-Fa-f0-9]{5}",
        "property" => UnicodeInfo::get()->getAllCategories(),
    );
}
if (maybeCodepoint($data) === false) {
    $api->throwError(API_BAD_REQUEST, _('No valid codepoint'));
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

header('Link: <https://codepoints.net/U+'.$cp->getId('hex').'>; rel=alternate', false);
$block = $cp->getBlock();
if ($block) {
    header('Link: <https://codepoints.net/api/v1/block'.Router::getRouter()->getUrl($block).'>; rel=up', false);
}
$next = $cp->getNext();
if ($next) {
    header('Link: <https://codepoints.net/api/v1/codepoint/'.$next->getId('hex').'>; rel=next', false);
}
$prev = $cp->getPrev();
if ($prev) {
    header('Link: <https://codepoints.net/api/v1/codepoint/'.$prev->getId('hex').'>; rel=prev', false);
}

return $properties;


#EOF
