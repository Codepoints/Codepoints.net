<?php

namespace Codepoints\Unicode\CodepointInfo;

use \Codepoints\Unicode\Codepoint;
use \Codepoints\Unicode\CodepointInfo;


/**
 * get a code point's pronunciation information, if any
 */
class Pronunciation extends CodepointInfo {

    /**
     * get the pinyin pronunciation of a code point
     */
    public function __invoke(Codepoint $codepoint) : ?string {
        $props = $codepoint->properties;
        $pr = null;
        if (! isset($props['kDefinition'])) {
            /* this code point is some non CJK code point, e.g. a PUA one */
            return $pr;
        }
        if ($props['kHanyuPinlu'] ?? null) {
            $pr = preg_replace('/^([\p{L}\p{N}]+).*/u', '$1',
                               (string)$props['kHanyuPinlu']);
        } elseif ($props['kXHC1983'] ?? null) {
            $pr = preg_replace('/^[0-9.*,]+:([^ ,]+)(?:[ ,].*)?$/u', '$1',
                               (string)$props['kXHC1983']);
        } elseif ($props['kHanyuPinyin'] ?? null) {
            $pr = preg_replace('/^[0-9.*,]+:([^ ,]+)(?:[ ,].*)?$/u', '$1',
                               (string)$props['kHanyuPinyin']);
        } elseif ($props['kMandarin'] ?? null) {
            $pr = strtolower(preg_replace('/^([\p{L}\p{N}]+).*/u', '$1',
                               (string)$props['kMandarin']));
        }
        return $pr;
    }

}
