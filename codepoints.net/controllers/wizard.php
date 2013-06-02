<?php

$router->registerAction('wizard', function ($request, $o) {
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
});

//__END__
