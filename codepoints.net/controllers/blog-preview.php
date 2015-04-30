<?php


$router->registerAction('blog-preview', function ($request, $o) {
    $cachefile = dirname(dirname(__FILE__)).'/cache/blog-preview';
    $lang = L10n::get('messages')->getLanguage();
    $cachefile .= '.'.$lang;
    if (! file_exists($cachefile) || filemtime($cachefile) < time() - 60*60) {
        $data = json_decode(file_get_contents('https://blog.codepoints.net/current.json'), true);
        if ($data) {
            $view = new View('blog-preview');
            $content = $view->render(array('post' => $data));
            file_put_contents($cachefile, $content);
        }
    }
    readfile($cachefile);
});


//__END__
