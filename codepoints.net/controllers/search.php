<?php

$router->registerAction('search', function ($request, $o) {
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
            } elseif (preg_match('/^\s*&[#0-9a-z]+;\s*$/i', $v)) {
                $v = trim($v);
                /* seems to be a single HTML escape sequence */
                $vv = html_entity_decode($v);
                if (mb_strlen($vv, 'UTF-8') === 1) {
                    $result->addQuery('cp', unpack('N',
                        mb_convert_encoding($vv, 'UCS-4BE', 'UTF-8')));
                } elseif ($v[1] !== '#') {
                    $result->addQuery('alias', ltrim(rtrim($v, ';'), '&'), '=', 'OR');
                } else {
                    # TODO we should warn about invalid escape sequence here
                }
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
                    $result->addQuery('na', $vv, 'LIKE', 'OR');
                    $result->addQuery('na1', $vv, 'LIKE', 'OR');
                    $vv = "%$vv%";
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
        } elseif ($k === 'gc' && array_key_exists($v, UnicodeInfo::$gc_shortcuts)) {
            $result->addQuery('gc', UnicodeInfo::$gc_shortcuts[$v]);
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
        } elseif ($result->getCount() === 1 && $page === 1) {
            /* redirect, if only one cp is found. Do not so on follow-up
             * pages, since that will torpedo pagination */
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
});

//__END__
