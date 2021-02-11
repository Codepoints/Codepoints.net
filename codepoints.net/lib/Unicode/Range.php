<?php

namespace Codepoints\Unicode;

use \Codepoints\Database;
use \Codepoints\Unicode\Codepoint;


/**
 * an arbitrary range of Unicode characters
 *
 * The range may have gaps.
 */
class Range implements \Iterator {

    /**
     * the database from which CP info is fetched
     */
    protected Database $db;

    /**
     * the set of codepoint instances
     */
    private ?Array $set;

    /**
     * the provisional set of codepoints
     */
    private Array $_set = [];

    /**
     * construct a new Unicode range
     *
     * The set may be an empty range and can be filled later.
     */
    public function __construct(Array $set, Database $db) {
        $this->db = $db;
        $set = array_unique($set);
        $this->_set = $set; # cache set for later lazy loading
    }

    /**
     * prepare the set by fetching codepoints
     */
    private function _prepare() : void {
        if ($this->set === null) {
            $this->set = $this->fetchNames($this->_set);
        }
    }

    /**
     * get the current set of codepoints
     */
    public function get() : Array {
        $this->_prepare();
        reset($this->set);
        return $this->set;
    }

    public function rewind() : void {
        $this->_prepare();
        reset($this->set);
    }

    public function current() {
        $this->_prepare();
        return current($this->set);
    }

    public function key() {
        $this->_prepare();
        return key($this->set);
    }

    public function next() : void {
        $this->_prepare();
        next($this->set);
    }

    public function valid() : bool {
        $this->_prepare();
        return key($this->set) !== null;
    }

    /**
     * get the IDs of the first and last valid codepoints
     * in the set
     */
    public function getBoundaries() : ?Array {
        $this->_prepare();
        $indices = array_keys($this->set);
        if (! count($indices)) {
            return null;
        }
        return [$indices[0], end($indices)];
    }

    /**
     * get the first codepoint ID from the set
     */
    public function getFirst() : ?int {
        $this->_prepare();
        $r = array_keys($this->set);
        if (! count($r)) {
            return null;
        }
        return $r[0];
    }

    /**
     * get the last codepoint ID from the set
     */
    public function getLast() : ?int {
        $this->_prepare();
        $r = array_keys($this->set);
        return end($r);
    }

    /**
     * add a single codepoint to the set
     */
    public function add(int $cp) : self {
        $this->_prepare();
        if (! array_key_exists($cp, $this->set)) {
            $this->set += $this->fetchNames([$cp]);
        }
        return $this;
    }

    /**
     * add several codepoints to the set
     */
    public function addSet(Array $set) : self {
        $this->_prepare();
        $this->set += $this->fetchNames(array_diff(array_unique($set), array_keys($this->set)));
        return $this;
    }

    /**
     * add another range to the set
     */
    public function addRange(Range $range) : self {
        $this->_prepare();
        $set = $range->get();
        $this->set = array_unique(array_merge($this->set, $set));
        return $this;
    }

    /**
     * get the names of all characters in the set
     */
    protected function fetchNames(Array $set) : Array {
        $names = [];
        if (count($set) > 0) {
            $query = $this->db->prepare("
                SELECT cp, name
                FROM codepoints
                WHERE cp IN (" . join(',', $set) . ")");
            $query->execute();
            $data = $query->fetchAll(\PDO::FETCH_ASSOC);
            $query->closeCursor();
            if ($data) {
                foreach ($data as $cp) {
                    $names[$cp['cp']] = Codepoint::getCached(
                        $cp, $this->db);
                }
            }
        }
        return $names;
    }

}
