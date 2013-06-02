<?php

$router->registerAction('api/bool', function ($request, $o) {

    $width = 512;
    $height = 384;

    $fields = array(
        'Bidi_M', 'Bidi_C', 'CE', 'Comp_Ex', 'XO_NFC', 'XO_NFD', 'XO_NFKC',
        'XO_NFKD', 'Join_C', 'Upper', 'Lower', 'OUpper', 'OLower', 'CI',
        'Cased', 'CWCF', 'CWCM', 'CWL', 'CWKCF', 'CWT', 'CWU', 'IDS', 'OIDS',
        'XIDS', 'IDC', 'OIDC', 'XIDC', 'Pat_Syn', 'Pat_WS', 'Dash', 'Hyphen',
        'QMark', 'Term', 'STerm', 'Dia', 'Ext', 'SD', 'Alpha', 'OAlpha',
        'Math', 'OMath', 'Hex', 'AHex', 'DI', 'ODI', 'LOE', 'WSpace',
        'Gr_Base', 'Gr_Ext', 'OGr_Ext', 'Gr_Link', 'Ideo', 'UIdeo', 'IDSB',
        'IDST', 'Radical', 'Dep', 'VS', 'NChar',
        'cp',
    );

    $q = @$_GET['q'];
    if (! in_array($q, $fields)) {
        header('', true, 400);
        die('Unknown value for key “q”. Allowed values: '.
             join(', ', $fields));
    }

    $gd = imagecreatetruecolor($width, $height);
    imagecolortransparent($gd, imagecolorallocate($gd, 0, 0, 0));

    $false = imagecolorallocate($gd, 255, 0, 0);
    $true = imagecolorallocate($gd, 0, 255, 0);

    $result = $o['db']->query('SELECT cp, '.$q.' AS Q
        FROM codepoints
        WHERE cp < 196608')->fetchAll(PDO::FETCH_ASSOC);
                // 196608 == 0x30000 (everything up to the third plane)

    foreach ($result as $cp) {
        $_x = $cp['cp'] % $width;
        $_y = round($cp['cp'] / $width);
        imagesetpixel($gd, $_x, $_y, $cp['Q']? $true : $false);
    }

    header('Content-Type: image/png');
    imagepng($gd);
    imagedestroy($gd);

});


//__END__
