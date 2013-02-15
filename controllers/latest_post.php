<?php


$router->registerAction('blog-preview', function ($request, $o) {
    $cachefile = dirname(dirname(__FILE__)).'/cache/blog-preview';
    if (! file_exists($cachefile) || filemtime($cachefile) < time() - 60*60) {
        $data = explode('{', substr(file_get_contents('http://blog.codepoints.net/api/read/json?start=0&num=1&filter=text'), 0, -2), 2);
        $data = json_decode('{'.$data[1], true);
        if ($data) {
            $view = new View('blog-preview');
            $content = $view->render(array('post' => $data['posts'][0]));
            file_put_contents($cachefile, $content);
        }
    }
    readfile($cachefile);
});


//__END__
