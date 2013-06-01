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
 * <http://codepoints.net/about#this_site> for details.
 */


/**
 * define Unicode Version in use
 */
define('UNICODE_VERSION', '6.1.0');


/**
 * set DEBUG level
 */
define('CP_DEBUG', 0);


/**
 * cache busting string (the Makefile will manipulate this line)
 */
define('CACHE_BUST', '866959e30335a9057d0d9e972ab2fd3cab5b2b20');


/* enable gzip compression of HTML */
ini_set('zlib.output_compression', True);


/**
 * install autoloader
 */
require_once 'lib/vendor/autoload.php';


/**
 * log $msg to /tmp/codepoints.log
 */
function flog($msg) {
   if (CP_DEBUG) {
       error_log(sprintf("[%s] %s\n", date("c"), trim($msg)), 3,
                 '/tmp/codepoints.log');
   }
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
    $zipped = False;
    $xgzip = '';
    if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) &&
        strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== False) {
        $zipped = True;
        if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== False) {
            $xgzip = 'x-';
        }
    }
    $cData = $cache->fetch(ltrim($_SERVER['REQUEST_URI'], "/"), $zipped);
    if ($cData) {
        flog('Cache hit: '.ltrim($_SERVER['REQUEST_URI'], "/"));
        header('Content-Type: text/html; charset=utf-8');
        if ($zipped) {
            flog('Send gzipped Cache hit');
            ini_set('zlib.output_compression', False);
            header('Content-Encoding: '.$xgzip.'gzip');
        }
        die($cData);
    }
}


/**
 * initialize DB connection and global router
 */
$db = new DB('sqlite:'.dirname(__FILE__).'/ucd.sqlite');
$router = Router::getRouter();


$router->addSetting('db', $db)
       ->addSetting('info', UnicodeInfo::get());


/**
 * add controllers sorted by complexity of routing
 * (i.e., simple string matches first)
 */
$controllers = array(
    'index', 'about', 'api_login', 'api_script', 'codepoint_of_the_day',
    'planes', 'random', 'scripts', 'search', 'wizard', 'sitemap',

    'api_font-face', 'api_bool', 'api_name', 'api_glyph', 'blog-preview',
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
if ($router->callAction() === False) {
    header('HTTP/1.0 404 Not Found');
    $block = Null;
    $plane = Null;
    $planes = UnicodePlane::getAll($db);

    // if the URL looks like a codepoint, give some extra hints
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
    $cps = Codepoint::getForString(rawurldecode($req->trunkUrl), $db);
    $view = new View('error404');
    echo $view->render(compact('planes', 'block', 'plane', 'cps'));
}


// __END__
