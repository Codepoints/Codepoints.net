<?php

namespace Codepoints\Controller;

use Codepoints\Controller\Search;
use Codepoints\Router\Pagination;
use Codepoints\Unicode\SearchResult;
use Codepoints\View;


class Wizard extends Search {

    /**
     * @param string $match
     */
    public function __invoke($match, Array $env) : string {
        $this->context += [
            'title' => __('Find My Codepoint'),
            'page_description' => __('Find a certain character by answering a set of questions. The questions will narrow down possible candidates in the wide range of Unicode codepoints.'),
        ];

        $query = filter_input(INPUT_SERVER, 'QUERY_STRING');

        $search_result = null;
        $pagination = null;
        if ($query) {
            $transformed_query = join(' ', $this->parseQuery($query, $env));

            $count_statement = $env['db']->prepare('
                SELECT COUNT(*) AS count
                FROM search_index
                WHERE MATCH(text) AGAINST (? IN BOOLEAN MODE)');
            $count_statement->execute([$transformed_query]);
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

        $all_block_names = [];
        $data = $env['db']->getAll('
        SELECT name FROM blocks
        ORDER BY first ASC');
        foreach ((array)$data as $item) {
            $all_block_names[$item['name']] = $item['name'];
        }

        $this->context += [
            'search_result' => $search_result,
            'alt_result' => [],
            'pagination' => $pagination,
            'all_block_names' => $all_block_names,
            'q' => '',
            'wizard' => true,
            'query' => [],
        ];
        return (new View('search'))($this->context + [
            'match' => $match,
        ], $env);
    }

    /**
     * overwrite the query parser to map the wizard query to search query
     *
     * phpcs:disable Generic.Metrics.CyclomaticComplexity.MaxExceeded
     * @return list<string>
     */
    protected function getTransformedQuery(string $key, string $value, Array $env) : Array {
        $result = [];
        switch ($key) {
            case 'def':
                if ($value) {
                    $result[] = sprintf('kDefinition_%s', $value);
                }
                break;
            case 'strokes':
                if (ctype_digit($value) && (int)$value > 0) {
                    $result[] = sprintf('"prop_kTotalStrokes_%s"', $value);
                }
                break;
            case 'archaic':
                if ($value === '1') {
                    $result[] = join(' ', array_map(function(string $sc) {
                        return sprintf('"sc_%s"', $sc);
                    }, $env['info']->script_age['archaic']));
                } elseif ($value === '0') {
                    $result[] = join(' ', array_map(function(string $sc) {
                        return sprintf('"sc_%s"', $sc);
                    }, $env['info']->script_age['recent']));
                }
                break;
            case 'confuse':
                if ($value === '1') {
                    $result[] = 'confusables_1';
                }
                break;
            case 'composed':
                if ($value >= 1) {
                    $result[] = 'prop_NFKD_QC_N';
                } elseif ($value === '0') {
                    $result[] = 'prop_NFKD_QC_Y';
                }
                break;
            case 'incomplete':
                if ($value === '1') {
                    $result[] = '-"prop_ccc_0"';
                } elseif ($value === '0') {
                    $result[] = 'prop_ccc_0';
                }
                break;
            case 'punctuation':
                if ($value === '1') {
                    $result[] = join(' ', array_map(function($value) {
                        return sprintf('"prop_gc_%s"', $value);
                    }, ['Pc', 'Pd', 'Ps', 'Pe', 'Pi', 'Pf', 'Po']));
                } elseif ($value === '0') {
                    $result[] = join(' ', array_map(function($value) {
                        return sprintf('-"prop_gc_%s"', $value);
                    }, ['Pc', 'Pd', 'Ps', 'Pe', 'Pi', 'Pf', 'Po']));
                }
                break;
            case 'symbol':
                if ($value === 's') {
                    $result[] = 'prop_gc_Sm prop_gc_Sc prop_gc_Sk prop_gc_So';
                } elseif ($value === 'c') {
                    $result[] = 'prop_gc_Cc prop_gc_Cf prop_gc_Cs prop_gc_Co prop_gc_Cn';
                } elseif ($value === 't') {
                    $result[] = '-"prop_gc_Sm" -"prop_gc_Sc" -"prop_gc_Sk" -"prop_gc_So" -"prop_gc_Cc" -"prop_gc_Cf" -"prop_gc_Cs" -"prop_gc_Co" -"prop_gc_Cn"';
                }
                break;
            case 'number':
                if ($value === '1') {
                    $result[] = join(' ', array_map(function($value) {
                        return sprintf('"prop_gc_%s"', $value);
                    }, ['Nd', 'Nl', 'No']));
                } elseif ($value === '0') {
                    $result[] = join(' ', array_map(function($value) {
                        return sprintf('-"prop_gc_%s"', $value);
                    }, ['Nd', 'Nl', 'No']));
                }
                break;
            case 'case':
                if ($value === 'l') {
                    $result[] = 'prop_gc_Ll';
                } elseif ($value === 'u') {
                    $result[] = 'prop_gc_Lu';
                } elseif ($value === 't') {
                    $result[] = 'prop_gc_Lt';
                } elseif ($value === 'y') {
                    $result[] = 'prop_gc_Ll prop_gc_Lu prop_gc_Lt';
                } elseif ($value === 'n') {
                    $result[] = '-"prop_gc_Ll" -"prop_gc_Lu" -"prop_gc_Lt"';
                }
                break;
            case 'region':
                if (array_key_exists($value, $env['info']->region_to_block)) {
                    $result[] = join(' ', array_map(function(string $value) {
                        return sprintf('blk_%s', $value);
                    }, $env['info']->region_to_block[$value]));
                }
                break;
        }
        return $result;
    }

}
