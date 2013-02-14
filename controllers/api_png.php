<?php

$router->registerAction(function ($url, $o) {
    $c = rawurldecode($url);
    if (preg_match('#^api/.*\.png#', $c) && mb_strlen($c, 'UTF-8') >= 9) {
        $c = substr(substr($c, 4), 0, -4);
        return array_map(function($x) {
            return unpack('N', mb_convert_encoding($x, 'UCS-4BE', 'UTF-8'));
        }, preg_split("//u", $c, -1, PREG_SPLIT_NO_EMPTY));
    }
    return False;
}, function ($request, $o) {
    $src = array_map(function($a) { return $a[1]; }, $request->data);
    if (count($src) !== 1) {
        header('', false, 501);
        die('Currently only a single codepoint is supported.');
    }

    $sql = $o['db']->prepare('SELECT cp, image FROM codepoint_image WHERE '.join("OR", array_fill(0, count($request->data), " cp = ? ")));
    $sql->execute($src);
    $data = $sql->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: image/png');
    if ($data) {
        // for multi-cp support, we must here add joining code
        echo base64_decode($data[0]['image']);
    } else {
        header('', false, 404);
        readfile(dirname(dirname(__FILE__)).'/'.Codepoint::$defaultImage);
    }

});


//__END__
