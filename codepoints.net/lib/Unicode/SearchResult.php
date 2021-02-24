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

    private int $currentPage;

    private \PDOStatement $query;

    private Array $params;

    /**
     * construct a new search result
     */
    public function __construct(Array $data, Database $db) {
        $this->db = $db;
        $this->first = 0;
        $this->current = 0;
        $this->last = 0;
        $this->count = 0;
        $this->currentPage = $data['page'];
        $data['count_statement']->execute($data['params']);
        $counter = $data['count_statement']->fetch(\PDO::FETCH_ASSOC);
        if ($counter) {
            $this->last = min($counter['count'], 0x100) - 1;
            $this->count = $counter['count'];
        }
        $data['query_statement']->execute($data['params']);
        $fetcher = $data['query_statement']->fetchAll(\PDO::FETCH_ASSOC);
        if ($fetcher) {
            foreach ($fetcher as $i => $item) {
                $this->cp_cache[$i] = $item;
            }
        }
        $this->query = $data['query_statement'];
        $this->params = $data['params'];
        $this->name = __('Search');
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
