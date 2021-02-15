<?php

use \Analog\Analog;
use \Analog\Handler\LevelName;
use \Analog\Handler\Stderr;
use \Analog\Handler\Threshold;
use \Codepoints\Controller\NotFound;
use \Codepoints\Database;
use \Codepoints\Router;
use \Codepoints\Router\NotFoundException;
use \Codepoints\Translator;

require 'vendor/autoload.php';

define('DEBUG', 1);

/**
 * configure the logger
 */
Analog::$format = '[%2$s] [codepoints:%3$s] %4$s'."\n";
Analog::$default_level = Analog::DEBUG;
Analog::handler(Threshold::init(
    LevelName::init(Stderr::init()),
    DEBUG? Analog::DEBUG : Analog::ERROR
));

/**
 * mute PHP's timezone warning
 */
date_default_timezone_set('Europe/Berlin');

/* enable gzip compression of HTML */
ini_set('zlib.output_compression', '1');

/**
 * set HSTS and other security headers
 */
header('Strict-Transport-Security: max-age=16070400; includeSubDomains; preload');
header('X-Xss-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');

/**
 * get the database connection ready
 */
$dbconfig = parse_ini_file(dirname(__DIR__).'/db.conf', true);
Router::addDependency('db', new Database(
    'mysql:host=localhost;dbname='.$dbconfig['client']['database'],
    $dbconfig['client']['user'],
    $dbconfig['client']['password'],
    [
        # make sure, we communicate with the real UTF-8 everywhere
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
        # make sure we get ints and floats back where appropriate. See
        # https://stackoverflow.com/a/58830039/113195
        PDO::ATTR_EMULATE_PREPARES => false,
    ]));
unset($dbconfig);

/**
 * start the translation engine
 */
$translator = new Translator();
Router::addDependency('lang', $translator->getLanguage());

/**
 * load the routes
 */
require_once 'router.php';

/**
 * run this thing!
 */
$url = preg_replace('/\?.*/', '', substr(
            $_SERVER['REQUEST_URI'],
            strlen(rtrim(dirname($_SERVER['PHP_SELF']), '/').'/')));
try {
    $content = Router::serve($url);
} catch (NotFoundException $e) {
    $content = null;
}

if ($content) {
    echo $content;
} else {
    http_response_code(404);
    echo (new NotFound())($url, Router::getDependencies());
}
