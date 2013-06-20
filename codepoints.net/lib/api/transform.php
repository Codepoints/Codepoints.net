<?php

require_once __DIR__.'/../tools.php';

$maxlength = 1024;
$subactions = array('lower', 'upper', /*'nfc', 'nfd', 'nfkc', 'nfkd'*/);

if (! strpos($data, '/')) {
    $api->throwError(API_BAD_REQUEST, _('No transform action given'));
}

list($action, $input) = explode('/', $data, 2);

if (strlen($input) > $maxlength) {
    $api->throwError(API_REQUEST_TOO_LONG, sprintf(_('Request too long: Only %d characters allowed.'), $maxlength));
}

if (! in_array($action, $subactions)) {
    $api->throwError(API_BAD_REQUEST, sprintf(_('Unknown transform action %s'), $action));
}

$codepoints = utf8_to_unicode($input);

switch ($action) {
    case 'lower':
        $relation = 'lc';
        break;
    case 'upper':
        $relation = 'uc';
        break;
}

$stm = $api->_db->prepare("SELECT DISTINCT cp, other, \"order\"
                             FROM codepoint_relation
                            WHERE relation = ?
                            AND cp != other
                            AND cp IN (".join(",", array_fill(0, count($codepoints), '?')).")");
$stm_input = $codepoints;
array_unshift($stm_input, $relation);
$stm->execute($stm_input);
$result = $stm->fetchAll(PDO::FETCH_ASSOC);
$mapping = array();
foreach ($result as $set) {
    if (! array_key_exists($set['cp'], $mapping)) {
        $mapping[$set['cp']] = [];
    }
    $mapping[$set['cp']][(int)$set['order']] = $set['other'];
}

$mapped_cps = array();
foreach ($codepoints as $cp) {
    if (array_key_exists($cp, $mapping)) {
        $mapped_cps = array_merge($mapped_cps, $mapping[$cp]);
    } else {
        $mapped_cps[] = $cp;
    }
}

return unicode_to_utf8($mapped_cps);

#EOF
