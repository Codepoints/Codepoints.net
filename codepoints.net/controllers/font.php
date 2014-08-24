<?php

$router->registerAction(function ($url, $o) {
    if (substr($url, 0, 5) === 'font/') {
        return urldecode(substr($url, 5));
    }
    return False;
}, function($request, $o) {
    $name = $request->data;
    $data = $o['db']->prepare('
        SELECT name, author, publisher, url, copyright, license,
               COUNT(cp) AS n
        FROM fonts
        INNER JOIN codepoint_fonts ON codepoint_fonts.font = fonts.id
         WHERE fonts."id" = ?');
    $data->execute(array($name));
    $font = $data->fetch(PDO::FETCH_ASSOC);
    $view = new View('font');
    $cache = new Cache();
    $data = $view->render(compact('font'));
    echo $data;
    $cache->write($request->url, $data);
});

//__END__
