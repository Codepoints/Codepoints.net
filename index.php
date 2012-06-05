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
    $daily = DailyCP::get(date('Y-m-d'), $o['db']);
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
    $region = array(
        'Africa' => array('Ethiopic', 'Ethiopic Extended', 'Ethiopic Extended-A',
            'Ethiopic Supplement', 'NKo', 'Osmanya', 'Tifinagh', 'Meroitic Cursive',
            'Meroitic Hieroglyphs', 'Bamum', 'Bamum Supplement', 'Vai',),
        'America' => array('Cherokee', 'Deseret', 'Unified Canadian Aboriginal Syllabics',
            'Unified Canadian Aboriginal Syllabics Extended'),
        'Central_Asia' => array('Mongolian', 'Phags-pa', 'Tibetan', 'Chakma',
            'Lepcha',),
        'Philippines' => array('Buhid', 'Hanunoo', 'Tagalog', 'Tagbanwa',
            'Batak', 'Javanese', 'Rejang', 'Sundanese', 'Sundanese Supplement'),
        'Europe' => array('Armenian', 'Basic Latin', 'Combining Diacritical Marks',
            'Combining Diacritical Marks Supplement', 'Combining Half Marks',
            'Coptic', 'Cypriot Syllabary', 'Cyrillic', 'Cyrillic Extended-A',
            'Cyrillic Extended-B', 'Cyrillic Supplement', 'Georgian',
            'Georgian Supplement', 'Glagolitic', 'Gothic', 'Greek and Coptic',
            'Greek Extended', 'IPA Extensions', 'Latin Extended Additional',
            'Latin Extended-A', 'Latin Extended-B', 'Latin Extended-C',
            'Latin Extended-D', 'Latin-1 Supplement', 'Linear B Ideograms',
            'Linear B Syllabary', 'Modifier Tone Letters', 'Ogham', 'Old Italic',
            'Phonetic Extensions', 'Phonetic Extensions Supplement', 'Runic',
            'Shavian', 'Spacing Modifier Letters', 'Ancient Greek Musical Notation',
            'Ancient Greek Numbers', 'Ancient Symbols', 'Byzantine Musical Symbols',
            'Aegean Numbers', 'Lycian', 'Phaistos Disc', 'Superscripts and Subscripts',),
        'Middle_East' => array('Alphabetic Presentation Forms', 'Arabic',
            'Old South Arabian', 'Arabic Extended-A', 'Arabic Mathematical Alphabetic Symbols',
            'Arabic Presentation Forms-A', 'Arabic Presentation Forms-B',
            'Arabic Supplement', 'Cuneiform', 'Hebrew', 'Old Persian',
            'Phoenician', 'Syriac', 'Ugaritic', 'Samaritan', 'Egyptian Hieroglyphs',
            'Avestan', 'Carian', 'Cuneiform Numbers and Punctuation',
            'Imperial Aramaic', 'Inscriptional Pahlavi', 'Inscriptional Parthian',
            'Lydian', 'Mandaic', 'Old Turkic',),
        'East_Asia' => array('Bopomofo', 'Bopomofo Extended', 'CJK Symbols and Punctuation',
            'CJK Compatibility', 'CJK Compatibility Forms', 'CJK Compatibility Ideographs',
            'CJK Compatibility Ideographs Supplement', 'CJK Radicals Supplement',
            'CJK Strokes', 'CJK Unified Ideographs', 'CJK Unified Ideographs Extension A',
            'CJK Unified Ideographs Extension B', 'CJK Unified Ideographs Extension C',
            'CJK Unified Ideographs Extension D', 'Hangul Compatibility Jamo',
            'Hangul Jamo', 'Hangul Jamo Extended-A', 'Hangul Jamo Extended-B',
            'Hangul Syllables', 'Hiragana', 'Ideographic Description Characters', 'Kanbun',
            'Kangxi Radicals', 'Katakana', 'Katakana Phonetic Extensions',
            'Yi Radicals', 'Yi Syllables', 'Yijing Hexagram Symbols', 'Vertical Forms',
            'Enclosed CJK Letters and Months', 'Enclosed Ideographic Supplement',
            'Counting Rod Numerals', 'Kana Supplement', 'Miao', 'Halfwidth and Fullwidth Forms',
            'Small Form Variants', ),
        'South_Asia' => array('Bengali', 'Devanagari', 'Devanagari Extended',
            'Common Indic Number Forms', 'Gujarati', 'Gurmukhi', 'Kannada', 'Kharoshthi',
            'Limbu', 'Malayalam', 'Oriya', 'Sinhala', 'Syloti Nagri', 'Tamil',
            'Telugu', 'Thaana', 'Brahmi', 'Meetei Mayek', 'Meetei Mayek Extensions',
            'Kaithi', 'Ol Chiki', 'Saurashtra', 'Sharada', 'Sora Sompeng',
            'Vedic Extensions', 'Takri',),
        'Southeast_Asia' => array('Balinese', 'Buginese', 'Khmer', 'Khmer Symbols',
            'Lao', 'Myanmar', 'Myanmar Extended-A', 'New Tai Lue', 'Tai Le',
            'Tai Tham', 'Tai Viet', 'Tai Xuan Jing Symbols', 'Thai', 'Cham',
            'Lisu', 'Kayah Li', 'Rumi Numeral Symbols',),
        'n' => array('Optical Character Recognition', 'Transport And Map Symbols',
            'Miscellaneous Symbols', 'Miscellaneous Technical', 'Musical Symbols',
            'Miscellaneous Symbols And Pictographs', 'Miscellaneous Symbols and Arrows',
            'Miscellaneous Mathematical Symbols-A', 'Miscellaneous Mathematical Symbols-B',
            'Playing Cards', 'Dingbats', 'Domino Tiles', 'Emoticons', 'Geometric Shapes',
            'Mahjong Tiles', 'Mathematical Alphanumeric Symbols', 'Mathematical Operators',
            'Control Pictures', 'Alchemical Symbols', 'Arrows', 'Block Elements',
            'Box Drawing', 'Currency Symbols', 'Supplemental Arrows-A', 'Supplemental Arrows-B',
            'Supplemental Mathematical Operators', 'Supplemental Punctuation',
            'Tags', 'Letterlike Symbols', 'Variation Selectors', 'Variation Selectors Supplement',
            'Number Forms', 'General Punctuation', 'Combining Diacritical Marks for Symbols',
            'Braille Patterns', 'Specials', 'Enclosed Alphanumerics', 'Enclosed Alphanumeric Supplement'),
    );
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
                    if (array_key_exists($v,$region)) {
                        $result->addQuery('block', $region[$v]);
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

->registerAction('codepoint_of_the_day', function($request, $o) {
    // Codepoint of the Day
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
        list($codepoint, $name, $description) = DailyCP::get($date, $o['db']);
        $tpl = 'dailycp';
        if (! $codepoint) {
            list($codepoint, $name, $description) = array(false, false, false);
            $tpl .= '_not';
        }
        $view = new View($tpl);
        echo $view->render(compact('codepoint', 'description',
                                   'date', 'today'));
    } else {
        throw new RoutingError();
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

->registerAction(function ($url, $o) {
    // Script description: script/Xxxx
    if (preg_match('/^script\/(?:[A-Z][a-z]{3})(?:%20[A-Z][a-z]{3})*$/', $url, $m)) {
        return True;
    }
    return False;
}, function($request, $o) {
    header('Content-Type: application/json; charset=UTF-8');
    $trunk = rawurldecode(substr($request->trunkUrl, 7));
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
