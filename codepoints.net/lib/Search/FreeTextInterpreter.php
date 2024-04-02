<?php

namespace Codepoints\Search;

use \Analog\Analog;


/**
 * try to extract what people might have meant in search queries
 */
class FreeTextInterpreter {

    private array $scripts;

    /**
     * @param array $env
     */
    public function __construct(Array $env) {
        $this->scripts = array_map('strtolower', $env['info']->script);
    }

    /**
     * @return list<string>
     */
    public function interpret(string $q) : Array {
        $r = [];

        $terms = preg_split('/\s+/', $q);
        $i = 0;
        foreach ($terms as $v) {
            $i += 1;
            $low_v = strtolower($v);
            $next_term = null;
            if (count($terms) > $i) {
                $next_term = $terms[$i];
            }

            if (mb_strlen($v) === 1) {
                /* seems to be one single character */
                $r[] = (string)mb_ord($v);
            }

            if (preg_match('/^&#?[0-9a-z]+;$/i', $v)) {
                /* seems to be a single HTML escape sequence */
                if ($v[1] === '#') {
                    $v = substr($v, 2, -1);
                    if (strtolower($v[0]) === 'x') {
                        $n = intval(substr($v, 1), 16);
                    } else {
                        $n = intval($v, 10);
                    }
                    $r[] = (string)$n;
                } else {
                    $r[] = substr($v, 1, -1);
                }
                /* continue with next term, b/c this is a very specific search */
                continue;
            }

            if (preg_match('/^(%[a-f0-9]{2})+$/i', $v)) {
                /* an URL-encoded UTF-8 character */
                $r[] = (string)mb_ord(rawurldecode($v));
                /* continue with next term, b/c this is a very specific search */
                continue;
            }

            if (ctype_xdigit($v) && in_array(strlen($v), [4,5,6])) {
                $r[] = (string)hexdec($v);
            }

            if (substr($low_v, 0, 2) === 'u+' &&
                ctype_xdigit(substr($v, 2)) &&
                in_array(strlen($v), [6,7,8])) {
                // U+1F456 escapes
                $r[] = 'int_'.(string)hexdec(substr($v, 2));
            }

            if (substr($low_v, 0, 2) === '\\u' &&
                ctype_xdigit(substr($v, 2)) &&
                in_array(strlen($v), [6,7,8,9,10])) {
                // \u00012345 escapes
                $r[] = 'int_'.(string)hexdec(substr($v, 2));
            }

            if (substr($low_v, 0, 1) === 'u' &&
                ctype_xdigit(substr($v, 1)) &&
                in_array(strlen($v), [5,6,7,8,9])) {
                // U0001F456 escapes
                $r[] = 'int_'.(string)hexdec(substr($v, 1));
            }

            if (ctype_digit($v) && strlen($v) < 8) {
                $r[] = (string)intval($v);
            }

            if (preg_match('/\blowercase\b/', $low_v)) {
                $r[] = 'prop_gc_Ll';
            }
            if (preg_match('/\buppercase\b/', $low_v)) {
                $r[] = 'prop_gc_Lu';
            }
            if (preg_match('/\btitlecase\b/', $low_v)) {
                $r[] = 'prop_gc_Lt';
            }
            if (preg_match('/\bnon-?char(acter)?\b/', $low_v)) {
                $r[] = 'prop_NChar_1';
            }
            if ($low_v === 'number') {
                $r[] = 'prop_nt_De';
                $r[] = 'prop_nt_Di';
                $r[] = 'prop_nt_Nu';
            }
            if (rtrim($low_v, 's') === 'emoji') {
                $r[] = 'prop_Emoji_1';
            }

            $v_sc = array_search($low_v, $this->scripts);
            if (is_string($v_sc)) {
                $r[] = 'sc_'.$v_sc;
            }

            /** @psalm-suppress RiskyTruthyFalsyComparison */
            if ($next_term) {
                $v_sc = array_search($low_v.' '.strtolower($next_term), $this->scripts);
                if (is_string($v_sc)) {
                    $r[] = 'sc_'.$v_sc;
                }
            }
        }

        return $r;
    }

}
