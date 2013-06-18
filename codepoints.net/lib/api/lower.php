<?php

require_once __DIR__.'/../tools.php';

$maxlength = 1024;

if (strlen($data) > $maxlength) {
    $api->throwError(API_REQUEST_TOO_LONG, sprintf(_('Request too long: Only %d characters allowed.'), $maxlength));
}

$codepoints = utf8_to_unicode($data);

$stm = $api->_db->prepare("SELECT DISTINCT cp, other
                             FROM codepoint_relation
                            WHERE relation = 'lc'
                            AND cp != other
                            AND cp IN (".join(",", array_fill(0, count($codepoints), '?')).")");
$stm->execute($codepoints);
$result = $stm->fetchAll(PDO::FETCH_ASSOC);
$mapping = array();
foreach ($result as $set) {
    $mapping[$set['cp']] = $set['other'];
}

$lowercase = array_map(function($cp) use ($mapping) {
    if (array_key_exists($cp, $mapping)) {
        return $mapping[$cp];
    }
    return $cp;
}, $codepoints);

return unicode_to_utf8($lowercase);


#EOF
