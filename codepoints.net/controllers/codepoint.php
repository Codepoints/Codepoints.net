<?php

$router->registerAction(function ($url, $o) {
    // Single Codepoint
    $url = rawurldecode($url);
    if (substr($url, 0, 2) === 'U+' && ctype_xdigit(substr($url, 2))) {
        $number = hexdec(substr($url, 2));
        if ((0xE000 <= $number && $number <= 0xF8FF) ||
            (0xF0000 <= $number && $number <= 0x10FFFF)) {
            return $number;
        }
        try {
            $codepoint = Codepoint::getCP($number, $o['db']);
            $codepoint->getName();
        } catch (Exception $e) {
            $router = Router::getRouter();
            $router->addSetting('noCP', true);
            return false;
        }
        return $codepoint;
    }
    return false;
}, function ($request, $o) {
    $root = 'codepoint';
    if (is_int($request->data)) {
        $root = 'pu_codepoint';
    }
    if (array_key_exists('embed', $_GET)) {
        $view = new View($root.'.embedded');
    } else {
        $view = new View($root.'.html');
    }
    $cache = new Cache();
    $data = $view->render(array('codepoint' => $request->data));
    echo $data;
    if (! array_key_exists('embed', $_GET)) {
        // cache only, if this is not the embedded view
        $cache->write($request->url, $data);
    }
});

//__END__
