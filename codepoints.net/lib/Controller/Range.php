<?php

namespace Codepoints\Controller;

use Codepoints\Controller;
use Codepoints\Router\NotFoundException;
use Codepoints\Router\Pagination;
use Codepoints\Unicode\Codepoint;
use Codepoints\Unicode\Range as UnicodeRange;
use Codepoints\Unicode\SearchResult;
use Codepoints\View;


class Range extends Controller {

    /**
     * @param Array $match
     */
    public function __invoke($match, Array $env) : string {
        if (substr_count($match[0], ',') > 128) {
            throw new NotFoundException('only up to 128 individual code points allowed');
        }
        $page = get_page();
        $set = parse_range($match[0], $env['db']);
        $count = 0;
        /* @var Array<Array{cp: int, name: string, gc: string}> */
        $items = [];
        /* we need to get the right pagination window out of a list of single
         * code points and ranges. We do that by counting and putting code
         * points in a list $items, when the count is in the range of our
         * current pagination window.
         */
        foreach ($set as $item) {
            if ($item instanceof Codepoint) {
                $add = 1;
            } elseif ($item instanceof UnicodeRange) {
                /* this calls the DB! */
                $add = $item->count();
            } else {
                /* possibly null: do nothing */
                continue;
            }
            if ($count + $add >= ($page - 1) * Pagination::PAGE_SIZE &&
                $count < $page * Pagination::PAGE_SIZE) {
                /* we need to add more code points */
                if ($item instanceof Codepoint) {
                    /* we know, that $add === 1, so we cannot overfill the
                     * $items list with just a single code point */
                    $items[] = ['cp' => $item->id, 'name' => $item->name, 'gc' => $item->gc];
                } else {
                    $tmp_count = $count;
                    foreach ($item as $codepoint) {
                        if (! $codepoint) {
                            continue;
                        }
                        $tmp_count += 1;
                        if ($tmp_count < ($page - 1) * Pagination::PAGE_SIZE + 1) {
                            /* skip until we reach our destination window */
                            continue;
                        }
                        $items[] = ['cp' => $codepoint->id, 'name' => $codepoint->name, 'gc' => $codepoint->gc];
                        if (count($items) >= Pagination::PAGE_SIZE) {
                            break;
                        }
                    }
                }
            }
            $count += $add;
        }
        $range = new SearchResult([
            'count' => $count,
            'items' => $items,
        ], $env['db']);

        $all_block_names = [];
        $data = $env['db']->getAll('
        SELECT name FROM blocks
        ORDER BY first ASC');
        foreach ((array)$data as $item) {
            $all_block_names[$item['name']] = $item['name'];
        }

        $this->context += [
            'title' => $match[0],
            'page_description' => '',
            'search_result' => $range,
            'alt_result' => [],
            'pagination' => new Pagination($range, $page),
            'all_block_names' => $all_block_names,
            'q' => $match[0],
            'query' => ['q' => [$match[0]]],
            'is_range' => true,
        ];
        return (new View('search'))($this->context + [
            'match' => $match,
        ], $env);
    }

}
