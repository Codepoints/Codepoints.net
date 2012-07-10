<?php

$router->registerAction('planes', function ($request, $o) {
    // all planes
    $view = new View('planes');
    echo $view->render(array('planes' => UnicodePlane::getAll($o['db'])));
});

//__END__
