<?php

$router->registerAction(array('api/', 'api'), function() {
    Router::getRouter()->redirect('/api/v1/', 302);
});

$router->registerAction(function ($url, $o) {
    if ($url === 'api/v1/' || $url === 'api/v1') {
        return '';
    } elseif (substr($url, 0, 7) === 'api/v1/') {
        return substr($url, 7);
    }
    return false;
}, function($request, $o) {
    $action = $data = "";
    if ($request->data) {
        if (strpos($request->data, '/') === false) {
            $action = $request->data;
        } else {
            list($action, $data) = explode('/', $request->data, 2);
        }

        /* special case: When the action is a single character, redirect
         * to the canonical /codepoint/HEX URL for convenience. */
        $c = rawurldecode($action);
        if (mb_strlen($c, 'UTF-8') === 1) {
            $cp = unpack('N', mb_convert_encoding($c, 'UCS-4BE', 'UTF-8'));
            $cp = $cp[1];
            $router = Router::getRouter();
            $get = '';
            if (count($_GET)) {
                $get = '?'.http_build_query($_GET);
            }
            $router->redirect(sprintf('/api/v1/codepoint/%04X%s', $cp, $get));
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
