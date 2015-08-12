<?php

$router->registerAction(function ($url, $o) {
    // Single characters
    $c = rawurldecode($url);
    if (mb_strlen($c, 'UTF-8') === 1) {
        return unpack('N', mb_convert_encoding($c, 'UCS-4BE', 'UTF-8'));
    }
    return false;
}, function($request) {
    $router = Router::getRouter();
    $router->redirect(sprintf('U+%04X', $request->data[1]));
});

//__END__
