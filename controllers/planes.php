<?php

$router->registerAction('planes', function ($request, $o) {
    // all planes
    $view = new View('planes');
    $cache = new Cache();
    $data = $view->render(array('planes' => UnicodePlane::getAll($o['db'])));
    echo $data;
    $cache->write($request->url, $data);
});

//__END__
