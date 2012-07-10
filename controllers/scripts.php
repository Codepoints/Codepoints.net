<?php

$router->registerAction('scripts', function ($request, $o) {
    // scripts
    $cur = $o['db']->prepare('SELECT iso, name,
        -- (SELECT abstract FROM script_abstract
        --   WHERE script_abstract.sc = scripts.iso) abstract,
        (SELECT COUNT(*) FROM codepoint_script
          WHERE codepoint_script.sc = scripts.iso) count
        FROM scripts');
    $cur->execute();
    $scripts = $cur->fetchAll(PDO::FETCH_ASSOC);
    $view = new View($request->trunkUrl);
    echo $view->render(array('scripts' => $scripts));
});

//__END__
