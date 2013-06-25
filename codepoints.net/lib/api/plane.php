<?php

require_once __DIR__.'/../tools.php';

if (! $data) {
    $host = get_origin().'api/v1';
    return array(
        "description" => "show information about a Unicode plane",
        "plane_url" => "$host/plane/{plane}",
        "plane" => array_map(function ($plane) {
            return str_replace(' ', '_', strtolower($plane->getName()));
        }, UnicodePlane::getAll($api->_db)),
    );
}


try {
    $plane = new UnicodePlane($data, $api->_db);
} catch (Exception $e) {
    $api->throwError(API_NOT_FOUND, _('Not a plane'));
}
$prev = $plane->getPrev();
$next = $plane->getNext();
$blocks = $plane->getBlocks();

$return = array(
    "name" => $plane->getName(),
    "first" => sprintf("U+%04X", $plane->first),
    "last" => sprintf("U+%04X", $plane->last),
    "blocks" => array_map(function($block) {
        return $block->getName();
    }, $blocks),
);

header('Link: <http://codepoints.net'.Router::getRouter()->getUrl($plane).'>; rel=alternate', false);
if ($next) {
    header('Link: <http://codepoints.net/api/v1/plane'.Router::getRouter()->getUrl($next).'>; rel=next', false);
    $return["next_plane"] = $next->getName();
}
if ($prev) {
    header('Link: <http://codepoints.net/api/v1/plane'.Router::getRouter()->getUrl($prev).'>; rel=prev', false);
    $return["prev_plane"] = $prev->getName();
}

return $return;


#EOF
