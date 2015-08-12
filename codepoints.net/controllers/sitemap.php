<?php

$router

->registerAction('sitemap', function($request, $o) {
    // sitemap
    header('Content-Type: application/xml; charset=utf-8');
    $view = new View('sitemap.xml');
    $blocks = UnicodeBlock::getAllNames($o['db']);
    echo $view->render(compact('blocks'));
})

->registerAction('sitemap/base', function($request, $o) {
    // sitemap part 2
    header('Content-Type: application/xml; charset=utf-8');
    $view = new View('sitemap/base.xml');
    echo $view->render();
})

->registerAction(function ($url, $o) {
    // sitemap for a block
    if (substr($url, 0, 8) === 'sitemap/') {
        try {
            $block = new UnicodeBlock(substr(substr($url, 8), 0),
                                      $o['db']);
        } catch(Exception $e) {
            return false;
        }
        return $block;
    }
    return false;
}, function($request, $o) {
    header('Content-Type: application/xml; charset=utf-8');
    $view = new View('sitemap/block.xml');
    echo $view->render(array('block'=>$request->data));
});

//__END__
