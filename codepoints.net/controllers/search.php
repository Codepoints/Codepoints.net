<?php

$router->registerAction('search', function ($request, $o) {
    // Search
    $router = Router::getRouter();
    $info = UnicodeInfo::get();
    $blocks = array();
    $_q = Null;
    if (array_key_exists('q', $_GET)) {
        $_q = $_GET['q'];
        preg_match_all('/\b[a-z0-9 \\-]+\b/i', $_q, $m);
        foreach ($m as $_m) {
            $blocks = array_unique(array_merge($blocks,
                        UnicodeBlock::search($_m[0], $o['db'])));
        }
    }
    $q = new SearchComposer($_GET, $o['db']);
    $result = $q->getSearchResult();
    $page = isset($_GET['page'])? intval($_GET['page']) : 1;
    $result->page = $page - 1;
    if (count($result->getQuery())) {
        if ($result->getCount() === 0 && $_q) {
            $cps = Codepoint::getForString($_q, $o['db']);
            $view = new View('result');
            echo $view->render(compact('result', 'blocks', 'cps',
                                       'page'));
        } elseif ($result->getCount() === 1 && $page === 1) {
            /* redirect, if only one cp is found. Do not so on follow-up
             * pages, since that will torpedo pagination */
            $cp = $result->current();
            $router->redirect('U+'.$cp);
        } else {
            $pagination = new Pagination($result->getCount(), 128);
            $pagination->setPage($page);
            $view = new View('result');
            echo $view->render(compact('result', 'blocks', 'pagination',
                                       'page'));
        }
    } else {
        $view = new View('search');
        echo $view->render();
    }
});

//__END__
