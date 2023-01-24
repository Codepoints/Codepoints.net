<?php

namespace Codepoints\Search;

use \Analog\Analog;
use Codepoints\Database;
use Codepoints\Controller;
use Codepoints\Router\Pagination;
use Codepoints\Search\FreeTextInterpreter;
use Codepoints\Unicode\Block;
use Codepoints\Unicode\Codepoint;
use Codepoints\Unicode\SearchResult;


/**
 * search engine
 *
 * We found that simply throwing every query onto the fulltext index is too
 * costly. This engine therefore optimizes the way we query the DB. Firstly,
 * we check if it might be a single cp that is searched in the "q" field. If
 * so we return this cp (as text).
 *
 * Secondly, if a single code point property is queried, we bypass the fulltext
 * index and query the property directly in the codepoint_props table.
 *
 * And only thirdly will we look into the fulltext index.
 *
 * Looking at logs of past searches this reduces fulltext queries by something
 * along the lines of 99.5%.
 */
class Engine {

    private Array $query = [];

    private Array $env = [];

    private Array $cp_properties = [];

    private Array $lower_cp_properties = [];

    /**
     * @param array $env
     */
    public function __construct(Array $env) {
        $this->env = $env;
        $this->cp_properties = array_keys($this->env['info']->properties);
        $this->lower_cp_properties = array_map('\\strtolower', $this->cp_properties);
    }

    /**
     * @param string $query_string the query string with values still URL-encoded
     * @return string|?SearchResult either a single code point (as the real
     *         char) or a SearchResult object or null, if nothing was searched
     *         or found
     */
    public function search(string $query_string) {
        $this->query = $this->parseQuery($query_string);
        $single_cp = $this->detectSingleCodepointSearch($this->query);
        if ($single_cp) {
            return $single_cp;
        }
        $single_prop_result = $this->detectSinglePropertySearch($this->query);
        if ($single_prop_result) {
            return $single_prop_result;
        }

        return $this->getSearchResult();
    }

    /**
     * access the currently handled query
     *
     * @return array<string, non-empty-list<string>>
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * Parse a query into a structure we can use to search
     *
     * This method will throw away all parameters that are not relevant to
     * a search.
     *
     * @return array<string, non-empty-list<string>>
     */
    private function parseQuery(string $query_string) : Array {
        $query = [];
        $parts = explode('&', $query_string);
        foreach ($parts as $part) {
            if (strpos($part, '=') === false) {
                continue;
            }
            list($key, $value) = explode('=', $part, 2);
            $key = rtrim(urldecode($key), '[]');
            if (in_array($key, ['lang', 'page'])) {
                continue;
            }
            if (substr($key, 0, 1) === 'k' && in_array($key, $this->lower_cp_properties)) {
                /* we found that many users search Unihan properties with
                 * all lower-case names. Fix the name here so that it gets
                 * picked up later during filtering. */
                foreach ($this->cp_properties as $prop) {
                    if (strtolower($prop) === $key) {
                        $key = $prop;
                        break;
                    }
                }
            }
            if ($key !== 'q' && ! in_array($key, $this->cp_properties)) {
                continue;
            }
            if ($value === '') {
                continue;
            }
            if (preg_match('/[^a-zA-Z0-9_]/', $key)) {
                continue;
            }
            $value = urldecode($value);
            if (! array_key_exists($key, $query)) {
                $query[$key] = [];
            }
            $query[$key][] = $value;
        }
        return $query;
    }

    /**
     * detect if only a single code point was searched
     *
     * More concretely, make sure that no search parameter was given but the
     * "q" parameter, and the latter contains only a single character.
     *
     * @param array<string, non-empty-list<string>> $query
     * @return ?string
     */
    private function detectSingleCodepointSearch(array $query) {
        $filtered_get = array_filter(
            $query,
            /**
             * @param non-empty-list<string> $var
             */
            function(array $var) : bool {
                return mb_strlen($var[0]) === 1;
            });
        if (count($filtered_get) === 1 && isset($filtered_get['q'])) {
            return $filtered_get['q'][0];
        }
        return null;
    }

