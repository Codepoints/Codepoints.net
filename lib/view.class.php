<?php


function q($s) {
    if (is_array($s)) {
        return array_map('q', $s);
    } elseif (is_string($s)) {
        return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    } else {
        return $s;
    }
}


function e($s) {
    echo q($s);
}


function f() {
    $params = func_get_args();
    $format = $params[0];
    $params = q(array_slice($params, 1));
    call_user_func_array('printf', array_merge(array($format), $params));
}


function u($s) {
    return preg_replace('/[^a-z0-9-]+/', '_', strtolower($s));
}


function cp($cp, $rel='', $class='', $wrap='') {
    echo _cp($cp, $rel, $class, $wrap);
}

function _cp($cp, $rel='', $class='', $wrap='') {
    $router = Router::getRouter();
    if ($rel) {
        $rel = ' rel="'.q($rel).'"';
    }
    if ($class) {
        $class = ' '.q($class);
    }
    $r = array();
    if ($cp instanceof Codepoint) {
        $cp = array($cp);
    }
    foreach ($cp as $c) {
        if ($wrap) {
            $r[] = sprintf('<a%s href="%s" title="%s"><%s class="cp%s">%s<img src="'.
                'data:%s" alt="" height="16" width="16" /></%s></a>',
                $rel, q($router->getUrl($c)), q($c->getName()), $wrap, $class,
                q($c), q($c->getImage()), $wrap);
        } else {
            $r[] = sprintf('<a class="cp%s"%s href="%s" title="%s">%s<img src="'.
                'data:%s" alt="" height="16" width="16" /></a>',
                $class, $rel, q($router->getUrl($c)), q($c->getName()), q($c),
                q($c->getImage()));
        }
    }
    return join(' ', $r);
}

function bl($bl, $rel='', $class='', $wrap='') {
    echo _bl($bl, $rel, $class, $wrap);
}

function _bl($bl, $rel='', $class='', $wrap='') {
    $router = Router::getRouter();
    if ($rel) {
        $rel = ' rel="'.q($rel).'"';
    }
    if ($class) {
        $class = ' '.q($class);
    }
    if ($wrap) {
        $r = sprintf('<a%s href="%s"><%s class="bl%s"><img src="%sstatic/images/'.
                'blocks.min/%s.png" alt="" width="16" height="16" /> %s</%s></a>',
                $rel, q($router->getUrl($bl)), $wrap, $class, q($router->getUrl()),
                q(str_replace(' ', '_', $bl->getName())), q($bl->getName()), $wrap);
    } else {
        $r = sprintf('<a class="bl%s"%s href="%s"><img src="%sstatic/images/'.
                'blocks.min/%s.png" alt="" width="16" height="16" /> %s</a>',
                $class, $rel, q($router->getUrl($bl)), q($router->getUrl()),
                q(str_replace(' ', '_', $bl->getName())), q($bl->getName()));
    }
    return $r;
}

function _get($key, $default='') {
    if (array_key_exists($key, $_GET)) {
        return q($_GET[$key]);
    }
    return q($default);
}


class View {

    protected $file;

    public function __construct($view) {
        $this->file = dirname(__FILE__)."/../views/$view.php";
    }

    public function render($params=array()) {
        $info = UnicodeInfo::get();
        $router = Router::getRouter();
        extract($params);
        ob_start();
        include($this->file);
        $out = ob_get_contents();
        @ob_end_clean();
        return $out;
    }

}


//__END__
