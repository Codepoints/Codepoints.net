<?php

$router->registerAction(function ($url, $o) {
    // Single Codepoint with space instead of "+"
    $url = rawurldecode($url);
    if (substr($url, 0, 2) === 'U ' &&
        ctype_xdigit(substr($url, 2))) {
        return substr($url, 2);
    }
    return False;
}, function ($request, $o) {
    $router = Router::getRouter();
    $router->redirect(sprintf('U+%s', $request->data));
});

//__END__
