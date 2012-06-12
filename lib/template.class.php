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
class Template extends Mustache {

    public $__ = array(__CLASS__, '__translate');

    public $name = "Bob";

    public static function __translate($s) {
        return T_gettext($s);
    }

}


//__END__
