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
        $page = get_page();

        $query = filter_input(INPUT_SERVER, 'QUERY_STRING');

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
            'q' => '',
            'wizard' => true,
        ];
        return (new View('search'))($this->context + [
            'match' => $match,
        ], $env);
    }

    /**
     * overwrite the query parser to map the wizard query to search query
     */
    protected function getTransformedQuery(string $key, string $value, Array $env) : Array {
        $result = [];
        switch ($key) {
            case 'def':
                if ($value) {
                    $result[] = ['AND', 'kDefinition', 'LIKE', "%$value%"];
                }
                break;
            case 'strokes':
                if (ctype_digit($value) && (int)$value > 0) {
                    $result[] = ['AND', 'kTotalStrokes', '=', $value];
                }
                break;
            case 'archaic':
                if ($value === '1') {
                    $result[] = ['AND', 'sc', '=', $env['info']->script_age['archaic']];
                } elseif ($value === '0') {
                    $result[] = ['AND', 'sc', '=', $env['info']->script_age['recent']];
                }
                break;
            case 'confuse':
                if ($value === '1') {
                    $result[] = ['AND', 'confusables', '>', 0];
                }
                break;
            case 'composed':
                if ($value >= 1) {
                    $result[] = ['AND', 'NFKD_QC', '=', 'No'];
                } elseif ($value === '0') {
                    $result[] = ['AND', 'NFKD_QC', '=', 'Yes'];
                }
                break;
            case 'incomplete':
                if ($value === '1') {
                    $result[] = ['AND', 'ccc', '!=', 0];
                } elseif ($value === '0') {
                    $result[] = ['AND', 'ccc', '=', 0];
                }
                break;
            case 'punctuation':
                if ($value === '1') {
                    $result[] = ['AND', 'gc', 'IN', ['Pc', 'Pd', 'Ps', 'Pe', 'Pi', 'Pf', 'Po']];
                } elseif ($value === '0') {
                    $result[] = ['AND', 'gc', 'NOT IN', ['Pc', 'Pd', 'Ps', 'Pe', 'Pi', 'Pf', 'Po']];
                }
                break;
            case 'symbol':
                if ($value === 's') {
                    $result[] = ['AND', 'gc', 'IN', ['Sm', 'Sc', 'Sk', 'So']];
                } elseif ($value === 'c') {
                    $result[] = ['AND', 'gc', 'IN', ['Cc', 'Cf', 'Cs', 'Co', 'Cn']];
                } elseif ($value === 't') {
                    $result[] = ['AND', 'gc', 'NOT IN', ['Sm', 'Sc', 'Sk', 'So', 'Cc', 'Cf', 'Cs', 'Co', 'Cn']];
                }
                break;
            case 'number':
                if ($value === '1') {
                    $result[] = ['AND', 'gc', 'IN', ['Nd', 'Nl', 'No']];
                } elseif ($value === '0') {
                    $result[] = ['AND', 'gc', 'NOT IN', ['Nd', 'Nl', 'No']];
                }
                break;
            case 'case':
                if ($value === 'l') {
                    $result[] = ['AND', 'gc', '=', 'Ll'];
                } elseif ($value === 'u') {
                    $result[] = ['AND', 'gc', '=', 'Lu'];
                } elseif ($value === 't') {
                    $result[] = ['AND', 'gc', '=', 'Lt'];
                } elseif ($value === 'y') {
                    $result[] = ['AND', 'gc', 'IN', ['Lu', 'Ll', 'Lt']];
                } elseif ($value === 'n') {
                    $result[] = ['AND', 'gc', 'NOT IN', ['Lu', 'Ll', 'Lt']];
                }
                break;
            case 'region':
                if (array_key_exists($value, $env['info']->region_to_block)) {
                    $result[] = ['AND', 'blk', 'IN', $env['info']->region_to_block[$value]];
                }
                break;
        }
        return $result;
    }

}
