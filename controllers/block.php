<?php

$router->registerAction(function ($url, $o) {
    // Block
    if (! preg_match('/[^a-z0-9_-]/', $url)) {
        try {
            $block = new UnicodeBlock($url, $o['db']);
        } catch(Exception $e) {
            return False;
        }
        return $block;
    }
    return False;
}, function($request) {
    $block = $request->data;
    $view = new View('block.html');
    $cache = new Cache();
    $data = $view->render(compact('block'));
    echo $data;
    // Quick fix: The caching doesn't support GET variables
    //$cache->write($request->url, $data);
});

//__END__
