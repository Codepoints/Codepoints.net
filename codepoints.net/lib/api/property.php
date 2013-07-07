<?php

require_once __DIR__.'/../tools.php';

$width = 512;
$height = 384;

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
    $api->_mime = 'application/json';
    $api->throwError(API_BAD_REQUEST,
        $data ? _('Unknown property')
              : _('Please specify a property to display'),
        array(
            'description' => _('show a PNG image where every codepoint is represented by one pixel. The pixel color determines the value.'),
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

$colorseed = 0;
$colors = array();

foreach ($result as $cp) {
    $_x = $cp['cp'] % $width;
    $_y = round($cp['cp'] / $width);
    if ($data === 'cp') {
        // codepoints are only checked for existance. There's no use coloring
        // each CP in an individual color
        $cp['Q'] = '1';
    }
    if (! array_key_exists($cp['Q'], $colors)) {
        $rgb = hsl2rgb(( ($colorseed++) * 53 ) % 360, 0.5, 100);
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
