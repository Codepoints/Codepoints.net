<?php

$router->registerAction(function ($url, $o) {
    // Plane
    if (substr($url, -6) === '_plane') {
        try {
            $plane = new UnicodePlane($url, $o['db']);
        } catch(Exception $e) {
            try {
                $plane = new UnicodePlane(substr($url, 0, -6), $o['db']);
            } catch(Exception $e) {
                return False;
            }
        }
        return $plane;
    }
    return False;
}, function($request) {
    $plane = $request->data;
    $view = new View('plane.html');
    $cache = new Cache();
    $data = $view->render(compact('plane'));
    echo $data;
    $cache->write($request->url, $data);
});

//__END__