    /**
     * detect if only a single property was searched
     *
     * In that case we bypass our fulltext index for performance reasons and
     * go straight after the property in the codepoint_props table.
     *
     * @param array<string, non-empty-list<string>> $query
     * @return ?SearchResult
     */
    private function detectSinglePropertySearch(array $query) {
        if (count($query) !== 1) {
            return null;
        }
        $prop = array_keys($query)[0];
        if (count($query[$prop]) !== 1) {
            return null;
        }
        /* sc and scx are in properties, but they are _not_ a table column.
         * TODO: try to find a possibility to shortcut those queries, too. */
        if (in_array($prop, ['sc', 'scx']) || ! in_array($prop, $this->cp_properties)) {
            return null;
        }

        $value = $query[$prop][0];

        /* this looks like an opportunity for an SQL injection attack, but
         * given that we run $prop against a kind of allow-list of defined
         * Unicode properties this should be safe :tm: . */
        try {
            $count_statement = $this->env['db']->prepare('
                SELECT COUNT(*) AS count
                FROM codepoints
                LEFT JOIN codepoint_props p USING (cp)
                WHERE p.' . $prop .' = ?');
        } catch (\PDOException $e) {
            /* but it is still possible, that we hit a property that we
             * haven't got a db table column for. Guard against that problem
             * by defaulting back to the fulltext search. Properties that
             * are not columns are e.g., relations. */
            return null;
        }

        $count_statement->execute([$value]);
        Analog::debug(sprintf('search for single prop: %s=%s', $prop, $value));
        $count = 0;
        $counter = $count_statement->fetch(\PDO::FETCH_ASSOC);
        if ($counter) {
            $count = $counter['count'];
        }

        $page = get_page();
        /** @psalm-suppress PossiblyUndefinedArrayOffset */
        $query_statement = $this->env['db']->prepare(sprintf('
            SELECT c.cp, c.name, c.gc
            FROM codepoints c
            LEFT JOIN codepoint_props p USING (cp)
            WHERE p.%s = ?
            LIMIT %s, %s',
            $prop, ($page - 1) * Pagination::PAGE_SIZE, Pagination::PAGE_SIZE));
        $query_statement->execute([$value]);
        $items = $query_statement->fetchAll(\PDO::FETCH_ASSOC);

        return new SearchResult([
            'count' => $count,
            'items' => $items,
        ], $this->env['db']);
    }

    /**
     * @return ?SearchResult
     */
    private function getSearchResult() {
        $search_result = null;

        if ($this->query) {
            $transformed_query_arr = [];
            foreach ($this->query as $key => $values) {
                foreach ($values as $value) {
                    array_push($transformed_query_arr, ...$this->getTransformedQuery($key, $value));
                }
            }
            $transformed_query = join(' ', $transformed_query_arr);

            /**
             * We create two SQL queries (one for the paginated results, a second for
             * the total number), because this is in our situation way more performant
             * than SQL_CALC_FOUND_ROWS. Cf.
             * https://stackoverflow.com/q/186588/113195
             * for details.
             */
            $count_statement = $this->env['db']->prepare('
                SELECT COUNT(*) AS count
                FROM search_index
                WHERE MATCH(text) AGAINST (? IN BOOLEAN MODE)');

            $count_statement->execute([$transformed_query]);
            Analog::debug(sprintf('search for: %s', $transformed_query));
            $count = 0;
            $counter = $count_statement->fetch(\PDO::FETCH_ASSOC);
            if ($counter) {
                $count = $counter['count'];
            }

            $page = get_page();
            $items = [];
            /* if $count is 0, it's no use searching again, when we
             * already know that there is no result. */
            if ($count) {
                $query_statement = $this->env['db']->prepare(sprintf('
                    SELECT c.cp, c.name, c.gc
                    FROM search_index
                    LEFT JOIN codepoints c USING (cp)
                    WHERE MATCH(text) AGAINST (? IN BOOLEAN MODE)
                    LIMIT %s, %s',
                    ($page - 1) * Pagination::PAGE_SIZE, Pagination::PAGE_SIZE));
                $query_statement->execute([$transformed_query]);
                $items = $query_statement->fetchAll(\PDO::FETCH_ASSOC);
            }

            $search_result = new SearchResult([
                'count' => $count,
                'items' => $items,
            ], $this->env['db']);
        }
        return $search_result;
    }

    /**
     * translate URL parameters to SQL query chips
     *
     * @return list<string>
     */
    protected function getTransformedQuery(string $key, string $value) : Array {
        $result = [];

        if ($key === 'q') {
            /* "q" is a special case: We parse the query and try to
             * figure, what's searched, but we also add the original query. */
            $result[] = $value;
            $interpreter = new FreeTextInterpreter($this->env);
            foreach ($interpreter->interpret($value) as $term) {
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
            if (array_key_exists($value, $this->env['info']->gc_shortcuts)) {
                foreach ($this->env['info']->gc_shortcuts[$value] as $gc) {
                    $result[] = sprintf('"prop_gc_%s"', $gc);
                }
            } else {
                $result[] = sprintf('"prop_gc_%s"', $value);
            }

        } elseif (in_array($key, array_keys($this->env['info']->properties))) {
            $result[] = sprintf('"prop_%s_%s"', $key, $value);

        } elseif (in_array($key, ['blk', 'block'])) {
            $result[] = sprintf('"prop_blk_%s"', $value);
        }
        return $result;
    }

}
