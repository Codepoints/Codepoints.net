<?php

namespace Codepoints\Controller;

use Codepoints\Database;
use Codepoints\Controller;
use Codepoints\Unicode\SearchResult;
use Codepoints\Router\Pagination;


class Search extends Controller {

    /**
     * @param string $match
     */
    public function __invoke($match, Array $env) : string {
        $this->context += [
            'title' => __('Search'),
            'page_description' => __('Search Codepoints.net by specifying many different possible parameters.'),
        ];
        $page = get_page();

        $query = filter_input(INPUT_SERVER, 'QUERY_STRING');
        $q = filter_input(INPUT_GET, 'q');
        if (! $q) {
            $q = '';
        }

        $search_result = null;
        $pagination = null;
        if ($query) {
            list($query_statement, $count_statement, $params) = $this->composeSearchQuery($query, $page, $env);

            $count_statement->execute($params);
            $count = 0;
            $counter = $count_statement->fetch(\PDO::FETCH_ASSOC);
            if ($counter) {
                $count = $counter['count'];
            }

            $query_statement->execute($params);
            $items = $query_statement->fetchAll(\PDO::FETCH_ASSOC);

            $search_result = new SearchResult([
                'count' => $count,
                'items' => $items,
            ], $env['db']);

            $pagination = new Pagination($search_result, $page);
        }

        $this->context += [
            'search_result' => $search_result,
            'pagination' => $pagination,
            'q' => $q,
        ];

        return parent::__invoke($match, $env);
    }

    /**
     * compose the search query
     */
    private function composeSearchQuery(string $query_string, int $page, Array $env) : Array {
        $query_list = $this->parseQuery($query_string, $env);
        $params = array();
        $search = '';
        foreach ($query_list as $i => $q) {
            if ($search !== '') {
                /* join the query with the last one using the $q0 parameter
                 * ("AND" or "OR"). */
                $search .= " ${q[0]} ";
            }
            if ($q[1] === 'term') {
                $search .= " term ${q[2]} :q$i ";
                $params[':q'.$i] = "${q[3]}";
            } elseif ($q[1] === 'na' || $q[1] === 'na1') {
                /* match names loosely, especially to make the search
                 * case-insensitive */
                $search .= " term LIKE :q$i ";
                $params[':q'.$i] = "na:%${q[3]}%";
            } elseif ($q[1] === 'cp' || $q[1] === 'int') {
                /* handle "cp" specially and search "cp" column directly */
                $search .= " cp = :q$i ";
                $params[':q'.$i] = $q[3];
            } elseif ($q[1] === 'block' || $q[1] === 'blk') {
                $search .= " term = :q$i ";
                $params[':q'.$i] = 'blk:'.$q[3];
            } else {
                /* the default is to query the column $q0 with the
                 * comparison $q1 for a value $q2 */
                $search .= " term ${q[2]} :q$i ";
                $params[':q'.$i] = $q[1].':'.$q[3];
            }
        }

        $query_statement = $env['db']->prepare(sprintf('
            SELECT c.cp, c.name, c.gc
            FROM search_index
            LEFT JOIN codepoints c USING (cp)
            WHERE %s
            GROUP BY cp
            ORDER BY SUM(weight) DESC, cp ASC
            LIMIT %s, %s', $search,
                ($page - 1) * Pagination::PAGE_SIZE, Pagination::PAGE_SIZE));
        $count_statement = $env['db']->prepare(sprintf('
            SELECT COUNT(*) AS count
            FROM search_index
            WHERE %s', $search));
        return [$query_statement, $count_statement, $params];
    }

    /**
     * @return Array<string, Array>
     */
    private function parseQuery(string $query_string, Array $env) : Array {
        $query = [];
        $parts = explode('&', $query_string);
        foreach ($parts as $part) {
            if (strpos($part, '=') === false) {
                continue;
            }
            list($key, $value) = explode('=', $part, 2);
            $key = rtrim($key, '[]');
            if (preg_match('/[^a-zA-Z0-9_]/', $key)) {
                continue;
            }
            $query = array_merge($query,
                $this->getTransformedQuery($key, rawurldecode($value), $env));
        }
        return $query;
    }

    /**
     *
     */
    private function getTransformedQuery(string $key, string $value, Array $env) : Array {
        $result = [];
        if ($key === 'q') {
            /* "q" is a special case: We parse the query and try to
             * figure, what's searched */
            $q = $this->_parseFreeText($value, $env);
            foreach ($q['cp'] as $cp) {
                $result[] = ['OR', 'cp', '=', $cp];
            }
            foreach ($q['term'] as $term) {
                $result[] = ['OR', 'term', 'LIKE', $term.'%'];
                /* prevent searches for "ccc" or "uc" to drain the whole
                 * search table due to "uc:1234" entries. */
                $result[] = ['AND', 'term', 'NOT LIKE', $term.':%'];
            }
        } elseif ($key === 'scx') {
            /* scx is a space-separated list of sc's */
            $result = array_map(function(string $sc) : Array {
                return ['OR', 'sc', '=', $sc];
            }, explode(' ', $value));
        } elseif ($key === 'int') {
            $value = preg_split('/\s+/', $value);
            foreach($value as $v2) {
                if (ctype_digit($v2)) {
                    $result[] = ['OR', $key, '=', $v2];
                }
            }
        } elseif ($key === 'gc') {
            if (array_key_exists($value, $env['info']->gc_shortcuts)) {
                foreach ($env['info']->gc_shortcuts[$value] as $gc) {
                    $result[] = ['OR', 'gc', '=', $gc];
                }
            } else {
                $result[] = ['OR', $key, '=', $value];
            }
        } elseif (in_array($key, array_keys($env['info']->properties))) {
            $result[] = ['OR', $key, '=', $value];
        } elseif ($key === 'block') {
            $result[] = ['OR', 'blk', '=', $value];
        }
        return $result;
    }

    private function _parseFreeText(string $q, Array $env) : Array {
        $r = ['cp' => [], 'term' => []];
        $sc = array_map('strtolower', $env['info']->script);

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
                    if (strtolower($v[0]) === 'x') {
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

    private function _getSingular(string $term) : string {
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
