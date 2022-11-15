<?php

namespace Codepoints\Controller;

use \Analog\Analog;
use Codepoints\Database;
use Codepoints\Controller;
use Codepoints\Unicode\Block;
use Codepoints\Unicode\Codepoint;
use Codepoints\Unicode\SearchResult;
use Codepoints\Router\NotFoundException;
use Codepoints\Router\Pagination;


class Search extends Controller {

    private int $query_count = 0;

    private Array $query = [];

    /**
     * @param string $match
     */
    public function __invoke($match, Array $env) : string {
        $query = rawurldecode(filter_input(INPUT_SERVER, 'QUERY_STRING'));
        try {
            list($search_result, $pagination) = $this->getSearchResult($query, $env);
        } catch (NotFoundException $err) {
            $search_result = null;
            $pagination = null;
        }

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

        $alt_result = null;
        if ($q && (! $search_result || ! $search_result->count())) {
            $alt_q = array_map('\\mb_ord', mb_str_split(substr($q, 0, 256)));
            $alt_result = $env['db']->getAll('
                SELECT cp, name, gc
                FROM codepoints
                WHERE cp IN ('.str_repeat('?, ', count($alt_q) - 1).' ?)',
                ...$alt_q);
            if ($alt_result) {
                $alt_result = array_map(function (Array $item) use ($env) {
                    return Codepoint::getCached($item, $env['db']);
                }, $alt_result);
            }
        }

        $all_block_names = [];
        $data = $env['db']->getAll('
        SELECT name FROM blocks
        ORDER BY first ASC');
        foreach ((array)$data as $item) {
            $all_block_names[$item['name']] = $item['name'];
        }

        $this->context += [
            'search_result' => $search_result,
            'alt_result' => $alt_result ?: [],
            'pagination' => $pagination,
            'blocks' => $blocks,
            'all_block_names' => $all_block_names,
            'q' => $q,
            'query' => $this->query,
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
            $transformed_query = join(' ', $this->parseQuery($query, $env));

            /**
             * We create two SQL queries (one for the paginated results, a second for
             * the total number), because this is in our situation way more performant
             * than SQL_CALC_FOUND_ROWS. Cf.
             * https://stackoverflow.com/q/186588/113195
             * for details.
             */
            $count_statement = $env['db']->prepare('
                SELECT COUNT(*) AS count
                FROM search_index
                WHERE MATCH(text) AGAINST (? IN BOOLEAN MODE)');

            $count_statement->execute([$transformed_query]);
            if (defined('DEBUG') && DEBUG) {
                Analog::log(sprintf('search for: %s', $transformed_query));
            }
            $count = 0;
            $counter = $count_statement->fetch(\PDO::FETCH_ASSOC);
            if ($counter) {
                $count = $counter['count'];
            }

            $page = get_page();
            $query_statement = $env['db']->prepare(sprintf('
                SELECT c.cp, c.name, c.gc
                FROM search_index
                LEFT JOIN codepoints c USING (cp)
                WHERE MATCH(text) AGAINST (? IN BOOLEAN MODE)
                LIMIT %s, %s',
                ($page - 1) * Pagination::PAGE_SIZE, Pagination::PAGE_SIZE));
            $query_statement->execute([$transformed_query]);
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
     * @return list<string>
     */
    protected function parseQuery(string $query_string, Array $env) : Array {
        $query = [];
        $template_query = [];
        $parts = explode('&', $query_string);
        foreach ($parts as $part) {
            if (strpos($part, '=') === false) {
                continue;
            }
            list($key, $value) = explode('=', $part, 2);
            $value = urldecode($value);
            $key = rtrim($key, '[]');
            if (preg_match('/[^a-zA-Z0-9_]/', $key)) {
                continue;
            }
            if (! $value && $value !== '0') {
                continue;
            }
            if (! array_key_exists($key, $template_query)) {
                $template_query[$key] = [];
            }
            $template_query[$key][] = $value;
            $query = array_merge($query,
                $this->getTransformedQuery($key, rawurldecode($value), $env));
        }
        $this->query = $template_query;
        $this->query_count = count($query);
        return $query;
    }

    /**
     * translate URL parameters to SQL query chips
     *
     * @return list<string>
     */
    protected function getTransformedQuery(string $key, string $value, Array $env) : Array {
        $result = [];
        $lower_case_properties = array_map('\\strtolower', array_keys($env['info']->properties));

        if ($key === 'q') {
            /* "q" is a special case: We parse the query and try to
             * figure, what's searched, but we also add the original query. */
            $result[] = $value;
            foreach ($this->_parseFreeText($value, $env) as $term) {
                $result[] = $term;
            }

        } elseif ($key === 'na') {
            $result[] = sprintf('"na_%s" %s', $value, $value);

        } elseif ($key === 'sc') {
            $result[] = sprintf('"sc_%s"', $value);

        } elseif ($key === 'scx') {
            /* scx is a space-separated list of sc's */
            $result[] = join(' ', array_map(function(string $sc) : string {
                return sprintf('"sc_%s"', $sc);
            }, explode(' ', $value)));

        } elseif ($key === 'int') {
            $value = preg_split('/\s+/', $value);
            foreach($value as $v2) {
                if (ctype_digit($v2)) {
                    $result[] = sprintf('"int_%s"', $v2);
                }
            }

        } elseif ($key === 'gc') {
            if (array_key_exists($value, $env['info']->gc_shortcuts)) {
                foreach ($env['info']->gc_shortcuts[$value] as $gc) {
                    $result[] = sprintf('"prop_gc_%s"', $gc);
                }
            } else {
                $result[] = sprintf('"prop_gc_%s"', $value);
            }

        } elseif (in_array($key, array_keys($env['info']->properties))) {
            $result[] = sprintf('"prop_%s_%s"', $key, $value);

        } elseif (in_array($key, ['blk', 'block'])) {
            $result[] = sprintf('"prop_blk_%s"', $value);
        }
        return $result;
    }

    private function _parseFreeText(string $q, Array $env) : Array {
        $r = [];
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

            if (mb_strlen($v) === 1) {
                /* seems to be one single character */
                $r[] = mb_ord($v);
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
                    $r[] = $n;
                } else {
                    $r[] = substr($v, 1, -1);
                }
                /* continue with next term, b/c this is a very specific search */
                continue;
            }

            if (preg_match('/^(%[a-f0-9]{2})+$/i', $v)) {
                /* an URL-encoded UTF-8 character */
                $r[] = mb_ord(rawurldecode($v));
                /* continue with next term, b/c this is a very specific search */
                continue;
            }

            if (ctype_xdigit($v) && in_array(strlen($v), [4,5,6])) {
                $r[] = hexdec($v);
            }

            if (substr($low_v, 0, 2) === 'u+' &&
                ctype_xdigit(substr($v, 2)) &&
                in_array(strlen($v), [6,7,8])) {
                // U+1F456 escapes
                $r[] = hexdec(substr($v, 2));
            }

            if (substr($low_v, 0, 1) === 'u' &&
                ctype_xdigit(substr($v, 1)) &&
                in_array(strlen($v), [5,6,7,8,9])) {
                // U0001F456 escapes
                $r[] = hexdec(substr($v, 1));
            }

            if (ctype_digit($v) && strlen($v) < 8) {
                $r[] = intval($v);
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

            $v_sc = array_search($low_v, $sc);
            if ($v_sc) {
                $r[] = 'sc_'.$v_sc;
            }

            if ($next_term) {
                $v_sc = array_search($low_v.' '.strtolower($next_term), $sc);
                if ($v_sc) {
                    $r[] = 'sc_'.$v_sc;
                }
            }
        }

        return $r;
    }

}
