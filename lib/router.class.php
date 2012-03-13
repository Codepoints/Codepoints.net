<?php


/**
 * handle requests and route them to the appropriate action
 */
class Router {

    protected $baseURL = '/';

    protected $actions = array();

    protected $urls = array();

    protected static $defaultRouter;

    /**
     * singleton: get instance
     */
    public static function getRouter() {
        if (! self::$defaultRouter) {
            self::$defaultRouter = new self();
            self::$defaultRouter->base(dirname($_SERVER['PHP_SELF']).'/');
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
            if ($url === False) {
                $url = '';
            }
        }
        foreach ($this->actions as $action) {
            $r = $action[0]($url);
            if ($r !== False) {
                $req = new Request($url);
                $req->data = $r;
                return array($action[1], $req);
            }
        }
        return Null;
    }

    /**
     * call the registered action for an URL
     */
    public function callAction($url=Null) {
        $action = $this->getAction($url);
        if ($action !== Null) {
            $action[0]($action[1]);
            return True;
        }
        return False;
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
        if (is_string($object)) {
            $class = $object;
        } else {
            $class = get_class($object);
        }
        if (array_key_exists($class, $this->urls)) {
            $path = $this->urls[$class]($object);
        }
        return $this->baseURL . $path;
    }

    /**
     * get or set the base URL
     */
    public function base($val=NULL) {
        $b = $this->baseURL;
        if ($val !== NULL) {
            $this->baseURL = $val;
        }
        return $b;
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
