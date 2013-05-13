<?php


/**
 * own wrapper around the Mustache base class + l10n
 */
class Template {

    protected $mustache = null;
    protected $l10n;

    public function __construct($template) {
        $this->mustache = new Mustache_Engine(array(
            'loader' => new Mustache_Loader_FilesystemLoader(dirname(dirname(__FILE__)).'/static/tpl'),
            'partials_loader' => new Mustache_Loader_FilesystemLoader(dirname(dirname(__FILE__)).'/static/tpl/partials'),
        ));
        $this->mustache->loadTemplate($template);
        $this->l10n = L10n::get('mustache');
    }

    public function render($view = array()) {
        $view['_'] = array($this, '_translate');
        return $this->mustache->render($view);
    }

    public function _translate($s) {
        return $this->l10n->gettext($s);
    }

}


//__END__
