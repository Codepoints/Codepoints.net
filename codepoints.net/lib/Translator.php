<?php

namespace Codepoints;

use Analog\Analog;
use Gettext\Translations;
use Negotiation\LanguageNegotiator;
use Codepoints\CachingMoLoader;

class Translator {

    private ?string $language = null;

    public const SUPPORTED_LANGUAGES = ['en', 'de', 'es', 'pl'];

    private ?Translations $translations = null;

    public function __construct() {
        $this->getLanguage();
        if ($this->language) {
            $mofile = dirname(__DIR__).'/locale/'.$this->language.'/LC_MESSAGES/messages.mo';
            if (is_file($mofile)) {
                $loader = new CachingMoLoader();
                $this->translations = $loader->loadFile($mofile);
            }
        }
    }

    public function translate(string $original) : string {
        if (! $this->translations) {
            return $original;
        }

        $text = $this->translations->find(null, $original);
        if (! $text) {
            return $original;
        }
        $text = $text->getTranslation();
        if (! $text) {
            return $original;
        }
        return $text;
    }

    public function getLanguage() : string {
        if (! $this->language) {
            $lang = preg_replace('/\s*([A-Za-z0-9_-]{2,32})?.*$/', '$1', filter_input(
                INPUT_SERVER,
                'HTTP_ACCEPT_LANGUAGE',
                FILTER_UNSAFE_RAW,
                FILTER_FLAG_STRIP_LOW & FILTER_FLAG_STRIP_HIGH & FILTER_FLAG_STRIP_BACKTICK
            ) ?? 'en');
            $persist = false;
            if (isset($_GET['lang']) && is_string($_GET['lang']) && ctype_alpha($_GET['lang'])) {
                $lang = $_GET['lang'];
                $persist = true;
            } elseif (isset($_COOKIE['lang']) && ctype_alpha($_COOKIE['lang'])) {
                $lang = $_COOKIE['lang'];
            } elseif (! $lang) {
                $lang = 'en';
            }
            $negotiator = new LanguageNegotiator();
            $bestLanguage = $negotiator->getBest($lang, self::SUPPORTED_LANGUAGES);

            $this->language = self::SUPPORTED_LANGUAGES[0];
            /**
             * @psalm-suppress UndefinedInterfaceMethod
             */
            if ($bestLanguage && in_array($bestLanguage->getType(), self::SUPPORTED_LANGUAGES)) {
                /**
                 * @psalm-suppress UndefinedInterfaceMethod
                 */
                $this->language = $bestLanguage->getType();
            }
            if ($persist) {
                setcookie('lang', (string)$this->language, time()+60*60*24*365, '/');
            }
        }

        return $this->language;
    }

    public static function getLanguageName(string $lang) : string {
        switch($lang) {
            case 'en': return 'english';
            case 'de': return 'deutsch';
            case 'es': return 'español';
            case 'pl': return 'polski';
            default:
                return $lang;
        }
    }

}
