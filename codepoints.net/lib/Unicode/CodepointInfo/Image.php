<?php

namespace Codepoints\Unicode\CodepointInfo;

use \Codepoints\Database;
use \Codepoints\Unicode\Codepoint;
use \Codepoints\Unicode\CodepointInfo;


/**
 * provide images for a given code point
 */
class Image extends CodepointInfo {

    private string $altText;

    public function __construct(Array $env) {
        /* this is a performance optimation to call __() less */
        $this->altText = __('Glyph for %s');
        parent::__construct($env);
    }

    /**
     * return the image element for a code point
     *
     * Initially we didn't use <img> due to
     * https://bugzilla.mozilla.org/show_bug.cgi?id=1027106, but we will
     * now stick with <svg>, because this allows us to color the glyphs
     * dynamically.
     */
    public function __invoke(Codepoint $codepoint) : callable {
        $altText = $this->altText;
        $template = '<svg width="%s" height="%s" class="cpfig__img cpfig__img--%s">'.
                    '<title>%s</title>'.
                    '%s<use href="%s"/></svg>';
        $cachebuster = substr(md5(SOFTWARE_VERSION), 0, 8);
        return function(int $width=16) use ($codepoint, $altText, $template, $cachebuster) : string {
            $alt = sprintf($altText, (string)$codepoint);
            if (in_array($codepoint->gc, ['Cn', 'Co', 'Cs', 'Xx'])) {
                /* special control characters and non-existing code points: Use
                 * our icon */
                $url = static_url('images/icon.svg').'#icon';
                return sprintf($template, $width, $width, $codepoint->gc, $alt, '', $url);
            }
            $id = get_printable_codepoint($codepoint->id, $codepoint->gc);
            $url = sprintf('/image/%04X.%s.svg#U%04X', $id - $id % 0x100, $cachebuster, $id);
            $modifier = '';
            if (in_array($codepoint->gc, ['Mn', 'Me', 'Lm', 'Sk'])) {
                /* for combining characters, add the appropriate U+25CC circle */
                $modifier = sprintf('<use xlink:href="/image/2500.%s.svg#U25CC" fill-opacity="0.25"/>', $cachebuster);
            }
            return sprintf($template, $width, $width, $codepoint->gc, $alt, $modifier, $url);
        };
    }

}
