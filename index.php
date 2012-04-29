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
define('UNICODE_VERSION', '6.1');


/**
 * set DEBUG level
 */
define('CP_DEBUG', 0);


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
   if (CP_DEBUG) {
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
    $x = $o['db']->prepare('SELECT COUNT(*) AS c FROM codepoints');
    $x->execute();
    $row = $x->fetch();
    echo $view->render(array('planes' => UnicodePlane::getAll($o['db']),
      'nCPs' => $row['c']));
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

->registerAction('wizard', function ($request, $o) {
    // the "find my CP" wizard
    if (isset($_GET['_wizard']) && $_GET['_wizard'] === '1') {
        $result = new SearchResult(array(), $o['db']);
        foreach ($_GET as $k => $v) {
            switch ($k) {
                case 'def':
                    $result->addQuery('kDefinition', "%$v%", 'LIKE');
                    break;
                case 'strokes':
                    if (ctype_digit($v) && (int)$v > 0) {
                        $result->addQuery('kTotalStrokes', $v);
                    }
                    break;
                case 'archaic':
                    if ($v === '1') {
                        throw new Exception('TODO');
                        $result->addQuery('', '');
                    } elseif ($v === '0') {
                        throw new Exception('TODO');
                        $result->addQuery('', '');
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
                    if ($v === 'Africa') {
                        $result->addQuery('blk', array('Ethiopic', 'Ethiopic_Ext', 'Ethiopic_Ext_A', 'Ethiopic_Sup', 'NKo', 'Osmanya', 'Tifinagh', 'Meroitic_Cursive', 'Meroitic_Hieroglyphs', 'Bamum', 'Bamum_Sup', 'Vai',));
                    } elseif ($v === 'America') {
                        $result->addQuery('blk', array('Cherokee', 'Deseret', 'UCAS', 'UCAS_Ext'));
                    } elseif ($v === 'Europe') {
                        $result->addQuery('blk', array('Armenian', 'Basic_Latin', 'Diacriticals', 'Diacriticals_Sup', 'Half_Marks', 'Coptic', 'Cypriot_Syllabary', 'Cyrillic', 'Cyrillic_Ext_A', 'Cyrillic_Ext_B', 'Cyrillic_Sup', 'Georgian', 'Georgian_Sup', 'Glagolitic', 'Gothic', 'Greek', 'Greek_Ext', 'IPA_Ext', 'Latin_Ext_Additional', 'Latin_Ext_A', 'Latin_Ext_B', 'Latin_Ext_C', 'Latin_Ext_D', 'Latin_1_Sup', 'Linear_B_Ideograms', 'Linear_B_Syllabary', 'Modifier_Tone_Letters', 'Ogham', 'Old_Italic', 'Phonetic_Ext', 'Phonetic_Ext_Sup', 'Runic', 'Shavian', 'Modifier_Letters', 'Ancient_Greek_Music', 'Ancient_Greek_Numbers', 'Ancient_Symbols', 'Byzantine_Music', 'Aegean_Numbers', 'Lycian', 'Phaistos', 'Super_And_Sub',));
                    } elseif ($v === 'Middle_East') {
                        $result->addQuery('blk', array('Alphabetic_PF', 'Arabic', 'Old_South_Arabian', 'Arabic_Ext_A', 'Arabic_Math', 'Arabic_PF_A', 'Arabic_PF_B', 'Arabic_Sup', 'Cuneiform', 'Hebrew', 'Old_Persian', 'Phoenician', 'Syriac', 'Ugaritic', 'Samaritan', 'Egyptian_Hieroglyphs', 'Avestan', 'Carian', 'Cuneiform_Numbers', 'Imperial_Aramaic', 'Inscriptional_Pahlavi', 'Inscriptional_Parthian', 'Lydian', 'Mandaic', 'Old_Turkic',));
                    } elseif ($v === 'Central_Asia') {
                        $result->addQuery('blk', array('Mongolian', 'Phags_Pa', 'Tibetan', 'Chakma', 'Lepcha',));
                    } elseif ($v === 'East_Asia') {
                        $result->addQuery('blk', array('Bopomofo', 'Bopomofo_Ext', 'CJK_Symbols', 'CJK_Compat', 'CJK_Compat_Forms', 'CJK_Compat_Ideographs', 'CJK_Compat_Ideographs_Sup', 'CJK_Radicals_Sup', 'CJK_Strokes', 'CJK', 'CJK_Ext_A', 'CJK_Ext_B', 'CJK_Ext_C', 'CJK_Ext_D', 'Compat_Jamo', 'Jamo', 'Jamo_Ext_A', 'Jamo_Ext_B', 'Hangul', 'Hiragana', 'IDC', 'Kanbun', 'Kangxi', 'Katakana', 'Katakana_Ext', 'Yi_Radicals', 'Yi_Syllables', 'Yijing', 'Vertical_Forms', 'Enclosed_CJK', 'Enclosed_Ideographic_Sup', 'Counting_Rod', 'Kana_Sup', 'Miao', 'Half_And_Full_Forms', 'Small_Forms', ));
                    } elseif ($v === 'South_Asia') {
                        $result->addQuery('blk', array('Bengali', 'Devanagari', 'Devanagari_Ext', 'Indic_Number_Forms', 'Gujarati', 'Gurmukhi', 'Kannada', 'Kharoshthi', 'Limbu', 'Malayalam', 'Oriya', 'Sinhala', 'Syloti_Nagri', 'Tamil', 'Telugu', 'Thaana', 'Brahmi', 'Meetei_Mayek', 'Meetei_Mayek_Ext', 'Kaithi', 'Ol_Chiki', 'Saurashtra', 'Sharada', 'Sora_Sompeng', 'Vedic_Ext', 'Takri',));
                    } elseif ($v === 'Southeast_Asia') {
                        $result->addQuery('blk', array('Balinese', 'Buginese', 'Khmer', 'Khmer_Symbols', 'Lao', 'Myanmar', 'Myanmar_Ext_A', 'New_Tai_Lue', 'Tai_Le', 'Tai_Tham', 'Tai_Viet', 'Tai_Xuan_Jing', 'Thai', 'Cham', 'Lisu', 'Kayah_Li', 'Rumi',));
                    } elseif ($v === 'Philippines') {
                        $result->addQuery('blk', array('Buhid', 'Hanunoo', 'Tagalog', 'Tagbanwa', 'Batak', 'Javanese', 'Rejang', 'Sundanese', 'Sundanese_Sup'));
                    } elseif ($v === 'n') {
                        $result->addQuery('blk', array('OCR', 'Transport_And_Map', 'Misc_Symbols', 'Misc_Technical', 'Music', 'Misc_Pictographs', 'Misc_Arrows', 'Misc_Math_Symbols_A', 'Misc_Math_Symbols_B', 'Playing_Cards', 'Dingbats', 'Domino', 'Emoticons', 'Geometric_Shapes', 'Majyong', 'Math_Alphanum', 'Math_Operators', 'Control_Pictures', 'Alchemical', 'Arrows', 'Block_Elements', 'Box_Drawing', 'Currency_Symbols', 'Sup_Arrows_A', 'Sup_Arrows_B', 'Sup_Math_Operators', 'Sup_Punctuation', 'Tags', 'Letterlike_Symbols', 'VS', 'VS_Sup', 'Number_Forms', 'Punctuation', 'Diacriticals_For_Symbols', 'Braille', 'Specials', 'Enclosed_Alphanum', 'Enclosed_Alphanum_Sup'));
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
                echo $view->render(compact('result', 'blocks', 'pagination',
                                        'page'));
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
    foreach ($_GET as $k => $v) {
        if ($k === 'q' && $v) {
            if (mb_strlen($v, 'UTF-8') === 1) {
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
                    if (preg_match('/\blowercase\b/i', $vv)) {
                        $result->addQuery('gc', 'lc', '=', 'OR');
                    }
                    if (preg_match('/\buppercase\b/i', $vv)) {
                        $result->addQuery('gc', 'uc', '=', 'OR');
                    }
                    if (preg_match('/\btitlecase\b/i', $vv)) {
                        $result->addQuery('gc', 'tc', '=', 'OR');
                    }
                    $blocks = UnicodeBlock::search($vv, $o['db']);
                }
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
    $req = $router->getSetting('request');
    $cps = array();
    $url = rawurldecode($req->trunkUrl);
    if (mb_strlen($url) < 128) {
        foreach (preg_split('/(?<!^)(?!$)/u', $url) as $c) {
            $cc = unpack('N', mb_convert_encoding($c, 'UCS-4BE', 'UTF-8'));
            try {
                $cx = Codepoint::getCP($cc[1], $db);
                $cx->getName();
                $cps[] = $cx;
            } catch (Exception $e) {
            }
        }
    }
    $view = new View('error404');
    echo $view->render(compact('planes', 'block', 'cps'));
}


// __END__
