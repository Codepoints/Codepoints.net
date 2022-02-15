<?php

namespace Codepoints\Router;


/**
 * helper class to signal the router, that this is a regexp
 */
class URLMatcher {

    public string $pattern;

    /**
     * create a regexp pattern to match URLs against
     *
     * We use "#" as delimiter, because that can't be part of a server-side
     * URL pattern.
     */
    public function __construct(string $pattern) {
        $this->pattern = "#^$pattern#u";
    }

}
