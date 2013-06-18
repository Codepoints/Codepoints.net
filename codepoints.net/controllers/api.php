<?php

$router->registerAction(function ($url, $o) {
    if (substr($url, 0, 7) === 'api/v1/') {
        return substr($url, 7);
    }
    return False;
}, function($request, $o) {
    list($action, $data) = explode('/', $request->data, 2);
    $api = new API_v1($action, $request, $o['db']);
    try {
        $api->run(rawurldecode($data));
    } catch (APIException $e) {
        $api->handleError();
    }
    $api->finish();
});

#EOF
