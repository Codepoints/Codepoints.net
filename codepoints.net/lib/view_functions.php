<?php

use Codepoints\Unicode\Codepoint;
use Codepoints\Unicode\Block;
use Codepoints\Unicode\Plane;


/**
 * translate a string, the quick way
 */
function __(string $original, ...$args): string {
    global $translator;
    return $translator->translate($original, ...$args);
}

/**
 * HTML-quote a string
 */
function q(string $s) : string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

/**
 * shortcut for "q(__())"
 */
function _q(string $s) : string {
    return q(__($s));
}

/**
 * return the representation for a code point link
 */
function cp(Codepoint $codepoint, string $rel='', string $class='') : string {
    if ($rel) {
        $rel = ' rel="'.q($rel).'"';
    }
    if ($class) {
        $class = ' '.q($class);
    }
    return sprintf('<a class="cp%s"%s href="%s" data-cp="%s">'.
            '%s <span class="title">%s</span>'.
        '</a>',
        $class, $rel, q(url($codepoint)), q($codepoint), cpimg($codepoint), q($codepoint->name));
}

/**
 * return the image element for a code point
 *
 * When https://bugzilla.mozilla.org/show_bug.cgi?id=1027106 is fixed, we
 * can switch back to good ol' <img> again.
 */
function cpimg(Codepoint $codepoint, int $width=16) : string {
    $hex = sprintf('%04X', $codepoint->id);
    $url = sprintf('image/%s.svg#U%s', preg_replace('/[0-9A-F]{2}$/', '00', $hex), $hex);
    return sprintf('<svg width="%s" height="%s">'.
        '<use xlink:href="%s"/></svg>', $width, $width, $url);
}

/**
 * return the representation for a block link
 */
function bl(Block $block, string $rel='', string $class='') : string {
    if ($rel) {
        $rel = ' rel="'.q($rel).'"';
    }
    if ($class) {
        $class = ' '.q($class);
    }
    return sprintf('<a class="bl%s"%s href="%s">'.
            '%s <span class="title">%s</span>'.
            ' <span class="meta">%s</span>'.
        '</a>',
        $class, $rel, q(url($block)), blimg($block), q($block->name),
        sprintf(__('(U+%04X to U+%04X)'), $block->first, $block->last));
}

/**
 * return the image element for a block
 */
function blimg(Block $block, int $width=16) : string {
    $name = str_replace([' ', '_', '-'], '', strtolower($block->name));
    $url = sprintf('/static/images/LastResort.svg#%s', $name);
    return sprintf('<svg width="%s" height="%s"><svg viewBox="194 97 1960 1960" width="100%%" height="100%%">'.
        '<use xlink:href="%s"/></svg></svg>', $width, $width, $url);
}

/**
 * return the representation for a plane link
 */
function pl(Plane $plane, string $rel='', string $class='') : string {
    if ($rel) {
        $rel = ' rel="'.q($rel).'"';
    }
    if ($class) {
        $class = ' '.q($class);
    }
    return sprintf('<a class="pl%s"%s href="%s">'.
            '%s <span class="title">%s</span>'.
            ' <span class="meta">%s</span>'.
        '</a>',
        $class, $rel, q(url($plane)), plimg($plane), q($plane->name),
        sprintf(__('(U+%04X to U+%04X)'), $plane->first, $plane->last));
}

/**
 * return the image element for a plane
 */
function plimg(Plane $plane, int $width=16) : string {
    $map = [
        'notdefplanezero',
        'notdefplaneone',
        'notdefplanetwo',
        'notdefplanethree',
        'notdefplanefour',
        'notdefplanefive',
        'notdefplanesix',
        'notdefplaneseven',
        'notdefplaneeight',
        'notdefplanenine',
        'notdefplaneten',
        'notdefplaneeleven',
        'notdefplanetwelve',
        'notdefplanethirteen',
        'notdefplanefourteen',
        'privateplane15',
        'privateplane16',
    ];
    $name = $map[$plane->first / 0x10000];
    $url = sprintf('/static/images/LastResort.svg#%s', $name);
    return sprintf('<svg width="%s" height="%s"><svg viewBox="194 97 1960 1960" width="100%%" height="100%%">'.
        '<use xlink:href="%s"/></svg></svg>', $width, $width, $url);
}

/**
 * generate an URL from any item (Codepoint, Block, ...)
 */
function url(/*mixed*/ $item) : string {
    if ($item instanceof Codepoint) {
        return sprintf('U+%04X', $item->id);
    }
    if ($item instanceof Block) {
        return str_replace(' ', '_', strtolower($item->name));
    }
    if ($item instanceof Plane) {
        return 'plane/' . str_replace(' ', '_', strtolower($item->name));
    }
    return urlencode($item);
}
