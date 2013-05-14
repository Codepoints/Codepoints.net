<?php

$router->registerAction(function($url) {
    if (preg_match('#^api/v1/name/([a-fA-F0-9]{1,6})$#', $url, $matches)) {
        return $matches[1];
    }
    return False;
}, function ($request, $o) {
    $cp = Codepoint::getCP(hexdec($request->data), $o['db']);

    header('Content-Type: text/plain; charset=UTF-8');
    echo $cp->getName();
});


//__END__
