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
        $pr = '';
        $toPinyin = false;
        if ($props['kHanyuPinlu']) {
            $toPinyin = true;
            $pr = preg_replace('/^([a-z0-9]+).*/', '$1',
                               $props['kHanyuPinlu']);
        }
        if (! $pr && $props['kXHC1983']) {
            $pr = preg_replace('/^[0-9.*,]+:([^ ,]+)(?:[ ,].*)?$/', '$1',
                               $props['kXHC1983']);
        }
        if (! $pr && $props['kHanyuPinyin']) {
            $pr = preg_replace('/^[0-9.*,]+:([^ ,]+)(?:[ ,].*)?$/', '$1',
                               $props['kHanyuPinyin']);
        }
        if (! $pr && $props['kMandarin']) {
            $toPinyin = true;
            $pr = strtolower(preg_replace('/^([A-Z0-9]+).*/', '$1',
                               $props['kMandarin']));
        }
        if ($toPinyin) {
            $pr = preg_replace_callback('/([aeiouü])([^aeiouü12345]*)([12345])/',
                function($matches) {
                    $map = array(
                        'a' => array(1 => '0101', 2 => '00E1', 3 => '01CE', 4 => '00E0'),
                        'e' => array(1 => '0113', 2 => '00E9', 3 => '011B', 4 => '00E8'),
                        'i' => array(1 => '012B', 2 => '00ED', 3 => '01D0', 4 => '00EC'),
                        'o' => array(1 => '014D', 2 => '00F3', 3 => '01D2', 4 => '00F2'),
                        'u' => array(1 => '016B', 2 => '00FA', 3 => '01D4', 4 => '00F9'),
                        'ü' => array(1 => '01D6', 2 => '01D8', 3 => '01DA', 4 => '01DC'),
                    );
                    if (array_key_exists($matches[1], $map) &&
                        array_key_exists($matches[3], $map[$matches[1]])) {
                        $mod = mb_convert_encoding('&#x'.$map[$matches[1]][$matches[3]].';', 'UTF-8',
                                'HTML-ENTITIES');
                    } else {
                        $mod = $matches[1];
                    }
                    return $mod.$matches[2];
                }, $pr);
        }
        return $pr?: null;
    }

}
