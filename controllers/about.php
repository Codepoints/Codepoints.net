<?php

$router->registerAction(array('about', 'glossary'), function ($request, $o) {
    // static pages
    $view = new View($request->trunkUrl);
    $cache = new Cache();
    $data = $view->render();
    echo $data;
    $cache->write($request->url, $data);
});


//__END__
