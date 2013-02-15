<?php


$router->registerAction('blog-preview', function ($request, $o) {
    $cachefile = dirname(dirname(__FILE__)).'/cache/blog-preview';
    if (! file_exists($cachefile) || filemtime($cachefile) < time() - 60*60) {
        $data = json_decode('{'.explode('{', substr(file_get_contents('http://blog.codepoints.net/api/read/json?start=0&num=1&filter=text'), 0, -2), 2)[1], true);
        if ($data) {
            $content = sprintf('<h2><a href="%1$s">%2$s</a></h2><p>%3$s <a href="%1$s">...read on</a>.</p>',
                $data['posts'][0]['url-with-slug'],
                $data['posts'][0]['regular-title'],
                substr($data['posts'][0]['regular-body'], 0, 255)
            );
            file_put_contents($cachefile, $content);
        }
    }
    readfile($cachefile);
});


//__END__
