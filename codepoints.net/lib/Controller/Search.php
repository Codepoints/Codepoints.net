<?php

namespace Codepoints\Controller;

use \Analog\Analog;
use Codepoints\Database;
use Codepoints\Controller;
use Codepoints\Unicode\Block;
use Codepoints\Unicode\Codepoint;
use Codepoints\Unicode\SearchResult;
use Codepoints\Router\Pagination;
use Codepoints\Router\Redirect;
use Codepoints\Search\Engine;


class Search extends Controller {

    private int $query_count = 0;

    private Array $query = [];

    /**
     * @param string $match
     */
    public function __invoke($match, Array $env) : string {

        $query = filter_input(INPUT_SERVER, 'QUERY_STRING');
        list($search_result, $pagination) = $this->getSearchResult($query, $env);
        if (is_string($search_result)) {
            throw new Redirect(sprintf('/U+%s', dechex(mb_ord($search_result))));
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
     * @return Array{0: string|SearchResult|null, 1: ?Pagination}
     */
    public function getSearchResult(string $query, Array $env) : Array {
        $search_result = null;
        $pagination = null;

        if ($query) {
            $engine = new Engine($env);
            $search_result = $engine->search($query);
            if ($search_result instanceof SearchResult) {
                $this->query = $engine->getQuery();
                $pagination = new Pagination($search_result, get_page());
            }
        }
        return [$search_result, $pagination];
    }

}
