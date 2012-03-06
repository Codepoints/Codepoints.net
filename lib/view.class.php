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


function cp($cp, $rel='') {
    echo _cp($cp, $rel);
}

function _cp($cp, $rel='') {
    if ($rel) {
        $rel = ' rel="'.q($rel).'"';
    }
    if (! $cp instanceof Codepoint) {
        if (is_array($cp)) {
            $cp = new Codepoint(hexdec($cp[0]), $cp[1]);
        } else {
            throw new Exception('Parameter 1 must be Codepoint or Array');
        }
    }
    return sprintf('<a class="cp"%s href="U+%s" title="%s">%s<img src="data:%s" alt="" /></a>',
           $rel, $cp, q($cp->getName()), $cp, $cp->getImage());
}


class View {

    protected $file;

    public function __construct($view) {
        $this->file = dirname(__FILE__)."/../views/$view.php";
    }

    public function render($params) {
        extract($params);
        ob_start();
        include($this->file);
        $out = ob_get_contents();
        @ob_end_clean();
        return $out;
    }

}


//__END__
