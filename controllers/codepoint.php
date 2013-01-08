<?php

$router->registerAction(function ($url, $o) {
    // Single Codepoint
    $url = rawurldecode($url);
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
    if (array_key_exists('embed', $_GET)) {
        $view = new View('codepoint.embedded');
    } else {
        $view = new View('codepoint.html');
    }
    $cache = new Cache();
    $data = $view->render(array('codepoint' => $request->data));
    echo $data;
    $cache->write($request->url, $data);
});

//__END__
