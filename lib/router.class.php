<?php


class Router {

    public $baseURL = '/projekte/visual-unicode/';

    protected $actions = array();

    protected $urls = array();

    protected static $defaultRouter;

    public static function getRouter() {
        if (! self::$defaultRouter) {
            self::$defaultRouter = new self();
        }
        return self::$defaultRouter;
    }

    public function registerAction($test, $action) {
        $this->actions[] = array($test, $action);
        return $this;
    }

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

    public function registerUrl($class, $action) {
        $this->urls[$class] = $action;
        return $this;
    }

    public function getUrl($object) {
        $path = '';
        $class = get_class($object);
        if (in_array($class, $this->urls)) {
            $path = $this->urls[$class]($object);
        }
        switch (get_class($object)) {
            case "Codepoint":
                $path = sprintf("U+%04X", $object);
                break;
            case "UnicodeBlock":
                $path = str_replace(' ', '_', strtolower($object->getName()));
                break;
            case "UnicodePlane":
                $path = str_replace(' ', '_', strtolower($object->getName()));
                if (substr($path, -6) !== '_plane') {
                    $path .= '_plane';
                }
                break;
        }
        return $this->baseURL . $path;
    }

}


//__END__
