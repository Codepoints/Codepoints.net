<?php


/**
 * the result of a Unicode codepoint search
 *
 * We extend UnicodeRange, because a search result is basically nothing
 * else than a set of codepoints. We adapt fetchNames() heavily for this.
 */
class SearchResult extends UnicodeRange {

    protected $query = array();

    public $pageLength = 128;

    public $page = 0;

    protected $count = 0;

    protected $needConfusables = false;

    /**
     * add a query, connect it to previous via $connector
     *
     * Note, that 'AND' as connector is usually not what you want, since
     * all searches act solely on the "terms" column of search_index, thus
     * making "AND" searches empty sets.
     */
    public function addQuery($field, $value, $op='=', $connector='OR') {
        if ($field === 'confusables') {
            $this->needConfusables = true;
        }
        $this->query[] = array($field, $op, $value, $connector);
    }

    /**
     * get the query list
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * do the search
     */
    protected function fetchNames($set=null) {
        $query = $this->query;
        if ($set !== null) {
            $this->sanitizeSet($set);
            $this->_set = $set;
        }

        list($search, $params) = $this->_getQuerySQL();
        $select = 'SELECT %s FROM search_index WHERE %s %s';

        if (count($this->query) === 0) {
            $stm = $this->db->prepare(sprintf($select, 'distinct cp', $search,
                'ORDER BY cp ASC LIMIT '.
                ($this->page * $this->pageLength).','.$this->pageLength));
        } else {
            $stm = $this->db->prepare(sprintf($select, 'cp', $search,
                'GROUP BY cp ORDER BY SUM(weight) DESC, cp ASC LIMIT '.
                ($this->page * $this->pageLength).','.$this->pageLength));
        }
        $stm->execute($params);
        $r = $stm->fetchAll(PDO::FETCH_ASSOC);
        $names = array();
        $stm->closeCursor();
        if ($r !== false) {
            $cps_ordered = array_map(function($item) {
                return $item['cp'];
            }, $r);
            $stm = $this->db->prepare(sprintf('
                SELECT cp, na, na1, image
                  FROM codepoints
             LEFT JOIN codepoint_image USING ( cp )
                 WHERE cp IN ( %s )', join(',', $cps_ordered)));
            $stm->execute();
            $r2 = $stm->fetchAll(PDO::FETCH_ASSOC);
            foreach ($cps_ordered as $n) {
                foreach ($r2 as $cp) {
                    if ($n === $cp['cp']) {
                        if (! $cp['image']) {
                            $cp['image'] = '';
                        }
                        $names[intval($cp['cp'])] = Codepoint::getCP(
                            intval($cp['cp']),
                            $this->db,
                            array(
                                'name' => $cp['na']?
                                    $cp['na'] :
                                    ($cp['na1']?
                                        $cp['na1'].'*' : '<control>'),
                                'image' => 'data:image/png;base64,' .
                                           $cp['image'],
                            )
                        );
                        break;
                    }
                }
            }
        }
        $this->set = $names;

        // we need to get the count separately, because the above
        // query is LIMITed
        $c = count($this->set);
        if ($this->page > 1 || $c === $this->pageLength) {
            $stm = $this->db->prepare(sprintf('
                SELECT COUNT(DISTINCT cp) AS c
                    FROM search_index
                    WHERE %s', $search));
            $stm->execute($params);
            $r = $stm->fetch(PDO::FETCH_ASSOC);
            $stm->closeCursor();
            $c = (int)$r['c'];
        }
        $this->count = $c;

        return $this->set;
    }

    /**
     * get the result count
     */
    public function getCount() {
        $this->_prepare();
        return $this->count;
    }

    /**
     * generate the SQL needed for the query
     */
    protected function _getQuerySQL() {
        $params = array();
        $search = '';
        if (count($this->query) === 0) {
            /* if there are no queries defined but a set to look into, use this.
             * This is basically to ensure compatibility with UnicodeRange. */
            $search = "cp IN (" . join(',', $this->_set) . ")";
        } else {
            foreach ($this->query as $i => $q) {
                if ($search !== '') {
                    /* join the query with the last one using the $q3 parameter
                     * (usually "AND" or "OR"). */
                    $search .= " ${q[3]} ";
                }
                if (is_array($q[2])) {
                    /* the query value is an array: we construct an "IN ()" SQL
                     * statement. */
                    $x = $q[0];
                    if ($x === 'block') { $x = 'blk'; }
                    $tmp = array_map(function ($s) use ($x) {
                        if ($x !== 'cp') {
                            $s = "$x:$s";
                        }
                        return $this->db->quote($s);
                    }, $q[2]);
                    if ($q[1] === '=') {
                        $q[1] = 'IN';
                    } elseif ($q[1] === '!=') {
                        $q[1] = 'NOT IN';
                    }
                    if ($q[0] === 'cp') {
                        $search .= " cp ${q[1]} ( " . join(',', $tmp) . " )";
                    } else {
                        $search .= " term ${q[1]} ( " . join(',', $tmp) . " )";
                    }
                } elseif ($q[0] === 'term') {
                    $search .= " term ${q[1]} :q$i ";
                    $params[':q'.$i] = "${q[2]}";
                } elseif ($q[0] === 'na' || $q[0] === 'na1') {
                    /* match names loosely, especially to make the search
                     * case-insensiitve */
                    $search .= " term LIKE :q$i ";
                    $params[':q'.$i] = "${q[2]}";
                } elseif ($q[0] === 'cp' || $q[0] === 'int') {
                    /* handle "cp" specially and search "cp" column directly */
                    $search .= " cp = :q$i ";
                    $params[':q'.$i] = $q[2];
                } elseif ($q[0] === 'block' || $q[0] === 'blk') {
                    $search .= " term = :q$i ";
                    $params[':q'.$i] = 'blk:'.$q[2];
                } else {
                    /* the default is to query the column $q0 with the
                     * comparison $q1 for a value $q2 */
                    $search .= " term ${q[1]} :q$i ";
                    $params[':q'.$i] = $q[0].':'.$q[2];
                }
            }
        }
        return array($search, $params);
    }

}


//__END__
