<?php
/**
 * Welcome to the source of Codepoints.net!
 *
 * This is the main (and sole) entry to the site. Classes in
 * lib/*.class.php are auto-loaded. The controller for the URL
 * structure is lib/router.class.php.
 *
 * In lib/view.class.php is a view system defined, with the
 * views guiding the output living in views/.
 *
 * To get an instance of the database up and running, visit
 * <https://github.com/Boldewyn/unicodeinfo>. On a regular
 * *NIX system, a simple `make` in that project should provide
 * you with the ucd.sqlite to run this instance.
 *
 * This code is dually licensed under GPL and MIT. See
 * <http://codepoints.net/about#main> for details.
 */


/**
 * define Unicode Version in use
 */
define('UNICODE_VERSION', '6.1.0');


/**
 * set DEBUG level
 */
define('CP_DEBUG', 1);


/**
 * cache busting string
 */
define('CACHE_BUST', 'fe4f058ae607d3a9ea3b66f0d65464d5d40e2e1a');


/* enable gzip compression of HTML */
ini_set('zlib.output_compression', True);


/**
 * load classes from lib/
 */
function __autoload($class) {
    require_once 'lib/' . strtolower($class) . '.class.php';
}


/**
 * log $msg to /tmp/codepoints.log
 */
function flog($msg) {
   if (CP_DEBUG) {
       error_log(sprintf("[%s] %s\n", date("c"), trim($msg)), 3,
                 '/tmp/codepoints.log');
   }
}


$db = new DB('sqlite:'.dirname(__FILE__).'/ucd.sqlite');
$router = Router::getRouter();


$router->addSetting('db', $db)
       ->addSetting('info', UnicodeInfo::get())

->registerAction('', function ($request, $o) {
    // Index
    $view = new View('front');
    $x = $o['db']->prepare('SELECT COUNT(*) AS c FROM codepoints');
    $x->execute();
    $row = $x->fetch();
    $Daily = new DailyCP();
    $daily = $Daily->get(date('Y-m-d'), $o['db']);
    echo $view->render(array('planes' => UnicodePlane::getAll($o['db']),
      'nCPs' => $row['c'], 'daily' => $daily));
})

->registerAction('planes', function ($request, $o) {
    // all planes
    $view = new View('planes');
    echo $view->render(array('planes' => UnicodePlane::getAll($o['db'])));
})

->registerAction('random', function ($request, $o) {
    // random codepoint
    $x = $o['db']->prepare('SELECT cp FROM codepoints ORDER BY RANDOM() LIMIT 1');
    $x->execute();
    $row = $x->fetch();
    $router = Router::getRouter();
    $router->redirect(sprintf('U+%04X', $row['cp']));
})

->registerAction('api/login', function ($request, $o) {
    // BrowserID login
    header('Content-Type: application/json');

    if (! isset($_GET['assertation'])) {
        die('{"status":"error","message":"Missing parameter"}');
    }

    $ch = curl_init();
    $data= array('assertation' => $_GET['assertation'],
                 'audience' => 'http://codepoints.net');
    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, 'https://browserid.org/verify');
    curl_setopt($ch, CURLOPT_POST, True);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $result = curl_exec($ch);
    curl_close($ch);
    $state = json_decode($result);

    if ($state === NULL || ! array_key_exists('status', $state)) {
        die('{"status":"error","message":"Couldn\'t verify assertation"}');
    } elseif ($state['status'] !== 'okay') {
        die('{"status":"error","message":"Assertation wrong"}');
    } else {
        echo '{"status":"okay"}';
    }
})

->registerAction(array('about', 'glossary'), function ($request, $o) {
    // static pages
    $view = new View($request->trunkUrl);
    echo $view->render();
})

