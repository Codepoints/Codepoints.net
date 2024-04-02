<?php

namespace Codepoints\Unicode;

use \Analog\Analog;
use \Codepoints\Database;
use \Codepoints\Router\Pagination;
use \Codepoints\Unicode\Codepoint;
use \Codepoints\Unicode\Range;


/**
 * a list of code points from a search query
 */
class SearchResult extends Range {

    /**
     * construct a new search result
     */
    public function __construct(Array $data, Database $db) {
        $this->db = $db;
        $this->name = array_get($data, 'name', __('Search'));
        $this->first = 0;
        $this->current = 0;
        $this->last = $data['count'] - 1;
        $this->count = $data['count'];
        $this->cp_cache = $data['items'];
    }

    /**
     * return a Codepoint object from the current position of the internal
     * array
     */
    public function current() : ?Codepoint {
        $codepoint = $this->current;
        $data = null;
        if (array_key_exists($codepoint, $this->cp_cache)) {
            $data = $this->cp_cache[$codepoint];
        }
        /** @psalm-suppress RiskyTruthyFalsyComparison */
        return $data? Codepoint::getCached($data, $this->db) : null;
    }

    /**
     * check with actual cache
     *
     * $this->last is unreliable, since we need to spoof it for pagination.
     * Check against the real cache for validity.
     *
     * @psalm-mutation-free
     */
    public function valid() : bool {
        return array_key_exists($this->current, $this->cp_cache);
    }

    /**
     * fake slicing
     *
     * We implement the correct pagination window in the controller. But to
     * keep the pagination class happy, we need to play with the "last"
     * parameter.
     */
    public function slice(int $offset, ?int $length = null) : self {
        /** @psalm-suppress RiskyTruthyFalsyComparison */
        return new self([
            'name' => $this->name,
            'count' => $length?: Pagination::PAGE_SIZE,
            'items' => $this->cp_cache,
        ], $this->db);
    }

}
