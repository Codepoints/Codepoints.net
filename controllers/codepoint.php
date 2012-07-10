<?php

$router->registerAction(function ($url, $o) {
    // Single Codepoint
    if (substr($url, 0, 2) === 'U+' && ctype_xdigit(substr($url, 2))) {
        try {
            $codepoint = Codepoint::getCP(hexdec(substr($url, 2)), $o['db']);
            $codepoint->getName();
        } catch (Exception $e) {
            $router = Router::getRouter();
            $router->addSetting('noCP', true);
            return False;
        }
        return $codepoint;
    }
    return False;
}, function ($request, $o) {
    $view = new View('codepoint.html');
    echo $view->render(array(
        'codepoint' => $request->data));
});

//__END__
