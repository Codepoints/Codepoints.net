<?php


/**
 * own wrapper around the Mustache base class + l10n
 */
class Template {

    protected $template;
    protected $mustache;

    public function __construct($template) {
        $basedir = dirname(__DIR__);
        $l10n = L10n::get('mustache');
        $this->template = $template;
        $this->mustache = new Mustache_Engine(array(
            'cache' => "$basedir/cache",
            'loader' => new Mustache_Loader_FilesystemLoader(
                            "$basedir/static/tpl"),
            'partials_loader' => new Mustache_Loader_FilesystemLoader(
                                     "$basedir/static/tpl/partials"),
            'helpers' => array(
                '_' => function ($s) use ($l10n) {
                    return $l10n->gettext($s);
                },
                'get_url' => function ($url) {
                    return Router::getRouter()->getUrl($url);
                },
                'CACHE_BUST' => CACHE_BUST,
            ),
            'strict_callables' => true,
        ));
    }

    public function render($view = array()) {
        return $this->mustache->render($this->template, $view);
    }

}


//__END__
