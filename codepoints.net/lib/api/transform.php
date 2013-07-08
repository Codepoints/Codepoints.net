<?php

require_once __DIR__.'/../tools.php';

$maxlength = 1024;
$subactions = array('lower', 'upper', 'title', 'mirror', 'nfc', 'nfd', 'nfkc', 'nfkd');

if (! strpos($data, '/')) {
    $possibilities = array(
        "description" => "transform a string to another according to a mapping, e.g., making all characters upper-case.",
    );
    $host = get_origin().'api/v1/transform';
    foreach($subactions as $part) {
        $possibilities["transform_{$part}_url"] = "$host/$part/{data}";
    }
    return $possibilities;
}

list($action, $input) = explode('/', $data, 2);

if (mb_strlen($input, 'UTF-8') > $maxlength) {
    $api->throwError(API_REQUEST_URI_TOO_LONG, sprintf(_('Request too long: Only %d characters allowed.'), $maxlength));
}

if (! in_array($action, $subactions)) {
    $api->throwError(API_BAD_REQUEST, sprintf(_('Unknown transform action %s'), $action));
}

$codepoints = utf8_to_unicode($input);

$mapped_cps = array();
switch ($action) {
    case 'lower':
        $mapped_cps = unicode_to_utf8(map_by_db($api->_db, $codepoints, 'lc'));
        break;
    case 'upper':
        $mapped_cps = unicode_to_utf8(map_by_db($api->_db, $codepoints, 'uc'));
        break;
    case 'title':
        $mapped_cps = unicode_to_utf8(map_by_db($api->_db, $codepoints, 'tc'));
        break;
    case 'mirror':
        $mapped_cps = unicode_to_utf8(map_by_db($api->_db, $codepoints, 'bmg'));
        break;
    case 'nfc':
        $mapped_cps = normalizer_normalize($input, Normalizer::FORM_C);
        break;
    case 'nfd':
        $mapped_cps = normalizer_normalize($input, Normalizer::FORM_D);
        break;
    case 'nfkc':
        $mapped_cps = normalizer_normalize($input, Normalizer::FORM_KC);
        break;
    case 'nfkd':
        $mapped_cps = normalizer_normalize($input, Normalizer::FORM_KD);
        break;
}

return $mapped_cps;


/**
 * map an array of codepoints to another array according to info from the
 * codepoint_relation table
 */
function map_by_db($db, $codepoints, $relation) {
    $stm = $db->prepare("SELECT DISTINCT cp, other, \"order\"
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

    return $mapped_cps;
}

#EOF
