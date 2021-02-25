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

    public function __construct(Database $db, string $lang) {
        /* this is a performance optimation to call __() less */
        $this->altText = __('Glyph for %s');
        parent::__construct($db, $lang);
    }

    /**
     * return the image element for a code point
     *
     * When https://bugzilla.mozilla.org/show_bug.cgi?id=1027106 is fixed, we
     * can switch back to good ol' <img> again.
     */
    public function __invoke(Codepoint $codepoint) : callable {
        $altText = $this->altText;
        return function(int $width=16) use ($codepoint, $altText) : string {
            $alt = sprintf($altText, (string)$codepoint);
            if (in_array($codepoint->gc, ['Co', 'Xx'])) {
                return sprintf(
                    '<img src="%s" width="%s" height="%s" alt="%s">',
                    q(url('/static/images/icon.svg')),
                    $width, $width, $alt);
            }
            $id = $codepoint->id;
            $url = sprintf('image/%04X.svg#U%04X', $id - $id % 0x100, $id);
            $modifier = '';
            if (in_array($codepoint->gc, ['Mn', 'Me', 'Lm', 'Sk'])) {
                /* for combining characters, add the U+25CC circle */
                $modifier = '<use xlink:href="image/2500.svg#U25CC" fill-opacity="0.25"/>';
            }
            return sprintf('<svg width="%s" height="%s">'.
                '<title>%s</title>'.
                '%s<use xlink:href="%s"/></svg>', $width, $width, $alt, $modifier, $url);
        };
    }

}
