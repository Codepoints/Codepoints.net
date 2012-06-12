<?php


require_once "lib/gettext/gettext.inc";
$locale = (isset($_COOKIE['lang']))? $_COOKIE['lang'] : 'en';
T_setlocale(LC_MESSAGES, $locale);
$domain = 'mustache';
T_bindtextdomain($domain, realpath('./locale'));
T_bind_textdomain_codeset($domain, 'UTF-8');
T_textdomain($domain);


/**
 * own extension to the Mustache base class + i18n
 */
class Template {

    protected $mustache = null;

    public function __construct($template) {
        $partials = new MustacheLoader(dirname(__FILE__)."/../static/tpl");
        $this->mustache = new Mustache($template, null, $partials, null);
    }

    public function render($view = array()) {
        $view['_'] = array($this, '_translate');
        return $this->mustache->render(null, $view, null);
    }

    public function _translate($s) {
        return T_gettext($s);
    }

}


//__END__
