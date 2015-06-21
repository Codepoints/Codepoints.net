<?php

$router->registerAction('', function ($request, $o) {
    // Index
    $view = new View('front');
    $x = $o['db']->prepare('SELECT COUNT(*) AS c FROM codepoints USE INDEX (PRIMARY)');
    $x->execute();
    $row = $x->fetch();
    $Daily = new DailyCP();
    $daily = $Daily->get(date('Y-m-d'), $o['db']);
    echo $view->render(array('planes' => UnicodePlane::getAll($o['db']),
      'nCPs' => $row['c'], 'daily' => $daily));
});

//__END__
