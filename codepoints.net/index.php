<?php

use \Codepoints\Controller\NotFound;
use \Codepoints\Router;
use \Codepoints\Router\NotFoundException;
use \Codepoints\Router\Redirect;

define('DEBUG', 1);

require 'init.php';

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
}

if ($content) {
    echo $content;
} else {
    http_response_code(404);
    echo (new NotFound())($url, Router::getDependencies());
}
