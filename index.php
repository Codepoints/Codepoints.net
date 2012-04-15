<?php


/**
 * set DEBUG level
 */
define('VU_DEBUG', False);


/**
 * load classes from lib/
 */
function __autoload($class) {
    require_once 'lib/' . strtolower($class) . '.class.php';
}


/**
 * log $msg to /tmp/visual-unicode.log
 */
function flog($msg) {
   if (VU_DEBUG) {
       error_log(sprintf("[%s]\n%s\n", date("r"), $msg), 3,
                 '/tmp/visual-unicode.log');
   }
}


$db = new DB('sqlite:'.dirname(__FILE__).'/ucd.sqlite');
$router = Router::getRouter();


$router->addSetting('db', $db)
       ->addSetting('info', UnicodeInfo::get())

->registerAction('', function ($request, $o) {
    // Index
    $view = new View('front');
    echo $view->render(array('planes' => UnicodePlane::getAll($o['db'])));
})

->registerAction('planes', function ($request, $o) {
    // all planes
    $view = new View('planes');
    echo $view->render(array('planes' => UnicodePlane::getAll($o['db'])));
})

->registerAction('wizard', function ($request, $o) {
    // a "find my CP" wizard
    $view = new View('wizard');
    echo $view->render();
})

->registerAction('random', function ($request, $o) {
    // random codepoint
    $x = $o['db']->prepare('SELECT cp FROM codepoints ORDER BY RANDOM() LIMIT 1');
    $x->execute();
    $row = $x->fetch();
    $router = Router::getRouter();
    $router->redirect(sprintf('U+%04X', $row['cp']));
})

->registerAction(array('about'), function ($request, $o) {
    // static pages
    $view = new View($request->trunkUrl);
    echo $view->render();
})

->registerAction('search', function ($request, $o) {
    // Search
    $router = Router::getRouter();
    $result = new SearchResult(array(), $o['db']);
    $info = UnicodeInfo::get();
    $cats = $info->getCategoryKeys();
    $cats = array_merge($cats, array('int'));
    $blocks = array();
    foreach ($_GET as $k => $v) {
        if ($k === 'q' && $v) {
            if (mb_strlen($v, 'UTF-8') === 1) {
                $result->addQuery('cp', unpack('N', mb_convert_encoding($v,
                                        'UCS-4BE', 'UTF-8')));
            } else {
                if (ctype_xdigit($v) && in_array(strlen($v), array(4,5,6))) {
                    $result->addQuery('cp', hexdec($v), '=', 'OR');
                }
                if (substr(strtolower($v), 0, 2) === 'u+' &&
                    ctype_xdigit(substr($v, 2))) {
                    $result->addQuery('cp', hexdec(substr($v, 2)), '=', 'OR');
                }
                if (ctype_digit($v) && strlen($v) < 8) {
                    $result->addQuery('cp', intval($v), '=', 'OR');
                }
                $v = "%$v%";
                $result->addQuery('na', $v, 'LIKE', 'OR');
                $result->addQuery('na1', $v, 'LIKE', 'OR');
                $result->addQuery('isc', $v, 'LIKE', 'OR');
                $result->addQuery('kDefinition', $v, 'LIKE', 'OR');
                if (preg_match('/\blowercase\b/i', $v)) {
                    $result->addQuery('gc', 'lc', '=', 'OR');
                }
                if (preg_match('/\buppercase\b/i', $v)) {
                    $result->addQuery('gc', 'uc', '=', 'OR');
                }
                if (preg_match('/\btitlecase\b/i', $v)) {
                    $result->addQuery('gc', 'tc', '=', 'OR');
                }
                $blocks = UnicodeBlock::search($v, $o['db']);
            }
        } elseif ($v && $k === 'scx') {
            // scx is a list of sc's
            $result->addQuery($k, $v);
            $v2 = explode(' ', $v);
            foreach($v2 as $v3) {
                $result->addQuery($k, "%$v3%", 'LIKE', 'OR');
            }
        } elseif ($v && in_array($k, $cats)) {
            $result->addQuery($k, $v);
        }
    }
    $page = isset($_GET['page'])? intval($_GET['page']) : 1;
    $result->page = $page - 1;
    if (count($result->getQuery())) {
        if ($result->getCount() === 1) {
            $cp = $result->current();
            $router->redirect('U+'.$cp);
        } else {
            $pagination = new Pagination($result->getCount(), 128);
            $pagination->setPage($page);
            $view = new View('result');
            echo $view->render(compact('result', 'blocks', 'pagination',
                                       'page'));
        }
    } else {
        $view = new View('search');
        echo $view->render();
    }
})

