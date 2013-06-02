<?php

$router->registerAction(function($url) {
    if (preg_match('#^api/v1/glyph/([a-fA-F0-9]{1,6})$#', $url, $matches)) {
        return $matches[1];
    }
    return False;
}, function ($request, $o) {
    $sql = $o['db']->prepare('SELECT image
        FROM codepoint_image
        WHERE cp = ?');
    $sql->execute(array(hexdec($request->data)));
    $data = $sql->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: image/png');
    if ($data) {
        echo base64_decode($data[0]['image']);
    } else {
        header('', false, 404);
        readfile(dirname(dirname(__FILE__)).'/'.Codepoint::$defaultImage);
    }
});


//__END__
