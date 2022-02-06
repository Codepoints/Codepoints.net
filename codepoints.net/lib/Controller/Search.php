<?php

namespace Codepoints\Controller;

use \Analog\Analog;
use Codepoints\Database;
use Codepoints\Controller;
use Codepoints\Unicode\Block;
use Codepoints\Unicode\SearchResult;
use Codepoints\Router\NotFoundException;
use Codepoints\Router\Pagination;


class Search extends Controller {

    private int $query_count = 0;

    /**
     * @param string $match
     */
    public function __invoke($match, Array $env) : string {
        $query = filter_input(INPUT_SERVER, 'QUERY_STRING');
        list($search_result, $pagination) = $this->getSearchResult($query, $env);

        /* needed in view to fill the <input> again */
        $q = (string)filter_input(INPUT_GET, 'q');
        $blocks = [];
        if ($q) {
            $data = $env['db']->getAll('
            SELECT name, first, last FROM blocks
            WHERE replace(replace(lower(name), "_", ""), " ", "") LIKE ?
            ORDER BY first ASC',
                str_replace([' ', '_'], '', strtolower("%$q%")));
            foreach ((array)$data as $item) {
                $blocks[] = new Block($item, $env['db']);
            }
        }

        $this->context += [
            'search_result' => $search_result,
            'pagination' => $pagination,
            'blocks' => $blocks,
            'q' => $q,
            'wizard' => false,
        ];

        $title = __('Search');
        $page_description = __('Search Codepoints.net by specifying many different possible parameters.');

        if ($search_result && $pagination) {
            $cQuery = $this->query_count;
            $cBlocks = count($blocks);
            $cBResult = $cBlocks > 0? ($cBlocks > 1?
                sprintf(__(' and %s Blocks'), $cBlocks) :
                __(' and 1 Block')) :
                '';
            $fQuery = $search_result->count();
            $title = $fQuery > 0? ($fQuery > 1?
                sprintf(__('%s Codepoints%s Found'), $fQuery, $cBResult) :
                    sprintf(__('1 Codepoint%s Found'), $cBResult)) :
                        sprintf(__('No Codepoints%s Found'), $cBResult);
            if ($fQuery === 0) {
                $page_description = __('No codepoints match the given search.');
            } else {
                $page = get_page();
                $page_description = sprintf(__('%s codepoints match the given search for %s properties.'), $fQuery, $cQuery);
                if ($page && $page > 1) {
                    $page_description .= ' ' . sprintf(__('This is page %s of %s.'), $page, $pagination->getNumberOfPages());
                }
            }
        }

        $this->context += [
            'title' => $title,
            'page_description' => $page_description,
        ];

        return parent::__invoke($match, $env);
    }

    /**
     * This method is public, because it is re-used in the API.
     *
     * @see \Codepoints\Api\Runner\Search
     *
     * @return Array{0: ?SearchResult, 1: ?Pagination}
     */
    public function getSearchResult(string $query, Array $env) : Array {
        $search_result = null;
        $pagination = null;

        if ($query) {
            $page = get_page();
            list($query_statement, $count_statement, $params) = $this->composeSearchQuery($query, $page, $env);
            if (! $query_statement || ! $count_statement) {
                throw new NotFoundException('no search query');
            }

            $count_statement->execute($params);
            if (defined('DEBUG') && DEBUG) {
                Analog::log(print_r($params, true));
            }
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
        return [$search_result, $pagination];
    }

    /**
     * compose the search query
     *
     * We create two SQL queries (one for the paginated results, a second for
     * the total number), because this is in our situation way more performant
     * than SQL_CALC_FOUND_ROWS. Cf.
     * https://stackoverflow.com/q/186588/113195
     * for details.
     */
    protected function composeSearchQuery(string $query_string, int $page, Array $env) : Array {
        $query_list = $this->parseQuery($query_string, $env);
        $params = [];
        $search = '';
        foreach ($query_list as $i => list($connector, $key, $comp, $value)) {
            if ($search !== '') {
                /* join the query with the last one using the $q0 parameter
                 * ("AND" or "OR"). */
                $search .= " $connector ";
            }
            if ($key === 'cp' || $key === 'int') {
                /* handle "cp" specially and search "cp" column directly */
                $search .= " cp $comp :q$i ";
                $params[':q'.$i] = $value;
            } else {
                if ($key === 'block') {
                    $key = 'blk';
                }
                if ($key === 'na' || $key === 'na1') {
                    /* match names loosely, especially to make the search
                     * case-insensitive */
                    $key = 'na';
                    $comp = 'LIKE';
                    $value = "%$value%";
                }
                if (is_array($value)) {
                    $search .= " term $comp ( ";
                    $search .= join(', ', array_map(function (string $item) use ($key, $env) : string {
                        return $env['db']->quote($key === 'term'? $item : $key.':'.$item);
                    }, $value));
                    $search .= " ) ";
                } else {
                    /* the default is to query the column "term" with the
                     * comparison $q2 for a combined value "$q1:$q3" */
                    $search .= " term $comp :q$i ";
                    $params[':q'.$i] = $key === 'term'? $value : $key.':'.$value;
                }
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
     * @return list<Array{"AND" | "OR", string, "=" | "!=" | ">" | "<" | "LIKE" | "NOT LIKE" | "IN" | "NOT IN", string | mixed}>
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
            if (! $value) {
                continue;
            }
            $query = array_merge($query,
                $this->getTransformedQuery($key, rawurldecode($value), $env));
        }
        $this->query_count = count($query);
        return $query;
    }

    /**
     * translate URL parameters to SQL query chips
     *
     * @return list<Array{"AND" | "OR", string, "=" | "!=" | ">" | "<" | "LIKE" | "NOT LIKE" | "IN" | "NOT IN", string | mixed}>
     */
    protected function getTransformedQuery(string $key, string $value, Array $env) : Array {
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
                if (array_key_exists($term, $env['info']->properties)) {
                    /* prevent searches for "ccc" or "uc" to drain the whole
                     * search table due to "uc:1234" entries. */
                    $result[] = ['AND', 'term', 'NOT LIKE', $term.':%'];
                }
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
        $booleans = [];
        foreach ($env['info']->booleans as $key) {
            $booleans[strtolower($key)] = $key;
        }

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
                $r['cp'][] = mb_ord($v);
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
                $r['cp'][] = mb_ord(rawurldecode($v));
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
            if ($v !== $low_v) {
                $r['term'][] = $low_v;
            }
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

            if (array_key_exists($low_v, $booleans)) {
                $r['term'][] = $booleans[$low_v].':1';
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
