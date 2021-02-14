<?php

namespace Codepoints;

use \Analog\Analog;


/**
 * request router
 */
class Router {

    private static array $routes = [];

    private static array $env = [];

    /**
     * add a route
     *
     * @param mixed $url the URL to register the handler to. This can be a
     *        plain string to match against, an URLMatcher object to check
     *        a regular expression, an array to check against a list of URLs
     *        or a function for more complicated matches.
     * @param callable $handler the handler to call with the matched URL. If
     *        $url is a function, the handler receives the return value as
     *        first argument. In the case of an URLMatcher, it's a regexp
     *        match array. In all other cases, it's the matched URL.
     */
    public static function add(/*string|array|callable*/ $url, callable $handler) : void {
        static::$routes[] = [$url, $handler];
    }

    /**
     * add a dependency, that will later be handed to the controller
     */
    public static function addDependency( string $name, /*mixed*/ $dependency) : void {
        static::$env[$name] = $dependency;
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
                $match = in_array($current_url, $url);
            } else {
                $match = ($current_url === $url);
            }
            if ($match) {
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
