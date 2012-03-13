<?php


/**
 * handle requests and route them to the appropriate action
 */
class Router {

    public $baseURL = '/projekte/visual-unicode/';

    protected $actions = array();

    protected $urls = array();

    protected static $defaultRouter;

    /**
     * singleton: get instance
     */
    public static function getRouter() {
        if (! self::$defaultRouter) {
            self::$defaultRouter = new self();
        }
        return self::$defaultRouter;
    }

    /**
     * register an action for an URL
     */
    public function registerAction($test, $action) {
        $this->actions[] = array($test, $action);
        return $this;
    }

    /**
     * get the action for an URL
     */
    public function getAction($url=Null) {
        if ($url === Null) {
            $url = substr($_SERVER['REQUEST_URI'], strlen($this->baseURL));
        }
        foreach ($this->actions as $action) {
            $r = $action[0]($url);
            if ($r !== False) {
                return array($action[1], $url, $r);
            }
        }
        return Null;
    }

    /**
     * register an URL creation scheme for a class
     */
    public function registerUrl($class, $action) {
        $this->urls[$class] = $action;
        return $this;
    }

    /**
     * get the URL for an object based on registerUrl input
     */
    public function getUrl($object) {
        $path = '';
        $class = get_class($object);
        if (array_key_exists($class, $this->urls)) {
            $path = $this->urls[$class]($object);
        }
        return $this->baseURL . $path;
    }

    /**
     * redirect to another URL
     */
    public function redirect($url) {
        if (substr($url, 0, 4) !== 'http' && $url[0] !== '/') {
            $url = $this->baseUrl . $url;
        }
        header('HTTP/1.0 303 See Other');
        header('Location: ' . $url);
        exit();
    }

}


//__END__
