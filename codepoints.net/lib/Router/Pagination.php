<?php

namespace Codepoints\Router;

use Codepoints\Unicode\Range;
use Codepoints\View;


/**
 * handle pagination
 *
 * @property int $page
 * @psalm-seal-properties
 */
class Pagination {

    private int $page = 1;

    public const PAGE_SIZE = 0x100;

    private string $urlTemplate;

    private Range $range;

    public function __construct(Range $range, int $current_page) {
        $this->range = $range;
        $this->page = $current_page;
        $urlTemplate = str_replace('%', '%%', (string)filter_input(INPUT_SERVER, 'REQUEST_URI'));
        if (preg_match('/[&?]page=[0-9]+(&|$)/', $urlTemplate)) {
            $urlTemplate = preg_replace('/([&?]page=)[0-9]+(&|$)/', '\\1%s\\2', $urlTemplate);
        } else {
            $urlTemplate .= (strpos($urlTemplate, '?') === false? '?' : '&') .
                'page=%s';
        }
        $this->urlTemplate = $urlTemplate;
    }

    public function slice() : Range {
        return $this->range->slice(($this->page - 1) * self::PAGE_SIZE,
                           self::PAGE_SIZE);
    }

    public function getNumberOfPages() : int {
        return intval(ceil(($this->range->last - $this->range->first + 1) / self::PAGE_SIZE));
    }

    /**
     * @return ?int
     */
    public function __get(string $name) {
        switch ($name) {
        case 'page':
            return $this->page;
        }
        return null;
    }

    public function __toString() : string {
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
