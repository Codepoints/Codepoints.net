<?php

require_once __DIR__.'/../tools.php';

$properties = UnicodeInfo::get()->getAllCategories();

if (! $data) {
    $host = get_origin().'api/v1';
    return array(
        "description" => "Filter a string of characters by Unicode property. You can negate properties by appending a “!” to it: filter/string?age!=5.5 finds all characters in “string” that were *not* added in Unicode 5.5.",
        "filter_url" => "$host/filter/{data}{?property*}",
        "property" => $properties,
    );
}

$codepoints = utf8_to_unicode($data);

$sql_filter = array();
$values = array();

foreach ($_GET as $property => $value) {
    if (! array_key_exists($property, $properties)) {
        $api->throwError(API_BAD_REQUEST,
            sprintf(_('Cannot filter for unknown property %s'), $property));
    }
    $value = (array)$value;
    $column = str_replace('"', '""', $property);
    $sql_filter[] = '"'.$column.'" IN ('.
                    join(',', array_fill(0, count($value), '?')).')';
    $values = array_merge($values, $value);
}

$stm = $api->_db->prepare("SELECT cp FROM codepoints WHERE "
    .join(' AND ', $sql_filter));

if (! $stm) {
    $api->throwError(500, _('Cannot filter.'));
}

$stm->execute($values);
$filtered_cps = $stm->fetchAll(PDO::FETCH_COLUMN, 0);

$codepoints = array_filter($codepoints, function($cp) use ($filtered_cps) {
    return in_array($cp, $filtered_cps);
});

return unicode_to_utf8($codepoints);


#EOF
