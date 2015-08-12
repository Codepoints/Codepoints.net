<?php

$router->registerAction(function ($url, $o) {
    // Codepoint Range
    if (preg_match('/^(?:U\+[0-9a-f]{4,6}(?:\.\.|-|,))+U\+[0-9a-f]{4,6}$/i', $url)) {
        return true;
    }
    return false;
}, function ($request, $o) {
    $range = $request->trunkUrl;
    $router = Router::getRouter();
    $result = SearchResult::parse($range, $o['db']);
    $page = isset($_GET['page'])? intval($_GET['page']) : 1;
    $result->page = $page - 1;
    if ($result->getCount() === 1) {
        $cp = $result->current();
        $router->redirect('U+'.$cp);
    }
    $pagination = new Pagination($result->getCount(), 128);
    $pagination->setPage($page);
    $view = new View('result');
    $blocks = null;
    echo $view->render(compact('range', 'blocks', 'result', 'pagination', 'page'));
});

//__END__
