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
        $items = [];
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
                    $items[] = $item;
                } else {
                    foreach ($item as $codepoint) {
                        $items[] = $codepoint;
                        if (count($items) >= Pagination::PAGE_SIZE) {
                            break;
                        }
                    }
                }
            }
            $count += $add;
        }
        $range = new SearchResult([
            'last' => min($count, Pagination::PAGE_SIZE) - 1,
            'count' => $count,
            'items' => $items,
        ], $env['db']);

        $this->context += [
            'title' => $match[0],
            'page_description' => '',
            'search_result' => $range,
            'pagination' => new Pagination($range, $page),
            'q' => $match[0],
        ];
        return (new View('search'))($this->context + [
            'match' => $match,
        ], $env);
    }

}
