<?php

namespace Codepoints\Unicode;

use \Analog\Analog;
use \Codepoints\Database;
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
        $this->last = $data['last'];
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
        return $data? new Codepoint($data, $this->db) : null;
    }

    /**
     * disable slicing
     *
     * We implement the correct pagination window in the controller.
     */
    public function slice(int $offset, ?int $length = null) : self {
        return $this;
    }

}
