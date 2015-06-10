<?php


/**
 *
 */
class SearchComposer {

    public function __construct($queries, $db) {
        $this->queries = array_filter($queries);
        $this->db = $db;
        $this->searchResult = new SearchResult(array(), $db);
    }

    public function getSearchResult() {
        $result = $this->searchResult;
        $cats = UnicodeInfo::get()->getCategoryKeys();
        $cats = array_merge($cats, array('int'));

        foreach ($this->queries as $k => $v) {
            if ($k === 'q') {
                // "q" is a special case: We parse the query and try to
                // figure, what's searched
                $q = $this->_parseFreeText($v);
                foreach ($q['cp'] as $cp) {
                    $result->addQuery('cp', $cp);
                }
                foreach ($q['term'] as $term) {
                    $result->addQuery('term', $term);
                }
            } elseif ($k === 'scx') {
                // scx is a list of sc's
                $result->addQuery($k, $v);
                $v2 = explode(' ', $v);
                foreach($v2 as $v3) {
                    $result->addQuery($k, "%$v3%", 'LIKE');
                }
            } elseif ($k === 'int') {
                $v = preg_split('/\s+/', $v);
                foreach($v as $v2) {
                    if (ctype_digit($v2)) {
                        $result->addQuery($k, $v2, '=');
                    }
                }
            } elseif ($k === 'gc') {
                foreach ((array)$v as $vv) {
                    if (array_key_exists($vv, UnicodeInfo::$gc_shortcuts)) {
                        $result->addQuery('gc', UnicodeInfo::$gc_shortcuts[$vv]);
                    } else {
                        $result->addQuery($k, $vv);
                    }
                }
            } elseif (in_array($k, $cats) || $k === 'block') {
                $result->addQuery($k, $v);
            }
            // else: that's an unrecognized option: Ignore it.
        }

        return $result;
    }

    private function _parseFreeText($q) {
        $r = ['cp' => [], 'term' => []];
        $sc = array_map('strtolower', UnicodeInfo::get()->getLegendForCategory('sc'));

        $terms = preg_split('/\s+/', $q);
        $i = 0;
        foreach ($terms as $v) {
            $i += 1;
            $low_v = strtolower($v);
            $next_term = null;
            if (count($terms) > $i) {
                $next_term = $terms[$i];
            }

            if (mb_strlen($v, 'UTF-8') === 1) {
                /* seems to be one single character */
                $r['cp'][] = unpack('N', mb_convert_encoding($v, 'UCS-4BE',
                                    'UTF-8'));
            }

            if (preg_match('/^&#?[0-9a-z]+;$/i', $v)) {
                /* seems to be a single HTML escape sequence */
                if ($v[1] === '#') {
                    $v = substr($v, 2, -1);
                    if ($lower_v[0] === 'x') {
                        $n = intval(substr($v, 1), 16);
                    } else {
                        $n = intval($v, 10);
                    }
                    $r['cp'][] = $n;
                } else {
                    $r['term'][] = 'alias:' . substr($v, 1, -1);
                }
                /* continue, b/c this is a very specific search */
                continue;
            }

            if (preg_match('/^(%[a-f0-9]{2})+$/i', $v)) {
                /* an URL-encoded UTF-8 character */
                $r['cp'][] = unpack('N', mb_convert_encoding(rawurldecode($v),
                                    'UCS-4BE', 'UTF-8'));
                /* continue, b/c this is a very specific search */
                continue;
            }

            if (ctype_xdigit($v) && in_array(strlen($v), [4,5,6])) {
                $r['cp'][] = hexdec($v);
            }

            if (substr($low_v, 0, 2) === 'u+' &&
                ctype_xdigit(substr($v, 2)) &&
                in_array(strlen($v), [6,7,8])) {
                // U+1F456 escapes
                $r['cp'][] = hexdec(substr($v, 2));
            }

            if (substr($low_v, 0, 1) === 'u' &&
                ctype_xdigit(substr($v, 1)) &&
                in_array(strlen($v), [5,6,7,8,9])) {
                // U0001F456 escapes
                $r['cp'][] = hexdec(substr($v, 1));
            }

            if (ctype_digit($v) && strlen($v) < 8) {
                $r['cp'][] = intval($v);
            }

            $r['term'][] = $v;
            $r['term'][] = $low_v;
            $singular = $this->_getSingular($low_v);
            if ($singular !== $low_v) {
                $r['term'][] = $singular;
            }

            if (preg_match('/\blowercase\b/', $low_v)) {
                $r['term'][] = 'gc:Ll';
            }
            if (preg_match('/\buppercase\b/', $low_v)) {
                $r['term'][] = 'gc:Lu';
            }
            if (preg_match('/\btitlecase\b/', $low_v)) {
                $r['term'][] = 'gc:Lt';
            }
            if (preg_match('/\bnon-?char(acter)?\b/', $low_v)) {
                $r['term'][] = 'NChar:1';
            }
            if ($low_v === 'number') {
                $r['term'][] = 'nt:De';
                $r['term'][] = 'nt:Di';
                $r['term'][] = 'nt:Nu';
            }

            $v_sc = array_search($low_v, $sc);
            if ($v_sc) {
                $r['term'][] = 'sc:'.$v_sc;
            }

            if ($next_term) {
                $v_sc = array_search($low_v.' '.strtolower($next_term), $sc);
                if ($v_sc) {
                    $r['term'][] = 'sc:'.$v_sc;
                }
            }

            /* TODO do the same as above for sc: with blk: */
        }

        return $r;
    }

    private function _getSingular($term) {
        if ($term === 'children') {
            return 'child';
        }
        if ($term === 'men') {
            return 'man';
        }
        if ($term === 'women') {
            return 'woman';
        }
        if (preg_match('/^[a-rt-z]+(s|sh|ch|o)es$/', $term)) {
            return substr($term, 0, -1);
        }
        if (preg_match('/^[a-rt-z]{2,}s$/', $term)) {
            return substr($term, 0, -1);
        }
        if (preg_match('/^[a-z]+[bcdfghj-np-tv-z]ies$/', $term)) {
            return substr($term, 0, -3).'y';
        }
        return $term;
    }

}


//__END__
