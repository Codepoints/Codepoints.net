<?php

/**
 * leave out the '.json' extension, because the router strips it away
 * already */
$router->registerAction('manifest', function ($request, $o) {
    header('Content-Type: application/manifest+json; charset=UTF-8');
    require_once __DIR__.'/../lib/view.class.php';
    $images = array();
    foreach(array('16','32','57','64','70','72','114','128','144','150','310') as $size) {
        $images[] = array(
            'src' => url('/static/images/icon'.$size.'.png'),
            'sizes' => $size.'x'.$size,
            'type' => 'image/png',
        );
    }
    $images[] = array(
        'src' => url('/static/images/icon.png'),
        'sizes' => '256x256',
        'type' => 'image/png',
    );
    $images[] = array(
        'src' => url('/static/images/icon.svg'),
        'type' => 'image/svg+xml',
    );
    echo json_encode(array(
        'lang' => L10n::getDefaultLanguage(),
        'name' => _('All of Unicode at Codepoints.net'),
        'short_name' => 'Codepoints',
        'scope' => '/',
        'display' => 'standalone',
        'start_url' => Router::getRouter()->getUrl(),
        'theme_color' => '#660000',
        'background_color' => '#F7F7F7',
        'icons' => $images,
    ));
});


//__END__
