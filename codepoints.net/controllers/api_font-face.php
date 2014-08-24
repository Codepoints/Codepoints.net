<?php

$router->registerAction(function ($url, $o) {
    // an @font-face definition
    if (substr($url, 0, 14) === 'api/font-face/') {
        return preg_replace('/[^a-zA-Z0-9_.\/-]/', '', substr($url, 14));
    }
    return False;
}, function($request) {
    $font = $request->data;
    $offset = 60 * 60 * 24 * 365;
    header('Content-Type: text/css');
    header("Cache-Control: max-age=".$offset);
    header("Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT");
    echo "@font-face{".
            "font-family:'$font';".
            "src:url('/static/fonts/$font.eot');\n".
            "src:url('/static/fonts/$font.eot?#iefix') format('eot'),".
            "url('/static/fonts/$font.woff') format('woff'),".
            "url('/static/fonts/$font.ttf') format('truetype'),".
            "url('/static/fonts/$font.svg#$font') format('svg');".
            "font-weight:normal;".
            "font-style:normal;".
         "}";
});

//__END__
