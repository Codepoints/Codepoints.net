<?php

$router->registerAction('wizard', function ($request, $o) {
    // the "find my CP" wizard
    $router = Router::getRouter();
    if (isset($_GET['_wizard']) && $_GET['_wizard'] === '1') {
        $result = new WizardResult(array(), $o['db']);
        foreach ($_GET as $k => $v) {
            switch ($k) {
                case 'def':
                    if ($v) {
                        $result->addQuery('kDefinition', "%$v%", 'LIKE', 'AND');
                    }
                    break;
                case 'strokes':
                    if (ctype_digit($v) && (int)$v > 0) {
                        $result->addQuery('kTotalStrokes', $v, '=', 'AND');
                    }
                    break;
                case 'archaic':
                    if ($v === '1') {
                        $result->addQuery('sc', UnicodeInfo::$archaicScripts, '=', 'AND');
                    } elseif ($v === '0') {
                        $result->addQuery('sc', UnicodeInfo::$recentScripts, '=', 'AND');
                    }
                    break;
                case 'confuse':
                    if ($v === '1') {
                        $result->addQuery('confusables', 0, '>', 'AND');
                    }
                    break;
                case 'composed':
                    if ($v >= 1) {
                        $result->addQuery('NFKD_QC', 'No', '=', 'AND');
                    } elseif ($v === '0') {
                        $result->addQuery('NFKD_QC', 'Yes', '=', 'AND');
                    }
                    break;
                case 'incomplete':
                    if ($v === '1') {
                        $result->addQuery('ccc', 0, '=', 'AND');
                    } elseif ($v === '0') {
                        $result->addQuery('ccc', 0, '=', 'AND');
                    }
                    break;
                case 'punctuation':
                    if ($v === '1') {
                        $result->addQuery('gc', array('Pc', 'Pd', 'Ps', 'Pe',
                                                      'Pi', 'Pf', 'Po'), '=', 'AND');
                    } elseif ($v === '0') {
                        $result->addQuery('gc', array('Pc', 'Pd', 'Ps', 'Pe',
                                                      'Pi', 'Pf', 'Po'), '!=', 'AND');
                    }
                    break;
                case 'symbol':
                    if ($v === 's') {
                        $result->addQuery('gc', array('Sm', 'Sc', 'Sk', 'So'), '=', 'AND');
                    } elseif ($v === 'c') {
                        $result->addQuery('gc', array('Cc', 'Cf', 'Cs', 'Co',
                                                      'Cn'), '=', 'AND');
                    } elseif ($v === 't') {
                        $result->addQuery('gc', array('Sm', 'Sc', 'Sk', 'So',
                                                      'Cc', 'Cf', 'Cs', 'Co',
                                                      'Cn'), '!=', 'AND');
                    }
                    break;
                case 'number':
                    if ($v === '1') {
                        $result->addQuery('gc', array('Nd', 'Nl', 'No'), '=', 'AND');
                    } elseif ($v === '0') {
                        $result->addQuery('gc', array('Nd', 'Nl', 'No'), '!=', 'AND');
                    }
                    break;
                case 'case':
                    if ($v === 'l') {
                        $result->addQuery('gc', 'Ll', '=', 'AND');
                    } elseif ($v === 'u') {
                        $result->addQuery('gc', 'Lu', '=', 'AND');
                    } elseif ($v === 't') {
                        $result->addQuery('gc', 'Lt', '=', 'AND');
                    } elseif ($v === 'y') {
                        $result->addQuery('gc', array('Lu', 'Ll', 'Lt'), '=', 'AND');
                    } elseif ($v === 'n') {
                        $result->addQuery('gc', array('Lu', 'Ll', 'Lt'), '!=', 'AND');
                    }
                    break;
                case 'region':
                    if (array_key_exists($v, UnicodeInfo::$regionToBlock)) {
                        $result->addQuery('block',
                                          UnicodeInfo::$regionToBlock[$v], '=', 'AND');
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
                $wizard = true;
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
