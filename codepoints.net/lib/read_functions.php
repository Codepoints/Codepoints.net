<?php

use Codepoints\Unicode\Range;

# TODO adapt to new lazy range, i.e., return Array<Range|Codepoint>
#/**
# * parse a string of form U+A..U+B,U+C into a Range
# */
#function parse_range(string $str, Database $db) : Range {
#    $set = [];
#    $junks = preg_split('/\s*(?:,\s*)+/', trim($str));
#    foreach ($junks as $j) {
#        $ranges = preg_split('/\s*(?:-|\.\.|:)\s*/', $j);
#        switch (count($ranges)) {
#            case 0:
#                break;
#            case 1:
#                $tmp = parse_codepoint($ranges[0], true);
#                if (is_int($tmp)) {
#                    $set[] = $tmp;
#                }
#                break;
#            case 2:
#                $low = parse_codepoint($ranges[0], true);
#                $high = parse_codepoint($ranges[1], true);
#                if (is_int($low) && is_int($high)) {
#                    $set = array_merge($set, range(min($low, $high),
#                                                   max($high, $low)));
#                }
#                break;
#            default:
#                $max = -1;
#                $min = 0x110000;
#                foreach ($ranges as $r) {
#                    $tmp = parse_codepoint($r, true);
#                    if (is_int($tmp) && $tmp > $max) {
#                        $max = $tmp;
#                    }
#                    if (is_int($tmp) && $tmp < $min) {
#                        $min = $tmp;
#                    }
#                }
#                if ($min < 0x110000 && $max > -1) {
#                    $set = array_merge($set, range(min($min, $max),
#                                                   max($max, $min)));
#                }
#        }
#    }
#    return new Range($set, $db);
#}


/**
 * return the codepoint for a single representation
 *
 * @param bool $lenient if other values than U+hex should be recognized
 */
function parse_codepoint(string $str, $lenient=false) : ?int {
    $prefix = 'U\\+';
    if ($lenient) {
        $prefix = '(?:U\\+|\\\\U|0x|U-?)?';
    }
    preg_match('/^'.$prefix.'([0-9a-f]+)$/i', $str, $matches);
    if (count($matches) === 2) {
        return intval($matches[1], 16);
    }
    return null;
}


/**
 * get a Codepoint object by the integer code point
 */
function get_codepoint(int $cp, Database $db) : ?Codepoint {
    if (is_pua($cp)) {
        $data = [
            'cp' => $cp,
            'name' => 'PRIVATE USE CHARACTER',
            'gc' => 'Co',
        ];
    } else {
        $data = $db->getOne('SELECT cp, name, gc FROM codepoints WHERE cp = ?',
            $cp);
    }
    if ($data) {
        return Codepoint::getCached($data, $db);
    }
    return null;
}
