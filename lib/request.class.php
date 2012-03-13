<?php


/**
 * a HTTP request plus data
 */
class Request {

    public $type = 'text/html';

    public $lang = 'en';

    public $url = '/';

    public $data;

    public function __construct($url=Null) {
        if ($url === Null) {
            $url = $_SERVER['REQUEST_URI'];
        }
        $this->url = $url;
        if (array_key_exists('HTTP_ACCEPT', $_SERVER)) {
            $type = explode(',', $_SERVER['HTTP_ACCEPT']);
            $this->type = preg_replace('/^\s*([^;\s]+).*$/', '$1', $type[0]);
        }
        if (array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
            $lang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $this->lang = preg_replace('/^\s*([^;\s]+).*$/', '$1', $lang[0]);
        }
    }

}


//__END__
