<?php

$router->registerAction('random', function ($request, $o) {
    // random codepoint
    $x = $o['db']->prepare('SELECT cp FROM codepoints ORDER BY RANDOM() LIMIT 1');
    $x->execute();
    $row = $x->fetch();
    $router = Router::getRouter();
    $router->redirect(sprintf('U+%04X', $row['cp']));
});

//__END__
