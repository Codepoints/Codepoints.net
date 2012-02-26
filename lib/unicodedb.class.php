<?php


/**
 * Handle access to the Unicode database
 */
class UnicodeDB {

    private $db;
    private $properties;
    private $propvals = array();
    private $codepoints = array();
    private $blocks;

    /**
     * Construct with PDO object of database
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Get all possible Unicode properties and their name
     */
    public function getProperties() {
        if ($this->properties === NULL) {
            $query = $this->db->prepare("
                SELECT abbr, name
                  FROM propval
                 WHERE prop = 'prop'");
            $query->execute();
            $p = array();
            while ($r = $query->fetch(PDO::FETCH_ASSOC)) {
                $p[$r['abbr']] = $r['name'];
            }
            $this->properties = $p;
            $query->closeCursor();
        }
        return $this->properties;
    }

    /**
     * Get a codepoint identified by its integer ID
     *
     * @param int $cp the codepoint to get
     * @return array the properties of the codepoint
     */
    public function getCP($cp) {
        if (! array_key_exists($cp, $this->codepoints)) {
            if (count($this->getCPs($cp, $cp)) === 0) {
                return NULL;
            }
        }
        return $this->codepoints[$cp];
    }

    /**
     * get the next and previous sibling
     */
    public function getSiblings($cp) {
        $query = $this->db->prepare('SELECT
            (SELECT cp FROM data
                WHERE cp < :cp
                ORDER BY cp DESC
                LIMIT 1) AS p,
            (SELECT cp FROM data
                WHERE cp > :cp
                ORDER BY cp ASC
                LIMIT 1) AS n');
        $query->execute(array(':cp' => $cp));
        $r = $query->fetch(PDO::FETCH_NUM);
        $query->closeCursor();
        return array_map(array($this, 'hex'), $r);
    }

    /**
     * Get codepoint IDs for a certain block
     *
     * @param string $name the name of the block
     * @return array a range of integers
     */
    public function getBlock($name) {
        $query = $this->db->prepare("
            SELECT first, last FROM blocks
             WHERE replace(replace(lower(name), '_', ''), ' ', '') = :name
               AND `type` = 'block'
             LIMIT 1");
        $query->execute(array(':name' => str_replace(array(' ', '_'), '', strtolower($name))));
        $r = $query->fetch(PDO::FETCH_ASSOC);
        if ($r !== False) {
            $cps = $this->getCPs($r['first'], $r['last'], true);
            $r = array();
            foreach ($cps as $cp) {
                $r[(int)$cp['cp']] = $cp['na'];
            }
        }
        $query->closeCursor();
        return $r? $r : NULL;
    }

    /**
     * Get properties of a certain plane
     *
     * @param string $name the name of the plane
     * @return array result
     */
    public function getPlane($name) {
        $query = $this->db->prepare("
            SELECT name, first, last FROM blocks
             WHERE replace(replace(lower(name), '_', ''), ' ', '') = :name
               AND `type` = 'plane'
             LIMIT 1");
        $query->execute(array(':name' => str_replace(array(' ', '_'), '', strtolower($name))));
        $r = $query->fetch(PDO::FETCH_ASSOC);
        $query->closeCursor();
        return $r? $r : NULL;
    }

    /**
     * Get planes of a certain plane
     *
     * @param string $name the name of the plane
     * @return array result
     */
    public function getPlanes() {
        $query = $this->db->prepare("SELECT name, first, last FROM blocks
             WHERE `type` = 'plane'");
        $query->execute();
        $r = $query->fetchAll(PDO::FETCH_ASSOC);
        $query->closeCursor();
        return $r? $r : NULL;
    }

    /**
     * get all codepoints, that meet a certain condition
     */
    public function getWhere($fields) {
    }

    /**
     * Get all Unicode blocks
     *
     * @return array an array with all blocks
     */
    public function getBlocks() {
        if ($this->blocks === NULL) {
            $query = $this->db->prepare("
                SELECT *
                  FROM blocks
                 WHERE `type` = 'block'");
            $query->execute();
            $this->blocks = array();
            while ($r = $query->fetch(PDO::FETCH_ASSOC)) {
                unset($r['type']);
                $r['first'] = (int)$r['first'];
                $r['last'] = (int)$r['last'];
                $this->blocks[] = $r;
            }
            $query->closeCursor();
        }
        return $this->blocks;
    }

    /**
     * int-to-hex with formatting
     */
    protected function hex($int) {
        if ($int === NULL || $int === False) {
            return NULL;
        }
        return sprintf("%04X", $int);
    }

    /**
     * fetch multiple codepoints plus their properties
     */
    public function getCPs($first, $last, $simple=false) {
        if (! $simple && array_key_exists($first, $this->codepoints) &&
            array_key_exists($last, $this->codepoints)) {
                $provResult = array_slice($this->codepoints,
                                          $first, $last - $first);
            if (count($provResult) === $last - $first + 1) {
                return $provResult;
            }
        }
        $fields = $simple? 'cp,na' : '*';
        $query = $this->db->prepare('SELECT '.$fields.' FROM data
                                     WHERE cp BETWEEN :first AND :last');
        $query->execute(array(':first' => $first, ':last' => $last));
        $codepoints = $query->fetchAll(PDO::FETCH_ASSOC);
        $query->closeCursor();

        if ($simple) {
            return $codepoints;
        }

        $query = $this->db->prepare('SELECT name, `type`, first, last
                                FROM blocks
                                WHERE first <= :first AND last >= :last');
        $query->execute(array(':first' => $first, ':last' => $last));
        $contained_in = $query->fetchAll(PDO::FETCH_ASSOC);
        $query->closeCursor();

        $query = $this->db->prepare('SELECT cp, name, `type` FROM alias
                                     WHERE cp BETWEEN :first AND  :last');
        $query->execute(array(':first' => $first, ':last' => $last));
        $alias = $query->fetchAll(PDO::FETCH_ASSOC);
        $query->closeCursor();

        $r = array();
        for ($i = 0; $i < count($codepoints); $i += 1) {
            $rx = $codepoints[$i];
            $rx['xcp'] = $this->hex($rx['cp']);
            $cp = (int)$rx['cp'];
            $rx['contained_in'] = array();
            for ($j = 0; $j < count($contained_in); $j++) {
                $t = $contained_in[$j];
                if ($t['first'] <= $cp && $cp <= $t['last']) {
                    $rx['contained_in'][] = $t;
                }
            }
            $rx['alias'] = array();
            for ($j = 0; $j < count($alias); $j++) {
                if ($cp === (int)$alias[$j]['cp']) {
                    unset($alias[$j]['cp']);
                    $rx['alias'][] = $alias[$j];
                    unset($alias[$j]);
                    break;
                }
            }
            $r[$cp] = $rx;
        }
        $this->codepoints += $r;
        return $r;
    }

}


//__END__
