<?php

$router->registerAction(function ($url, $o) {
    if ($url === 'api/v1/' || $url === 'api/v1') {
        return '';
    } elseif (substr($url, 0, 7) === 'api/v1/') {
        return substr($url, 7);
    }
    return False;
}, function($request, $o) {
    $action = $data = "";
    if ($request->data) {
        if (strpos($request->data, '/') === false) {
            $action = $request->data;
        } else {
            list($action, $data) = explode('/', $request->data, 2);
        }
    }
    $api = new API_v1($action, $request, $o['db']);
    try {
        $api->run(rawurldecode($data));
    } catch (APIException $e) {
        $api->handleError();
    }
    $api->finish();
});

#EOF
