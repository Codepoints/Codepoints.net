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

    /**
     * add a query, connect it to previous via $connector
     */
    public function addQuery($field, $value, $op='=', $connector='AND') {
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
    protected function fetchNames($set=Null) {
        $query = $this->query;
        if ($set !== Null) {
            $this->sanitizeSet($set);
            $this->_set = $set;
        }

        list($search, $params) = $this->_getQuerySQL();
        $select = 'SELECT codepoints.cp cp, na, na1,
            (SELECT codepoint_image.image
               FROM codepoint_image
              WHERE codepoint_image.cp = codepoints.cp) image,
            (SELECT name FROM blocks
              WHERE first <= codepoints.cp AND last >= codepoints.cp
              LIMIT 1) block,
            (SELECT COUNT(*)
               FROM codepoint_confusables
              WHERE codepoint_confusables.cp = codepoints.cp
                 OR codepoint_confusables.other = codepoints.cp) confusables
        FROM codepoints
        LEFT JOIN codepoint_script USING ( cp )
        LEFT JOIN codepoint_alias USING ( cp )
        LEFT JOIN codepoint_abstract USING ( cp )
        WHERE ' . $search;

        $stm = $this->db->prepare($select.' LIMIT '.($this->page * $this->pageLength).','.$this->pageLength);
        $stm->execute($params);
        $r = $stm->fetchAll(PDO::FETCH_ASSOC);
        $names = array();
        $stm->closeCursor();
        if ($r !== False) {
            foreach ($r as $cp) {
                if (! $cp['image']) {
                    $cp['image'] = '';
                }
                $names[intval($cp['cp'])] = Codepoint::getCP(intval($cp['cp']),
                    $this->db, array('name' => $cp['na']? $cp['na'] : ($cp['na1']? $cp['na1'].'*' : '<control>'),
                    'image' => 'data:image/png;base64,' . $cp['image']));
            }
        }
        $this->set = $names;

        // we need to get the count separately, because the above
        // query is LIMITed
        $c = count($this->set);
        if ($this->page > 1 || $c === $this->pageLength) {
            $stm = $this->db->prepare('SELECT COUNT(*) AS c FROM ( '.$select.' )');
            $stm->execute($params);
            $r = $stm->fetch(PDO::FETCH_ASSOC);
            $stm->closeCursor();
            $c = $r['c'];
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
            $search = "codepoints.cp IN (" . join(',', $this->_set) . ")";
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
                    $tmp = array_map(array($this->db, 'quote'), $q[2]);
                    if ($q[1] === '=') {
                        $q[1] = 'IN';
                    } elseif ($q[1] === '!=') {
                        $q[1] = 'NOT IN';
                    }
                    if ($q[0] === 'cp') {
                        $search .= " codepoints.cp ${q[1]} ( " . join(',', $tmp) . " )";
                    } else {
                        $search .= " `${q[0]}` ${q[1]} ( " . join(',', $tmp) . " )";
                    }
                } elseif ($q[0] === 'na' || $q[0] === 'na1') {
                    /* match names loosely, especially to make the search case-insensiitve */
                    $search .= " `${q[0]}` LIKE :q$i ";
                    $params[':q'.$i] = "%${q[2]}%";
                } elseif ($q[0] === 'cp' || $q[0] === 'int') {
                    /* handle "cp" specially to fight "ambiguous column" SQLite errors */
                    $search .= " codepoints.cp = :q$i ";
                    $params[':q'.$i] = $q[2];
                } else {
                    /* the default is to query the column $q0 with the comparison $q1
                     * for a value $q2 */
                    $search .= " `${q[0]}` ${q[1]} :q$i ";
                    $params[':q'.$i] = $q[2];
                }
            }
        }
        return array($search, $params);
    }

}


//__END__
