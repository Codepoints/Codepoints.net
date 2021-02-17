<?php

namespace Codepoints\Unicode;

use \Analog\Analog;
use \Codepoints\Database;
use \Codepoints\Unicode\Codepoint;


/**
 * a range of contiguous Unicode code points
 *
 * The range can contain unassigned code points.
 */
class Range implements \Iterator {

    /**
     * the database from which CP info is fetched
     */
    protected Database $db;

    /**
     * a string representation
     */
    protected string $name;

    /**
     * the limits of the range and current pointer
     */
    protected int $first;
    protected int $last;
    private int $current;

    /**
     * the number of read code points in this range
     */
    private ?int $count = null;

    /**
     * a cache of code point data already fetched from the DB
     */
    private Array $cp_cache = [];

    /**
     * construct a new Unicode range
     *
     * The set may be an empty range and can be filled later.
     */
    public function __construct(Array $data, Database $db) {
        $this->db = $db;
        $this->first = $data['first'];
        $this->last = $data['last'];
        $this->current = $data['first'];
        $this->name = sprintf('U+%04X..U+%04X', $data['first'], $data['last']);
    }

    /**
     * get the range's official name
     */
    public function __toString() {
        return $this->name;
    }

    /**
     * access info about this block
     */
    public function __get(string $name) /*: mixed*/ {
        switch ($name) {
        case 'name':
            return $this->name;
        case 'first':
            return $this->first;
        case 'last':
            return $this->last;
        }
    }

    /**
     * return the number of code points in this range
     *
     * This method will call the DB and look, how many real code points are
     * there. If you want the size of the whole range, use
     *
     *     $range->last - $range->first + 1
     */
    public function count() : int {
        if (is_null($this->count)) {
            $data = $this->db->getOne('SELECT COUNT(*) c
                FROM codepoints
                WHERE cp >= ? AND cp <= ?', $this->first, $this->last);
            $this->count = 0;
            if ($data) {
                $this->count = $data['c'];
            }
        }
        return $this->count;
    }

    public function rewind() : void {
        $this->current = $this->first;
    }

    /**
     * return a Codepoint object from the current position of the internal
     * array
     *
     * Returns null, if there is no such code point. This should not happen
     * due to the prior call to _prepare(), though.
     *
     * For performance reasons we fetch a set of up to 256 code points around
     * this code point, too.
     */
    public function current() : ?Codepoint {
        $codepoint = $this->current;
        if (array_key_exists($codepoint, $this->cp_cache)) {
            $data = $this->cp_cache[$codepoint];
        } else {
            $first = $this->first;
            $last = $this->last;
            if ($last - $first > 0x100) {
                /* take a 256 chars window containing our code point. The
                 * window is fixed to probable block boundaries. E.g., for
                 * U+1A2B we fetch U+1A00..U+1AFF. If this range has less
                 * than 256 characters, the window will be shorter, too. */
                $first = max($first, $codepoint - ($codepoint % 0x100));
                $last = min($last, $codepoint - ($codepoint % 0x100) + 0xFF);
            }

            $items = $this->db->getAll('SELECT cp, name, gc
                FROM codepoints
                WHERE cp >= ? AND cp <= ?', $first, $last);
            if (! $items) {
                /* something went wrong. We return early. */
                return null;
            }

            /* set all cp candidates to null, so that we have cache hits for
             * non-codepoints, too. */
            $data = null;
            for ($i = $first; $i <= $last; $i++) {
                $this->cp_cache[$i] = null;
            }

            foreach ($items as $item) {
                $this->cp_cache[$item['cp']] = $item;
                if ($item['cp'] === $codepoint) {
                    $data = $item;
                }
            }
        }
        return $data? new Codepoint($data, $this->db) : null;
    }

    public function key() : int {
        return $this->current;
    }

    public function next() : void {
        $this->current += 1;
    }

    public function valid() : bool {
        return $this->current >= $this->first && $this->current <= $this->last;
    }

    /**
     * return a sub-range
     */
    public function slice(int $offset, ?int $length = null) : self {
        $new_first = $this->first + $offset;
        if ($new_first > $this->last) {
            Analog::warning(sprintf(
                'slice beyond boundaries in Range: U+%04X + %d',
                $this->first, $offset));
            /* return a range, that is guaranteed to contain no valid code
             * point */
            return new self(['first' => 0x110000, 'last' => 0x110000], $this->db);
        }
        $new_last = $this->last;
        if ($length && $new_first + $length - 1 <= $this->last) {
            $new_last = $new_first + $length - 1;
        }
        return new self(['first' => $new_first, 'last' => $new_last], $this->db);
    }

}
