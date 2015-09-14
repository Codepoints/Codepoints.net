<?php


require_once __DIR__."/tools.php";


function _e($s) {
    echo q(L10n::get('messages')->gettext($s));
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
        $img = $c->getImage();
        if (substr($img, -strlen(Codepoint::$defaultImage)) ===
            Codepoint::$defaultImage) {
            $img = sprintf('<span class="img">%s</span>', $c->getChar());
        } else {
            $img = sprintf('<img src="%s" alt="%s" height="16" width="16">',
                        $img, $c->getSafeChar());
        }
        if ($wrap) {
            $r[] = sprintf('<a%s href="%s" title="%s"><%s class="cp%s" data-cp="%s">'.
                '%s</%s></a>',
                $rel, q($router->getUrl($c)), q($c->getName()), $wrap, $class,
                q($c), $img, $wrap);
        } else {
            $r[] = sprintf('<a class="cp%s"%s href="%s" title="%s" data-cp="%s">%s</a>',
                $class, $rel, q($router->getUrl($c)), q($c->getName()), q($c),
                $img);
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
                'blocks/%s.svgz" alt="" width="16" height="16" /> %s</%s></a>',
                $rel, q($router->getUrl($bl)), $wrap, $class, q($router->getUrl()),
                q(str_replace(' ', '_', $bl->getName())), q($bl->getName()), $wrap);
    } else {
        $r = sprintf('<a class="bl%s"%s href="%s"><img src="%sstatic/images/'.
                'blocks/%s.svgz" alt="" width="16" height="16" /> %s</a>',
                $class, $rel, q($router->getUrl($bl)), q($router->getUrl()),
                q(str_replace(' ', '_', $bl->getName())), q($bl->getName()));
    }
    return $r;
}

function icon($name) {
    return sprintf('<i class="icon-%s" aria-role="presentation"></i>', $name);
}

function _get($key, $default='') {
    if (array_key_exists($key, $_GET)) {
        return q($_GET[$key]);
    }
    return q($default);
}

function render($view, $params=array()) {
    $view = new View($view);
    echo $view->render($params);
}


class View {

    protected $file;

    protected $isTemplate = false;

    public function __construct($view) {
        $base = dirname(__DIR__);
        if (is_file($base."/static/tpl/$view.mustache")) {
            $this->file = $view;
            $this->isTemplate = true;
        } else {
            $this->file = $base."/views/$view.php";
        }
    }

    public function render($params=array()) {
        $params['info'] = UnicodeInfo::get();
        $params['router'] = Router::getRouter();
        $params['lang'] = L10n::getDefaultLanguage();

        if ($this->isTemplate) {
            $tpl = new Template($this->file);
            $out = $tpl->render($params);
        } else {
            extract($params);
            ob_start();
            include($this->file);
            $out = ob_get_contents();
            @ob_end_clean();
        }
        return $out;
    }

}


//__END__
