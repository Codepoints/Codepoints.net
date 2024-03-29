<?php

use Analog\Analog;
use Codepoints\CommandLine;
use Codepoints\Controller\NotFound;
use Codepoints\Controller\Error as ErrorController;
use Codepoints\Router;
use Codepoints\Router\NotFoundException;
use Codepoints\Router\Redirect;

define('DEBUG', 1);
define('SOFTWARE_VERSION', '00000002');

try {
    $init_successful = require 'init.php';
} catch (Throwable $e) {
    $init_successful = false;
}

if (PHP_SAPI === 'cli') {
    $cli = new CommandLine($argv, Router::getDependencies());
    exit($cli->run());
}

/**
 * disable FLoC for enhanced privacy
 * @see https://spreadprivacy.com/block-floc-with-duckduckgo/
 */
header('Permissions-Policy: interest-cohort=()');

/**
 * enable CSP protection (reporting only for now)
 */
header('Content-Security-Policy-Report-Only: '.
    'default-src \'self\' https://stats.codepoints.net:443; '.
    'img-src \'self\' data: https://stats.codepoints.net:443; '.
    'style-src \'self\' \'unsafe-inline\'; '.
    'font-src \'self\'; '.
    (array_key_exists('embed', $_GET)? 'frame-ancestors *; ' : '').
    #'upgrade-insecure-requests; '.
    #'report-uri https://codepoints.report-uri.com/r/d/csp/reportOnly');
    '');

/**
 * load the routes
 */
require_once 'router.php';

/**
 * run this thing!
 */
$url = rawurldecode(preg_replace('/\?.*/', '', substr(
            filter_input(INPUT_SERVER, 'REQUEST_URI'),
            strlen(rtrim(dirname(filter_input(INPUT_SERVER, 'PHP_SELF')), '/').'/'))));
try {
    if (! $init_successful) {
        throw new RuntimeException();
    }
    $content = Router::serve($url);
} catch (NotFoundException $e) {
    $content = null;
} catch (Redirect $redirect) {
    $code = 303;
    if (is_int($redirect->getCode()) && $redirect->getCode() >= 300 && $redirect->getCode() <= 399) {
        $code = $redirect->getCode();
    }
    $location = '/';
    if ($redirect->getMessage()) {
        $location = $redirect->getMessage();
    }
    http_response_code($code);
    header(sprintf('Location: %s', $location));
    exit();
} catch (Exception $exc) {
    echo (new ErrorController())($url, Router::getDependencies());
    Analog::error($exc->getMessage());
    exit(1);
}

if ($content) {
    echo $content;
} else {
    echo (new NotFound())($url, Router::getDependencies());
}