->registerAction('scripts', function ($request, $o) {
    // scripts
    $cur = $o['db']->prepare('SELECT iso, name,
        -- (SELECT abstract FROM script_abstract
        --   WHERE script_abstract.sc = scripts.iso) abstract,
        (SELECT COUNT(*) FROM codepoint_script
          WHERE codepoint_script.sc = scripts.iso) count
        FROM scripts');
    $cur->execute();
    $scripts = $cur->fetchAll(PDO::FETCH_ASSOC);
    $view = new View($request->trunkUrl);
    echo $view->render(array('scripts' => $scripts));
})

->registerAction('wizard', function ($request, $o) {
    // the "find my CP" wizard
    $router = Router::getRouter();
    if (isset($_GET['_wizard']) && $_GET['_wizard'] === '1') {
        $result = new SearchResult(array(), $o['db']);
        foreach ($_GET as $k => $v) {
            switch ($k) {
                case 'def':
                    if ($v) {
                        $result->addQuery('kDefinition', "%$v%", 'LIKE');
                    }
                    break;
                case 'strokes':
                    if (ctype_digit($v) && (int)$v > 0) {
                        $result->addQuery('kTotalStrokes', $v);
                    }
                    break;
                case 'archaic':
                    if ($v === '1') {
                        $result->addQuery('sc', UnicodeInfo::$archaicScripts);
                    } elseif ($v === '0') {
                        $result->addQuery('sc', UnicodeInfo::$recentScripts);
                    }
                    break;
                case 'confuse':
                    if ($v === '1') {
                        $result->addQuery('confusables', 0, '>');
                    }
                    break;
                case 'composed':
                    if ($v >= 1) {
                        $result->addQuery('NFKD_QC', 'No');
                    } elseif ($v === '0') {
                        $result->addQuery('NFKD_QC', 'Yes');
                    }
                    break;
                case 'incomplete':
                    if ($v === '1') {
                        $result->addQuery('ccc', 0, '>');
                    } elseif ($v === '0') {
                        $result->addQuery('ccc', 0);
                    }
                    break;
                case 'punctuation':
                    if ($v === '1') {
                        $result->addQuery('gc', array('Pc', 'Pd', 'Ps', 'Pe',
                                                      'Pi', 'Pf', 'Po'));
                    } elseif ($v === '0') {
                        $result->addQuery('gc', array('Pc', 'Pd', 'Ps', 'Pe',
                                                      'Pi', 'Pf', 'Po'), '!=');
                    }
                    break;
                case 'symbol':
                    if ($v === 's') {
                        $result->addQuery('gc', array('Sm', 'Sc', 'Sk', 'So'));
                    } elseif ($v === 'c') {
                        $result->addQuery('gc', array('Cc', 'Cf', 'Cs', 'Co',
                                                      'Cn'));
                    } elseif ($v === 't') {
                        $result->addQuery('gc', array('Sm', 'Sc', 'Sk', 'So',
                                                      'Cc', 'Cf', 'Cs', 'Co',
                                                      'Cn'), '!=');
                    }
                    break;
                case 'number':
                    if ($v === '1') {
                        $result->addQuery('gc', array('Nd', 'Nl', 'No'));
                    } elseif ($v === '0') {
                        $result->addQuery('gc', array('Nd', 'Nl', 'No'), '!=');
                    }
                    break;
                case 'case':
                    if ($v === 'l') {
                        $result->addQuery('gc', 'Ll');
                    } elseif ($v === 'u') {
                        $result->addQuery('gc', 'Lu');
                    } elseif ($v === 't') {
                        $result->addQuery('gc', 'Lt');
                    } elseif ($v === 'y') {
                        $result->addQuery('gc', array('Lu', 'Ll', 'Lt'));
                    } elseif ($v === 'n') {
                        $result->addQuery('gc', array('Lu', 'Ll', 'Lt'), '!=');
                    }
                    break;
                case 'region':
                    if (array_key_exists($v, UnicodeInfo::$regionToBlock)) {
                        $result->addQuery('block',
                                          UnicodeInfo::$regionToBlock[$v]);
                    }
                    break;
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
                $blocks = array();
                $wizard = True;
                echo $view->render(compact('result', 'blocks', 'pagination',
                                        'page', 'wizard'));
            }
        } else {
            $view = new View('wizard');
            echo $view->render(array('message'=> 'Nothing found'));
        }
    } else {
        $view = new View('wizard');
        echo $view->render();
    }
})

->registerAction('search', function ($request, $o) {
    // Search
    $router = Router::getRouter();
    $result = new SearchResult(array(), $o['db']);
    $info = UnicodeInfo::get();
    $cats = $info->getCategoryKeys();
    $cats = array_merge($cats, array('int'));
    $blocks = array();
    $_q = Null;
    foreach ($_GET as $k => $v) {
        if ($k === 'q' && $v) {
            $_q = $v;
            // "q" is a special case: We parse the query and try to
            // figure, what's searched
            if (mb_strlen($v, 'UTF-8') === 1) {
                // seems to be one single character
                $result->addQuery('cp', unpack('N', mb_convert_encoding($v,
                                        'UCS-4BE', 'UTF-8')));
            } else {
                foreach (preg_split('/\s+/', $v) as $vv) {
                    if (ctype_xdigit($vv) && in_array(strlen($vv), array(4,5,6))) {
                        $result->addQuery('cp', hexdec($vv), '=', 'OR');
                    }
                    if (substr(strtolower($vv), 0, 2) === 'u+' &&
                        ctype_xdigit(substr($vv, 2))) {
                        $result->addQuery('cp', hexdec(substr($vv, 2)), '=', 'OR');
                    }
                    if (ctype_digit($vv) && strlen($vv) < 8) {
                        $result->addQuery('cp', intval($vv), '=', 'OR');
                    }
                    $vv = "%$vv%";
                    $result->addQuery('na', $vv, 'LIKE', 'OR');
                    $result->addQuery('na1', $vv, 'LIKE', 'OR');
                    $result->addQuery('kDefinition', $vv, 'LIKE', 'OR');
                    $result->addQuery('alias', $vv, 'LIKE', 'OR');
                    $result->addQuery('abstract', $vv, 'LIKE', 'OR');
                    if (preg_match('/\blowercase\b/i', $vv)) {
                        $result->addQuery('gc', 'lc', '=', 'OR');
                    }
                    if (preg_match('/\buppercase\b/i', $vv)) {
                        $result->addQuery('gc', 'uc', '=', 'OR');
                    }
                    if (preg_match('/\btitlecase\b/i', $vv)) {
                        $result->addQuery('gc', 'tc', '=', 'OR');
                    }
                    $blocks = array_unique(array_merge($blocks,
                                        UnicodeBlock::search($vv, $o['db'])));
                }
            }
        } elseif ($v && $k === 'scx') {
            // scx is a list of sc's
            $result->addQuery($k, $v);
            $v2 = explode(' ', $v);
            foreach($v2 as $v3) {
                $result->addQuery($k, "%$v3%", 'LIKE', 'OR');
            }
        } elseif ($k === 'int' && $v !== "") {
            $v = preg_split('/\s+/', $v);
            foreach($v as $v2) {
                if (ctype_digit($v2)) {
                    $result->addQuery($k, $v2, '=', 'OR');
                }
            }
        } elseif ($v && in_array($k, $cats)) {
            $result->addQuery($k, $v);
        }
        // else: that's an unrecognized GET param. Ignore it.
    }
    $page = isset($_GET['page'])? intval($_GET['page']) : 1;
    $result->page = $page - 1;
    if (count($result->getQuery())) {
        if ($result->getCount() === 0 && $_q) {
            $cps = Codepoint::getForString($_q, $o['db']);
            $view = new View('result');
            echo $view->render(compact('result', 'blocks', 'cps',
                                       'page'));
        } elseif ($result->getCount() === 1) {
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

->registerAction('codepoint_of_the_day', function($request, $o) {
    // Codepoint of the Day
    if ($request->type === 'application/xml') {
        header('Content-Type: application/xml; charset=utf-8');
        $Daily = new DailyCP($o['db']);
        $cps = $Daily->getSome(30);
        $view = new View('dailycp.feed');
        echo $view->render(compact('cps'));
        return;
    }
    $date = NULL;
    $today = date('Y-m-d');
    if (isset($_GET['date'])) {
        if (preg_match('/^20[0-9]{2}-[01][0-9]-[0-3][0-9]$/', $_GET['date'])) {
            $date = $_GET['date'];
        }
    } else {
        $date = $today;
    }
    if ($date) {
        $Daily = new DailyCP();
        list($codepoint, $description) = $Daily->get($date, $o['db']);
        $tpl = 'dailycp';
        if (! $codepoint) {
            list($codepoint, $description) = array(false, false);
            $tpl .= '_not';
        }
        $view = new View($tpl);
        echo $view->render(compact('codepoint', 'description',
                                   'date', 'today'));
    } else {
        throw new RoutingError();
    }
})

->registerAction('sitemap', function($request, $o) {
    // sitemap
    header('Content-Type: application/xml; charset=utf-8');
    $view = new View('sitemap.xml');
    $blocks = UnicodeBlock::getAllNames($o['db']);
    echo $view->render(compact('blocks'));
})

->registerAction('sitemap/base', function($request, $o) {
    // sitemap part 2
    header('Content-Type: application/xml; charset=utf-8');
    $view = new View('sitemap/base.xml');
    echo $view->render();
})

->registerAction(function ($url, $o) {
    // sitemap for a block
    if (substr($url, 0, 8) === 'sitemap/') {
        try {
            $block = new UnicodeBlock(substr(substr($url, 8), 0),
                                      $o['db']);
        } catch(Exception $e) {
            return False;
        }
        return $block;
    }
    return False;
}, function($request, $o) {
    header('Content-Type: application/xml; charset=utf-8');
    $view = new View('sitemap/block.xml');
    echo $view->render(array('block'=>$request->data));
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

->registerAction(function ($url, $o) {
    // Possible codepoint name, like "LATIN CAPITAL LETTER A"
    $c = rawurldecode($url);
    if (preg_match('/^[A-Z][A-Z0-9_ -]{1,127}$/', $c)) {
        // shortest: "OX", longest has 83 chars (Unicode 6.1)
        $cp = False;
        try {
            $cp = Codepoint::getByName($c, $o['db']);
        } catch (Exception $e) {
            return False;
        }
        return $cp;
    }
    return False;
}, function($request) {
    $router = Router::getRouter();
    $router->redirect(sprintf('U+%04X', $request->data->getId()));
})

->registerAction(function ($url, $o) {
    // Script description: script/Xxxx
    if (preg_match('/^api\/script\/(?:[A-Z][a-z]{3})(?:%20[A-Z][a-z]{3})*$/', $url, $m)) {
        return True;
    }
    return False;
}, function($request, $o) {
    header('Content-Type: application/json; charset=UTF-8');
    $trunk = rawurldecode(substr($request->trunkUrl, 11));
    $j = array();
    $found = False;
    $stm = $o['db']->prepare('SELECT abstract, src
                                FROM script_abstract WHERE sc = :sc');
    foreach (explode(' ', $trunk) as $sc) {
        $stm->execute(array('sc'=>$sc));
        $r = $stm->fetch(PDO::FETCH_ASSOC);
        if ($r['abstract']) {
            $j[$sc] = array(
                'name' => $o['info']->getLabel('sc', $sc),
                'abstract' => strip_tags($r['abstract'], '<p><b><strong class="selflink"><strong><em><i><var><sup><sub><tt><ul><ol><li><samp><small><hr><h2><h3><h4><h5><dfn><dl><dd><dt><u><abbr><big><blockquote><br><center><del><ins><kbd>'),
                'src' => $r['src'],
            );
            $found = true;
        } else {
            $j[$sc] = Null;
        }
    }
    if (! $found) {
        header('HTTP/1.0 404 Not Found');
    }
    echo json_encode($j);
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
    $plane = Null;
    $planes = UnicodePlane::getAll($db);
    if ($router->getSetting('noCP')) {
        $int = hexdec(substr($router->getSetting('request')->trunkUrl, 2));
        try {
            $block = UnicodeBlock::getForCodepoint($int,
                                    $router->getSetting('db'));
        } catch(Exception $e) {
            foreach ($planes as $p) {
                if ((int)$p->first <= $int && (int)$p->last >= $int) {
                    $plane = $p;
                    break;
                }
            }
        }
    }
    $req = $router->getSetting('request');
    $cps = codepoint::getForString(rawurldecode($req->trunkUrl), $db);
    $view = new View('error404');
    echo $view->render(compact('planes', 'block', 'plane', 'cps'));
}


// __END__
