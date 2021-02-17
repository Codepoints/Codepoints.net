<?php

namespace Codepoints;

use \Analog\Analog;
use Codepoints\Router\URLMatcher;


/**
 * request router
 */
class Router {

    private static Array $routes = [];

    private static Array $env = [];

    /**
     * add a route
     *
     * @param string|Array|callable|URLMatcher $url the URL to register the
     *        handler to. This can be a plain string to match against, an
     *        URLMatcher object to check a regular expression, an array to
     *        check against a list of URLs or a function for more complicated
     *        matches.
     * @param callable $handler the handler to call with the matched URL. If
     *        $url is a function, the handler receives the return value as
     *        first argument. In the case of an URLMatcher, it's a regexp
     *        match array. In all other cases, it's the matched URL.
     */
    public static function add($url, callable $handler) : void {
        static::$routes[] = [$url, $handler];
    }

    /**
     * add a dependency, that will later be handed to the controller
     *
     * @param mixed $dependency
     */
    public static function addDependency(string $name, $dependency) : void {
        static::$env[$name] = $dependency;
    }

    /**
     * access the dependencies
     *
     * Necessary for 404 error handling.
     */
    public static function getDependencies() : Array {
        return static::$env;
    }

    public static function serve(string $current_url) : ?string {
        Analog::log('current url: '.$current_url);

        $current_handler = null;
        $match = null;
        foreach (static::$routes as [$url, $handler]) {
            if ($url instanceof URLMatcher) {
                preg_match($url->pattern, $current_url, $match);
            } elseif (is_callable($url)) {
                $match = $url($current_url, static::$env);
            } elseif (is_array($url)) {
                if (in_array($current_url, $url, true)) {
                    $match = $current_url;
                }
            } elseif ($current_url === $url) {
                $match = $current_url;
            }
            /* empty string: home page */
            if ($match || $match === '') {
                $current_handler = $handler;
                break;
            }
        }

        if ($current_handler) {
            return $current_handler($match, static::$env);
        }

        return null;
    }

}
