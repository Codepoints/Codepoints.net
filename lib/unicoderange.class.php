<?php


class UnicodeRange implements Iterator {

    protected $set = array();
    protected $names;

    public function __construct(Array $set=array()) {
        $this->set = array_unique($set);
    }

    /**
     * get the current set of codepoints
     */
    public function get() {
        reset($this->set);
        return $this->set;
    }

    public function getBoundaries() {
        return array(
            $this->set[0],
            end($this->set)
        );
    }

    public function rewind() {
        reset($this->set);
    }

    public function current() {
        return current($this->set);
    }

    public function key() {
        return key($this->set);
    }

    public function next() {
        return next($this->set);
    }

    public function valid() {
        $state = each($this->set);
        if ($state !== False) {
            prev($this->set);
        }
        return $state;
    }

    /**
     * add a codepoint to the set
     */
    public function add($cp) {
        if (! in_array($cp, $this->set)) {
            $this->set[] = $cp;
            $this->names = NULL;
        }
        return $this;
    }

    /**
     * add several codepoints to the set
     */
    public function addSet(Array $set) {
        $this->set = array_unique(array_merge($this->set, $set));
        $this->names = NULL;
        return $this;
    }

    /**
     * add another range to the set
     */
    public function addRange(UnicodeRange $range) {
        $set = $range->get();
        $this->set = array_unique(array_merge($this->set, $set));
        $this->names = NULL;
        return $this;
    }

    /**
     * get the names of all characters in the set
     */
    public function getSetNames() {
        if ($this->names === NULL) {
            $query = $this->db->prepare("
            SELECT cp, na, na1 FROM data
            WHERE cp >= :first
            AND cp <= :last
            ");
            $query->execute(array(':first' => $this->set[0],
                                  ':last' => end($this->set)));
            $r = $query->fetchAll(PDO::FETCH_ASSOC);
            $query->closeCursor();
            if ($r === False) {
                $this->names = array();
            } else {
                $x = array();
                foreach ($r as $cp) {
                    if (in_array($cp['cp'], $this->set)) {
                        $x[intval($cp['cp'])] = $cp['na']? $cp['na'] : $cp['na1'].'*';
                    }
                }
                $this->names = $x;
            }
        }
        return $this->names;
    }

    /**
     * parse a string of form U+A..U+B,U+C in a UnicodeRange
     */
    public static function parse($str) {
        $set = array();
        $junks = preg_split('/\s*(?:,\s*)+/', trim($str));
        foreach ($junks as $j) {
            $ranges = preg_split('/\s*(?:-|\.\.|:)\s*/', $j);
            switch (count($ranges)) {
                case 0:
                    break;
                case 1:
                    $tmp = self::parse_cp($ranges[0]);
                    if (is_int($tmp)) {
                        $set[] = $tmp;
                    }
                    break;
                case 2:
                    $low = self::parse_cp($ranges[0]);
                    $high = self::parse_cp($ranges[1]);
                    if (is_int($low) && is_int($high)) {
                        $set = array_merge($set, range(min($low, $high),
                                                       max($high, $low)));
                    }
                    break;
                default:
                    $max = -1;
                    $min = 0x110000;
                    foreach ($ranges as $r) {
                        $tmp = self::parse_cp($r);
                        if (is_int($tmp) && $tmp > $max) {
                            $max = $tmp;
                        }
                        if (is_int($tmp) && $tmp < $min) {
                            $min = $tmp;
                        }
                    }
                    if ($min < 0x110000 && $max > -1) {
                        $set = array_merge($set, range(min($min, $max),
                                                       max($max, $min)));
                    }
            }
        }
        return new self($set);
    }

    /**
     * return the codepoint for a single representation
     */
    public static function parse_cp($str) {
        preg_match('/^(?:U[\+-]|\\\\U|0x|U)?([0-9a-f]+)$/i', $str, $matches);
        if (count($matches) === 2) {
            return intval($matches[1], 16);
        }
        return NULL;
    }

}


//__END__
