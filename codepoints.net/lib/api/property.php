<?php

require_once __DIR__.'/../tools.php';

$width = 256;
$height = 256 * 3; // three planes high

$fields = array(
    'cp', 'age', 'gc', 'ccc', 'bc', 'Bidi_M', 'Bidi_C', 'dt', 'CE',
    'Comp_Ex', 'NFC_QC', 'NFD_QC', 'NFKC_QC', 'NFKD_QC', 'XO_NFC',
    'XO_NFD', 'XO_NFKC', 'XO_NFKD', 'nt', 'nv', 'jt', 'jg', 'Join_C',
    'lb', 'ea', 'Upper', 'Lower', 'OUpper', 'OLower', 'CI', 'Cased',
    'CWCF', 'CWCM', 'CWL', 'CWKCF', 'CWT', 'CWU', 'hst', 'JSN', 'IDS',
    'OIDS', 'XIDS', 'IDC', 'OIDC', 'XIDC', 'Pat_Syn', 'Pat_WS', 'Dash',
    'Hyphen', 'QMark', 'Term', 'STerm', 'Dia', 'Ext', 'SD', 'Alpha',
    'OAlpha', 'Math', 'OMath', 'Hex', 'AHex', 'DI', 'ODI', 'LOE',
    'WSpace', 'Gr_Base', 'Gr_Ext', 'OGr_Ext', 'Gr_Link', 'GCB', 'WB',
    'SB', 'Ideo', 'UIdeo', 'IDSB', 'IDST', 'Radical', 'Dep', 'VS',
    'NChar', 'kTotalStrokes', 'blk', 'scx', 'sc', 'confusables', 'block',
);

if ($data === 'block') {
    $data = 'blk';
}
if (! in_array($data, $fields)) {
    $host = get_origin().'api/v1';
    $api->_mime = 'application/json';
    $api->throwError(API_BAD_REQUEST,
        $data ? _('Unknown property')
              : _('Please specify a property to display'),
        array(
            'detail' => _('show a PNG image where every codepoint is represented by one pixel. The pixel color determines the value.'),
            'property_url' => "$host/property/{property}",
            'properties' => $fields,
        ));
}

$gd = imagecreatetruecolor($width, $height);
imagecolortransparent($gd, imagecolorallocate($gd, 0, 0, 0));

switch ($data) {
    case 'confusables':
        $query = 'SELECT cp, COUNT(\'other\') AS Q FROM codepoint_confusables
            WHERE cp < 196608
            GROUP BY cp';
        break;
    case 'sc':
        $query = 'SELECT cp, sc AS Q FROM codepoint_script
            WHERE cp < 196608';
        break;
    default:
        $query = 'SELECT cp, '.$data.' AS Q FROM codepoints
            WHERE cp < 196608';
                    // 196608 == 0x30000 (everything up to the third plane)
        break;
}
$result = $api->_db->query($query)->fetchAll(PDO::FETCH_ASSOC);

$coloroptions = array(
    'frequency1' => 1.666,
    'frequency2' => 2.666,
    'frequency3' => 3.666,
    'phase1' => 0,
    'phase2' => 2,
    'phase3' => 4,
);
if ($data === 'age') {
    $coloroptions = array(
        'frequency1' => .2,
        'frequency2' => .2,
        'frequency3' => .2,
        'phase1' => 1.6,
        'phase2' => -0.6,
        'phase3' => 4.0,
    );
}
$colors = array();

foreach ($result as $cp) {
    $_x = $cp['cp'] % $width;
    $_y = floor($cp['cp'] / $width);
    if ($data === 'cp') {
        // codepoints are only checked for existance. There's no use coloring
        // each CP in an individual color
        $cp['Q'] = '1';
    }
    if (! array_key_exists($cp['Q'], $colors)) {
        $rgb = call_user_func_array('getNextColor', $coloroptions);
        array_unshift($rgb, $gd);
        $colors[$cp['Q']] = call_user_func_array('imagecolorallocate', $rgb);
    }
    imagesetpixel($gd, $_x, $_y, $colors[$cp['Q']]);
}

$api->_mime = 'image/png';

ob_start();
imagepng($gd);
imagedestroy($gd);
return ob_get_clean();


#EOF
