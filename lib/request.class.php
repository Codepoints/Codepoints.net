<?php


/**
 * a HTTP request plus data
 */
class Request {

    public $type = 'text/html';

    public $availableTypes = array('text/html', 'application/json');

    public $lang = 'en';

    public $availableLanguages = array('en');

    public $url = '/';

    public $trunkUrl = '/';

    public $data;

    public function __construct($url=Null) {
        if ($url === Null) {
            $url = $_SERVER['REQUEST_URI'];
        }
        $this->url = $this->trunkUrl = $url;
        if (array_key_exists('HTTP_ACCEPT', $_SERVER)) {
            $types = explode(',', $_SERVER['HTTP_ACCEPT']);
            for ($i = 0, $j = count($types); $i < $j; $i++) {
                $type = explode(';', $types[$i]);
                $type = trim($type[0]);
                if (in_array($type, $this->availableTypes)) {
                    $this->type = $type;
                    break;
                }
            }
        }
        if (array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
            $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            for ($i = 0, $j = count($langs); $i < $j; $i++) {
                $lang = explode(';', $langs[$i]);
                $lang = trim($lang[0]);
                if (in_array($lang, $this->availableLanguages)) {
                    $this->lang = $lang;
                    break;
                }
            }
        }
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        if ($ext) {
            $this->trunkUrl = substr($url, 0, -strlen($ext)-1);
            $ext = strtolower(ltrim($ext, '.'));
            switch ($ext) {
                case "js":
                case "json":
                    $this->type = 'application/json';
                    break;
                case "htm":
                case "html":
                    $this->type = 'text/html';
                    break;
            }
        }
    }

}


//__END__
