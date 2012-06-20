<?php


require_once "lib/gettext/gettext.inc";


/**
 * wrapper for php_gettext
 */
class L10n {

    /**
     * the language in use, default is english
     */
    protected $lang = "en";

    /**
     * the message domain
     */
    protected $domain = "messages";

    /**
     * the cached L10n instances
     */
    protected static $instances = array();

    /**
     * construct a new l10n wrapper
     */
    function __construct($lang = Null, $domain = Null) {
        if ($lang) {
            $this->setLanguage($lang);
        }
        if ($domain) {
            $this->domain = $domain;
        }
        $this->setDomain($this->domain);
    }

    /**
     * set the language in use
     */
    public function setLanguage($lang) {
        $this->lang = $lang;
        _setlocale(LC_MESSAGES, $lang);
    }

    /**
     * set the domain in use
     */
    public function setDomain($domain) {
        $this->domain = $domain;
        _bindtextdomain($domain, realpath('./locale'));
        _bind_textdomain_codeset($domain, 'UTF-8');
        _textdomain($domain);
    }

    /**
     * translate a string
     */
    public function gettext($s) {
        $l10n = _get_reader($this->domain);
        return $l10n->translate($s);
    }

    /**
     * singleton getter
     */
    public static function get($domain="messages") {
        if (! array_key_exists($domain, self::$instances)) {
            self::$instances[$domain] = new self(Null, $domain);
        }
        return self::$instances[$domain];
    }

}


//__END__
