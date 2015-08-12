<?php

$router->registerAction('codepoint_of_the_day', function($request, $o) {
    // Codepoint of the Day
    if ($request->type === 'application/xml') {
        header('Content-Type: application/xml; charset=utf-8');
        $Daily = new DailyCP($o['db']);
        $cps = $Daily->getSome(30);
        $view = new View('dailycp.feed');
        echo $view->render(compact('cps'));
        return;
    }
    $date = null;
    $today = date('Y-m-d');
    if (isset($_GET['date'])) {
        if (preg_match('/^20[0-9]{2}-[01][0-9]-[0-3][0-9]$/', $_GET['date'])) {
            $date = $_GET['date'];
        }
    } else {
        $date = $today;
    }
    if ($date) {
        $Daily = new DailyCP();
        list($codepoint, $description) = $Daily->get($date, $o['db']);
        $tpl = 'dailycp';
        if (! $codepoint) {
            list($codepoint, $description) = array(false, false);
            $tpl .= '_not';
        }
        $view = new View($tpl);
        echo $view->render(compact('codepoint', 'description',
                                   'date', 'today'));
    } else {
        throw new RoutingError();
    }
});

//__END__
