<?php

namespace Codepoints;

use \Analog\Analog;
use \Gettext\Loader\MoLoader;
use \Gettext\Translations;
use \Negotiation\LanguageNegotiator;

class Translator {

    private ?string $language = null;

    private array $supportedLanguages = ['en', 'de', 'pl'];

    private ?Translations $translations = null;

    public function __construct() {
        $this->getLanguage();
        if ($this->language) {
            $mofile = dirname(__DIR__).'/locale/'.$this->language.'/LC_MESSAGES/messages.mo';
            if (is_file($mofile)) {
                $loader = new MoLoader();
                $this->translations = $loader->loadFile($mofile);
            }
        }
    }

    /**
     * @param array $args
     */
    public function translate(string $original, ...$args) : string {
        if (! $this->translations) {
            return $original;
        }

        $text = $this->translations->find(null, $original);
        if (! $text) {
            return $original;
        }
        $text = $text->getTranslation();

        if (empty($args)) {
            return $text;
        }
        return is_array($args[0]) ? strtr($text, $args[0]) : vsprintf($text, $args);
    }

    public function getLanguage() : string {
        if (! $this->language) {
            $lang = filter_input(
                INPUT_SERVER,
                'HTTP_ACCEPT_LANGUAGE',
                FILTER_SANITIZE_STRING,
                FILTER_FLAG_STRIP_LOW & FILTER_FLAG_STRIP_HIGH & FILTER_FLAG_STRIP_BACKTICK
            );
            $persist = false;
            if (isset($_GET['lang']) && ctype_alpha($_GET['lang'])) {
                $lang = $_GET['lang'];
                $persist = true;
            } elseif (isset($_COOKIE['lang']) && ctype_alpha($_COOKIE['lang'])) {
                $lang = $_COOKIE['lang'];
            }
            $negotiator = new LanguageNegotiator();
            $bestLanguage = $negotiator->getBest($lang, $this->supportedLanguages);

            $this->language = $this->supportedLanguages[0];
            if ($bestLanguage && in_array($bestLanguage->getType(), $this->supportedLanguages)) {
                $this->language = $bestLanguage->getType();
            }
            if ($persist) {
                setcookie('lang', (string)$this->language, time()+60*60*24*365, '/');
            }
        }

        return $this->language;
    }

}
