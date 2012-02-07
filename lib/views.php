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


function u($s) {
    return preg_replace('/[^a-z0-9-]+/', '_', strtolower($s));
}


function load_view($view, $params=array(), $safe=array()) {
    foreach ($params as $k => $v) {
        if (!in_array($k, $safe)) {
            $params[$k] = q($v);
        }
    }
    extract($params);
    ob_start();
    include(dirname(__file__)."/../views/$view.php");
    $out = ob_get_contents();
    @ob_end_clean();
    return $out;
}


function print_view($view, $params=array(), $safe=array()) {
    echo load_view($view, $params, $safe);
}


//__END__
