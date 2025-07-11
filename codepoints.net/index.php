<?php

use Analog\Analog;
use Codepoints\CommandLine;
use Codepoints\Controller\NotFound;
use Codepoints\Controller\Error as ErrorController;
use Codepoints\Router;
use Codepoints\Router\NotFoundException;
use Codepoints\Router\Redirect;
use Codepoints\Router\RateLimitReached;

define('SOFTWARE_VERSION', '00000004');

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
    'script-src-elem \'self\' \'unsafe-inline\' https://stats.codepoints.net:443; '.
    'font-src \'self\'; '.
    (array_key_exists('embed', $_GET)? 'frame-ancestors *; ' : ''));

/**
 * load the routes
 */
require_once 'router.php';

/**
 * run this thing!
 */
$url = rawurldecode((string)preg_replace('/\?.*/', '', substr(
            filter_input(INPUT_SERVER, 'REQUEST_URI') ?? '',
            strlen(rtrim(dirname(filter_input(INPUT_SERVER, 'PHP_SELF') ?? ''), '/').'/'))));
try {
    if (! $init_successful) {
        throw new RuntimeException();
    }
    $content = Router::serve($url);
} catch (NotFoundException $e) {
    $content = null;
} catch (RateLimitReached $e) {
    http_response_code(429);
    die('It seems that you sent a lot of requests in a short amount of time.
         This is a non-profit site. Its resource limits prohibit this excessive use.
         Please come back again later!
         If you think this is an error, please reach out to us on Mastodon:
         <a href="https://typo.social/@codepoints">@codepoints@typo.social</a>.
         Thank you!');
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

if (is_string($content)) {
    echo $content;
} else {
    echo (new NotFound())($url, Router::getDependencies());
}
