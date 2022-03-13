<?php

use Codepoints\Database;
use Codepoints\Unicode\Codepoint;
use Codepoints\Unicode\Range;


/**
 * parse a string of form U+A..U+B,U+C into a list of Ranges and code points
 *
 * @return list<Range|Codepoint|null>
 */
function parse_range(string $str, Database $db) {
    $set = [];
    $junks = preg_split('/\s*(?:,\s*)+/', trim($str));
    foreach ($junks as $j) {
        $ranges = preg_split('/\s*(?:-|\.\.|:)\s*/', $j);
        switch (count($ranges)) {
            case 0:
                break;
            case 1:
                $tmp = parse_codepoint($ranges[0], true);
                if (is_int($tmp)) {
                    $set[] = get_codepoint($tmp, $db);
                }
                break;
            case 2:
                $low = parse_codepoint($ranges[0], true);
                $high = parse_codepoint($ranges[1], true);
                if (is_int($low) && is_int($high)) {
                    $set[] = new Range([
                        'first' => min($low, $high),
                        'last' => max($high, $low)], $db);
                }
                break;
            default:
                /* the strange case of U+1234..U+4567..U+7890. We try to handle
                 * it gracefully. */
                $max = -1;
                $min = 0x110000;
                foreach ($ranges as $r) {
                    $tmp = parse_codepoint($r, true);
                    if (is_int($tmp) && $tmp > $max) {
                        $max = $tmp;
                    }
                    if (is_int($tmp) && $tmp < $min) {
                        $min = $tmp;
                    }
                }
                if ($min < 0x110000 && $max > -1) {
                    $set[] = new Range([
                        'first' => min($min, $max),
                        'last' => max($max, $min)], $db);
                }
        }
    }
    return $set;
}


/**
 * return the codepoint for a single "U+" representation
 *
 * The code point is not checked for existence.
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
    } elseif (is_surrogate($cp)) {
        $data = [
            'cp' => $cp,
            'name' => 'SURROGATE CODE POINT',
            'gc' => 'Cs',
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


/**
 * check, if a code point is in a private use area
 */
function is_pua(int $cp) : bool {
    return ((0xE000 <= $cp && $cp <= 0xF8FF) ||
            (0xF0000 <= $cp && $cp <= 0xFFFFD) ||
            (0x100000 <= $cp && $cp <= 0x10FFFD));
}


/**
 * check, if a code point is a surrogate code point
 */
function is_surrogate(int $cp) : bool {
    return (0xD800 <= $cp && $cp <= 0xDFFF);
}


/**
 * map a code point ID to a printable code point, if such a mapping exists
 *
 * @param int $id
 * @param string $gc
 * @return int
 */
function get_printable_codepoint(int $id, string $gc) {
    if ($id < 0x21) {
        /* low ASCII controls: there’s a dedicated symbol */
        $id = $id + 0x2400;
    } elseif ($id === 0x7F) {
        /* U+007F DELETE: special symbol, too */
        $id = 0x2421;
    } elseif (substr($gc, 0, 1) === 'C' && $gc !== 'Cf') {
        /* any other control apart from formatting controls (Cf), that
         * actually have a rendering */
        $id = 0xFFFD;
    } elseif ($gc === 'Zl') {
        /* new line => symbol for newline */
        $id = 0x2424;
    } elseif ($gc === 'Zp') {
        /* new paragraph => pilcrow */
        $id = 0x00B6;
    }
    return $id;
}
