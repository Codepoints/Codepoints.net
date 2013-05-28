<?php


/**
 * own wrapper around the Mustache base class + l10n
 */
class Template {

    protected $mustache = null;

    public function __construct($template) {
        $l10n = L10n::get('mustache');
        $this->mustache = new Mustache_Engine(array(
            'cache' => __DIR__.'/../cache',
            'loader' => new Mustache_Loader_FilesystemLoader(dirname(dirname(__FILE__)).'/static/tpl'),
            'partials_loader' => new Mustache_Loader_FilesystemLoader(dirname(dirname(__FILE__)).'/static/tpl/partials'),
            'helpers' => array('_' => function ($s) use ($l10n) {
                return $l10n->gettext($s);
            }),
            'strict_callables' => true,
        ));
        $this->mustache->loadTemplate($template);
    }

    public function render($view = array()) {
        return $this->mustache->render($view);
    }

}


//__END__
