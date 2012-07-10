<?php

$router->registerAction(array('about', 'glossary'), function ($request, $o) {
    // static pages
    $view = new View($request->trunkUrl);
    echo $view->render();
});


//__END__
