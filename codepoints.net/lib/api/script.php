<?php

$j = array();
$found = false;
$stm = $api->_db->prepare('SELECT abstract, src
                            FROM script_abstract WHERE sc = ?');

foreach (explode(',', $data) as $sc) {
    $stm->execute(array($sc));
    $r = $stm->fetch(PDO::FETCH_ASSOC);
    if ($r['abstract']) {
        $j[$sc] = array(
            'name' => UnicodeInfo::get()->getLabel('sc', $sc),
            'abstract' => strip_tags($r['abstract'], '<p><b><strong class="selflink"><strong><em><i><var><sup><sub><tt><ul><ol><li><samp><small><hr><h2><h3><h4><h5><dfn><dl><dd><dt><u><abbr><big><blockquote><br><center><del><ins><kbd>'),
            'src' => $r['src'],
        );
        $found = true;
    } else {
        $j[$sc] = null;
    }
}

if (! $found) {
    $res = $api->_db->query('SELECT iso, name FROM scripts')->fetchAll(PDO::FETCH_ASSOC);
    $scripts = array();
    foreach($res as $script) {
        $scripts[$script['iso']] = $script['name'];
    }
    $api->throwError(API_NOT_FOUND, $data? _('This script is unknown') : _('Please specify a script'), array(
        "detail" => _("Specify one or more ISO short names separated by comma. The response is a list of detail informations about these scripts."),
        "iso" => "[A-Z][a-z]{3}",
        "scripts" => $scripts,
    ));
}

return $j;


#EOF
