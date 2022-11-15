<?php

use \Codepoints\Router;
use \Codepoints\Router\URLMatcher;
use \Codepoints\Router\Redirect;
use \Codepoints\Controller\Api;
use \Codepoints\Controller\Block;
use \Codepoints\Controller\Codepoint;
use \Codepoints\Controller\Image;
use \Codepoints\Controller\Index;
use \Codepoints\Controller\Plane;
use \Codepoints\Controller\Planes;
use \Codepoints\Controller\PossibleName;
use \Codepoints\Controller\Random;
use \Codepoints\Controller\Range;
use \Codepoints\Controller\Scripts;
use \Codepoints\Controller\Search;
use \Codepoints\Controller\Sitemap;
use \Codepoints\Controller\StaticPage;


Router::add('', new Index());

Router::add(['about', 'glossary', 'offline.html'], new StaticPage());

Router::add('planes', new Planes());

Router::add('random', new Random());

Router::add('search', new Search());
Router::add('wizard', function(Array $match, Array $env) : void {
    throw new Redirect('/search');
});

Router::add('scripts', new Scripts());

Router::add(new URLMatcher('api(/(v1)?)?$'), function(Array $match, Array $env) : void {
    throw new Redirect('/api/v1/');
});
Router::add('api/v1/', function(string $match, Array $env) : string {
    return (new Api())(['action' => 'usage'], $env);
});
Router::add(new URLMatcher('api/v1/(?P<action>[a-z][a-z]+)(?:/(?P<data>.*))?$'), new Api());

Router::add(new URLMatcher('plane_([a-zA-Z0-9()_-]+)$'), new Plane());
Router::add(new URLMatcher('([a-zA-Z0-9()_-]+)_plane$'), new Plane());

Router::add(new URLMatcher('image/([0-9A-F]{2,4}00).svg$'), new Image());

Router::add(new URLMatcher('sitemap(|/(static|u[0-9A-F]+)).xml$'), new Sitemap());

Router::add(new URLMatcher('U\\+([0-9a-fA-F]{4,6})$'), new Codepoint());

/**
 * redirect to canonical 0-padded URL
 */
Router::add(new URLMatcher('U\\+([0-9a-fA-F]{1,3})$'), function(Array $match, Array $env) : void {
    throw new Redirect(sprintf('/U+%04X', hexdec($match[1])));
});

/**
 * Block names
 *
 * Currently shortest block name: NKo. Currently longest, UCAS-Ext, has 48 chars.
 */
Router::add(function(string $url, Array $env) : ?Array {
    if (strlen($url) < 3 || strlen($url) > 64 || preg_match('/[^a-z0-9_-]/', $url)) {
        return null;
    }
    $data = $env['db']->getOne("
        SELECT name, first, last FROM blocks
        WHERE replace(replace(lower(name), '_', ''), ' ', '') = ?
        LIMIT 1", str_replace([' ', '_'], '', strtolower($url)));
    return $data ?: null;
}, new Block());

/**
 * single character: redirect
 *
 * special case: API call to /api/v1/<cp> is redirected to canonical API URL.
 */
Router::add(new URLMatcher('(api/v1/)?(.|(%[A-Fa-f0-9]{2}){1,4})$'), function(Array $match, Array $env) : ?string {
    $txt = rawurldecode($match[2]);
    if (mb_strlen($txt) !== 1) {
        return null;
    }
    $cp = mb_ord($txt);
    if (! $cp) {
        return null;
    }
    throw new Redirect(sprintf($match[1]? '/api/v1/codepoint/%04X' : '/U+%04X', $cp));
});

/**
 * support Unicode ranges like U+0123..U+3456
 */
Router::add(new URLMatcher('(?:U\\+[0-9a-fA-F]{4,6}(?:\\.\\.|-|,))+U\\+[0-9a-fA-F]{4,6}$'), new Range());

/**
 * check for possible code point name as last resort
 */
Router::add(function(string $url, Array $env) : ?Array {
    if (strlen($url) > 127 || ! ctype_alpha(substr($url, 0, 1)) ||
        preg_match('/[^a-za-z0-9_ -]/i', $url)) {
        return null;
    }
    $data = $env['db']->getOne("
        SELECT c.cp cp, c.name name, c.gc gc
        FROM codepoints c
        LEFT JOIN codepoint_props
            USING (cp)
        WHERE replace(replace(lower(na), '_', ''), ' ', '') = ?
        LIMIT 1", str_replace([' ', '_'], '', strtolower($url)));
    return $data?: null;
}, new PossibleName());
