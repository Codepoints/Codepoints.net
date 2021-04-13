<?php

use Codepoints\Controller\NotFound;
use Codepoints\Controller\Error as ErrorController;
use Codepoints\Router;
use Codepoints\Router\NotFoundException;
use Codepoints\Router\Redirect;

define('DEBUG', 1);

try {
    $init_successful = require 'init.php';
} catch (Throwable $e) {
    $init_successful = false;
}

/**
 * disable FLoC for enhanced privacy
 * @see https://spreadprivacy.com/block-floc-with-duckduckgo/
 */
header('Permissions-Policy: interest-cohort=()');

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
    exit(1);
}

if ($content) {
    echo $content;
} else {
    echo (new NotFound())($url, Router::getDependencies());
}
