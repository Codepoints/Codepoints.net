<?php
/**
 * Welcome to the source of Codepoints.net!
 *
 * This is the main (and sole) entry to the site. Classes in
 * lib/*.class.php are auto-loaded. The controller for the URL
 * structure is lib/router.class.php.
 *
 * URLs are mapped to controllers in the following way: Every file in
 * ./controllers/*.php is included and registers an action for a
 * specific URL pattern. If the pattern is detected, the according action
 * is called.
 *
 * In lib/view.class.php a view system is defined, with the
 * views to guide the output living in views/. Optionally Mustache
 * templates could be used, but we're not doing that at the moment.
 * The templates would live in static/tpl/*.mustache.
 *
 * To get an instance of the database up and running, visit
 * <https://github.com/Boldewyn/unicodeinfo>. On a regular
 * *NIX system, a simple `make` in that project should provide
 * you with the ucd.sqlite to run this instance. (Don't forget to run
 * `make ucd.sqlite`, when you copied it here.)
 *
 * This code is dually licensed under GPL and MIT. See
 * <https://codepoints.net/about#this_site> for details.
 */


/**
 * mute PHP's timezone warning
 */
date_default_timezone_set('Europe/Berlin');


/**
 * define Unicode Version in use
 */
define('UNICODE_VERSION', '8.0.0');


/**
 * set DEBUG level
 */
define('CP_DEBUG', 0);


/**
 * set database path
 */
define('DB_PATH', realpath(__DIR__.'/../ucd.sqlite'));


/**
 * cache busting string (the Makefile will manipulate this line)
 */
define('CACHE_BUST', 'e1307df0d19be0cc7e34fc68646c544c28454335');


/* enable gzip compression of HTML */
ini_set('zlib.output_compression', true);


/**
 * install autoloader
 */
require_once 'lib/vendor/autoload.php';


/**
 * set HSTS and other security headers
 */
header('Strict-Transport-Security: max-age=16070400; includeSubDomains; preload');
header('X-Xss-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');


/**
 * set a (very weak) CSP header
 */
header("Content-Security-Policy: ".
    "default-src 'self'; ".
    "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://stats.codepoints.net; ".
    "object-src 'none'; ".
    "style-src 'self' 'unsafe-inline'; ".
    "img-src 'self' data: https://stats.codepoints.net; ".
    "media-src 'none'; ".
    "frame-src 'self' https://stats.codepoints.net; ".
    "child-src 'self' https://stats.codepoints.net; ".
    "font-src 'self' data:; ".
    "connect-src 'self' https://stats.codepoints.net; ".
    "report-uri https://report-uri.io/report/codepoints");


/**
 * set X-UA-Compat, if necessary
 */
if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (strpos($ua, 'opera') === false &&
        (strpos($ua, 'msie') !== false ||
         strpos($ua, 'trident') !== false)) {
        header('X-UA-Compatible: IE=edge,chrome=1');
    }
}


/**
 * allow caching resources for an hour
 */
if (! CP_DEBUG) {
    header('Cache-Control: public, mag-age=3600');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
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


if (array_key_exists('REQUEST_METHOD', $_SERVER) &&
    strtoupper($_SERVER['REQUEST_METHOD']) === "POST") {
    header('HTTP/1.0 405 Method Not Allowed');
    $view = new View('error404');
    die($view->render(array('block'=>null, 'plane'=>null, 'cps'=>null,
                      'int'=>null, 'prev'=>null, 'next'=>null)));
}


/**
 * check for existing cached entry and exit here, if a match exists
 *
 * We don't return cached entries, when POST variables are set or GET
 * variables are immensely long.
 */
if (! CP_DEBUG && strlen($_SERVER['REQUEST_URI']) < 255 && ! count($_POST)) {
    $cache = new Cache();
    // we return the already gzipped cache entry, if the browser requests it
    $zipped = false;
    $xgzip = '';
    if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) &&
        strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
        $zipped = true;
        if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
            $xgzip = 'x-';
        }
    }
    $cData = $cache->fetch(ltrim($_SERVER['REQUEST_URI'], "/"), $zipped);
    if ($cData) {
        flog('Cache hit: '.ltrim($_SERVER['REQUEST_URI'], "/"));
        header('Content-Type: text/html; charset=utf-8');
        if ($zipped) {
            flog('Send gzipped Cache hit');
            ini_set('zlib.output_compression', false);
            header('Content-Encoding: '.$xgzip.'gzip');
        }
        die($cData);
    }
}


/**
 * initialize DB connection and global router
 */
$dbconfig = parse_ini_file(dirname(__DIR__).'/db.conf', true);
$db = new DB(
    'mysql:host=localhost;dbname='.$dbconfig['clientreadonly']['database'],
    $dbconfig['clientreadonly']['user'],
    $dbconfig['clientreadonly']['password'],
    [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',]);
unset($dbconfig);
$router = Router::getRouter();

$router->addSetting('db', $db)
       ->addSetting('info', UnicodeInfo::get());


/**
 * add controllers sorted by complexity of routing
 * (i.e., simple string matches first)
 */
$controllers = array(
    'index', 'about', 'api_login', 'codepoint_of_the_day',
    'planes', 'random', 'scripts', 'search', 'wizard', 'sitemap',

    'api_font-face', 'api',
    'blog-preview', 'font',
    'single_character', 'plane', 'codepoint', 'block', 'possible_name',
    'range',
);

foreach ($controllers as $ctrl) {
    require_once "controllers/$ctrl.php";
}


/**
 * register URL schemes for some class instances
 */
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


/**
 * call the main action or produce a 404 error
 */
if ($router->callAction() === false) {
    $block = null;
    $plane = null;
    $int = null;
    $prev = null;
    $next = null;

    // if the URL looks like a codepoint, give some extra hints
    if ($router->getSetting('noCP')) {
        $int = hexdec(substr($router->getSetting('request')->trunkUrl, 2));
        if ($int && $int >= 0 && $int <= 0x10FFFF) {
            try {
                $_cp = Codepoint::getCP($int, $router->getSetting('db'));
                $prev = $_cp->getPrev();
                $next = $_cp->getNext();
            } catch(Exception $e) {}
            try {
                $block = UnicodeBlock::getForCodepoint($int,
                                        $router->getSetting('db'));
            } catch(Exception $e) {}
            foreach (UnicodePlane::getAll($db) as $p) {
                if ((int)$p->first <= $int && (int)$p->last >= $int) {
                    $plane = $p;
                    break;
                }
            }
        } else {
            $int = null; // re-set emptiness
        }
    } else {
        /* send the 404 only, if this is not a possible CP */
        header('HTTP/1.0 404 Not Found');
    }

    $req = $router->getSetting('request');
    $cps = Codepoint::getForString(rawurldecode($req->trunkUrl), $db);
    $view = new View('error404');
    echo $view->render(compact('block', 'plane', 'cps', 'int', 'prev', 'next'));
}


// __END__
