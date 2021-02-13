<?php

namespace Codepoints\Router;

use Codepoints\Unicode\Range;
use Codepoints\View;


/**
 * handle pagination
 */
class Pagination {

    private int $page = 1;

    private int $pagesize = 0x100;

    private string $urlTemplate;

    private Range $range;

    public function __construct(Range $range, int $current_page) {
        $this->range = $range;
        $this->page = $current_page;
        $urlTemplate = filter_input(INPUT_SERVER, 'REQUEST_URI');
        if (preg_match('/[&?]page=[0-9]+(&|$)/', $urlTemplate)) {
            $urlTemplate = preg_replace('/([&?]page=)[0-9]+(&|$)/', '\\1%s\\2',
                str_replace('%', '%%', $urlTemplate));
        } else {
            $urlTemplate .= (strpos('?', $urlTemplate) === false? '?' : '&') .
                'page=%s';
        }
        $this->urlTemplate = $urlTemplate;
    }

    public function slice() {
        return $this->range->slice(($this->page - 1) * $this->pagesize,
                           $this->pagesize);
    }

    public function getNumberOfPages() {
        return intval(ceil($this->range->count(true) / $this->pagesize));
    }

    public function __toString() {
        $pages = $this->getNumberOfPages();
        if ($pages > 1) {
            $page = $this->page;
            $pages_shown = 5;
            $url = $this->urlTemplate;
            return (new View('partials/pagination'))(
                compact('page', 'pages', 'pages_shown', 'url'));
        }
        return '';
    }

}