->registerAction(function ($url, $o) {
    // Plane
    if (substr($url, -6) === '_plane') {
        try {
            $plane = new UnicodePlane($url, $o['db']);
        } catch(Exception $e) {
            try {
                $plane = new UnicodePlane(substr($url, 0, -6), $o['db']);
            } catch(Exception $e) {
                return False;
            }
        }
        return $plane;
    }
    return False;
}, function($request) {
    $plane = $request->data;
    $view = new View('plane.html');
    echo $view->render(compact('plane'));
})

->registerAction(function ($url, $o) {
    // Single Codepoint
    if (substr($url, 0, 2) === 'U+' && ctype_xdigit(substr($url, 2))) {
        try {
            $codepoint = Codepoint::getCP(hexdec(substr($url, 2)), $o['db']);
            $codepoint->getName();
        } catch (Exception $e) {
            $router = Router::getRouter();
            $router->addSetting('noCP', true);
            return False;
        }
        return $codepoint;
    }
    return False;
}, function ($request, $o) {
    $view = new View('codepoint.html');
    echo $view->render(array(
        'codepoint' => $request->data));
})

->registerAction(function ($url, $o) {
    // Codepoint Range
    if (preg_match('/^(?:U\+[0-9a-f]{4,6}(?:\.\.|-|,))+U\+[0-9a-f]{4,6}$/i', $url)) {
        return True;
    }
    return False;
}, function ($request, $o) {
    $range = $request->trunkUrl;
    $router = Router::getRouter();
    $result = SearchResult::parse($range, $o['db']);
    $page = isset($_GET['page'])? intval($_GET['page']) : 1;
    $result->page = $page - 1;
    if ($result->getCount() === 1) {
        $cp = $result->current();
        $router->redirect('U+'.$cp);
    }
    $pagination = new Pagination($result->getCount(), 128);
    $pagination->setPage($page);
    $view = new View('result');
    $blocks = Null;
    echo $view->render(compact('range', 'blocks', 'result', 'pagination', 'page'));
})

->registerAction(function ($url, $o) {
    // Block
    if (! preg_match('/[^a-z0-9_-]/', $url)) {
        try {
            $block = new UnicodeBlock($url, $o['db']);
        } catch(Exception $e) {
            return False;
        }
        return $block;
    }
    return False;
}, function($request) {
    $block = $request->data;
    $view = new View('block.html');
    echo $view->render(compact('block'));
})

->registerAction(function ($url, $o) {
    // Single characters
    $c = rawurldecode($url);
    if (mb_strlen($c, 'UTF-8') === 1) {
        return unpack('N', mb_convert_encoding($c, 'UCS-4BE', 'UTF-8'));
    }
    return False;
}, function($request) {
    $router = Router::getRouter();
    $router->redirect(sprintf('U+%04X', $request->data[1]));
})

;

$router->registerUrl('Codepoint', function ($object) {
    return sprintf("U+%s", $object->getId('hex'));
})
->registerUrl('UnicodeBlock', function ($object) {
    return str_replace(' ', '_', strtolower($object->getName()));
})
->registerUrl('UnicodePlane', function ($object) {
    $path = str_replace(' ', '_', strtolower($object->getName()));
    if (substr($path, -6) !== '_plane') {
        $path .= '_plane';
    }
    return $path;
})
->registerUrl('SearchResult', function ($object) {
    $path = 'search';
    if ($object instanceof SearchResult) {
        $q = $object->getQuery;
        $path .= http_build_query($q);
    }
    return $path;
});


if ($router->callAction() === False) {
    header('HTTP/1.0 404 Not Found');
    $block = Null;
    $planes = UnicodePlane::getAll($db);
    if ($router->getSetting('noCP')) {
        try {
            $block = UnicodeBlock::getForCodepoint(
                hexdec(substr($router->getSetting('request')->trunkUrl, 2)),
                $router->getSetting('db'));
        } catch(Exception $e) {}
    }
    $view = new View('error404');
    echo $view->render(compact('planes', 'block'));
}


// __END__
