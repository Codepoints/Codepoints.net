<?php

use \Analog\Analog;
use \Analog\Handler\LevelName;
use \Analog\Handler\Stderr;
use \Analog\Handler\Threshold;
use \Codepoints\Database;
use \Codepoints\Router;
use \Codepoints\Translator;
use Codepoints\Unicode\CodepointInfo\Image;
use Codepoints\Unicode\PropertyInfo;

require 'vendor/autoload.php';

define('UNICODE_VERSION', '13.0.0');

/**
 * set mb encoding globally
 */
mb_internal_encoding('UTF-8');

/**
 * configure the logger
 */
Analog::$format = '[%2$s] [codepoints:%3$s] %4$s'."\n";
Analog::$default_level = Analog::DEBUG;
Analog::handler(Threshold::init(
    LevelName::init(Stderr::init()),
    ((defined('DEBUG') && DEBUG) || PHP_SAPI === 'cli')?
        Analog::DEBUG : Analog::ERROR
));

/**
 * mute PHP's timezone warning
 */
date_default_timezone_set('Europe/Berlin');

/* enable gzip compression of HTML */
ini_set('zlib.output_compression', '1');

/**
 * get the database connection ready
 */
$config = parse_ini_file(dirname(__DIR__).'/config.ini', true);
Router::addDependency('db', $db = new Database(
    'mysql:host=localhost;dbname='.$config['db']['database'],
    $config['db']['user'],
    $config['db']['password'],
    [
        # make sure, we communicate with the real UTF-8 everywhere
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
        # make sure we get ints and floats back where appropriate. See
        # https://stackoverflow.com/a/58830039/113195
        PDO::ATTR_EMULATE_PREPARES => false,
    ]));
unset($config['db']);
Router::addDependency('config', $config);

/**
 * start the translation engine
 */
$translator = new Translator();
Router::addDependency('lang', $lang = $translator->getLanguage());

/**
 * get the general info class
 */
Router::addDependency('info', new PropertyInfo());

/**
 * make sure, we can access codepoint images
 */
new Image(Router::getDependencies());
